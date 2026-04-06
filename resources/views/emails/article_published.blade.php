{{-- resources/views/emails/article_published.blade.php --}}
<x-mail::message>
# Nouvel article publié 📚

**{{ $authorName }}** vient de publier un nouvel article sur CampusHub.

## {{ $articleTitle }}

@if($articleDescription)
{{ $articleDescription }}
@endif

<x-mail::button :url="$articleUrl">
Lire l'article
</x-mail::button>

Cordialement,<br>
L'équipe {{ config('app.name') }}

---

<small style="color: #999;">
Si le bouton ne fonctionne pas, copiez ce lien dans votre navigateur :<br>
{{ $articleUrl }}
</small>
</x-mail::message>
