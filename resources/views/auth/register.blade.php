<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Register - COCAINE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; display: flex; align-items: center; height: 100vh; font-family: 'Plus Jakarta Sans', sans-serif; }
        .auth-card { width: 100%; max-width: 400px; padding: 30px; border-radius: 20px; background: white; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
    </style>
</head>
<body>
    <div class="auth-card mx-auto">
        <div class="text-center mb-4">
            <h3 class="fw-bold text-primary">COCAINE</h3>
            <p class="text-muted">Daftar Akun Baru</p>
        </div>

        <form action="{{ route('register.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label small">Email</label>
                <input type="email" name="email" class="form-control rounded-3" required>
            </div>
            <div class="mb-3">
                <label class="form-label small">Username</label>
                <input type="text" name="username" class="form-control rounded-3" required>
            </div>
            <div class="mb-3">
                <label class="form-label small">Password</label>
                <input type="password" name="password" class="form-control rounded-3" required>
            </div>
            <button type="submit" class="btn btn-primary w-100 rounded-3 py-2">Daftar Sekarang</button>
        </form>

        <div class="text-center mt-4">
            <small>Sudah punya akun? <a href="{{ route('login') }}" class="text-decoration-none">Login di sini</a></small>
        </div>
    </div>
</body>
</html>