<?php
// app/Http/Controllers/CommentController.php

namespace App\Http\Controllers;

use App\Enums\RealtimeEvent;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Models\CommentVote;
use App\Models\Post;
use App\Traits\PublishesRedisEvents;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    use PublishesRedisEvents;
    /**
     * GET /posts/{postId}/comments
     * Commentaires racines paginés + leurs replies (niveau 1)
     */
    public function index(Request $request, string $postId): JsonResponse
    {
        $post = Post::findOrFail($postId);

        $isQuestion = $post->type === 'question';

        $query = Comment::with(['author.info', 'replies.author.info'])
            ->where('post_id', $post->id)
            ->whereNull('parent_id')
            ->withTrashed();

        if ($isQuestion) {
            // Accepted answer first, then by votes descending, then newest
            $query->orderByDesc('is_accepted_answer')
                  ->orderByDesc('votes_count')
                  ->orderByDesc('created_at');
        } else {
            $query->latest();
        }

        $comments = $query->paginate(20);

        return response()->json([
            'data' => CommentResource::collection($comments->items()),
            'meta' => [
                'current_page' => $comments->currentPage(),
                'last_page'    => $comments->lastPage(),
                'per_page'     => $comments->perPage(),
                'total'        => $comments->total(),
            ],
        ]);
    }

    /**
     * GET /comments/{id}/replies
     * Replies d'un commentaire (pagination si nombreuses)
     */
    public function replies(Request $request, string $id): JsonResponse
    {
        $comment = Comment::findOrFail($id);

        $replies = Comment::with(['author.info'])
            ->where('parent_id', $comment->id)
            ->withTrashed()
            ->latest()
            ->paginate(10);

        return response()->json([
            'data' => CommentResource::collection($replies->items()),
            'meta' => [
                'current_page' => $replies->currentPage(),
                'last_page'    => $replies->lastPage(),
                'per_page'     => $replies->perPage(),
                'total'        => $replies->total(),
            ],
        ]);
    }

    /**
     * POST /posts/{postId}/comments
     * Créer un commentaire ou une réponse
     * Body: { content, parent_id? }
     */
    public function store(Request $request, string $postId): JsonResponse
    {
        $post = Post::findOrFail($postId);

        // On ne commente que les posts publiés
        abort_unless($post->status === 'published', 422, 'Ce post n\'est pas publié');

        $data = $request->validate([
            'content'   => 'required|string|max:2000',
            'parent_id' => 'nullable|uuid|exists:comments,id',
        ]);

        // Vérifier que le parent appartient bien à ce post
        if (!empty($data['parent_id'])) {
            $parent = Comment::findOrFail($data['parent_id']);
            abort_unless($parent->post_id === $post->id, 422, 'Parent invalide');
            // On n'autorise qu'un niveau de réponse (pas de réponse à une réponse)
            abort_if(!is_null($parent->parent_id), 422, 'Réponses imbriquées non supportées');
        }

        $comment = Comment::create([
            'post_id'   => $post->id,
            'author_id' => $request->user()->id,
            'parent_id' => $data['parent_id'] ?? null,
            'content'   => $data['content'],
        ]);

        $comment->load(['author.info']);

        $this->publishEvent(RealtimeEvent::COMMENT_CREATED, [
            'postId'                => $post->id,
            'postTitle'             => $post->title,
            'authorId'              => $request->user()->id,
            'authorName'            => $request->user()->name,
            'postOwnerId'           => $post->author_id,
            'parentCommentAuthorId' => isset($parent) ? $parent->author_id : null,
        ]);

        return response()->json([
            'data'    => new CommentResource($comment),
            'message' => 'Commentaire ajouté',
        ], 201);
    }

    /**
     * PATCH /comments/{id}
     * Modifier son propre commentaire
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $comment = Comment::findOrFail($id);
        abort_unless($request->user()->id === $comment->author_id, 403);

        $data = $request->validate([
            'content' => 'required|string|max:2000',
        ]);

        $comment->update($data);

        return response()->json([
            'data'    => new CommentResource($comment->fresh(['author.info'])),
            'message' => 'Commentaire modifié',
        ]);
    }

    /**
     * DELETE /comments/{id}
     * Soft delete — garde la structure, masque le contenu
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        $comment = Comment::findOrFail($id);
        abort_unless($request->user()->id === $comment->author_id, 403);

        $comment->delete();

        return response()->json(['message' => 'Commentaire supprimé']);
    }

    /**
     * POST /comments/{id}/vote
     * Body: { value: 1 | -1 }
     * Toggle: same value removes the vote
     */
    public function vote(Request $request, string $id): JsonResponse
    {
        $request->validate([
            'value' => 'required|in:1,-1',
        ]);

        $comment  = Comment::findOrFail($id);
        $userId   = $request->user()->id;
        $newValue = (int) $request->input('value');

        // Cannot vote on own comment
        abort_if($comment->author_id === $userId, 403, 'Vous ne pouvez pas voter pour votre propre réponse');

        $existing = CommentVote::where('comment_id', $comment->id)
                               ->where('user_id', $userId)
                               ->first();

        if ($existing) {
            if ($existing->value === $newValue) {
                // Toggle off: remove the vote
                $comment->update(['votes_count' => $comment->votes_count - $existing->value]);
                $existing->delete();
                $userVote = null;
            } else {
                // Switch direction: remove old, add new
                $comment->update(['votes_count' => $comment->votes_count - $existing->value + $newValue]);
                $existing->update(['value' => $newValue]);
                $userVote = $newValue;
            }
        } else {
            CommentVote::create([
                'comment_id' => $comment->id,
                'user_id'    => $userId,
                'value'      => $newValue,
            ]);
            $comment->update(['votes_count' => $comment->votes_count + $newValue]);
            $userVote = $newValue;
        }

        $comment->refresh();

        return response()->json([
            'data' => [
                'votes_count' => $comment->votes_count,
                'user_vote'   => $userVote,
            ],
        ]);
    }

    /**
     * POST /comments/{id}/accept
     * Only the post author can mark an answer as accepted (toggle)
     */
    public function accept(Request $request, string $id): JsonResponse
    {
        $comment = Comment::with('post')->findOrFail($id);
        $post    = $comment->post;

        abort_unless($request->user()->id === $post->author_id, 403, 'Seul l\'auteur peut accepter une réponse');
        abort_if($post->type !== 'question', 422, 'Ce post n\'est pas une question');
        abort_if(!is_null($comment->parent_id), 422, 'Seules les réponses racines peuvent être acceptées');

        $isAlreadyAccepted = $comment->is_accepted_answer;

        // Unaccept any previously accepted answer
        Comment::where('post_id', $post->id)
               ->where('is_accepted_answer', true)
               ->update(['is_accepted_answer' => false]);

        // Toggle: if it was already accepted, we just un-accept (leave all false)
        if (!$isAlreadyAccepted) {
            $comment->update(['is_accepted_answer' => true]);
        }

        $comment->refresh();

        return response()->json([
            'data' => [
                'id'                 => $comment->id,
                'is_accepted_answer' => $comment->is_accepted_answer,
            ],
            'message' => $comment->is_accepted_answer
                ? 'Réponse acceptée'
                : 'Réponse désacceptée',
        ]);
    }
}