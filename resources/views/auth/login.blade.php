<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk ke akun Anda – Yayasan Al Ghozaly</title>
    <meta name="description" content="Sistem Informasi Akademik Yayasan Al Ghozaly">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>

    <style>
        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Plus Jakarta Sans', 'Inter', -apple-system, sans-serif;
            background-color: #eef3f0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            color: #374151;
            -webkit-font-smoothing: antialiased;
        }

        /* Centered Split Card Container */
        .login-card-container {
            width: 100%;
            max-width: 960px;
            background-color: #ffffff;
            border-radius: 24px;
            overflow: hidden;
            display: grid;
            grid-template-columns: 1fr 1fr;
            box-shadow: 0 20px 45px -10px rgba(18, 51, 40, 0.12), 0 8px 16px -4px rgba(0, 0, 0, 0.04);
            border: 1px solid #e1ebe5;
        }

        /* Left Forest Green Panel */
        .login-left-panel {
            background: linear-gradient(155deg, #184337 0%, #113429 100%);
            padding: 44px 40px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
            color: #ffffff;
        }

        /* Concentric Circles Watermark */
        .login-left-panel::before {
            content: '';
            position: absolute;
            right: -120px;
            bottom: -120px;
            width: 440px;
            height: 440px;
            border-radius: 50%;
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: inset 0 0 0 50px rgba(255, 255, 255, 0.02),
                        inset 0 0 0 100px rgba(255, 255, 255, 0.02),
                        inset 0 0 0 160px rgba(255, 255, 255, 0.02);
            pointer-events: none;
        }

        .login-left-panel::after {
            content: '';
            position: absolute;
            right: 40px;
            top: -60px;
            width: 240px;
            height: 240px;
            border-radius: 50%;
            border: 1px solid rgba(255, 255, 255, 0.06);
            pointer-events: none;
        }

        /* Brand Row */
        .left-brand-row {
            display: flex;
            align-items: center;
            gap: 12px;
            position: relative;
            z-index: 2;
        }

        .left-brand-badge {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            border: 1.5px solid rgba(255, 255, 255, 0.45);
            background: rgba(255, 255, 255, 0.04);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
            font-weight: 800;
            color: #ffffff;
            letter-spacing: 0.5px;
            flex-shrink: 0;
        }

        .left-brand-text {
            display: flex;
            flex-direction: column;
        }

        .left-brand-title {
            font-size: 16px;
            font-weight: 700;
            color: #ffffff;
            line-height: 1.2;
        }

        .left-brand-sub {
            font-size: 10px;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.6);
            letter-spacing: 0.8px;
            text-transform: uppercase;
            margin-top: 2px;
        }

        /* Middle Pitch Content */
        .left-pitch-content {
            margin: 48px 0;
            position: relative;
            z-index: 2;
        }

        .pitch-eyebrow {
            font-size: 11px;
            font-weight: 700;
            color: #6ee7b7;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-bottom: 12px;
        }

        .pitch-headline {
            font-size: 28px;
            font-weight: 800;
            line-height: 1.25;
            color: #ffffff;
            letter-spacing: -0.4px;
            margin-bottom: 14px;
        }

        .pitch-desc {
            font-size: 13.5px;
            color: rgba(255, 255, 255, 0.78);
            line-height: 1.6;
            max-width: 360px;
        }

        /* Left Footer Badge */
        .left-footer-units {
            font-size: 11.5px;
            color: rgba(255, 255, 255, 0.6);
            letter-spacing: 0.4px;
            position: relative;
            z-index: 2;
        }

        /* Right White Panel */
        .login-right-panel {
            padding: 48px 44px;
            background-color: #ffffff;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .right-header-block {
            margin-bottom: 26px;
        }

        .right-eyebrow {
            font-size: 10.5px;
            font-weight: 700;
            color: #1e5645;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-bottom: 6px;
        }

        .right-headline {
            font-size: 24px;
            font-weight: 800;
            color: #111827;
            letter-spacing: -0.3px;
            margin-bottom: 6px;
        }

        .right-subheadline {
            font-size: 13px;
            color: #6b7280;
            line-height: 1.45;
        }

        /* Form Elements */
        .form-field-group {
            margin-bottom: 18px;
        }

        .form-label-title {
            display: block;
            font-size: 12.5px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 7px;
        }

        .input-relative-box {
            position: relative;
        }

        .login-form-input {
            width: 100%;
            padding: 11px 14px;
            border: 1.5px solid #e5e7eb;
            border-radius: 10px;
            font-size: 13.5px;
            font-family: inherit;
            color: #111827;
            background-color: #ffffff;
            transition: all 0.15s ease;
            outline: none;
        }

        .login-form-input:focus {
            border-color: #1e5645;
            box-shadow: 0 0 0 3px rgba(30, 86, 69, 0.1);
        }

        .login-form-input::placeholder {
            color: #9ca3af;
        }

        .password-toggle-btn {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #9ca3af;
            cursor: pointer;
            padding: 4px;
            display: flex;
            align-items: center;
        }

        .password-toggle-btn:hover {
            color: #4b5563;
        }

        /* Checkbox & Forgot row */
        .form-options-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 22px;
            font-size: 12.5px;
        }

        .checkbox-container {
            display: flex;
            align-items: center;
            gap: 7px;
            cursor: pointer;
            color: #4b5563;
            user-select: none;
        }

        .checkbox-container input {
            accent-color: #184337;
            width: 15px;
            height: 15px;
            cursor: pointer;
        }

        .forgot-password-link {
            color: #1e5645;
            font-weight: 600;
            text-decoration: none;
        }

        .forgot-password-link:hover {
            text-decoration: underline;
        }

        /* Submit Button */
        .btn-login-submit {
            width: 100%;
            padding: 12px 18px;
            background-color: #164235;
            color: #ffffff;
            border: none;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.15s ease;
            font-family: inherit;
        }

        .btn-login-submit:hover {
            background-color: #103328;
            box-shadow: 0 4px 12px rgba(22, 66, 53, 0.25);
        }

        /* Prototype info note */
        .prototype-note-box {
            margin-top: 16px;
            text-align: center;
            font-size: 11.5px;
            color: #9ca3af;
            line-height: 1.45;
        }

        /* Quick Demo Account Selector (Helpful for test/review) */
        .demo-quick-box {
            margin-top: 18px;
            padding: 12px;
            background-color: #f7faf8;
            border: 1px dashed #cfe0d8;
            border-radius: 10px;
        }

        .demo-quick-title {
            font-size: 11px;
            font-weight: 700;
            color: #1e5645;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }

        .demo-pills-row {
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
        }

        .demo-account-pill {
            background-color: #ffffff;
            border: 1px solid #d1d5db;
            border-radius: 9999px;
            padding: 4px 10px;
            font-size: 11px;
            font-weight: 600;
            color: #374151;
            cursor: pointer;
            transition: all 0.15s;
        }

        .demo-account-pill:hover {
            border-color: #1e5645;
            color: #1e5645;
            background-color: #eef7f3;
        }

        /* Help link footer */
        .right-help-footer {
            margin-top: 24px;
            text-align: center;
            font-size: 12px;
            color: #6b7280;
            border-top: 1px solid #f3f4f6;
            padding-top: 16px;
        }

        .right-help-footer a {
            color: #1e5645;
            font-weight: 600;
            text-decoration: none;
        }

        .right-help-footer a:hover {
            text-decoration: underline;
        }

        /* Alert message */
        .login-error-alert {
            background-color: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
            padding: 10px 14px;
            border-radius: 8px;
            font-size: 12.5px;
            margin-bottom: 18px;
        }

        @media (max-width: 820px) {
            .login-card-container {
                grid-template-columns: 1fr;
            }
            .login-left-panel {
                display: none;
            }
            .login-right-panel {
                padding: 36px 24px;
            }
        }
    </style>
</head>
<body>

<div class="login-card-container">
    <!-- Left Forest Green Panel -->
    <div class="login-left-panel">
        <div class="left-brand-row">
            <div class="left-brand-badge">AG</div>
            <div class="left-brand-text">
                <span class="left-brand-title">Yayasan Al Ghozaly</span>
                <span class="left-brand-sub">SISTEM INFORMASI AKADEMIK</span>
            </div>
        </div>

        <div class="left-pitch-content">
            <div class="pitch-eyebrow">Satu Sistem · Tiga Jenjang</div>
            <h1 class="pitch-headline">Bersama mendidik, menuju masa depan.</h1>
            <p class="pitch-desc">
                Ruang kerja terpadu untuk mendukung kegiatan akademik, administrasi sekolah, dan pengembangan mutu pendidikan.
            </p>
        </div>

        <div class="left-footer-units">
            SD Al Ghozaly · MTs Al Ghozaly · SMK Al Ghozaly
        </div>
    </div>

    <!-- Right Form Panel -->
    <div class="login-right-panel">
        <div class="right-header-block">
            <div class="right-eyebrow">Selamat Datang Kembali</div>
            <h2 class="right-headline">Masuk ke akun Anda</h2>
            <p class="right-subheadline">Gunakan akun resmi yang telah terdaftar di sistem Yayasan Al Ghozaly.</p>
        </div>

        @if (isset($errors) && $errors->any())
        <div class="login-error-alert">
            {{ $errors->first() }}
        </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="form-field-group">
                <label class="form-label-title" for="email">Email atau username</label>
                <div class="input-relative-box">
                    <input type="text" id="email" name="email" class="login-form-input"
                           placeholder="nama@alghozaly.sch.id"
                           value="{{ old('email', 'admin_sd') }}" required autofocus autocomplete="username">
                </div>
            </div>

            <div class="form-field-group">
                <label class="form-label-title" for="password">Kata sandi</label>
                <div class="input-relative-box">
                    <input type="password" id="password" name="password" class="login-form-input"
                           placeholder="Masukkan kata sandi" value="password" required autocomplete="current-password">
                    <button type="button" class="password-toggle-btn" onclick="togglePasswordVisibility()" aria-label="Lihat kata sandi">
                        <i data-lucide="eye" id="eyeIcon" style="width:16px;height:16px;"></i>
                    </button>
                </div>
            </div>

            <div class="form-options-row">
                <label class="checkbox-container">
                    <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                    <span>Ingat saya</span>
                </label>
                <a href="#" class="forgot-password-link" onclick="alert('Silakan hubungi administrator sekolah untuk mengatur ulang kata sandi Anda.')">
                    Lupa kata sandi?
                </a>
            </div>

            <button type="submit" class="btn-login-submit">
                <span>Masuk ke sistem</span>
                <i data-lucide="arrow-right" style="width:16px;height:16px;"></i>
            </button>
        </form>

        <!-- Quick Demo Switcher -->
        <div class="demo-quick-box">
            <div class="demo-quick-title">Coba Akun Demo (Klik untuk isi cepat):</div>
            <div class="demo-pills-row">
                <button type="button" class="demo-account-pill" onclick="fillAccount('admin_sd', 'password')">Admin SD</button>
                <button type="button" class="demo-account-pill" onclick="fillAccount('guru_sd1', 'password')">Guru SD</button>
                <button type="button" class="demo-account-pill" onclick="fillAccount('kepsek_sd', 'password')">Kepala SD</button>
                <button type="button" class="demo-account-pill" onclick="fillAccount('pengurus', 'password')">Pengurus</button>
            </div>
        </div>

        <div class="prototype-note-box">
            Prototipe demonstrasi: tombol masuk akan membuka dashboard sesuai peran. Autentikasi terhubung ke server database.
        </div>

        <div class="right-help-footer">
            Butuh bantuan akses? <a href="mailto:admin@alghozaly.sch.id">Hubungi Admin Sekolah</a>
        </div>
    </div>
</div>

<script>
    lucide.createIcons();

    function togglePasswordVisibility() {
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            eyeIcon.setAttribute('data-lucide', 'eye-off');
        } else {
            passwordInput.type = 'password';
            eyeIcon.setAttribute('data-lucide', 'eye');
        }
        lucide.createIcons();
    }

    function fillAccount(username, password) {
        document.getElementById('email').value = username;
        document.getElementById('password').value = password;
    }
</script>

</body>
</html>
