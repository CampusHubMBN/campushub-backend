{{-- resources/views/emails/event_updated.blade.php --}}
<x-mail::message>
# Mise à jour d'un événement 📢

Bonjour **{{ $userName }}**,

L'événement **{{ $eventTitle }}** auquel vous êtes inscrit(e) a été modifié. Voici les informations à jour :

<x-mail::panel>
**{{ $eventType }}**

📅 **Date :** {{ $eventDate }}

📍 **Lieu :** {{ $eventLocation }}
</x-mail::panel>

<x-mail::button :url="$eventUrl">
Voir les détails de l'événement
</x-mail::button>

Si ces changements ne vous conviennent pas, vous pouvez vous désinscrire depuis la page de l'événement.

À bientôt,<br>
L'équipe {{ config('app.name') }}
</x-mail::message>
