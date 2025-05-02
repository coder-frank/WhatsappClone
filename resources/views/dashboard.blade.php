<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>WhatsApp Clone UI</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script src="https://js.pusher.com/8.4.0/pusher.min.js"></script>
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background-color: #111b21;
            color: #fff;
        }

        .app-container {
            display: flex;
            height: 100vh;
            overflow: hidden;
            flex-direction: row;
        }

        .chat-list-panel {
            width: 320px;
            background-color: #202c33;
            display: flex;
            flex-direction: column;
            border-right: 1px solid #2a3942;
        }

        .chat-window-panel {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            background-color: #111b21;
        }

        .chat-header,
        .message-header {
            height: 60px;
            padding: 0 15px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background-color: #202c33;
            border-bottom: 1px solid #2a3942;
        }

        .chat-search {
            background-color: #111b21;
            padding: 10px;
        }

        .chat-search input {
            background-color: #2a3942;
            border: none;
            color: #fff;
        }

        .chat-search input::placeholder {
            color: #bbb;
            /* Change to any color you want */
            opacity: 1;
            /* Optional: ensure full opacity */
        }


        .chat-list {
            flex-grow: 1;
            overflow-y: auto;
        }

        .chat-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 15px;
            border-bottom: 1px solid #2a3942;
            cursor: pointer;
        }

        .chat-item:hover {
            background-color: #2a3942;
        }

        .chat-item img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
        }

        .chat-content {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .chat-content .name {
            font-weight: bold;
        }

        .main-chat {
            flex: 1;
            display: flex;
            flex-direction: column;
            background: url('https://i.imgur.com/2s9G4Lb.png') repeat;
            background-size: contain;
            overflow: hidden;
        }

        .messages {
            flex: 1;
            padding: 15px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            min-height: 0;
            overflow-y: auto;
        }

        .message {
            max-width: 60%;
            padding: 10px 15px;
            border-radius: 8px;
            font-size: 14px;
            position: relative;
            flex: 1;
        }

        .message.sent {
            background-color: #005c4b;
            color: #fff;
            align-self: flex-end;
            border-bottom-right-radius: 0;
        }

        .message.received {
            background-color: #2a3942;
            align-self: flex-start;
            border-bottom-left-radius: 0;
        }

        .message-time {
            font-size: 10px;
            text-align: right;
            margin-top: 4px;
            opacity: 0.6;
        }

        .chat-input {
            display: flex;
            align-items: center;
            padding: 10px 15px;
            background-color: #202c33;
            border-top: 1px solid #2a3942;
        }

        .chat-input input {
            flex: 1;
            background-color: #2a3942;
            border: none;
            color: #fff;
            padding: 10px;
            border-radius: 20px;
        }

        .chat-input button {
            background: none;
            border: none;
            color: #fff;
            font-size: 20px;
            margin-left: 10px;
        }

        .chat-window-panel {
            display: flex;
            flex-direction: column;
            flex-grow: 1;
            height: 100vh;
        }

        .attachment-menu {
            background-color: #2a2f32;
            color: white;
            padding: 0.5rem 0;
            width: 240px;
            bottom: 60px;
            left: 23rem;
            z-index: 999;
        }

        .attachment-option {
            display: flex;
            align-items: center;
            padding: 10px 16px;
            cursor: pointer;
            transition: background-color 0.2s ease;
            font-size: 15px;
        }

        .attachment-option i {
            font-size: 16px;
        }

        .attachment-option:hover {
            background-color: #3c454c;
        }

        @media (max-width: 768px) {
            .chat-list-panel {
                width: 100%;
            }

            .chat-window-panel {
                width: 100%;
            }
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
</head>

<body>
    <div class="app-container">
        <!-- Chat List Panel -->
        <div class="chat-list-panel d-md-flex flex-column" id="chatListPanel">
            <div class="chat-header">
                <div class="d-flex align-items-center gap-2">
                    <img src="{{ asset('images/user.png') }}" class="rounded-circle" alt="User" width="40px"
                        height="40px">
                </div>
                <div class="d-flex gap-4">
                    <i class="fas fa-circle-notch"></i>
                    <i class="fas fa-comment-dots"></i>
                    <i class="fas fa-ellipsis-v"></i>
                </div>
            </div>

            <div class="chat-search">
                <form id="startChatForm" class="d-flex gap-2">
                    <input type="text" id="contactInput" class="form-control form-control-sm"
                        placeholder="Enter email or phone" required>
                    <button class="btn btn-success btn-sm" type="submit"><i class="fas fa-paper-plane"></i></button>
                </form>

            </div>

            <div class="chat-list">
                @forelse ($friends as $friend)
                    <div class="chat-item" data-friendid="{{ $friend['id'] }}">
                        <img src="{{ $friend['profile_picture'] ?? asset('images/avatar.png') }}" alt="">
                        <div class="chat-content w-100">
                            <div class="d-flex justify-content-between">
                                <span class="name">{{ $friend['name'] }}</span>

                                <span id="badge-{{ $friend['id'] }}"
                                    class="badge bg-success {{ $friend['unread_count'] > 0 ? '' : 'd-none' }}">{{ $friend['unread_count'] }}</span>


                            </div>
                            <div class="d-flex justify-content-between">
                                @php
                                    $isYou = $friend['last_message_sender_id'] == auth()->id();
                                    $rawMessage = $friend['last_message'] ?? 'Start chatting';

                                    // Detect media type
                                    $preview = $rawMessage;

                                    if (Str::contains($rawMessage, '<img')) {
                                        $preview = '<i class="fas fa-image me-1"></i> Image';
                                    } elseif (Str::contains($rawMessage, '<video')) {
                                        $preview = '<i class="fas fa-video me-1"></i> Video';
                                    } elseif (Str::contains($rawMessage, 'Download Document')) {
                                        $preview = '<i class="fas fa-file-alt me-1"></i> Document';
                                    } else {
                                        // Truncate text (strip HTML just in case)
                                        $plain = strip_tags($rawMessage);
                                        $preview = Str::limit($plain, 40);
                                    }

                                @endphp

                                <span id="preview-{{ $friend['id'] }}"
                                    class="text-{{ $friend['unread_count'] > 0 ? 'success fw-bold' : 'white small' }}">{{ $isYou ? 'You: ' : '' }}
                                    {!! $friend['last_message'] !!}</span>

                                @if ($friend['last_message_time'])
                                    <small id="time-{{ $friend['id'] }}"
                                        class="text-white">{{ $friend['last_message_time'] }}</small>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-white py-5 mt-5">
                        <i class="fas fa-user-friends fa-2x mb-3"></i>
                        <p>No friends yet. Start chatting by searching an email or phone number.</p>
                    </div>
                @endforelse


            </div>
        </div>

        <!-- Chat Window Panel -->
        <div class="chat-window-panel d-none d-md-flex flex-column flex-grow-1" id="chatWindowPanel">
            <div class="message-header" id="chatHeader" style="display: none;">
                <div class="d-flex align-items-center gap-2">
                    <span class="d-md-none me-2" onclick="showChatList()">
                        <i class="fas fa-arrow-left"></i>
                    </span>
                    <img src="" id="chatAvatar" class="rounded-circle" alt="" width="40px"
                        height="35px">
                    <div>
                        <div class="fw-bold" id="chatName">Friend</div>
                        <small class="text-success" id="chatStatus">online</small>
                    </div>
                </div>
                <div class="d-flex gap-4">
                    <i class="fas fa-video"></i>
                    <i class="fas fa-phone"></i>
                    <i class="fas fa-search"></i>
                </div>
            </div>

            <div class="main-chat">
                <div class="messages text-center text-white py-5" id="chatPlaceholder">
                    <i class="fas fa-comment-dots fa-2x mb-3"></i>
                    <p>Select a friend to start chatting</p>
                </div>

                <div class="messages d-none" id="messageContainer">
                    <!-- Messages will be injected here -->
                </div>

                <!-- Hidden attachment options -->
                <div id="attachmentOptions" class="d-none attachment-menu position-absolute rounded-3 shadow">
                    <div class="attachment-option" data-type="media">
                        <i class="fas fa-image me-2"></i> Photos & videos
                    </div>
                    <div class="attachment-option" data-type="camera">
                        <i class="fas fa-camera me-2"></i> Camera
                    </div>
                    <div class="attachment-option" data-type="document">
                        <i class="fas fa-file-alt me-2"></i> Document
                    </div>
                    <div class="attachment-option" data-type="contact">
                        <i class="fas fa-user me-2"></i> Contact
                    </div>
                    <div class="attachment-option" data-type="poll">
                        <i class="fas fa-poll me-2"></i> Poll
                    </div>
                    <div class="attachment-option" data-type="drawing">
                        <i class="fas fa-pen me-2"></i> Drawing
                    </div>
                </div>



                <div class="chat-input d-none align-items-center gap-2" id="chatInputBox">
                    <button type="button" id="attachBtn"><i class="fas fa-paperclip"></i></button>
                    <button type="button"><i class="far fa-smile"></i></button>

                    <input type="text" id="messageInput" class="form-control" placeholder="Type a message">

                    <button type="button" id="micBtn"><i class="fas fa-microphone"></i></button>
                    <button type="button" id="sendBtn" class="d-none"><i class="fas fa-paper-plane"></i></button>
                </div>


            </div>
        </div>

    </div>
    <!-- File Preview Popup -->
    <div id="filePreviewPopup"
        class="d-none position-fixed top-0 start-0 w-100 h-100 bg-dark bg-opacity-75 d-flex align-items-center justify-content-center"
        style="z-index: 9999;">
        <div class="bg-white text-dark p-4 rounded" style="max-width: 400px; width: 90%;">
            <h5 class="mb-3">Send File</h5>
            <div id="filePreview" class="mb-3 text-center"></div>
            <div class="d-flex justify-content-between">
                <button class="btn btn-secondary" onclick="$('#filePreviewPopup').addClass('d-none')">Cancel</button>
                <button class="btn btn-success" id="confirmSendFile">Send</button>
            </div>
        </div>
    </div>

    <!-- Hidden File Input -->
    <input type="file" id="fileInput" class="d-none">

    <script>
        const AUTH_ID = {{ auth()->id() }};

        function showChatWindow() {
            $('#chatListPanel').addClass('d-none');
            $('#chatWindowPanel').removeClass('d-none');
        }

        function showChatList() {
            $('#chatWindowPanel').addClass('d-none');
            $('#chatListPanel').removeClass('d-none');
        }

        function showToast(message, isError = false) {
            Toastify({
                text: message,
                duration: 3000,
                gravity: "top",
                position: "center",
                backgroundColor: isError ? "#dc3545" : "#198754",
                close: true
            }).showToast();
        }

        const $input = $('#messageInput');
        const $micBtn = $('#micBtn');
        const $sendBtn = $('#sendBtn');
        const $attachBtn = $('#attachBtn');
        const $attachmentOptions = $('#attachmentOptions');

        // Typing toggle
        $input.on('input', function() {
            if ($(this).val().trim() !== '') {
                $micBtn.addClass('d-none');
                $sendBtn.removeClass('d-none');
            } else {
                $micBtn.removeClass('d-none');
                $sendBtn.addClass('d-none');
            }
        });

        // Toggle attachment options
        $attachBtn.on('click', function() {
            $attachmentOptions.slideToggle().toggleClass('d-none');
        });

        $sendBtn.on('click', function() {
            const message = $input.val().trim();
            const receiverId = window.CURRENT_CHAT_ID;

            if (!message || !receiverId) return;

            const now = new Date();
            const time = now.toLocaleTimeString([], {
                hour: '2-digit',
                minute: '2-digit'
            });

            const bubble = `
                <div class="message sent">
                    ${message}
                    <div class="message-time">${time}</div>
                </div>
            `;
            $('#messageContainer').append(bubble);
            requestAnimationFrame(() => {
                $('#messageContainer').scrollTop($('#messageContainer')[0].scrollHeight);
            });

            $input.val('').trigger('input');

            $.post('/send-message', {
                _token: $('meta[name="csrf-token"]').attr('content'),
                message,
                receiver_id: receiverId
            }).fail(() => {
                showToast('Message failed to send', true);
            });
        });


        $('#startChatForm').on('submit', function(e) {
            e.preventDefault();

            const target = $('#contactInput').val();
            const submitButton = $(this).find('button[type="submit"]');

            // Disable button and show loading spinner
            submitButton.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');

            $.ajax({
                url: '/start-chat',
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                        'content')
                },
                data: {
                    target: target,
                    message: 'Hi 👋' // Optional: default message to initiate chat
                },
                success: function(res) {
                    showToast("Chat started successfully!");
                },
                error: function(err) {
                    showToast(err.responseJSON?.message || "Failed to start chat.", true);
                },
                complete: function() {
                    // Re-enable button and restore original content
                    submitButton.prop('disabled', false).html('<i class="fas fa-paper-plane"></i>');
                }
            });
        });

        $('.chat-item').on('click', function() {
            const friendId = $(this).data('friendid');
            const name = $(this).find('.name').text();
            const avatar = $(this).find('img').attr('src');

            // 🔥 Target by ID
            $(`#badge-${friendId}`).addClass('d-none');
            $(`#preview-${friendId}`)
                .removeClass('text-success fw-bold')
                .addClass('text-white small');

            // UI setup
            $('#chatName').text(name);
            $('#chatAvatar').attr('src', avatar);
            $('#chatHeader').show();
            $('#chatPlaceholder').addClass('d-none');
            $('#messageContainer').removeClass('d-none').html('');
            $('#chatInputBox').removeClass('d-none');

            // Fetch messages
            $.ajax({
                url: `/messages/${friendId}`,
                method: 'GET',
                success: function(data) {
                    if (data.length === 0) {
                        $('#messageContainer').html(
                            '<p class="text-center text-white">No messages yet.</p>');
                        return;
                    }

                    data.forEach(msg => {
                        const type = msg.sender_id == {{ auth()->id() }} ? 'sent' :
                            'received';
                        const bubble = `
                    <div class="message ${type}">
                        ${msg.message}
                        <div class="message-time">${msg.time}</div>
                    </div>`;
                        $('#messageContainer').append(bubble);
                    });

                    $('#messageContainer').scrollTop($('#messageContainer')[0].scrollHeight);
                },
                error: function() {
                    $('#messageContainer').html(
                        '<p class="text-danger text-center">Failed to load messages</p>');
                },
                complete: function() {
                    window.CURRENT_CHAT_ID = friendId;
                }
            });
        });


        let selectedFile = null;
        let currentUploadType = 'media';

        $('.attachment-option').on('click', function() {
            $attachmentOptions.slideToggle().toggleClass('d-none');
            currentUploadType = $(this).data('type');
            let accept = '*/*';

            if (currentUploadType === 'media') accept = 'image/*,video/*';
            if (currentUploadType === 'document') accept = '.pdf,.doc,.docx,.txt';

            $('#fileInput').attr('accept', accept).click();
        });

        $('#fileInput').on('change', function() {
            const file = this.files[0];
            if (!file) return;

            selectedFile = file;

            // Preview
            const preview = $('#filePreview');
            preview.html('');

            const type = file.type;

            if (type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.html(`<img src="${e.target.result}" class="img-fluid rounded" />`);
                };
                reader.readAsDataURL(file);
            } else if (type.startsWith('video/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.html(`<video controls class="w-100"><source src="${e.target.result}" /></video>`);
                };
                reader.readAsDataURL(file);
            } else {
                preview.html(`<p><i class="fas fa-file-alt fa-2x me-2"></i>${file.name}</p>`);
            }

            $('#filePreviewPopup').removeClass('d-none');
        });

        $('#confirmSendFile').on('click', function() {
            if (!selectedFile || !window.CURRENT_CHAT_ID) return;

            const formData = new FormData();
            formData.append('file', selectedFile);
            formData.append('receiver_id', window.CURRENT_CHAT_ID);

            $.ajax({
                url: '/send-media',
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: formData,
                processData: false,
                contentType: false,
                success: function(res) {
                    const bubble = `
                    <div class="message sent">
                        ${res.preview}
                        <div class="message-time">${res.time}</div>
                    </div>
                `;
                    $('#messageContainer').append(bubble).scrollTop($('#messageContainer')[0]
                        .scrollHeight);
                    $('#filePreviewPopup').addClass('d-none');
                    selectedFile = null;
                    $('#fileInput').val('');
                },
                error: function() {
                    showToast('Failed to upload file', true);
                }
            });
        });


        Pusher.logToConsole = true;

        var pusher = new Pusher('1e35cab1f9e93844a8dc', {
            cluster: 'eu'
        });

        var channel = pusher.subscribe('chat.{{ auth()->id() }}');
        channel.bind('new-message', function(e) {
            const friendId = e.sender_id;

            // If the chat is not currently open
            if (window.CURRENT_CHAT_ID != friendId) {
                // Update last message preview
                let previewText = '';
                const msgLower = e.message.toLowerCase();

                if (msgLower.includes('<img')) {
                    previewText = '<i class="fas fa-image me-1"></i> Image';
                } else if (msgLower.includes('<video')) {
                    previewText = '<i class="fas fa-video me-1"></i> Video';
                } else if (msgLower.includes('download document')) {
                    previewText = '<i class="fas fa-file-alt me-1"></i> Document';
                } else {
                    previewText = e.message.length > 40 ? e.message.slice(0, 40) + '...' : e.message;
                }

                $(`#preview-${friendId}`)
                    .html(previewText)
                    .removeClass('text-white small')
                    .addClass('text-success fw-bold');


                // Update time
                $(`#time-${friendId}`).text(e.time);

                // Update or show unread badge
                const $badge = $(`#badge-${friendId}`);
                if ($badge.length) {
                    let count = parseInt($badge.text()) || 0;
                    $badge.text(count + 1).removeClass('d-none');
                } else {
                    // If badge doesn't exist (just in case)
                    $(`#chat-item-${friendId} .name`).append(
                        `<span id="badge-${friendId}" class="badge bg-success ms-2">1</span>`
                    );
                }

                return;
            }

            // Chat is open – append directly
            const bubble = `
                <div class="message received">
                    ${e.message}
                    <div class="message-time">${e.time}</div>
                </div>
            `;
            $('#messageContainer').append(bubble).scrollTop($('#messageContainer')[0].scrollHeight);
        });
    </script>
</body>

</html>
