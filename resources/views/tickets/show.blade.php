@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Ticket Details: {{ $ticket['ticket_id'] ?? 'N/A' }}</h1>
        <a href="{{ route('tickets.my-tickets') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to My Tickets
        </a>
    </div>

    @include('tickets.partials.ticket-detail-body')

</div>
@endsection
