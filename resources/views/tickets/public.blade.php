@extends('layouts.guest')

@section('title', 'Ticket ' . $ticket->ticket_id)

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <!-- Ticket Info Card -->
            <div class="shad-card mb-4">
                <div class="px-6 py-4 border-bottom">
                    <h5 class="m-0 font-weight-bold text-gray-900">
                        <i class="fas fa-ticket-alt mr-2"></i> {{ $ticket->ticket_id }}
                    </h5>
                </div>
                <div class="p-6">
                    <div class="mb-4">
                        <h6 class="font-weight-bold mb-2">{{ $ticket->title }}</h6>
                        <p class="text-muted mb-0">{{ $ticket->description }}</p>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <small class="text-muted">Status</small>
                            <div>
                                @if($ticket->status == 'Open')
                                    <span class="shad-badge shad-badge-yellow">{{ $ticket->status }}</span>
                                @elseif($ticket->status == 'In Progress')
                                    <span class="shad-badge shad-badge-blue">{{ $ticket->status }}</span>
                                @elseif($ticket->status == 'Closed')
                                    <span class="shad-badge shad-badge-green">{{ $ticket->status }}</span>
                                @else
                                    <span class="shad-badge shad-badge-outline">{{ $ticket->status }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <small class="text-muted">Priority</small>
                            <div><strong>{{ $ticket->priority }}</strong></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <small class="text-muted">Category</small>
                            <div>{{ $ticket->category }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <small class="text-muted">Created</small>
                            <div>{{ $ticket->created_at->format('d M Y, h:i A') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Reply Form -->
            <div class="shad-card mb-4">
                <div class="px-6 py-4 border-bottom">
                    <h6 class="m-0 font-weight-bold text-gray-900">Add Reply</h6>
                </div>
                <div class="p-6">
                    <form action="{{ route('tickets.public.reply', $ticket->public_token) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="shad-label">Your Email</label>
                            <input type="email" name="email" class="shad-input" value="{{ $ticket->email }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="shad-label">Your Message</label>
                            <textarea name="message" class="shad-input" rows="4" placeholder="Type your reply here..." required></textarea>
                        </div>
                        <button type="submit" class="shad-btn shad-btn-primary">
                            <i class="fas fa-paper-plane mr-1"></i> Send Reply
                        </button>
                    </form>
                    
                    @if(session('success'))
                        <div class="alert alert-success mt-3">{{ session('success') }}</div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger mt-3">{{ session('error') }}</div>
                    @endif
                </div>
            </div>

            <!-- Activity Logs -->
            <div class="shad-card">
                <div class="px-6 py-4 border-bottom">
                    <h6 class="m-0 font-weight-bold text-gray-900">Conversation History</h6>
                </div>
                <div class="p-6">
                    @if($logs->count() > 0)
                        @foreach($logs as $log)
                            <div class="shad-card mb-3 p-3" style="background-color: #f8f9fa;">
                                <div class="mb-2">
                                    <strong class="text-dark">{{ $log->user->email ?? $log->guest_email ?? 'System' }}</strong>
                                    @if($log->is_staff)
                                        <span class="shad-badge shad-badge-blue ml-2">Staff</span>
                                    @endif
                                </div>
                                <div class="mb-2">
                                    <small class="text-muted">{{ $log->created_at->format('Y-m-d H:i:s') }}</small>
                                </div>
                                <div class="text-dark">
                                    {{ $log->message }}
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-inbox fa-2x mb-2"></i>
                            <p class="mb-0">No messages yet</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
