<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - COCAINE</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
</head>
<body>
    <div class="auth-card mx-auto">
        <div class="text-center mb-4">
            <div class="auth-icon">
                <i class="bi bi-person-plus-fill"></i>
            </div>
            <h3 class="fw-bold text-primary">COCAINE</h3>
            <p class="auth-footer-text small">Buat Akun Baru Celengan Pintar</p>
        </div>

        <form action="{{ route('register.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label small">Email</label>
                <input type="email" name="email" class="form-control rounded-3" placeholder="contoh@email.com" required>
            </div>
            <div class="mb-3">
                <label class="form-label small">Username</label>
                <input type="text" name="username" class="form-control rounded-3" placeholder="Buat username" required>
            </div>
            <div class="mb-4">
                <label class="form-label small">Password</label>
                <input type="password" name="password" class="form-control rounded-3" placeholder="Buat password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100 rounded-3 mb-3">Daftar Akun</button>
            
            <div class="text-center">
                <p class="small auth-footer-text mb-0">
                    Sudah punya akun? <a href="{{ route('login') }}" class="text-primary text-decoration-none fw-bold">Login</a>
                </p>
            </div>
        </form>
    </div>
</body>
</html>