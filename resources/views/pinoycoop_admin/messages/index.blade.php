@extends('pinoycoop_admin.layouts.app', ['title' => 'Messages'])

@section('content')
    <style>
        .message-card { display:grid; gap:.5rem; }
        .message-meta { display:flex; flex-wrap:wrap; gap:.55rem 1rem; color:#607993; font-size:.84rem; }
        .message-text { margin:.3rem 0 0; color:#334766; line-height:1.55; white-space:pre-wrap; }
        .message-status { display:inline-flex; border-radius:999px; padding:.2rem .55rem; font-size:.74rem; font-weight:700; }
        .message-unread { background:rgba(0,167,225,.14); color:#11658a; }
        .message-read { background:rgba(127,143,178,.18); color:#4a5c84; }
        .message-actions { display:flex; flex-wrap:wrap; gap:.4rem; }
    </style>

    <div class="top">
        <h2>Messages</h2>
    </div>

    <div class="card">
        <div class="head">Contact Form Submissions</div>
        <div class="body">
            @forelse ($messages as $message)
                <div class="message-card" style="padding:1rem 0; border-bottom:1px solid #edf2f7;">
                    <div style="display:flex; justify-content:space-between; gap:1rem; align-items:flex-start;">
                        <div>
                            <strong>{{ $message->name }}</strong>
                            <div class="message-meta">
                                <span><a href="mailto:{{ $message->email }}">{{ $message->email }}</a></span>
                                <span>IP: {{ $message->ip_address ?? 'Unknown' }}</span>
                                <span>{{ optional($message->created_at)->format('M d, Y h:i A') }}</span>
                            </div>
                        </div>
                        <span class="message-status {{ $message->read_at ? 'message-read' : 'message-unread' }}">
                            {{ $message->read_at ? 'Read' : 'Unread' }}
                        </span>
                    </div>

                    <p class="message-text">{{ $message->message }}</p>

                    <div class="message-actions">
                        @unless ($message->read_at)
                            <form method="POST" action="{{ route('pinoycoop.admin.messages.read', $message) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-g btn-sm">Mark Read</button>
                            </form>
                        @endunless
                        <form method="POST" action="{{ route('pinoycoop.admin.messages.destroy', $message) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-d btn-sm" onclick="return confirm('Delete this message?')">Delete</button>
                        </form>
                    </div>
                </div>
            @empty
                <p style="margin:0; color:#6d839b;">No contact messages yet.</p>
            @endforelse

            @if ($messages->hasPages())
                <div style="margin-top:1rem;">
                    {{ $messages->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
