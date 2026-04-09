<x-mail::message>
# Ticket Submitted Successfully

Hi **{{ $ticket->full_name }}**,

Your support ticket has been received and is now being reviewed by our team. We will get back to you as soon as possible.

<x-mail::panel>
**Ticket ID:** {{ $ticket->ticket_id }}
**Title:** {{ $ticket->title }}
**Category:** {{ $ticket->category }}
**Priority:** {{ $ticket->priority }}
**Status:** {{ $ticket->status }}
**Submitted:** {{ $ticket->created_at->format('d M Y, h:i A') }}
</x-mail::panel>

If you have any updates or additional information regarding this ticket, please do not hesitate to contact us.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
