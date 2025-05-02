<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Register</title>
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
    </style>
</head>

<body class="d-flex align-items-center justify-content-center vh-100">
    <div class="card shadow-sm bg-white auth-card">
        <h4 class="mb-4 text-center">📝 Create Account</h4>
        <form id="registerForm">
            <div class="mb-3">
                <label for="name" class="form-label"><i class="fas fa-user"></i> Full Name</label>
                <input type="text" class="form-control" id="name" placeholder="John Doe" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label"><i class="fas fa-envelope"></i> Email Address</label>
                <input type="email" class="form-control" id="email" placeholder="you@example.com" required>
            </div>
            <div class="mb-3">
                <label for="phone" class="form-label"><i class="fas fa-phone"></i> Phone Number</label>
                <div class="input-group">
                    <span class="input-group-text">+234</span>
                    <input type="tel" class="form-control" id="phone" placeholder="8012345678" required>
                </div>
            </div>

            <button type="submit" class="btn btn-success w-100"><i class="fas fa-user-plus"></i> Register</button>

            <div class="text-center mt-3">
                <small>Already have an account? <a href="/login">Login here</a></small>
            </div>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name=\"csrf-token\"]').getAttribute('content')
            }
        });

        $('#registerForm').on('submit', function(e) {
            e.preventDefault();

            const name = $('#name').val().trim();
            const email = $('#email').val().trim();
            const phone = $('#phone').val().trim();

            if (!name || !email || !phone) {
                return showToast('Please fill all fields', true);
            }

            $.post('/register', {
                name,
                email,
                phone
            }, function(res) {
                if (res.success) {
                    showToast('Account created! Redirecting...');
                    setTimeout(() => location.href = '/login', 1500);
                } else {
                    showToast(res.message || 'Failed to register.', true);
                }
            }).fail(() => {
                showToast('Server error occurred.', true);
            });
        });

        function showToast(msg, isError = false) {
            Toastify({
                text: msg,
                duration: 3000,
                gravity: 'top',
                position: 'center',
                backgroundColor: isError ? '#dc3545' : '#198754',
                close: true
            }).showToast();
        }
    </script>
</body>

</html>
