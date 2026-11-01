<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login – Yayasan Al-Ghozaly</title>
    <meta name="description" content="Sistem Informasi Manajemen Guru Yayasan Al-Ghozaly">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html, body { height: 100%; font-family: 'Inter', system-ui, sans-serif; }

        body {
            display: flex;
            min-height: 100vh;
            background: #0f2419;
        }

        /* Left Panel */
        .left-panel {
            flex: 1;
            background: linear-gradient(155deg, #0a1f12 0%, #0f2419 30%, #1a3a2a 60%, #16a34a 100%);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 40px;
            position: relative;
            overflow: hidden;
        }

        .left-panel::before {
            content: '';
            position: absolute;
            top: -100px; right: -100px;
            width: 400px; height: 400px;
            background: radial-gradient(circle, rgba(22,163,74,0.15) 0%, transparent 70%);
            border-radius: 50%;
        }

        .left-panel::after {
            content: '';
            position: absolute;
            bottom: -80px; left: -80px;
            width: 300px; height: 300px;
            background: radial-gradient(circle, rgba(34,197,94,0.1) 0%, transparent 70%);
            border-radius: 50%;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 14px;
            position: relative;
            z-index: 1;
        }

        .brand-icon {
            width: 52px; height: 52px;
            background: linear-gradient(135deg, #16a34a 0%, #22c55e 100%);
            border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 8px 24px rgba(22,163,74,0.4);
        }

        .brand-text h1 { font-size: 20px; font-weight: 800; color: #fff; line-height: 1.2; }
        .brand-text p { font-size: 12.5px; color: rgba(255,255,255,0.55); margin-top: 2px; }

        .left-content {
            position: relative;
            z-index: 1;
        }

        .left-content .tagline {
            font-size: 32px;
            font-weight: 800;
            color: #fff;
            line-height: 1.25;
            margin-bottom: 16px;
        }

        .left-content .tagline span { color: #4ade80; }

        .left-content .desc {
            font-size: 14px;
            color: rgba(255,255,255,0.6);
            line-height: 1.7;
            max-width: 340px;
            margin-bottom: 32px;
        }

        .feature-list { display: flex; flex-direction: column; gap: 12px; }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 13.5px;
            color: rgba(255,255,255,0.75);
        }

        .feature-dot {
            width: 32px; height: 32px;
            background: rgba(22,163,74,0.2);
            border: 1px solid rgba(22,163,74,0.4);
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
            font-size: 14px;
        }

        .school-badges {
            display: flex;
            gap: 10px;
            position: relative;
            z-index: 1;
        }

        .school-badge {
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            border: 1px solid rgba(255,255,255,0.15);
        }

        .badge-sd { background: rgba(59,130,246,0.2); color: #93c5fd; }
        .badge-mts { background: rgba(245,158,11,0.2); color: #fcd34d; }
        .badge-smk { background: rgba(236,72,153,0.2); color: #f9a8d4; }

        /* Right Panel */
        .right-panel {
            width: 480px;
            background: #fff;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 48px 44px;
        }

        .login-header { margin-bottom: 32px; }
        .login-header h2 { font-size: 26px; font-weight: 800; color: #0f172a; margin-bottom: 6px; }
        .login-header p { font-size: 14px; color: #64748b; }

        .form-group { margin-bottom: 18px; }

        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 7px;
        }

        .input-wrapper {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 13px; top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            pointer-events: none;
        }

        .input-icon svg { width: 17px; height: 17px; }

        .form-control {
            width: 100%;
            padding: 11px 14px 11px 42px;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            font-size: 14px;
            color: #0f172a;
            background: #f8fafc;
            transition: all 0.2s;
            font-family: inherit;
        }

        .form-control:focus {
            outline: none;
            border-color: #16a34a;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(22,163,74,0.1);
        }

        .form-control::placeholder { color: #94a3b8; }
        .form-control.is-invalid { border-color: #ef4444; background: #fff5f5; }

        .invalid-feedback {
            font-size: 12px;
            color: #dc2626;
            margin-top: 5px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .form-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
            margin-top: -4px;
        }

        .checkbox-wrapper {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }

        .checkbox-wrapper input { width: 15px; height: 15px; accent-color: #16a34a; cursor: pointer; }
        .checkbox-wrapper span { font-size: 13px; color: #64748b; cursor: pointer; }

        .forgot-link {
            font-size: 13px;
            color: #16a34a;
            text-decoration: none;
            font-weight: 500;
        }

        .forgot-link:hover { text-decoration: underline; }

        .btn-login {
            width: 100%;
            padding: 13px;
            background: linear-gradient(135deg, #16a34a 0%, #15803d 100%);
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
            box-shadow: 0 4px 14px rgba(22,163,74,0.3);
            font-family: inherit;
            letter-spacing: 0.01em;
        }

        .btn-login:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(22,163,74,0.4);
        }

        .btn-login:active { transform: translateY(0); }

        .divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 24px 0 20px;
        }

        .divider::before, .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e2e8f0;
        }

        .divider span { font-size: 12px; color: #94a3b8; white-space: nowrap; }

        .demo-accounts {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 10px;
            padding: 14px 16px;
        }

        .demo-accounts h4 {
            font-size: 11.5px;
            font-weight: 700;
            color: #166534;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            margin-bottom: 10px;
        }

        .demo-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 4px 0;
            border-bottom: 1px dashed #bbf7d0;
        }

        .demo-item:last-child { border-bottom: none; }

        .demo-role { font-size: 12px; color: #166534; font-weight: 600; }
        .demo-cred {
            font-size: 11.5px;
            color: #15803d;
            font-family: monospace;
            background: #dcfce7;
            padding: 2px 8px;
            border-radius: 4px;
        }

        .footer-note {
            text-align: center;
            font-size: 12px;
            color: #94a3b8;
            margin-top: 24px;
        }

        /* Eye toggle for password */
        .eye-toggle {
            position: absolute;
            right: 13px; top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: #9ca3af;
            padding: 2px;
        }
        .eye-toggle:hover { color: #64748b; }
        .eye-toggle svg { width: 17px; height: 17px; }

        @media (max-width: 900px) {
            .left-panel { display: none; }
            .right-panel { width: 100%; padding: 40px 28px; }
        }

        @media (max-width: 480px) {
            .right-panel { padding: 32px 20px; }
        }
    </style>
</head>
<body>

    <!-- Left Panel -->
    <div class="left-panel">
        <div class="brand">
            <div class="brand-icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="white" width="26" height="26">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5" />
                </svg>
            </div>
            <div class="brand-text">
                <h1>Yayasan Al-Ghozaly</h1>
                <p>Sistem Informasi Manajemen Guru</p>
            </div>
        </div>

        <div class="left-content">
            <p class="tagline">Bersama Membangun<br><span>Pendidikan</span> yang<br>Lebih Baik</p>
            <p class="desc">Platform terintegrasi untuk pengelolaan administrasi guru di seluruh jenjang pendidikan Yayasan Al-Ghozaly.</p>
            <div class="feature-list">
                <div class="feature-item"><div class="feature-dot">📅</div> Jadwal & Jurnal Mengajar Digital</div>
                <div class="feature-item"><div class="feature-dot">✅</div> Presensi & Monitoring Kehadiran</div>
                <div class="feature-item"><div class="feature-dot">📄</div> RPP, Modul Ajar & Bahan Ajar</div>
                <div class="feature-item"><div class="feature-dot">⭐</div> Penilaian & Evaluasi Kinerja</div>
            </div>
        </div>

        <div class="school-badges">
            <div class="school-badge badge-sd">SD Al-Ghozaly</div>
            <div class="school-badge badge-mts">MTs Al-Ghozaly</div>
            <div class="school-badge badge-smk">SMK Al-Ghozaly</div>
        </div>
    </div>

    <!-- Right Panel -->
    <div class="right-panel">
        <div class="login-header">
            <h2>Selamat Datang 👋</h2>
            <p>Silakan masuk untuk melanjutkan ke sistem</p>
        </div>

        @if ($errors->any())
        <div style="background:#fee2e2;border:1px solid #fecaca;border-radius:10px;padding:12px 16px;margin-bottom:20px;font-size:13px;color:#b91c1c;display:flex;align-items:center;gap:8px;">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" width="17" height="17" style="flex-shrink:0"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" /></svg>
            {{ $errors->first() }}
        </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="form-group">
                <label class="form-label" for="email">Email atau Username</label>
                <div class="input-wrapper">
                    <span class="input-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" /></svg>
                    </span>
                    <input type="text" id="email" name="email" class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}"
                        placeholder="Email atau username" value="{{ old('email') }}" required autofocus autocomplete="username">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="password">Password</label>
                <div class="input-wrapper">
                    <span class="input-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" /></svg>
                    </span>
                    <input type="password" id="password" name="password" class="form-control"
                        placeholder="Kata sandi" required autocomplete="current-password">
                    <button type="button" class="eye-toggle" onclick="togglePassword()" id="eyeBtn">
                        <svg id="eyeOpen" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                        <svg id="eyeClosed" style="display:none" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" /></svg>
                    </button>
                </div>
            </div>

            <div class="form-row">
                <label class="checkbox-wrapper">
                    <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                    <span>Ingat saya</span>
                </label>
                <a href="#" class="forgot-link">Lupa password?</a>
            </div>

            <button type="submit" class="btn-login">Masuk ke Sistem</button>
        </form>

        <div class="divider">
            <span>Akun Demo Tersedia</span>
        </div>

        <div class="demo-accounts">
            <h4>🔑 Akun Demo (Password: password)</h4>
            <div class="demo-item">
                <span class="demo-role">Pengurus Yayasan</span>
                <span class="demo-cred">pengurus</span>
            </div>
            <div class="demo-item">
                <span class="demo-role">Kepala Sekolah SD</span>
                <span class="demo-cred">kepsek_sd</span>
            </div>
            <div class="demo-item">
                <span class="demo-role">Admin Sekolah MTs</span>
                <span class="demo-cred">admin_mts</span>
            </div>
            <div class="demo-item">
                <span class="demo-role">Guru SD</span>
                <span class="demo-cred">guru_sd1</span>
            </div>
        </div>

        <p class="footer-note">© {{ date('Y') }} Yayasan Al-Ghozaly. All rights reserved.</p>
    </div>

    <script>
        function togglePassword() {
            const input = document.getElementById('password');
            const eyeOpen = document.getElementById('eyeOpen');
            const eyeClosed = document.getElementById('eyeClosed');
            if (input.type === 'password') {
                input.type = 'text';
                eyeOpen.style.display = 'none';
                eyeClosed.style.display = 'block';
            } else {
                input.type = 'password';
                eyeOpen.style.display = 'block';
                eyeClosed.style.display = 'none';
            }
        }
    </script>
</body>
</html>
