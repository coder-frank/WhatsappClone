<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Login / Register</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6f8;
        }

        .auth-card {
            width: 100%;
            max-width: 400px;
            padding: 2rem;
            border-radius: 12px;
        }

        .form-label i {
            margin-right: 5px;
        }

        .spinner-border-sm {
            margin-left: 8px;
        }
    </style>
</head>

<body class="d-flex align-items-center justify-content-center vh-100">

    <div class="card shadow-sm bg-white auth-card">
        <h4 class="mb-4 text-center">🔐 Secure Login</h4>
        <form id="authForm" novalidate>
            @csrf
            <div class="mb-3">
                <label for="email" class="form-label">
                    <i class="fas fa-envelope"></i> Email Address
                </label>
                <input type="email" class="form-control" id="email" placeholder="you@example.com" required>
            </div>

            <div id="codeSection" class="mb-3" style="display: none;">
                <label for="code" class="form-label">
                    <i class="fas fa-key"></i> Enter 6-digit Code
                </label>
                <input type="text" class="form-control" id="code" maxlength="6" placeholder="123456">
            </div>

            <button type="submit" class="btn btn-primary w-100" id="submitBtn">
                <span id="btnText"><i class="fas fa-paper-plane"></i> Send Code</span>
                <span class="spinner-border spinner-border-sm d-none" id="spinner" role="status"
                    aria-hidden="true"></span>
            </button>
        </form>
        <div class="text-center mt-3">
            <small>Don't have an account? <a href="/register">Register here</a></small>
        </div>

    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

    <script>
        let mode = 'send';

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });

        $('#authForm').on('submit', function(e) {
            e.preventDefault();

            const email = $('#email').val().trim();
            const code = $('#code').val().trim();
            const $submitBtn = $('#submitBtn');
            const $btnText = $('#btnText');
            const $spinner = $('#spinner');

            if (!email || (mode === 'verify' && !code)) {
                showToast('Please fill in all required fields.', true);
                return;
            }

            $submitBtn.prop('disabled', true);
            $spinner.removeClass('d-none');

            if (mode === 'send') {
                $btnText.html('<i class="fas fa-spinner"></i> Sending...');
                $.ajax({
                    url: '/send-login-code',
                    method: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify({
                        email
                    }),
                    success: function(res) {
                        if (res.success) {
                            $('#codeSection').slideDown();
                            mode = 'verify';
                            $btnText.html('<i class="fas fa-check"></i> Verify Code');
                            showToast("Code sent to your email");
                        } else {
                            showToast(res.message || "Something went wrong", true);
                            $btnText.html('<i class="fas fa-paper-plane"></i> Send Code');
                        }
                    },
                    error: function() {
                        showToast("Error sending code", true);
                        $btnText.html('<i class="fas fa-paper-plane"></i> Send Code');
                    },
                    complete: function() {
                        $submitBtn.prop('disabled', false);
                        $spinner.addClass('d-none');
                    }
                });
            } else {
                $btnText.html('<i class="fas fa-spinner"></i> Verifying...');
                $.ajax({
                    url: '/verify-login-code',
                    method: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify({
                        email,
                        code
                    }),
                    success: function(res) {
                        if (res.success) {
                            showToast("Login successful!");
                            $btnText.html('<i class="fas fa-check-circle"></i> Logged In');
                            setTimeout(() => {
                                window.location.href = '/dashboard';
                            }, 1000);
                        } else {
                            showToast(res.message || "Invalid code", true);
                            $btnText.html('<i class="fas fa-check"></i> Verify Code');
                        }
                    },
                    error: function() {
                        showToast("Error verifying code", true);
                    },
                    complete: function() {
                        $submitBtn.prop('disabled', false);
                        $spinner.addClass('d-none');
                    }
                });
            }
        });

        function showToast(message, isError = false) {
            Toastify({
                text: message,
                duration: 3000,
                close: true,
                gravity: "top",
                position: "center",
                backgroundColor: isError ? "#dc3545" : "#198754",
            }).showToast();
        }
    </script>
</body>

</html>
