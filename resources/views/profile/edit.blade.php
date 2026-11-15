@extends('layouts.app')

@php
    $errors = $errors ?? new \Illuminate\Support\ViewErrorBag();
@endphp

@section('content')
<div class="page-header-row" style="margin-bottom: 24px;">
    <div>
        <h1 class="page-title">Pengaturan Profil</h1>
        <p class="page-subtitle">Kelola informasi data pribadi, foto profil, dan keamanan akun Anda</p>
    </div>
    <div class="page-header-actions">
        <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('dashboard') }}" class="btn-pill-action btn-pill-secondary">
            <i data-lucide="arrow-left" style="width:14px;height:14px;"></i>
            <span>Kembali</span>
        </a>
    </div>
</div>

<div style="display: grid; grid-template-columns: 320px 1fr; gap: 24px; align-items: start;">
    <!-- Left Column: Profile Card -->
    <div class="surface-white-card" style="text-align: center; padding: 28px 20px;">
        <div style="position: relative; display: inline-block; margin-bottom: 16px;">
            <div id="pageAvatarContainer" class="user-avatar-circle" style="width: 110px; height: 110px; font-size: 36px; margin: 0 auto; border: 3px solid #ffffff; box-shadow: 0 4px 14px rgba(0,0,0,0.12); overflow: hidden; position: relative;">
                @php
                    $nameParts = explode(' ', $user->name ?? 'User');
                    $userInitials = count($nameParts) > 1
                        ? strtoupper(substr($nameParts[0], 0, 1) . substr($nameParts[1], 0, 1))
                        : strtoupper(substr($user->name ?? 'US', 0, 2));
                @endphp
                <span id="pageAvatarInitials" style="{{ $user->foto ? 'display:none;' : '' }}">{{ $userInitials }}</span>
                <img id="pageAvatarImg" src="{{ $user->foto ? asset('storage/' . $user->foto) : '' }}" alt="{{ $user->name }}" style="{{ $user->foto ? 'width: 100%; height: 100%; object-fit: cover;' : 'display: none;' }}" onerror="this.onerror=null;this.style.display='none';document.getElementById('pageAvatarInitials').style.display='inline-block';">
            </div>

            <label for="pageFotoInput" style="position: absolute; bottom: 0; right: 0; background: var(--forest-800); color: #ffffff; width: 34px; height: 34px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; box-shadow: 0 2px 6px rgba(0,0,0,0.2); border: 2px solid #ffffff;" title="Ubah foto profil">
                <i data-lucide="camera" style="width: 16px; height: 16px;"></i>
            </label>
        </div>

        <h3 style="font-size: 16px; font-weight: 700; color: var(--text-heading); margin-bottom: 4px;">{{ $user->name }}</h3>
        <p style="font-size: 12.5px; color: var(--text-muted); margin-bottom: 12px;">{{ $user->email }}</p>

        <div style="display: flex; gap: 6px; justify-content: center; flex-wrap: wrap; margin-bottom: 20px;">
            @php
                $roleTitles = [
                    'pengurus_yayasan' => 'Pengurus Yayasan',
                    'kepala_sekolah' => 'Kepala Sekolah',
                    'admin_sekolah' => 'Admin Sekolah',
                    'guru' => 'Guru Pengajar',
                ];
                $roleBadge = $roleTitles[$user->role] ?? ucfirst($user->role);
            @endphp
            <span class="badge badge-success">{{ $roleBadge }}</span>
            @if($user->jenjang)
                <span class="badge badge-info">Unit {{ $user->jenjang }}</span>
            @endif
        </div>

        @if($user->foto)
            <form method="POST" action="{{ route('profile.delete-photo') }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus foto profil ini?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-pill-action" style="font-size: 12px; color: #ef4444; border-color: #fca5a5; width: 100%; justify-content: center;">
                    <i data-lucide="trash-2" style="width: 13px; height: 13px;"></i>
                    <span>Hapus Foto Profil</span>
                </button>
            </form>
        @endif

        <div style="border-top: 1px solid var(--card-border); margin-top: 20px; padding-top: 16px; text-align: left; font-size: 12.5px; color: var(--text-muted); line-height: 1.8;">
            <div style="display: flex; justify-content: space-between; margin-bottom: 6px;">
                <span>NIP / NUPTK</span>
                <strong style="color: var(--text-heading);">{{ $user->nip ?? '-' }}</strong>
            </div>
            <div style="display: flex; justify-content: space-between; margin-bottom: 6px;">
                <span>Jabatan</span>
                <strong style="color: var(--text-heading);">{{ $user->jabatan ?? '-' }}</strong>
            </div>
            <div style="display: flex; justify-content: space-between; margin-bottom: 6px;">
                <span>Telepon / WA</span>
                <strong style="color: var(--text-heading);">{{ $user->telepon ?? '-' }}</strong>
            </div>
            <div style="display: flex; justify-content: space-between;">
                <span>Status Akun</span>
                <span style="color: #16a34a; font-weight: 700;">● Aktif</span>
            </div>
        </div>
    </div>

    <!-- Right Column: Edit Profile Form -->
    <div class="surface-white-card" style="padding: 24px 28px;">
        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" id="mainProfileForm">
            @csrf
            @method('PUT')

            <!-- Hidden File Input linked to camera label -->
            <input type="file" name="foto" id="pageFotoInput" accept="image/png,image/jpeg,image/jpg,image/webp" style="display: none;" onchange="previewPageAvatar(event)">

            <div style="border-bottom: 1px solid var(--card-border); padding-bottom: 12px; margin-bottom: 20px;">
                <h3 style="font-size: 15px; font-weight: 700; color: var(--text-heading); display: flex; align-items: center; gap: 8px;">
                    <i data-lucide="user" style="width: 16px; height: 16px; color: var(--forest-700);"></i>
                    Informasi Data Pribadi
                </h3>
                <p style="font-size: 12px; color: var(--text-muted); margin-top: 2px;">Perbarui data diri Anda yang terdaftar pada sistem akademik</p>
            </div>

            <div class="form-grid form-grid-2" style="margin-bottom: 16px;">
                <div class="form-group">
                    <label class="form-label" for="name">Nama Lengkap <span style="color: #ef4444;">*</span></label>
                    <input type="text" id="name" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                    @error('name')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="email">Alamat Email <span style="color: #ef4444;">*</span></label>
                    <input type="email" id="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                    @error('email')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="nip">NIP / NUPTK</label>
                    <input type="text" id="nip" name="nip" class="form-control" value="{{ old('nip', $user->nip) }}" placeholder="Nomor Induk Pegawai">
                    @error('nip')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="telepon">No. Telepon / WhatsApp</label>
                    <input type="text" id="telepon" name="telepon" class="form-control" value="{{ old('telepon', $user->telepon) }}" placeholder="Contoh: 081234567890">
                    @error('telepon')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 16px;">
                <label class="form-label" for="jabatan">Jabatan Struktural / Tugas</label>
                <input type="text" id="jabatan" name="jabatan" class="form-control" value="{{ old('jabatan', $user->jabatan) }}" placeholder="Contoh: Guru Produktif RPL / Kepala Lab">
                @error('jabatan')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group" style="margin-bottom: 24px;">
                <label class="form-label" for="alamat">Alamat Tempat Tinggal</label>
                <textarea id="alamat" name="alamat" class="form-control" rows="3" placeholder="Alamat domisili lengkap...">{{ old('alamat', $user->alamat) }}</textarea>
                @error('alamat')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <!-- Password Change Section -->
            <div style="border-top: 1px solid var(--card-border); padding-top: 20px; margin-bottom: 24px;">
                <div style="margin-bottom: 16px;">
                    <h3 style="font-size: 15px; font-weight: 700; color: var(--text-heading); display: flex; align-items: center; gap: 8px;">
                        <i data-lucide="shield-check" style="width: 16px; height: 16px; color: var(--forest-700);"></i>
                        Keamanan Akun & Kata Sandi
                    </h3>
                    <p style="font-size: 12px; color: var(--text-muted); margin-top: 2px;">Kosongkan kolom kata sandi di bawah jika Anda tidak ingin mengubahnya</p>
                </div>

                <div class="form-grid form-grid-2">
                    <div class="form-group">
                        <label class="form-label" for="password">Kata Sandi Baru</label>
                        <input type="password" id="password" name="password" class="form-control" placeholder="Minimal 8 karakter" autocomplete="new-password">
                        @error('password')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="password_confirmation">Konfirmasi Kata Sandi Baru</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" placeholder="Ulangi kata sandi baru" autocomplete="new-password">
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div style="display: flex; align-items: center; justify-content: flex-end; gap: 12px; border-top: 1px solid var(--card-border); padding-top: 18px;">
                <a href="{{ route('dashboard') }}" class="btn-pill-action btn-pill-secondary">Batal</a>
                <button type="submit" class="btn-forest-primary" style="padding: 9px 22px; font-size: 13.5px;">
                    <i data-lucide="save" style="width: 15px; height: 15px;"></i>
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function previewPageAvatar(event) {
        const file = event.target.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = function(e) {
            const img = document.getElementById('pageAvatarImg');
            const initials = document.getElementById('pageAvatarInitials');
            img.src = e.target.result;
            img.style.display = 'block';
            if (initials) {
                initials.style.display = 'none';
            }
        };
        reader.readAsDataURL(file);
    }
</script>
@endsection
