{{-- resources/views/emails/event_confirmation.blade.php --}}
<x-mail::message>
# Inscription confirmée ! 🎉

Bonjour **{{ $userName }}**,

Votre inscription à l'événement **{{ $eventTitle }}** a bien été enregistrée.

<x-mail::panel>
**{{ $eventType }}**

📅 **Date :** {{ $eventDate }}

📍 **Lieu :** {{ $eventLocation }}
</x-mail::panel>

<x-mail::button :url="$eventUrl">
Voir l'événement
</x-mail::button>

Vous recevrez un rappel 24h avant le début de l'événement.

À bientôt,<br>
L'équipe {{ config('app.name') }}

---

<small style="color: #999;">
Si vous souhaitez vous désinscrire, rendez-vous sur la page de l'événement.
</small>
</x-mail::message>
