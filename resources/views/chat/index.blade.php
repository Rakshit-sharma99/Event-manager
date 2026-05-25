@extends('layouts.app', ['title' => 'Messages - Eventra'])
@section('page-title', 'Messages')

@section('content')
<style>
    /* Premium WhatsApp-Inspired Messaging Interface */
    .whatsapp-container {
        display: flex;
        height: calc(100vh - 120px);
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        overflow: hidden;
        background: #fff;
        box-shadow: 0 4px 20px rgba(0,0,0,0.04);
        font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, Roboto, Helvetica, Arial, sans-serif;
    }

    /* Sidebars and Panels */
    .whatsapp-sidebar {
        width: 380px;
        min-width: 320px;
        border-right: 1px solid #eaeaea;
        display: flex;
        flex-direction: column;
        background: #ffffff;
        position: relative;
    }
    .whatsapp-main {
        flex: 1;
        display: flex;
        flex-direction: column;
        background: #efeae2; /* Warm classic WhatsApp background */
        position: relative;
    }

    /* Sidebar Headers and Search */
    .sidebar-search-container {
        padding: 12px 16px;
        background: #f0f2f5;
        border-bottom: 1px solid #e1e1e1;
    }
    .sidebar-tabs {
        display: flex;
        background: #f0f2f5;
        border-bottom: 1px solid #e1e1e1;
        padding: 6px 12px 0 12px;
        gap: 8px;
    }
    .sidebar-tab-btn {
        background: none;
        border: none;
        padding: 8px 12px;
        font-weight: 700;
        font-size: 0.85rem;
        color: #64748b;
        cursor: pointer;
        border-bottom: 3px solid transparent;
        transition: all 0.2s;
    }
    .sidebar-tab-btn.active {
        color: #008069; /* WhatsApp Teal */
        border-bottom-color: #008069;
    }
    .search-wrapper {
        position: relative;
        display: flex;
        align-items: center;
        background: #ffffff;
        border-radius: 8px;
        padding: 0 12px;
        border: 1px solid #eaeaea;
    }
    .search-wrapper input {
        width: 100%;
        border: none;
        padding: 8px 6px;
        outline: none;
        font-size: 0.9rem;
    }
    .search-clear-btn {
        background: none;
        border: none;
        color: #94a3b8;
        cursor: pointer;
        font-size: 1rem;
        padding: 4px;
        display: none;
    }

    /* Conversation Items list */
    .conv-list-scrollable {
        flex: 1;
        overflow-y: auto;
        background: #ffffff;
    }
    .conv-group-title {
        padding: 12px 16px 4px 16px;
        font-size: 0.75rem;
        font-weight: 800;
        color: #008069;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        background: #f8fafc;
    }
    .whatsapp-conv-item {
        display: flex;
        padding: 12px 16px;
        border-bottom: 1px solid #f1f5f9;
        cursor: pointer;
        transition: background 0.15s;
        align-items: center;
        gap: 12px;
    }
    .whatsapp-conv-item:hover {
        background: #f5f6f6;
    }
    .whatsapp-conv-item.active-chat {
        background: #eaeaea;
    }

    /* Initial/Avatar Badges */
    .avatar-badge {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        color: #475569;
        font-size: 1.1rem;
        flex-shrink: 0;
        position: relative;
    }
    .online-indicator {
        position: absolute;
        bottom: 1px;
        right: 1px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: #10b981; /* Online green */
        border: 2px solid #ffffff;
        display: none;
    }

    /* Conversation Info */
    .conv-details {
        flex: 1;
        min-width: 0;
    }
    .conv-row-1 {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 4px;
    }
    .conv-name {
        font-weight: 700;
        color: #111b21;
        font-size: 0.95rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .conv-time {
        font-size: 0.75rem;
        color: #667781;
    }
    .conv-row-2 {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .conv-last-msg {
        font-size: 0.83rem;
        color: #667781;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        flex-grow: 1;
        margin-right: 8px;
    }
    .conv-unread-badge {
        background: #25d366; /* WhatsApp Light Green */
        color: #ffffff;
        font-size: 0.72rem;
        font-weight: 700;
        min-width: 18px;
        height: 18px;
        border-radius: 9px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0 4px;
        box-sizing: border-box;
    }

    /* Active Chat Panels */
    .active-chat-header {
        background: #f0f2f5;
        padding: 10px 16px;
        border-bottom: 1px solid #e1e1e1;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .active-chat-avatar-details {
        display: flex;
        align-items: center;
        gap: 12px;
        cursor: pointer;
    }
    .active-chat-header-name {
        font-weight: 700;
        color: #111b21;
        font-size: 1rem;
    }
    .active-chat-header-status {
        font-size: 0.78rem;
        color: #667781;
        margin-top: 1px;
    }
    .active-chat-controls {
        display: flex;
        gap: 16px;
        color: #54656f;
        font-size: 1.2rem;
    }
    .active-chat-controls i {
        cursor: pointer;
        transition: color 0.15s;
    }
    .active-chat-controls i:hover {
        color: #111b21;
    }

    /* Messages History */
    .messages-scrollable {
        flex: 1;
        overflow-y: auto;
        padding: 20px 24px;
        display: flex;
        flex-direction: column;
        gap: 8px;
        background-image: url("data:image/svg+xml,%3Csvg width='80' height='80' viewBox='0 0 80 80' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%239C92AC' fill-opacity='0.06'%3E%3Cpath d='M50 50c0-5.523 4.477-10 10-10s10 4.477 10 10-4.477 10-10 10c0 5.523-4.477 10-10 10s-10-4.477-10-10 4.477-10 10-10zM10 10c0-5.523 4.477-10 10-10s10 4.477 10 10-4.477 10-10 10c0 5.523-4.477 10-10 10S0 25.523 0 20s4.477-10 10-10zm10 8c4.418 0 8-3.582 8-8s-3.582-8-8-8-8 3.582-8 8 3.582 8 8 8zm40 40c4.418 0 8-3.582 8-8s-3.582-8-8-8-8 3.582-8 8 3.582 8 8 8z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        background-color: #efeae2;
    }

    /* Message Bubble styling */
    .msg-bubble {
        max-width: 65%;
        padding: 8px 12px;
        box-shadow: 0 1px 2px rgba(0,0,0,0.08);
        font-size: 0.93rem;
        line-height: 1.4;
        word-wrap: break-word;
        position: relative;
    }
    .msg-sent {
        align-self: flex-end;
        background: #d9fdd3; /* WhatsApp light green bubble */
        border-radius: 8px 0 8px 8px;
    }
    .msg-received {
        align-self: flex-start;
        background: #ffffff;
        border-radius: 0 8px 8px 8px;
    }
    .bubble-meta {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 4px;
        font-size: 0.68rem;
        color: #667781;
        margin-top: 4px;
        text-align: right;
    }
    .receipt-tick {
        font-size: 0.8rem;
        font-weight: bold;
    }
    .tick-read {
        color: #53bdeb; /* WhatsApp blue double-ticks */
    }
    .tick-delivered {
        color: #8696a0; /* WhatsApp gray double-ticks */
    }
    .tick-sent {
        color: #8696a0; /* Single tick gray */
    }

    /* Message Input Bar */
    .message-input-bar {
        background: #f0f2f5;
        padding: 10px 16px;
        display: flex;
        align-items: center;
        gap: 12px;
        border-top: 1px solid #e1e1e1;
    }
    .chat-attachment-btn, .chat-voice-btn, .chat-emoji-btn {
        background: none;
        border: none;
        font-size: 1.3rem;
        color: #54656f;
        cursor: pointer;
        padding: 4px;
        transition: color 0.2s;
    }
    .chat-attachment-btn:hover, .chat-voice-btn:hover, .chat-emoji-btn:hover {
        color: #111b21;
    }
    .message-input-bar input {
        flex: 1;
        border: none;
        outline: none;
        padding: 10px 16px;
        border-radius: 8px;
        font-size: 0.95rem;
        background: #ffffff;
    }

    /* Mobile Back Button */
    .mobile-back-btn {
        display: none;
        background: none;
        border: none;
        font-size: 1.2rem;
        color: #54656f;
        cursor: pointer;
        padding: 4px 8px;
    }

    /* Inactive Guest / Search Invite Styling */
    .uncontacted-action-panel {
        display: flex;
        flex-direction: column;
        gap: 6px;
        margin-top: 4px;
    }
    .uncontacted-status-badge {
        font-size: 0.72rem;
        background: rgba(245,158,11,0.12);
        color: #b45309;
        font-weight: 700;
        padding: 2px 8px;
        border-radius: 4px;
        align-self: flex-start;
    }
    .btn-invite-register {
        background: #008069;
        color: #ffffff;
        border: none;
        padding: 5px 10px;
        border-radius: 6px;
        font-size: 0.78rem;
        font-weight: 700;
        cursor: pointer;
        transition: background 0.15s;
    }
    .btn-invite-register:hover {
        background: #006b57;
    }
    .btn-start-chat-search {
        background: #2563eb;
        color: #ffffff;
        border: none;
        padding: 5px 10px;
        border-radius: 6px;
        font-size: 0.78rem;
        font-weight: 700;
        cursor: pointer;
    }

    /* Empty states */
    .whatsapp-empty-state {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        color: #667781;
        text-align: center;
        background: #f8fafc;
        padding: 24px;
    }

    /* Mobile Responsive Layout overrides */
    @media (max-width: 767px) {
        .whatsapp-sidebar {
            width: 100% !important;
            min-width: 0 !important;
            display: flex;
        }
        .whatsapp-main {
            display: none;
            width: 100% !important;
            height: calc(100vh - 120px) !important;
            position: absolute;
            top: 0;
            left: 0;
            z-index: 10;
        }
        .mobile-back-btn {
            display: block !important;
        }
        .whatsapp-container.show-chat .whatsapp-sidebar {
            display: none !important;
        }
        .whatsapp-container.show-chat .whatsapp-main {
            display: flex !important;
        }
    }
</style>

<div class="whatsapp-container" id="whatsapp-shell">
    
    <!-- ═══ SIDEBAR: Conversation List + Sticky Search ═══ -->
    <div class="whatsapp-sidebar" id="sidebar-panel">
        
        <!-- Search bar -->
        <div class="sidebar-search-container">
            <div class="search-wrapper">
                <span style="color: #667781; font-size: 1rem; margin-right: 4px;">🔍</span>
                <input id="whatsapp-search-input" type="text" placeholder="Search chats or invited guests..." oninput="debounceSearch()" autocomplete="off">
                <button class="search-clear-btn" id="search-clear-btn" onclick="clearSearch()">×</button>
            </div>
        </div>

        <!-- Sticky Navigation tabs (placeholders) -->
        @if($user->role === 'planner')
            <div class="sidebar-tabs">
                <button class="sidebar-tab-btn active" id="tab-guest" onclick="switchSidebarTab('guest')">Guests</button>
                <button class="sidebar-tab-btn" id="tab-vendor" onclick="switchSidebarTab('vendor')">Vendors</button>
            </div>
        @endif

        <!-- Conversation Scroll list -->
        <div class="conv-list-scrollable" id="conv-list-container">
            <!-- Dynamic list populated via JS -->
            <div id="default-chat-list">
                @php
                    $filteredInitialConvs = $conversations;
                    if ($user->role === 'planner') {
                        $filteredInitialConvs = array_filter($conversations, function($c) {
                            return ($c['other_role'] ?? '') === 'guest';
                        });
                    }
                @endphp
                @if(empty($filteredInitialConvs))
                    <div class="whatsapp-empty-state" style="padding-top: 60px; background: none;">
                        <span style="font-size: 2.5rem; display: block; margin-bottom: 8px;">📭</span>
                        <p style="font-weight: 700; margin-bottom: 4px; color: #111b21;">No conversations yet</p>
                        <p style="font-size: 0.82rem;">Your active chats will appear here.</p>
                    </div>
                @else
                    @foreach($filteredInitialConvs as $conv)
                        <div class="whatsapp-conv-item {{ ($activeBookingId ?? '') === $conv['booking_id'] ? 'active-chat' : '' }}" 
                             data-booking-id="{{ $conv['booking_id'] }}"
                             data-profile="{{ json_encode($conv['profile']) }}"
                             onclick="selectConversation('{{ $conv['booking_id'] }}')">
                            
                            <div class="avatar-badge" style="background: none; padding: 0;">
                                @if(!empty($conv['other_avatar']))
                                    <img src="{{ $conv['other_avatar'] }}" alt="Avatar" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">
                                @else
                                    <span style="font-weight: 700; color: #475569; display: flex; align-items: center; justify-content: center; width: 100%; height: 100%; background: #e2e8f0; border-radius: 50%;">{{ strtoupper(substr($conv['other_name'], 0, 1)) }}</span>
                                @endif
                                <div class="online-indicator"></div>
                            </div>

                            <div class="conv-details">
                                <div class="conv-row-1">
                                    <span class="conv-name">{{ $conv['other_name'] }}</span>
                                    <span class="conv-time">{{ $conv['last_time'] }}</span>
                                </div>
                                <div class="conv-row-2">
                                    <span class="conv-last-msg">
                                        @if(str_starts_with($conv['last_message'], '✓'))
                                            {{ $conv['last_message'] }}
                                        @else
                                            {{ $conv['last_message'] ?: 'No messages yet' }}
                                        @endif
                                    </span>
                                    @if($conv['message_count'] > 0)
                                        <span class="conv-unread-badge">{{ $conv['message_count'] }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
            
            <!-- Search Results Display Section (Hidden by default) -->
            <div id="search-results-section" style="display: none;">
                <!-- Group 1: Matches in current active chats -->
                <div id="search-active-chats-group">
                    <div class="conv-group-title">Chats</div>
                    <div id="search-chats-results-list"></div>
                </div>

                <!-- Group 2: Invited Guests not chatted with yet -->
                <div id="search-invited-guests-group" style="margin-top: 12px;">
                    <div class="conv-group-title">Invited Guests</div>
                    <div id="search-guests-results-list"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- ═══ MAIN PANEL: Active Chat Screen ═══ -->
    <div class="whatsapp-main" id="chat-main-panel">
        
        <!-- Empty State -->
        <div class="whatsapp-empty-state" id="chat-empty-panel">
            <span style="font-size: 4.5rem; display: block; margin-bottom: 12px; opacity: 0.85;">💬</span>
            <h2 style="font-size: 1.5rem; font-weight: 800; color: #111b21; margin: 0 0 6px 0;">Eventra Web</h2>
            <p style="font-size: 0.95rem; margin: 0; max-width: 360px; line-height: 1.5;">Select an active chat or search invited guests from the sidebar list to start exchanging real-time messages.</p>
        </div>

        <!-- Chat Panel Header (Hidden by default) -->
        <div class="active-chat-header" id="chat-header" style="display: none;">
            <div class="active-chat-avatar-details" onclick="toggleProfileSidebar()">
                <button class="mobile-back-btn" onclick="closeActiveChatOnMobile(event)">←</button>
                <div class="avatar-badge" id="chat-header-avatar" style="width: 40px; height: 40px; font-size: 0.95rem;">
                    P
                </div>
                <div>
                    <div class="active-chat-header-name" id="chat-header-name">Aarav Sharma</div>
                    <div class="active-chat-header-status" id="chat-header-event">Event: Priya Wedding</div>
                </div>
            </div>
            <div class="active-chat-controls">
                <span id="chat-header-status-badge" style="font-size: 0.72rem; font-weight: 800; text-transform: uppercase; padding: 3px 8px; border-radius: 4px; color: #475569; background: #e2e8f0; margin-right: 8px;"></span>
                <i title="Archive conversation" onclick="alert('Conversation archiving placeholder.')">📥</i>
                <i title="Pin conversation" onclick="alert('Pinned chats structure placeholder.')">📌</i>
            </div>
        </div>

        <!-- Messages scroll background -->
        <div class="messages-scrollable" id="chat-messages-container" style="display: none;">
            <!-- Message bubbles loaded dynamically -->
        </div>

        <!-- Message Input Bar -->
        <div class="message-input-bar" id="chat-input-bar" style="display: none;">
            <input id="chat-msg-input" type="text" placeholder="Type a message..." autocomplete="off" onkeydown="if(event.key==='Enter'){sendMsg(); event.preventDefault();}">
            <button onclick="sendMsg()" style="background: #008069; color: white; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 700; cursor: pointer; transition: background 0.2s;" onmouseover="this.style.background='#006b57'" onmouseout="this.style.background='#008069'">Send Message</button>
        </div>
    </div>

    <!-- ═══ FAR RIGHT: Profile Info Panel ═══ -->
    <div id="chat-profile" style="display: none; width: 320px; min-width: 280px; border-left: 1px solid #e0e0e0; background: #f9f9f9; flex-direction: column;">
        <div style="padding: 24px 16px; text-align: center; border-bottom: 1px solid #e0e0e0; background: #fff;">
            <div style="width: 80px; height: 80px; border-radius: 50%; background: #e2e8f0; margin: 0 auto 12px; display: flex; align-items: center; justify-content: center; font-size: 2.2rem; font-weight: bold; color: #475569;" id="profile-panel-avatar">👤</div>
            <h3 id="profile-name" style="margin: 0; font-size: 1.15rem; font-weight: 800; color: #111b21;"></h3>
            <p id="profile-role" style="margin: 4px 0 0; color: #008069; font-size: 0.85rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;"></p>
        </div>
        <div id="profile-details" style="padding: 16px; flex: 1; overflow-y: auto; display: flex; flex-direction: column; gap: 14px;">
            <!-- Profile details injected here via JS -->
        </div>
    </div>
</div>

<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
let activeThreadId = '{{ $activeBookingId ?? '' }}';
let messagePollInterval = null;
let sidebarPollInterval = null;
let searchDebounceTimer = null;
let currentSidebarTab = '{{ $user->role === "planner" ? "guest" : "all" }}';
let allConversations = @json($conversations);

/* ── Mobile Back Control ── */
function closeActiveChatOnMobile(event) {
    if (event) event.stopPropagation();
    activeThreadId = '';
    
    // Stop active polling
    if (messagePollInterval) clearInterval(messagePollInterval);
    
    document.getElementById('whatsapp-shell').classList.remove('show-chat');
    document.querySelectorAll('.whatsapp-conv-item').forEach(el => el.classList.remove('active-chat'));
    
    document.getElementById('chat-empty-panel').style.display = 'flex';
    document.getElementById('chat-header').style.display = 'none';
    document.getElementById('chat-messages-container').style.display = 'none';
    document.getElementById('chat-input-bar').style.display = 'none';
    document.getElementById('chat-profile').style.display = 'none';
}

/* ── Toggle Profile Panel ── */
function toggleProfileSidebar() {
    const profileEl = document.getElementById('chat-profile');
    if (profileEl.style.display === 'none' || profileEl.style.display === '') {
        profileEl.style.display = 'flex';
    } else {
        profileEl.style.display = 'none';
    }
}

/* ── Debounced Search ── */
function debounceSearch() {
    clearTimeout(searchDebounceTimer);
    searchDebounceTimer = setTimeout(() => executeSearch(), 350);
}

/* ── Clear Search Input ── */
function clearSearch() {
    const searchInp = document.getElementById('whatsapp-search-input');
    searchInp.value = '';
    searchInp.focus();
    document.getElementById('search-clear-btn').style.display = 'none';
    document.getElementById('search-results-section').style.display = 'none';
    document.getElementById('default-chat-list').style.display = 'block';
}

/* ── Search Execution ── */
function executeSearch() {
    const q = document.getElementById('whatsapp-search-input').value.trim();
    const clearBtn = document.getElementById('search-clear-btn');
    
    if (q.length === 0) {
        clearSearch();
        return;
    }
    
    clearBtn.style.display = 'block';
    
    fetch('/threads/search-guests?q=' + encodeURIComponent(q), {
        headers: { 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(data => {
        document.getElementById('default-chat-list').style.display = 'none';
        document.getElementById('search-results-section').style.display = 'block';
        
        const chatListContainer = document.getElementById('search-chats-results-list');
        const guestListContainer = document.getElementById('search-guests-results-list');
        const guestsGroup = document.getElementById('search-invited-guests-group');
        
        // Filter existing chats results based on active tab
        let filteredChats = data.chats;
        if (currentSidebarTab !== 'all') {
            filteredChats = data.chats.filter(conv => conv.other_role === currentSidebarTab);
        }
        
        // Populate Existing Chats matches
        if (filteredChats.length === 0) {
            chatListContainer.innerHTML = '<p class="plain-muted" style="padding: 12px 16px; margin: 0; font-size: 0.85rem; color:#888;">No active chats match your query.</p>';
        } else {
            chatListContainer.innerHTML = filteredChats.map(conv => {
                const avatarHtml = conv.other_avatar 
                    ? `<img src="${conv.other_avatar}" alt="Avatar" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">`
                    : `<span style="font-weight: 700; color: #475569; display: flex; align-items: center; justify-content: center; width: 100%; height: 100%; background: #e2e8f0; border-radius: 50%;">${esc(conv.other_name.substring(0, 1))}</span>`;
                
                return `
                    <div class="whatsapp-conv-item ${activeThreadId === conv.booking_id ? 'active-chat' : ''}" 
                         onclick="selectConversation('${conv.booking_id}')">
                        <div class="avatar-badge" style="background: none; padding: 0;">
                            ${avatarHtml}
                        </div>
                        <div class="conv-details">
                            <div class="conv-row-1">
                                <span class="conv-name">${esc(conv.other_name)}</span>
                                <span class="conv-time">${esc(conv.last_time)}</span>
                            </div>
                            <div class="conv-row-2">
                                <span class="conv-last-msg">${esc(conv.last_message || 'No messages yet')}</span>
                                ${conv.message_count > 0 ? `<span class="conv-unread-badge">${conv.message_count}</span>` : ''}
                            </div>
                        </div>
                    </div>
                `;
            }).join('');
        }
        
        // Populate Uncontacted Invited Guests (only show if active tab is guest or all)
        if (currentSidebarTab === 'vendor') {
            if (guestsGroup) guestsGroup.style.display = 'none';
        } else {
            if (guestsGroup) guestsGroup.style.display = 'block';
            if (data.guests.length === 0) {
                guestListContainer.innerHTML = '<p class="plain-muted" style="padding: 12px 16px; margin: 0; font-size: 0.85rem; color:#888;">No uncontacted guests found.</p>';
            } else {
                guestListContainer.innerHTML = data.guests.map(guest => {
                    let actionHtml = '';
                    
                    if (guest.is_active || guest.rsvp_status === 'yes' || guest.rsvp_status === 'maybe') {
                        // Active or RSVP accepted: ready to chat
                        actionHtml = `<button class="btn-start-chat-search" onclick="startNewChat('${guest.id}')">Start Chat</button>`;
                    } else {
                        // Inactive and no RSVP: invite needed
                        actionHtml = `
                            <div class="uncontacted-action-panel">
                                <span class="uncontacted-status-badge">Guest not active yet</span>
                                <button class="btn-invite-register" id="btn-inv-${guest.id}" onclick="sendAccountInvite('${guest.id}')">Send Invitation</button>
                            </div>
                        `;
                    }
                    
                    const avatarHtml = guest.avatar_url
                        ? `<img src="${guest.avatar_url}" alt="Avatar" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">`
                        : `<span style="font-weight: 700; color: #475569; display: flex; align-items: center; justify-content: center; width: 100%; height: 100%; background: #e2e8f0; border-radius: 50%;">${esc(guest.name.substring(0, 1))}</span>`;
                    
                    return `
                        <div class="whatsapp-conv-item" style="cursor: default;">
                            <div class="avatar-badge" style="background: none; padding: 0;">
                                ${avatarHtml}
                            </div>
                            <div class="conv-details">
                                <div class="conv-row-1">
                                    <span class="conv-name" style="font-weight: 700;">${esc(guest.name)}</span>
                                </div>
                                <div style="font-size: 0.8rem; color:#667781; margin-top:2px;">Event: ${esc(guest.event_name)}</div>
                                <div style="display:flex; justify-content:space-between; align-items:center; margin-top: 8px;">
                                    <span style="font-size: 0.8rem; color:#667781;">RSVP: <strong>${esc(guest.rsvp_status.toUpperCase())}</strong></span>
                                    ${actionHtml}
                                </div>
                            </div>
                        </div>
                    `;
                }).join('');
            }
        }
    })
    .catch(() => {});
}

/* ── Start dynamic new chat thread ── */
function startNewChat(guestId) {
    fetch('/threads/create-guest-thread', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
        body: JSON.stringify({ guest_id: guestId })
    })
    .then(r => r.json())
    .then(data => {
        if (data.ok && data.thread_id) {
            clearSearch();
            // Fetch sidebar conversations list to force loading the new thread in sidebar instantly
            loadSidebarConversations().then(() => {
                selectConversation(data.thread_id);
            });
        }
    })
    .catch(() => {});
}

/* ── Send register invitation email ── */
function sendAccountInvite(guestId) {
    const btn = document.getElementById('btn-inv-' + guestId);
    if (!btn) return;
    
    btn.disabled = true;
    btn.textContent = 'Sending...';
    
    fetch('/threads/invite-guest', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
        body: JSON.stringify({ guest_id: guestId })
    })
    .then(r => r.json())
    .then(data => {
        if (data.ok) {
            btn.textContent = 'Invited! ✓';
            btn.style.background = '#10b981';
            alert('An account registration invite email has been successfully sent to the guest.');
        } else {
            btn.disabled = false;
            btn.textContent = 'Send Invitation';
            alert(data.error || 'Failed to send invite.');
        }
    })
    .catch(() => {
        btn.disabled = false;
        btn.textContent = 'Send Invitation';
    });
}

/* ── Select a thread ── */
function selectConversation(threadId) {
    activeThreadId = threadId;

    // Highlight active element in list
    document.querySelectorAll('.whatsapp-conv-item').forEach(el => {
        el.classList.remove('active-chat');
        if (el.dataset.bookingId === threadId) {
            el.classList.add('active-chat');
        }
    });

    const activeEl = document.querySelector(`.whatsapp-conv-item[data-booking-id="${threadId}"]`);
    if (!activeEl) return;
    
    // Add visual shell flag for mobile responsive sizing
    document.getElementById('whatsapp-shell').classList.add('show-chat');

    const name = activeEl.querySelector('.conv-name')?.textContent || 'Chat';
    const eventName = activeEl.querySelector('.conv-row-2')?.previousElementSibling?.textContent || activeEl.querySelector('div:nth-child(2)')?.textContent || 'Event';
    const unreadEl = activeEl.querySelector('.conv-unread-badge');
    
    let profile = null;
    try {
        if (activeEl.dataset.profile) {
            profile = JSON.parse(activeEl.dataset.profile);
        }
    } catch(e) {}

    // Populate active chat header
    document.getElementById('chat-header-name').textContent = name;
    document.getElementById('chat-header-event').textContent = eventName;

    const imgEl = activeEl.querySelector('.avatar-badge img');
    const headerAvatar = document.getElementById('chat-header-avatar');
    const profileAvatar = document.getElementById('profile-panel-avatar');

    if (imgEl) {
        headerAvatar.innerHTML = `<img src="${imgEl.src}" alt="Avatar" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">`;
        headerAvatar.style.background = 'none';
        headerAvatar.style.padding = '0';
    } else {
        headerAvatar.textContent = name.substring(0, 1).toUpperCase();
        headerAvatar.style.background = '#e2e8f0';
        headerAvatar.style.padding = '';
    }

    const statusBadge = document.getElementById('chat-header-status-badge');
    if (profile) {
        statusBadge.textContent = profile.status || 'Active';
        statusBadge.style.display = 'inline-block';
    } else {
        statusBadge.style.display = 'none';
    }

    // Toggle panels
    document.getElementById('chat-empty-panel').style.display = 'none';
    document.getElementById('chat-header').style.display = 'flex';
    document.getElementById('chat-messages-container').style.display = 'flex';
    document.getElementById('chat-input-bar').style.display = 'flex';

    // Populate profile details
    if (profile) {
        if (imgEl) {
            profileAvatar.innerHTML = `<img src="${imgEl.src}" alt="Avatar" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">`;
            profileAvatar.style.background = 'none';
            profileAvatar.style.padding = '0';
        } else {
            profileAvatar.textContent = name.substring(0, 1).toUpperCase();
            profileAvatar.style.background = '#e2e8f0';
            profileAvatar.style.padding = '';
        }
        document.getElementById('profile-name').textContent = profile.name;
        
        let roleName = 'User';
        if (profile.type === 'vendor_to_planner') roleName = 'Planner';
        else if (profile.type === 'planner_to_vendor') roleName = 'Vendor';
        else if (profile.type === 'planner_to_guest') roleName = 'Guest';
        else if (profile.type === 'guest_to_planner') roleName = 'Planner';
        document.getElementById('profile-role').textContent = roleName;
        
        let detailsHtml = '';
        if (profile.type === 'vendor_to_planner') {
            detailsHtml += `<div><strong style="display:block; color: #64748b; font-size: 0.75rem; text-transform: uppercase; margin-bottom: 2px;">Event Booked For</strong><div style="color: #111b21; font-weight: 700;">${esc(profile.event_name)}</div></div>`;
            detailsHtml += `<div><strong style="display:block; color: #64748b; font-size: 0.75rem; text-transform: uppercase; margin-bottom: 2px;">Phone Number</strong><div style="color: #111b21; font-weight: 700;">${esc(profile.phone)}</div></div>`;
            detailsHtml += `<div><strong style="display:block; color: #64748b; font-size: 0.75rem; text-transform: uppercase; margin-bottom: 2px;">Event Location</strong><div style="color: #111b21; font-weight: 700;">${esc(profile.event_location)}</div></div>`;
            detailsHtml += `<div><strong style="display:block; color: #64748b; font-size: 0.75rem; text-transform: uppercase; margin-bottom: 2px;">Date</strong><div style="color: #111b21; font-weight: 700;">${esc(profile.booking_date)}</div></div>`;
            detailsHtml += `<div><strong style="display:block; color: #64748b; font-size: 0.75rem; text-transform: uppercase; margin-bottom: 4px;">Requirements</strong><div style="color: #333; background: #fff; padding: 10px; border-radius: 8px; border: 1px solid #eaeaea; font-size: 0.9rem;">${esc(profile.requirement) || 'None provided'}</div></div>`;
        } else if (profile.type === 'planner_to_vendor') {
            detailsHtml += `<div><strong style="display:block; color: #64748b; font-size: 0.75rem; text-transform: uppercase; margin-bottom: 2px;">Event Booked For</strong><div style="color: #111b21; font-weight: 700;">${esc(profile.event_name)}</div></div>`;
            detailsHtml += `<div><strong style="display:block; color: #64748b; font-size: 0.75rem; text-transform: uppercase; margin-bottom: 2px;">Phone Number</strong><div style="color: #111b21; font-weight: 700;">${esc(profile.phone)}</div></div>`;
            detailsHtml += `<div><strong style="display:block; color: #64748b; font-size: 0.75rem; text-transform: uppercase; margin-bottom: 2px;">Speciality</strong><div style="color: #111b21; font-weight: 700;">${esc(profile.speciality)}</div></div>`;
            detailsHtml += `<div><strong style="display:block; color: #64748b; font-size: 0.75rem; text-transform: uppercase; margin-bottom: 2px;">Cost / Price</strong><div style="color: #008069; font-weight: 800; font-size: 1.1rem;">${esc(profile.price)}</div></div>`;
            detailsHtml += `<div><strong style="display:block; color: #64748b; font-size: 0.75rem; text-transform: uppercase; margin-bottom: 2px;">Location</strong><div style="color: #111b21; font-weight: 700;">${esc(profile.location)}</div></div>`;
            detailsHtml += `<div><strong style="display:block; color: #64748b; font-size: 0.75rem; text-transform: uppercase; margin-bottom: 4px;">Booking Status</strong><div>
                <span style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; padding: 4px 10px; border-radius: 12px; display: inline-block; background: #e2e8f0; color: #475569;">${esc(profile.status)}</span>
            </div></div>`;
        } else if (profile.type === 'planner_to_guest') {
            detailsHtml += `<div><strong style="display:block; color: #64748b; font-size: 0.75rem; text-transform: uppercase; margin-bottom: 2px;">Event Invited To</strong><div style="color: #111b21; font-weight: 700;">${esc(profile.event_name)}</div></div>`;
            detailsHtml += `<div><strong style="display:block; color: #64748b; font-size: 0.75rem; text-transform: uppercase; margin-bottom: 2px;">Phone Number</strong><div style="color: #111b21; font-weight: 700;">${esc(profile.phone)}</div></div>`;
            detailsHtml += `<div><strong style="display:block; color: #64748b; font-size: 0.75rem; text-transform: uppercase; margin-bottom: 2px;">RSVP Status</strong><div>
                <span style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; padding: 4px 10px; border-radius: 12px; display: inline-block; background: #e2e8f0; color: #475569;">${esc(profile.status)}</span>
            </div></div>`;
            detailsHtml += `<div><strong style="display:block; color: #64748b; font-size: 0.75rem; text-transform: uppercase; margin-bottom: 2px;">Dietary Preference</strong><div style="color: #111b21; font-weight: 700; text-transform: uppercase;">${esc(profile.dietary_preference)}</div></div>`;
            detailsHtml += `<div><strong style="display:block; color: #64748b; font-size: 0.75rem; text-transform: uppercase; margin-bottom: 2px;">Seat</strong><div style="color: #111b21; font-weight: 700;">${esc(profile.seat)}</div></div>`;
            detailsHtml += `<div><strong style="display:block; color: #64748b; font-size: 0.75rem; text-transform: uppercase; margin-bottom: 2px;">Plus-ones</strong><div style="color: #111b21; font-weight: 700;">${esc(profile.plus_one_count)}</div></div>`;
        } else if (profile.type === 'guest_to_planner') {
            detailsHtml += `<div><strong style="display:block; color: #64748b; font-size: 0.75rem; text-transform: uppercase; margin-bottom: 2px;">Event</strong><div style="color: #111b21; font-weight: 700;">${esc(profile.event_name)}</div></div>`;
            detailsHtml += `<div><strong style="display:block; color: #64748b; font-size: 0.75rem; text-transform: uppercase; margin-bottom: 2px;">Organizer</strong><div style="color: #111b21; font-weight: 700;">${esc(profile.name)}</div></div>`;
            detailsHtml += `<div><strong style="display:block; color: #64748b; font-size: 0.75rem; text-transform: uppercase; margin-bottom: 2px;">Phone Number</strong><div style="color: #111b21; font-weight: 700;">${esc(profile.phone)}</div></div>`;
            detailsHtml += `<div><strong style="display:block; color: #64748b; font-size: 0.75rem; text-transform: uppercase; margin-bottom: 2px;">Venue Location</strong><div style="color: #111b21; font-weight: 700;">${esc(profile.event_location)}</div></div>`;
            detailsHtml += `<div><strong style="display:block; color: #64748b; font-size: 0.75rem; text-transform: uppercase; margin-bottom: 2px;">Event Date</strong><div style="color: #111b21; font-weight: 700;">${esc(profile.event_date)}</div></div>`;
        }
        document.getElementById('profile-details').innerHTML = detailsHtml;
    } else {
        document.getElementById('chat-profile').style.display = 'none';
    }

    // Mark as read only if tab/window is visible to prevent false reads
    triggerReadReceipt();

    // Load messages instantly
    loadMessages();

    // Active message polling (3s)
    if (messagePollInterval) clearInterval(messagePollInterval);
    messagePollInterval = setInterval(() => {
        if (document.visibilityState === 'visible') {
            loadMessages();
            triggerReadReceipt();
        }
    }, 3000);

    // Focus input
    document.getElementById('chat-msg-input').focus();
}

/* ── Trigger Read Receipt POST ── */
function triggerReadReceipt() {
    if (!activeThreadId || document.visibilityState !== 'visible') return;

    fetch(`/threads/${activeThreadId}/read`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }
    })
    .then(() => {
        // Clear unread badge in sidebar list
        const activeEl = document.querySelector(`.whatsapp-conv-item[data-booking-id="${activeThreadId}"]`);
        if (activeEl) {
            const badge = activeEl.querySelector('.conv-unread-badge');
            if (badge) badge.remove();
        }
    })
    .catch(() => {});
}

/* ── Load Messages ── */
let lastMessageCount = 0;
function loadMessages() {
    if (!activeThreadId) return;

    fetch(`/threads/${activeThreadId}/messages`, {
        headers: { 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(msgs => {
        const container = document.getElementById('chat-messages-container');
        const shouldScroll = lastMessageCount !== msgs.length;
        lastMessageCount = msgs.length;

        if (!msgs.length) {
            container.innerHTML = `
                <div style="text-align: center; color: #667781; margin: auto; padding: 32px;">
                    <p style="font-size: 2.2rem; margin: 0 0 6px 0;">👋</p>
                    <p style="font-weight: 700; margin: 0;">No messages yet. Say hello!</p>
                </div>
            `;
            return;
        }

        let html = '';
        let lastDate = '';
        msgs.forEach(m => {
            // Group separator
            if (m.date !== lastDate) {
                html += `
                    <div style="text-align: center; margin: 12px 0;">
                        <span style="background: #ffffff; color: #54656f; padding: 4px 12px; border-radius: 8px; font-size: 0.72rem; font-weight: 700; box-shadow: 0 1px 1.5px rgba(0,0,0,0.06); text-transform: uppercase;">${esc(m.date)}</span>
                    </div>
                `;
                lastDate = m.date;
            }

            const alignClass = m.is_mine ? 'msg-sent' : 'msg-received';
            const nameColor = m.sender_role === 'vendor' ? '#00a884' : (m.sender_role === 'planner' ? '#027eb5' : '#8b5cf6');
            
            // Render receipt checkmarks
            let checkmarks = '';
            if (m.is_mine) {
                if (m.status === 'read') {
                    checkmarks = '<span class="receipt-tick tick-read">✓✓</span>';
                } else if (m.status === 'delivered') {
                    checkmarks = '<span class="receipt-tick tick-delivered">✓✓</span>';
                } else {
                    checkmarks = '<span class="receipt-tick tick-sent">✓</span>';
                }
            }

            html += `
                <div class="msg-bubble ${alignClass}">
                    ${!m.is_mine ? `<div style="font-size: 0.72rem; font-weight: 800; color: ${nameColor}; margin-bottom: 2px;">${esc(m.sender_name)}</div>` : ''}
                    <div style="word-wrap: break-word;">${esc(m.message)}</div>
                    <div class="bubble-meta">
                        <span>${esc(m.time)}</span>
                        ${checkmarks}
                    </div>
                </div>
            `;
        });

        container.innerHTML = html;
        if (shouldScroll) container.scrollTop = container.scrollHeight;
    })
    .catch(() => {});
}

/* ── Send Message ── */
function sendMsg() {
    const input = document.getElementById('chat-msg-input');
    const msg = input.value.trim();
    if (!msg || !activeThreadId) return;
    input.value = '';

    fetch(`/threads/${activeThreadId}/messages`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
        body: JSON.stringify({ message: msg })
    }).then(() => {
        loadMessages();
        // Force refresh conversation list to bubble thread up
        loadSidebarConversations();
    });
}

/* ── Render filtered default conversations list ── */
function renderConversations(conversations) {
    const container = document.getElementById('default-chat-list');
    
    // Filter conversations based on current tab if user is a planner
    let filtered = conversations;
    if (currentSidebarTab !== 'all') {
        filtered = conversations.filter(conv => conv.other_role === currentSidebarTab);
    }

    if (filtered.length === 0) {
        container.innerHTML = `
            <div class="whatsapp-empty-state" style="padding-top: 60px; background: none;">
                <span style="font-size: 2.5rem; display: block; margin-bottom: 8px;">📭</span>
                <p style="font-weight: 700; margin-bottom: 4px; color: #111b21;">No conversations yet</p>
                <p style="font-size: 0.82rem;">Your active chats will appear here.</p>
            </div>
        `;
        return;
    }

    container.innerHTML = filtered.map(conv => `
        <div class="whatsapp-conv-item ${activeThreadId === conv.booking_id ? 'active-chat' : ''}" 
             data-booking-id="${conv.booking_id}"
             data-profile="${escAttr(JSON.stringify(conv.profile))}"
             onclick="selectConversation('${conv.booking_id}')">
            
            <div class="avatar-badge">
                ${esc(conv.other_name.substring(0, 1))}
            </div>

            <div class="conv-details">
                <div class="conv-row-1">
                    <span class="conv-name">${esc(conv.other_name)}</span>
                    <span class="conv-time">${esc(conv.last_time)}</span>
                </div>
                <div class="conv-row-2">
                    <span class="conv-last-msg">${esc(conv.last_message || 'No messages yet')}</span>
                    ${conv.message_count > 0 ? `<span class="conv-unread-badge">${conv.message_count}</span>` : ''}
                </div>
            </div>
        </div>
    `).join('');
}

/* ── Switch Sidebar Tab ── */
function switchSidebarTab(tab) {
    currentSidebarTab = tab;
    
    // Update active class on tab buttons
    document.querySelectorAll('.sidebar-tab-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    const activeBtn = document.getElementById('tab-' + tab);
    if (activeBtn) activeBtn.classList.add('active');
    
    // Rerender filtered default chat list
    renderConversations(allConversations);
    
    // If search is active, execute it again to respect current tab filtering
    const q = document.getElementById('whatsapp-search-input').value.trim();
    if (q.length > 0) {
        executeSearch();
    }
}

/* ── Load Sidebar Conversation list (AJAX) ── */
function loadSidebarConversations() {
    return fetch('/threads/conversations', {
        headers: { 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(conversations => {
        allConversations = conversations;
        
        // Only update if search bar is empty
        const q = document.getElementById('whatsapp-search-input').value.trim();
        if (q.length > 0) return;
        
        renderConversations(allConversations);
    })
    .catch(() => {});
}

/* ── Escape strings ── */
function esc(s) {
    if (!s) return '';
    const d = document.createElement('div');
    d.textContent = s;
    return d.innerHTML;
}
function escAttr(s) {
    if (!s) return '';
    return s.replace(/&/g, '&amp;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');
}

/* ── Initializations ── */
document.addEventListener('DOMContentLoaded', () => {
    // Force select active conversations on load if pre-set
    if (activeThreadId) {
        selectConversation(activeThreadId);
    }

    // Sidebar Polling (10s)
    sidebarPollInterval = setInterval(() => {
        if (document.visibilityState === 'visible') {
            loadSidebarConversations();
        }
    }, 10000);

    // Visibility Listener to pause polling when tab minimized
    document.addEventListener('visibilitychange', () => {
        if (document.visibilityState !== 'visible') {
            // Tab blurred: clear active pollers
            if (messagePollInterval) clearInterval(messagePollInterval);
        } else {
            // Tab active: restart pollers
            if (activeThreadId) {
                loadMessages();
                triggerReadReceipt();
                messagePollInterval = setInterval(() => {
                    if (document.visibilityState === 'visible') {
                        loadMessages();
                        triggerReadReceipt();
                    }
                }, 3000);
            }
            loadSidebarConversations();
        }
    });
});
</script>
@endsection