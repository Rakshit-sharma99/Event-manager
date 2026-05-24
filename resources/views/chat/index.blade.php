@extends('layouts.app', ['title' => 'Messages - Eventra'])
@section('page-title', 'Messages')

@section('content')
<div id="chat-app" style="display: flex; height: calc(100vh - 120px); border: 1px solid #d8d8d8; border-radius: 12px; overflow: hidden; background: #fff;">

    <!-- ═══ LEFT: Conversation List ═══ -->
    <div id="conv-sidebar" style="width: 320px; min-width: 280px; border-right: 1px solid #e0e0e0; display: flex; flex-direction: column; background: #f9f9f9;">
        <!-- Header -->
        <div style="padding: 16px; border-bottom: 1px solid #e0e0e0; background: #fff;">
            <h3 style="margin: 0 0 8px; font-size: 1.2rem; font-weight: 700;">💬 Messages</h3>
            <input id="conv-search" type="text" placeholder="Search conversations..." oninput="filterConversations()" style="width: 100%; padding: 8px 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 0.9rem; box-sizing: border-box;">
        </div>

        <!-- Conversation items -->
        <div id="conv-list" style="flex: 1; overflow-y: auto;">
            @if(empty($conversations))
                <div style="padding: 32px 16px; text-align: center; color: #999;">
                    <p style="font-size: 1.5rem; margin: 0 0 8px;">📭</p>
                    <p style="font-weight: 600;">No conversations yet</p>
                    <p style="font-size: 0.85rem;">When you book a vendor or receive a booking, chats will appear here.</p>
                </div>
            @else
                @foreach($conversations as $conv)
                    <div class="conv-item" data-booking-id="{{ $conv['booking_id'] }}" data-name="{{ strtolower($conv['other_name'] . ' ' . $conv['event_name']) }}"
                         onclick="selectConversation('{{ $conv['booking_id'] }}')"
                         style="padding: 12px 16px; border-bottom: 1px solid #eee; cursor: pointer; transition: background 0.15s; {{ ($activeBookingId ?? '') === $conv['booking_id'] ? 'background: #e8f0fe;' : '' }}"
                         onmouseenter="if(!this.classList.contains('active-conv'))this.style.background='#f0f0f0'"
                         onmouseleave="if(!this.classList.contains('active-conv'))this.style.background=''">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <strong style="font-size: 0.95rem;">{{ $conv['other_name'] }}</strong>
                            <small style="color: #999; font-size: 0.75rem;">{{ $conv['last_time'] }}</small>
                        </div>
                        <div style="font-size: 0.82rem; color: #666; margin-top: 2px;">{{ $conv['event_name'] }}</div>
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 4px;">
                            <span style="font-size: 0.82rem; color: #888; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 180px;">{{ $conv['last_message'] ?: 'No messages yet' }}</span>
                            <span style="
                                font-size: 0.7rem; font-weight: 700; text-transform: uppercase; padding: 2px 6px; border-radius: 4px;
                                @if($conv['status'] === 'accepted') background: rgba(16,185,129,0.12); color: #059669;
                                @elseif($conv['status'] === 'declined') background: rgba(239,68,68,0.12); color: #dc2626;
                                @elseif($conv['status'] === 'negotiating') background: rgba(245,158,11,0.12); color: #d97706;
                                @else background: rgba(136,136,136,0.1); color: #888;
                                @endif
                            ">{{ $conv['status'] }}</span>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>

    <!-- ═══ RIGHT: Chat Area ═══ -->
    <div id="chat-area" style="flex: 1; display: flex; flex-direction: column; background: #f0f0f0;">
        <!-- Empty state -->
        <div id="chat-empty" style="flex: 1; display: flex; align-items: center; justify-content: center; flex-direction: column; color: #999;">
            <p style="font-size: 3rem; margin: 0;">💬</p>
            <h3 style="margin: 8px 0 4px; color: #666;">Select a conversation</h3>
            <p style="font-size: 0.9rem;">Choose a chat from the sidebar to start messaging</p>
        </div>

        <!-- Chat header (hidden initially) -->
        <div id="chat-header" style="display: none; padding: 12px 20px; background: #fff; border-bottom: 1px solid #e0e0e0;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <strong id="chat-header-name" style="font-size: 1.05rem;"></strong>
                    <span id="chat-header-event" style="font-size: 0.85rem; color: #888; margin-left: 8px;"></span>
                </div>
                <span id="chat-header-status" style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; padding: 3px 8px; border-radius: 4px;"></span>
            </div>
        </div>

        <!-- Messages container (hidden initially) -->
        <div id="chat-messages" style="display: none; flex: 1; overflow-y: auto; padding: 16px 20px;">
        </div>

        <!-- Input bar (hidden initially) -->
        <div id="chat-input-bar" style="display: none; padding: 12px 16px; background: #fff; border-top: 1px solid #e0e0e0;">
            <div style="display: flex; gap: 8px; align-items: center;">
                <input id="chat-msg-input" type="text" placeholder="Type a message..." style="flex: 1; padding: 10px 16px; border: 1px solid #ddd; border-radius: 24px; font-size: 0.95rem; outline: none;" onkeydown="if(event.key==='Enter'){sendMsg();event.preventDefault();}">
                <button onclick="sendMsg()" style="background: #2563eb; color: #fff; border: none; border-radius: 50%; width: 40px; height: 40px; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; flex-shrink: 0;" title="Send">
                    ➤
                </button>
            </div>
        </div>
    </div>
</div>

<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
let activeBookingId = '{{ $activeBookingId ?? '' }}';
let pollInterval = null;

/* ── Conversation search filter ── */
function filterConversations() {
    const q = document.getElementById('conv-search').value.toLowerCase();
    document.querySelectorAll('.conv-item').forEach(el => {
        el.style.display = el.dataset.name.includes(q) ? '' : 'none';
    });
}

/* ── Select a conversation ── */
function selectConversation(bookingId) {
    activeBookingId = bookingId;

    // Highlight active item
    document.querySelectorAll('.conv-item').forEach(el => {
        el.classList.remove('active-conv');
        el.style.background = '';
    });
    const activeEl = document.querySelector(`.conv-item[data-booking-id="${bookingId}"]`);
    if (activeEl) {
        activeEl.classList.add('active-conv');
        activeEl.style.background = '#e8f0fe';
    }

    // Copy header info from the conversation item
    const name = activeEl?.querySelector('strong')?.textContent || 'Chat';
    const event = activeEl?.querySelector('div:nth-child(2)')?.textContent || '';
    const statusEl = activeEl?.querySelector('span[style*="text-transform"]');
    const status = statusEl?.textContent || '';

    document.getElementById('chat-header-name').textContent = name;
    document.getElementById('chat-header-event').textContent = event;

    const headerStatus = document.getElementById('chat-header-status');
    headerStatus.textContent = status;
    headerStatus.style.background = statusEl?.style.background || '#eee';
    headerStatus.style.color = statusEl?.style.color || '#888';

    // Show chat UI
    document.getElementById('chat-empty').style.display = 'none';
    document.getElementById('chat-header').style.display = '';
    document.getElementById('chat-messages').style.display = 'flex';
    document.getElementById('chat-messages').style.flexDirection = 'column';
    document.getElementById('chat-messages').style.gap = '4px';
    document.getElementById('chat-input-bar').style.display = '';

    // Load messages
    loadMessages();

    // Start polling
    if (pollInterval) clearInterval(pollInterval);
    pollInterval = setInterval(loadMessages, 3000);

    // Focus input
    document.getElementById('chat-msg-input').focus();

    // On mobile, hide sidebar
    if (window.innerWidth < 768) {
        document.getElementById('conv-sidebar').style.display = 'none';
    }
}

/* ── Load messages ── */
let lastMsgCount = 0;
function loadMessages() {
    if (!activeBookingId) return;

    fetch('/messages/' + activeBookingId, {
        headers: { 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(msgs => {
        const container = document.getElementById('chat-messages');
        const shouldScroll = lastMsgCount !== msgs.length;
        lastMsgCount = msgs.length;

        if (!msgs.length) {
            container.innerHTML = '<div style="text-align: center; color: #999; margin: auto; padding: 32px;"><p style="font-size: 2rem;">👋</p><p>No messages yet. Say hello!</p></div>';
            return;
        }

        let html = '';
        let lastDate = '';
        msgs.forEach(m => {
            // Date separator
            if (m.date !== lastDate) {
                html += `<div style="text-align: center; margin: 12px 0;">
                    <span style="background: rgba(0,0,0,0.06); color: #666; padding: 4px 12px; border-radius: 8px; font-size: 0.75rem; font-weight: 600;">${esc(m.date)}</span>
                </div>`;
                lastDate = m.date;
            }

            const align = m.is_mine ? 'flex-end' : 'flex-start';
            const bg = m.is_mine ? '#dcf8c6' : '#fff';
            const tail = m.is_mine ? 'border-radius: 12px 12px 0 12px;' : 'border-radius: 12px 12px 12px 0;';
            const nameColor = m.sender_role === 'vendor' ? '#059669' : '#2563eb';

            html += `<div style="align-self: ${align}; max-width: 70%; padding: 8px 12px; background: ${bg}; ${tail} box-shadow: 0 1px 2px rgba(0,0,0,0.06); margin: 2px 0;">
                <div style="font-size: 0.75rem; font-weight: 700; color: ${nameColor}; margin-bottom: 2px;">${esc(m.sender_name)}</div>
                <div style="font-size: 0.93rem; line-height: 1.4; word-break: break-word;">${esc(m.message)}</div>
                <div style="font-size: 0.7rem; color: #999; text-align: right; margin-top: 2px;">${esc(m.time)}</div>
            </div>`;
        });

        container.innerHTML = html;
        if (shouldScroll) container.scrollTop = container.scrollHeight;
    })
    .catch(() => {});
}

/* ── Send message ── */
function sendMsg() {
    const input = document.getElementById('chat-msg-input');
    const msg = input.value.trim();
    if (!msg || !activeBookingId) return;
    input.value = '';

    fetch('/messages/' + activeBookingId, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
        body: JSON.stringify({ message: msg })
    }).then(() => {
        loadMessages();
    });
}

/* ── Escape HTML ── */
function esc(s) {
    if (!s) return '';
    const d = document.createElement('div');
    d.textContent = s;
    return d.innerHTML;
}

/* ── Auto-select if booking param present ── */
document.addEventListener('DOMContentLoaded', () => {
    if (activeBookingId) {
        selectConversation(activeBookingId);
    }
});

/* ── Mobile: back button for sidebar ── */
window.addEventListener('resize', () => {
    if (window.innerWidth >= 768) {
        document.getElementById('conv-sidebar').style.display = '';
    }
});
</script>

<style>
    @media (max-width: 767px) {
        #chat-app { flex-direction: column !important; height: calc(100vh - 80px) !important; }
        #conv-sidebar { width: 100% !important; min-width: 0 !important; max-height: 40vh; }
        #chat-area { min-height: 60vh; }
    }
    #chat-messages { background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23d0d0d0' fill-opacity='0.08'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E") #e5ddd5; }
</style>
@endsection
