<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Show the profile edit page.
     */
    public function edit(): View
    {
        /** @var User $user */
        $user = auth()->user();

        return view('profile.edit', compact('user'));
    }

    /**
     * Update the authenticated user's profile.
     */
    public function update(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = auth()->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'nip' => ['nullable', 'string', 'max:50'],
            'jabatan' => ['nullable', 'string', 'max:100'],
            'telepon' => ['nullable', 'string', 'max:25'],
            'alamat' => ['nullable', 'string', 'max:500'],
            'foto' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah digunakan oleh pengguna lain.',
            'foto.image' => 'Berkas foto profil harus berupa gambar.',
            'foto.mimes' => 'Format foto yang didukung: JPG, PNG, WEBP.',
            'foto.max' => 'Ukuran foto maksimal 2 MB.',
            'password.min' => 'Password minimal terdiri dari 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        // Check if user requested to remove existing photo
        if ($request->boolean('hapus_foto')) {
            if ($user->foto && Storage::disk('public')->exists($user->foto)) {
                Storage::disk('public')->delete($user->foto);
            }
            $user->foto = null;
        } elseif ($request->hasFile('foto')) {
            // Delete old photo if exists
            if ($user->foto && Storage::disk('public')->exists($user->foto)) {
                Storage::disk('public')->delete($user->foto);
            }
            $path = $request->file('foto')->store('profile-photos', 'public');
            $user->foto = $path;
        }

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->nip = $validated['nip'] ?? null;
        $user->jabatan = $validated['jabatan'] ?? null;
        $user->telepon = $validated['telepon'] ?? null;
        $user->alamat = $validated['alamat'] ?? null;

        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        // Sync NIP to guru profile if exists
        if ($user->guru) {
            $user->guru->update([
                'nip' => $user->nip,
            ]);
        }

        return redirect()->back()->with('success', 'Profil Anda berhasil diperbarui!');
    }

    /**
     * Delete the authenticated user's profile photo.
     */
    public function deletePhoto(): RedirectResponse
    {
        /** @var User $user */
        $user = auth()->user();

        if ($user->foto && Storage::disk('public')->exists($user->foto)) {
            Storage::disk('public')->delete($user->foto);
        }

        $user->foto = null;
        $user->save();

        return redirect()->back()->with('success', 'Foto profil berhasil dihapus.');
    }
}
