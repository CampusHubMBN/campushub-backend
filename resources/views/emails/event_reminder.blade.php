{{-- resources/views/emails/event_reminder.blade.php --}}
<x-mail::message>
# Rappel : votre événement commence demain ! ⏰

Bonjour **{{ $userName }}**,

Nous vous rappelons que vous êtes inscrit(e) à **{{ $eventTitle }}** qui commence **demain**.

<x-mail::panel>
📅 **Date :** {{ $eventDate }}

📍 **Lieu :** {{ $eventLocation }}
</x-mail::panel>

<x-mail::button :url="$eventUrl">
Voir l'événement
</x-mail::button>

À demain !<br>
L'équipe {{ config('app.name') }}
</x-mail::message>
