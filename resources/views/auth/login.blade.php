<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - COCAINE</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #285A48;
            --secondary-color: #408A71;
            --accent-color: #B0E4CC;
            --dark-bg: #091413;
            --card-bg: #121f1d;
        }

        body { 
            background: var(--dark-bg); 
            display: flex; 
            align-items: center; 
            height: 100vh; 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            color: white;
        }

        .auth-card { 
            width: 100%; 
            max-width: 400px; 
            padding: 40px; 
            border-radius: 24px; 
            background: var(--card-bg); 
            box-shadow: 0 20px 40px rgba(0,0,0,0.4);
            border: 1px solid rgba(255,255,255,0.05);
        }

        .auth-logo-container {
            margin-bottom: 15px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .auth-logo-img {
            max-width: 150px;
            height: auto;
            display: block;
        }

        .text-primary { color: var(--accent-color) !important; }

        .auth-footer-text {
            color: rgba(255, 255, 255, 0.7);
        }
        
        .form-label { color: rgba(255,255,255,0.8); font-weight: 500; }
        
        .form-control { 
            background: #091413; 
            border: 1px solid rgba(255,255,255,0.1); 
            color: white;
            padding: 12px 15px;
        }

        .form-control:focus {
            background: #091413;
            border-color: var(--secondary-color);
            color: white;
            box-shadow: 0 0 0 0.25rem rgba(64, 138, 113, 0.25);
        }

        .btn-primary { 
            background-color: var(--primary-color); 
            border: none; 
            padding: 12px;
            font-weight: 600;
        }

        .btn-primary:hover { background-color: var(--secondary-color); }

        /* Style untuk teks keterangan agar putih terang */
        .auth-footer-text {
            color: rgba(255, 255, 255, 0.7);
        }

        .alert { border-radius: 12px; border: none; }
    </style>
</head>
<body>
    <div class="auth-card mx-auto">
        <div class="text-center mb-4">
            <div class="auth-logo-container">
                <img src="{{ asset('images/logo/Frame 7.png') }}" alt="Logo COCAINE" class="auth-logo-img">
            </div>
            <h3 class="fw-bold text-primary">COCAINE</h3>
            <p class="auth-footer-text small">Masuk ke Dashboard Celengan Pintar</p>
        </div>

        @if(session('success'))
            <div class="alert alert-success bg-success-subtle text-success small border-0">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger bg-danger-subtle text-danger small border-0">{{ $errors->first() }}</div>
        @endif

        <form action="{{ route('login.authenticate') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label small">Username</label>
                <input type="text" name="username" class="form-control rounded-3" placeholder="Masukkan username" required>
            </div>
            <div class="mb-4">
                <label class="form-label small">Password</label>
                <input type="password" name="password" class="form-control rounded-3" placeholder="Masukkan password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100 rounded-3 mb-3">Masuk Sekarang</button>
            <div class="text-center">
                <p class="small auth-footer-text mb-0">
                    Belum punya akun? <a href="{{ route('register') }}" class="text-primary text-decoration-none fw-bold">Daftar</a>
                </p>
            </div>
        </form>
    </div>
</body>
</html>