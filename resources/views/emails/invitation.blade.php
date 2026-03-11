{{-- resources/views/emails/invitation.blade.php --}}
<x-mail::message>
# Bienvenue sur CampusHub ! 🎓

Vous avez été invité(e) à rejoindre la plateforme **CampusHub** en tant que **{{ $roleName }}**.

Cliquez sur le bouton ci-dessous pour créer votre compte :

<x-mail::button :url="$invitationUrl">
Créer mon compte
</x-mail::button>

**Ce lien est valable jusqu'au {{ $expiresAt }}.**

Si vous n'avez pas demandé cette invitation, vous pouvez ignorer cet email.

Cordialement,<br>
L'équipe {{ config('app.name') }}

---

<small style="color: #999;">
Si le bouton ne fonctionne pas, copiez ce lien dans votre navigateur :<br>
{{ $invitationUrl }}
</small>
</x-mail::message>