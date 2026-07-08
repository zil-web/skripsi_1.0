@guest
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - SIKEU MTs</title>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, #091e14 0%, #064e3b 28%, #0f766e 52%, #10b981 78%, #091e14 100%);
            background-attachment: fixed;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: 
                radial-gradient(circle at 20% 50%, rgba(16, 185, 129, 0.15) 0%, transparent 50%),
                radial-gradient(circle at 80% 50%, rgba(20, 184, 166, 0.12) 0%, transparent 50%);
            pointer-events: none;
            z-index: 1;
        }

        .login-container {
            width: 100%;
            max-width: 420px;
            padding: 20px;
            position: relative;
            z-index: 2;
        }

        .glass-card {
            background: rgba(10, 25, 20, 0.8);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(16, 185, 129, 0.22);
            border-radius: 24px;
            padding: 40px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.4),
                        inset 0 1px 1px rgba(255, 255, 255, 0.08);
        }

        .logo-section {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 32px;
            justify-content: center;
        }

        .logo-icon {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, #10b981 0%, #047857 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 16px rgba(16, 185, 129, 0.35);
        }

        .logo-icon svg {
            width: 28px;
            height: 28px;
            color: white;
        }

        .logo-text h2 {
            font-size: 24px;
            font-weight: 700;
            color: #ffffff;
            line-height: 1.2;
        }

        .logo-text p {
            font-size: 12px;
            color: #a7f3d0;
            margin-top: 4px;
        }

        .header-text {
            text-align: center;
            margin-bottom: 32px;
        }

        .header-text h1 {
            font-size: 28px;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 8px;
        }

        .header-text p {
            font-size: 14px;
            color: #a7f3d0;
        }

        .alert {
            padding: 12px 16px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-size: 14px;
            border: 1px solid;
            animation: slideDown 0.3s ease-out;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alert-success {
            background: rgba(34, 197, 94, 0.1);
            border-color: rgba(34, 197, 94, 0.3);
            color: #86efac;
        }

        .alert-error {
            background: rgba(239, 68, 68, 0.1);
            border-color: rgba(239, 68, 68, 0.3);
            color: #fca5a5;
        }

        .alert ul {
            list-style: none;
            margin-top: 8px;
        }

        .alert li {
            font-size: 13px;
            margin-top: 4px;
        }

        .alert strong {
            display: block;
            font-weight: 600;
            margin-bottom: 4px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #d1fae5;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .form-group-wrapper {
            position: relative;
        }

        .form-group-wrapper .icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            width: 20px;
            height: 20px;
            color: #10b981;
            opacity: 0.7;
        }

        .form-group-wrapper input {
            width: 100%;
            padding: 12px 16px 12px 44px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(16, 185, 129, 0.32);
            border-radius: 10px;
            font-size: 14px;
            color: #ffffff;
            transition: all 0.3s ease;
            backdrop-filter: blur(4px);
        }

        .form-group-wrapper input::placeholder {
            color: rgba(255, 255, 255, 0.4);
        }

        .form-group-wrapper input:focus {
            outline: none;
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(16, 185, 129, 0.62);
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.12);
        }

        .password-toggle {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #10b981;
            cursor: pointer;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0.7;
            transition: opacity 0.2s;
        }

        .password-toggle:hover {
            opacity: 1;
        }

        .password-toggle svg {
            width: 20px;
            height: 20px;
        }

        .error-text {
            margin-top: 8px;
            font-size: 12px;
            color: #fca5a5;
        }

        .submit-btn {
            width: 100%;
            padding: 12px 16px;
            margin-top: 28px;
            background: linear-gradient(135deg, #10b981 0%, #047857 100%);
            color: white;
            border: 1px solid rgba(16, 185, 129, 0.5);
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            box-shadow: 0 8px 16px rgba(16, 185, 129, 0.3);
        }

        .submit-btn:hover {
            background: linear-gradient(135deg, #34d399 0%, #059669 100%);
            box-shadow: 0 12px 24px rgba(16, 185, 129, 0.36);
            transform: translateY(-2px);
        }

        .submit-btn:active {
            transform: translateY(0);
        }

        .footer-text {
            margin-top: 24px;
            text-align: center;
            font-size: 12px;
            color: #a7f3d0;
            line-height: 1.6;
        }

        @media (max-width: 480px) {
            .glass-card {
                padding: 32px 20px;
            }

            .header-text h1 {
                font-size: 24px;
            }

            .form-group label {
                font-size: 12px;
            }

            .submit-btn {
                font-size: 13px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="glass-card">
            <div class="logo-section">
                <div class="logo-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 2l3 7h7l-5.5 4 2 7L12 16l-6.5 4 2-7L2 9h7l3-7z"></path>
                    </svg>
                </div>
                <div class="logo-text">
                    <h2>SIKEU MTs</h2>
                    <p>Sistem Keuangan Sekolah</p>
                </div>
            </div>

            <div class="header-text">
                <h1>Selamat Datang</h1>
                <p>Masuk ke sistem keuangan sekolah</p>
            </div>

            @if(session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-error">
                    <strong>Login gagal</strong>
                    <ul>
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" novalidate>
                @csrf

                <div class="form-group">
                    <label for="username">Username</label>
                    <div class="form-group-wrapper">
                        <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM4 20v-1a4 4 0 014-4h8a4 4 0 014 4v1"/>
                        </svg>
                        <input id="username" name="username" type="text" value="{{ old('username') }}" required autofocus placeholder="Username">
                    </div>
                    @error('username')<p class="error-text">{{ $message }}</p>@enderror
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="form-group-wrapper" x-data="{ show: false }">
                        <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        <input :type="show ? 'text' : 'password'" id="password" name="password" required placeholder="Password">
                        <button type="button" class="password-toggle" @click="show = !show">
                            <svg x-show="!show" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            <svg x-show="show" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-4.803m2.676-2.422A10.08 10.08 0 0112 5c4.478 0 8.268 2.943 9.543 7a9.988 9.988 0 01-1.563 4.803m-2.676 2.422m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"></path>
                            </svg>
                        </button>
                    </div>
                    @error('password')<p class="error-text">{{ $message }}</p>@enderror
                </div>

                <button type="submit" class="submit-btn">Masuk</button>

                <div class="footer-text">
                    Lupa password? Hubungi administrator sistem
                </div>
            </form>
        </div>
    </div>
</body>
</html>
@endguest