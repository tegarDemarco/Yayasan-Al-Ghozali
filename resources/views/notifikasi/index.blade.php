@extends('layouts.app')

@section('title', 'Notifikasi')
@section('page-title', 'Notifikasi')

@section('content')
<nav class="breadcrumb">
    <a href="{{ route('dashboard') }}">Dashboard</a>
    <span class="breadcrumb-sep">/</span>
    <span class="breadcrumb-current">Notifikasi</span>
</nav>

<div class="page-header">
    <div class="page-header-left">
        <h1>Notifikasi</h1>
        <p>Kelola pemberitahuan dan aktivitas penting terkait akun Anda</p>
    </div>
    @if($unreadCount > 0)
    <div class="page-header-actions">
        <form method="POST" action="{{ route('notifikasi.mark-all-read') }}">
            @csrf
            <button type="submit" class="btn btn-secondary" style="display:inline-flex;align-items:center;gap:8px;padding:8px 16px;font-size:13px;border-radius:var(--radius-sm);cursor:pointer;border:1px solid var(--border);background:#fff;font-weight:600;">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" width="16" height="16"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                Tandai Semua Dibaca
            </button>
        </form>
    </div>
    @endif
</div>

<div class="card" style="padding: 0; overflow: hidden;">
    <!-- Filter Tabs -->
    <div style="display:flex;align-items:center;gap:12px;padding:16px 20px;border-bottom:1px solid var(--border);background:#fafafa;">
        <a href="{{ route('notifikasi.index') }}" 
           style="padding:6px 14px;border-radius:20px;font-size:13px;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:6px;transition:all var(--transition);{{ $filter === 'all' ? 'background:var(--primary);color:#fff;' : 'background:#fff;color:var(--text-secondary);border:1px solid var(--border);' }}">
            <span>Semua</span>
            <span style="font-size:11px;padding:1px 6px;border-radius:10px;{{ $filter === 'all' ? 'background:rgba(255,255,255,0.25);color:#fff;' : 'background:#e2e8f0;color:var(--text-primary);' }}">{{ $totalCount }}</span>
        </a>
        <a href="{{ route('notifikasi.index', ['filter' => 'unread']) }}" 
           style="padding:6px 14px;border-radius:20px;font-size:13px;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:6px;transition:all var(--transition);{{ $filter === 'unread' ? 'background:var(--primary);color:#fff;' : 'background:#fff;color:var(--text-secondary);border:1px solid var(--border);' }}">
            <span>Belum Dibaca</span>
            @if($unreadCount > 0)
                <span style="font-size:11px;padding:1px 6px;border-radius:10px;background:var(--red);color:#fff;">{{ $unreadCount }}</span>
            @else
                <span style="font-size:11px;padding:1px 6px;border-radius:10px;background:#e2e8f0;color:var(--text-primary);">0</span>
            @endif
        </a>
    </div>

    <!-- Notification List -->
    @if($notifications->count() > 0)
        <div>
            @foreach($notifications as $item)
                @php
                    $data = $item->data;
                    $isUnread = is_null($item->read_at);
                    $icon = $data['icon'] ?? 'bell';
                    $type = $data['type'] ?? 'info';
                @endphp
                <div style="display:flex;align-items:flex-start;gap:16px;padding:18px 20px;border-bottom:1px solid var(--border);transition:background var(--transition);position:relative;{{ $isUnread ? 'background:#f0fdf4;' : 'background:#fff;' }}">
                    @if($isUnread)
                        <div style="position:absolute;left:0;top:0;bottom:0;width:4px;background:var(--primary);"></div>
                    @endif

                    <!-- Type Icon -->
                    <div class="notif-item-icon notif-icon-{{ $type }}" style="margin-top:2px;">
                        <i data-lucide="{{ $icon }}" style="width: 18px; height: 18px;"></i>
                    </div>

                    <!-- Content -->
                    <div style="flex:1;min-width:0;">
                        <div style="display:flex;align-items:center;justify-content:space-between;gap:8px;margin-bottom:4px;flex-wrap:wrap;">
                            <div style="display:flex;align-items:center;gap:8px;">
                                <h4 style="font-size:14px;font-weight:700;color:var(--text-primary);margin:0;">
                                    {{ $data['title'] ?? 'Pemberitahuan' }}
                                </h4>
                                @if($isUnread)
                                    <span class="badge" style="background:#dcfce7;color:#15803d;font-size:10.5px;padding:1px 7px;">Baru</span>
                                @endif
                            </div>
                            <span style="font-size:12px;color:var(--text-muted);display:flex;align-items:center;gap:4px;">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" width="13" height="13"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                {{ $item->created_at->diffForHumans() }} ({{ $item->created_at->translatedFormat('d M Y, H:i') }})
                            </span>
                        </div>

                        <p style="font-size:13px;color:var(--text-secondary);line-height:1.5;margin-bottom:8px;">
                            {{ $data['message'] ?? '' }}
                        </p>

                        @if(!empty($data['sender_name']))
                            <div style="font-size:11.5px;color:var(--text-muted);margin-bottom:8px;">
                                Pengirim: <span style="font-weight:600;color:var(--text-secondary);">{{ $data['sender_name'] }}</span>
                            </div>
                        @endif

                        <div style="display:flex;align-items:center;gap:10px;margin-top:6px;flex-wrap:wrap;">
                            @if(!empty($data['url']))
                                <a href="{{ route('notifikasi.read-and-redirect', $item->id) }}" 
                                   style="font-size:12px;font-weight:600;color:var(--primary);text-decoration:none;display:inline-flex;align-items:center;gap:4px;background:var(--primary-50);padding:4px 10px;border-radius:6px;border:1px solid var(--primary-200);">
                                    Buka Halaman Terkait
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" width="12" height="12"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12h15m0 0l-6.75-6.75M19.5 12l-6.75 6.75" /></svg>
                                </a>
                            @endif

                            @if($isUnread)
                                <form method="POST" action="{{ route('notifikasi.mark-as-read', $item->id) }}" style="margin:0;">
                                    @csrf
                                    <button type="submit" style="font-size:12px;color:var(--text-secondary);background:none;border:none;cursor:pointer;padding:4px 6px;text-decoration:underline;">
                                        Tandai sudah dibaca
                                    </button>
                                </form>
                            @endif

                            <form method="POST" action="{{ route('notifikasi.destroy', $item->id) }}" style="margin:0;margin-left:auto;" onsubmit="return confirm('Hapus notifikasi ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" title="Hapus Notifikasi" style="background:none;border:none;cursor:pointer;color:var(--text-muted);padding:4px 6px;border-radius:4px;display:flex;align-items:center;transition:color 0.2s;" onmouseover="this.style.color='var(--red)'" onmouseout="this.style.color='var(--text-muted)'">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" width="15" height="15"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @if($notifications->hasPages())
            <div style="padding:16px 20px;">
                {{ $notifications->links() }}
            </div>
        @endif
    @else
        <div class="empty-state" style="padding:56px 20px;">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
            </svg>
            <h3>Belum Ada Notifikasi</h3>
            <p>
                @if($filter === 'unread')
                    Semua notifikasi sudah Anda baca.
                @else
                    Pemberitahuan mengenai izin, RPP, SK tugas, dan kegiatan sekolah akan muncul di sini.
                @endif
            </p>
        </div>
    @endif
</div>
@endsection

