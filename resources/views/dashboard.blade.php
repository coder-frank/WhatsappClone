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

        #messageInput {
            resize: none;
            min-height: 40px;
            line-height: 1.5;
            overflow-y: hidden;
        }


        #messageInput::placeholder {
            color: #bbb;
            /* Change to any color you want */
            opacity: 1;
            /* Optional: ensure full opacity */
        }

        #messageInput:focus {
            background-color: #2a3942;
            outline: none;
            color: white;
            /* Change to any color you want */
            opacity: 1;
            /* Optional: ensure full opacity */
        }

        .message.bg-transparent {
            background: transparent;
            padding: 0;
            box-shadow: none;
        }

        .custom-audio-player {
            background-color: #2a3942;
            padding: 8px 12px;
            border-radius: 20px;
            max-width: 250px;
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
    <!-- Emoji Picker CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/emojionearea@3.4.2/dist/emojionearea.min.css">

    <!-- Emoji Picker JS -->
    <script src="https://cdn.jsdelivr.net/npm/emojionearea@3.4.2/dist/emojionearea.min.js"></script>

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
                                    {!! $preview !!}</span>

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
                    <button type="button" id="emojiBtn"><i class="far fa-smile"></i></button>

                    <textarea id="messageInput" class="form-control" placeholder="Type a message" rows="1" style="resize: none;"></textarea>

                    <button type="button" id="micBtn"><i class="fas fa-microphone"></i></button>
                    <button type="button" id="sendBtn" class="d-none"><i class="fas fa-paper-plane"></i></button>


                </div>

                <!-- Voice Recorder UI -->
                <div id="voiceRecorderUI" class="d-none align-items-center justify-content-between px-3 py-2 w-100"
                    style="background-color: #202c33; border-top: 1px solid #2a3942;">
                    <button id="cancelRecording" class="btn text-danger"><i class="fas fa-trash"></i></button>
                    <span class="text-white"><i class="fas fa-circle text-danger me-2"></i><span
                            id="recordingTime">0:00</span></span>
                    <button id="sendRecording" class="btn btn-success rounded"><i
                            class="fas fa-paper-plane"></i></button>
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

    <!-- Camera Capture Modal -->
    <div id="cameraModal"
        class="d-none position-fixed top-0 start-0 w-100 h-100 bg-dark bg-opacity-75 d-flex align-items-center justify-content-center"
        style="z-index: 9999;">
        <div class="bg-black p-4 rounded text-white" style="width: 90%; max-width: 400px;">
            <h5 class="mb-3">📷 Take a Picture</h5>
            <video id="cameraStream" autoplay playsinline class="w-100 rounded mb-3"
                style="height: 240px; object-fit: cover;"></video>
            <canvas id="cameraCanvas" class="d-none"></canvas>
            <div class="d-flex justify-content-between">
                <button class="btn btn-secondary" id="cancelCamera">Cancel</button>
                <button class="btn btn-success" id="capturePhoto">Capture</button>
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

        function renderMessageBubble(messageHtml, type, time, direction = 'sent') {
            const isMedia = ['image', 'video', 'voice', 'document'].includes(type);

            return `
        <div class="message ${direction}${isMedia ? ' bg-transparent p-0 border-0' : ''}">
            ${messageHtml}
            <div class="message-time">${time}</div>
        </div>
    `;
        }


        const $input = $('#messageInput');
        const $micBtn = $('#micBtn');
        const $sendBtn = $('#sendBtn');
        const $attachBtn = $('#attachBtn');
        const $attachmentOptions = $('#attachmentOptions');

        $('#messageInput').on('keydown', function(e) {
            if (e.key === 'Enter') {
                if (e.shiftKey) {
                    // Allow new line
                    return;
                } else {
                    e.preventDefault();
                    $('#sendBtn').click();
                }
            }
        });

        $('#messageInput').on('input', function() {
            $(this).css("height", "auto");

            const maxHeight = 120; // set your max height in px
            const newHeight = this.scrollHeight;

            // Apply the lesser of scrollHeight or maxHeight
            this.style.height = Math.min(newHeight, maxHeight) + 'px';

            // Add scroll if content exceeds max height
            this.style.overflowY = newHeight > maxHeight ? 'scroll' : 'hidden';
        });



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
                    ${message.replace(/\n/g, '<br>')}
                    <div class="message-time">${time}</div>
                </div>
            `;

            $('#messageContainer').append(bubble);
            requestAnimationFrame(() => {
                $('#messageContainer').scrollTop($('#messageContainer')[0].scrollHeight);
            });

            // ✅ Clear input
            $input.val('').trigger('input');

            // ✅ Update preview and time
            let previewText = '';
            if (message.includes('<img')) {
                previewText = '<i class="fas fa-image me-1"></i> Image';
            } else if (message.includes('<video')) {
                previewText = '<i class="fas fa-video me-1"></i> Video';
            } else if (message.toLowerCase().includes('download document')) {
                previewText = '<i class="fas fa-file-alt me-1"></i> Document';
            } else if (res.preview.includes('<audio')) {
                previewText = 'You: <i class="fas fa-microphone me-1"></i> Voice Note';
            } else {
                previewText = message.length > 40 ? message.slice(0, 40) + '...' : message;
                previewText = 'You: ' + previewText;
            }

            $(`#preview-${receiverId}`)
                .html(previewText)
                .removeClass('text-success fw-bold')
                .addClass('text-white small');

            $(`#time-${receiverId}`).text(time);
            $(`#badge-${receiverId}`).addClass('d-none');

            // ✅ Send to server
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
                        ${msg.message.replace(/\n/g, '<br>')}
                        <div class="message-time">${msg.time}</div>
                    </div>`;
                        $('#messageContainer').append(bubble);
                    });

                    $('#messageContainer').scrollTop($('#messageContainer')[0].scrollHeight);
                    $('#messageInput').focus();
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

            if (currentUploadType === 'media' || currentUploadType === 'document') {
                $('#fileInput').attr('accept', accept).click();
            } else if (currentUploadType === 'camera') {
                launchCamera()
            }

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

            // Generate preview text
            let previewText = '';
            const msgLower = e.message.toLowerCase();

            if (msgLower.includes('<img')) {
                previewText = '<i class="fas fa-image me-1"></i> Image';
            } else if (msgLower.includes('<video')) {
                previewText = '<i class="fas fa-video me-1"></i> Video';
            } else if (msgLower.includes('download document')) {
                previewText = '<i class="fas fa-file-alt me-1"></i> Document';
            } else if (msgLower.includes('<audio')) {
                previewText = '<i class="fas fa-microphone me-1"></i> Voice Note';
            } else {
                previewText = e.message.length > 40 ? e.message.slice(0, 40) + '...' : e.message;
            }

            // Update time
            $(`#time-${friendId}`).text(e.time);

            // If the chat is NOT open
            if (window.CURRENT_CHAT_ID != friendId) {
                // Highlight new message
                $(`#preview-${friendId}`)
                    .html(previewText)
                    .removeClass('text-white small')
                    .addClass('text-success fw-bold');

                const $badge = $(`#badge-${friendId}`);
                if ($badge.length) {
                    let count = parseInt($badge.text()) || 0;
                    $badge.text(count + 1).removeClass('d-none');
                } else {
                    $(`#chat-item-${friendId} .name`).append(
                        `<span id="badge-${friendId}" class="badge bg-success ms-2">1</span>`
                    );
                }

                return;
            }

            // ✅ Chat is open – show message and reset preview style
            const bubble = `
                <div class="message received">
                    ${e.message.replace(/\n/g, '<br>')}
                    <div class="message-time">${e.time}</div>
                </div>
            `;
            $('#messageContainer').append(bubble).scrollTop($('#messageContainer')[0].scrollHeight);

            // Reset preview style to normal
            $(`#preview-${friendId}`)
                .html(previewText)
                .removeClass('text-success fw-bold')
                .addClass('text-white small');

            // Hide unread badge
            $(`#badge-${friendId}`).addClass('d-none');
        });


        let videoStream = null;

        function launchCamera() {
            $('#cameraModal').removeClass('d-none');
            const video = document.getElementById('cameraStream');
            navigator.mediaDevices.getUserMedia({
                    video: true
                })
                .then(stream => {
                    videoStream = stream;
                    video.srcObject = stream;
                })
                .catch(() => {
                    showToast("Cannot access camera", true);
                    $('#cameraModal').addClass('d-none');
                });
        };

        $('#cancelCamera').on('click', function() {
            stopCamera();
            $('#cameraModal').addClass('d-none');
        });

        $('#capturePhoto').on('click', function() {
            const canvas = document.getElementById('cameraCanvas');
            const video = document.getElementById('cameraStream');
            const context = canvas.getContext('2d');

            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            context.drawImage(video, 0, 0, canvas.width, canvas.height);

            canvas.toBlob(function(blob) {
                stopCamera();
                $('#cameraModal').addClass('d-none');

                // Preview image
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#filePreview').html(
                        `<img src="${e.target.result}" class="img-fluid rounded" />`);
                    $('#filePreviewPopup').removeClass('d-none');
                    selectedFile = new File([blob], `photo-${Date.now()}.png`, {
                        type: "image/png"
                    });
                };
                reader.readAsDataURL(blob);
            }, 'image/png');
        });

        function stopCamera() {
            if (videoStream) {
                videoStream.getTracks().forEach(track => track.stop());
                videoStream = null;
            }
        }

        let mediaRecorder;
        let audioChunks = [];
        let recordingInterval;
        let seconds = 0;

        // Format time like 0:03
        function formatTime(s) {
            const m = Math.floor(s / 60);
            const ss = s % 60;
            return `${m}:${ss.toString().padStart(2, '0')}`;
        }

        // Start recording
        $('#micBtn').on('click', async function() {
            $('#chatInputBox').addClass('d-none');
            $('#voiceRecorderUI').removeClass('d-none');
            $('#recordingTime').text('0:00');

            seconds = 0;
            audioChunks = [];

            try {
                const stream = await navigator.mediaDevices.getUserMedia({
                    audio: true
                });
                mediaRecorder = new MediaRecorder(stream);

                mediaRecorder.ondataavailable = (e) => {
                    audioChunks.push(e.data);
                };

                mediaRecorder.start();

                recordingInterval = setInterval(() => {
                    seconds++;
                    $('#recordingTime').text(formatTime(seconds));
                }, 1000);
            } catch (err) {
                showToast('Unable to access microphone', true);
                $('#chatInputBox').removeClass('d-none');
                $('#voiceRecorderUI').addClass('d-none');
            }
        });

        // Cancel recording
        $('#cancelRecording').on('click', function() {
            if (mediaRecorder && mediaRecorder.state !== 'inactive') {
                mediaRecorder.stop();
            }

            clearInterval(recordingInterval);
            $('#voiceRecorderUI').addClass('d-none');
            $('#chatInputBox').removeClass('d-none');
        });

        // Send voice note
        $('#sendRecording').on('click', function() {
            if (!mediaRecorder || mediaRecorder.state === 'inactive') return;

            mediaRecorder.onstop = function() {
                const blob = new Blob(audioChunks, {
                    type: 'audio/webm'
                });

                if (blob.size === 0) {
                    showToast("Voice note is empty!", true);
                    return;
                }

                const file = new File([blob], `voice_${Date.now()}.webm`, {
                    type: 'audio/webm'
                });

                const formData = new FormData();
                formData.append('file', file);
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
                    },
                    error: function() {
                        showToast('Failed to upload voice note', true);
                    },
                    complete: function() {
                        $('#voiceRecorderUI').addClass('d-none');
                        $('#chatInputBox').removeClass('d-none');
                    }
                });
            };

            mediaRecorder.stop(); // triggers onstop callback
            clearInterval(recordingInterval);
        });
        $(document).on('click', '.play-audio', function() {
            const $btn = $(this);
            const $audio = $btn.siblings('audio')[0];
            const $icon = $btn.find('i');
            const $duration = $btn.siblings('.duration');

            if ($audio.paused) {
                $('audio').each((i, el) => el.pause()); // Pause others
                $audio.play();
                $icon.removeClass('fa-play').addClass('fa-pause');

                $audio.ontimeupdate = () => {
                    const min = Math.floor($audio.currentTime / 60);
                    const sec = Math.floor($audio.currentTime % 60);
                    $duration.text(`${min}:${sec.toString().padStart(2, '0')}`);
                };

                $audio.onended = () => {
                    $icon.removeClass('fa-pause').addClass('fa-play');
                };
            } else {
                $audio.pause();
                $icon.removeClass('fa-pause').addClass('fa-play');
            }
        });

        setTimeout(() => {
            $('#messageContainer').scrollTop($('#messageContainer')[0].scrollHeight);
        }, 100);
    </script>
</body>

</html>
