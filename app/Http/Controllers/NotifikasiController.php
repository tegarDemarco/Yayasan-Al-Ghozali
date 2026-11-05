<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotifikasiController extends Controller
{
    /**
     * Display a listing of notifications for the authenticated user.
     */
    public function index(Request $request): View
    {
        $user = auth()->user();
        $filter = $request->query('filter', 'all');

        $query = $user->notifications();

        if ($filter === 'unread') {
            $query->whereNull('read_at');
        }

        $notifications = $query->latest()->paginate(15)->withQueryString();
        $unreadCount = $user->unreadNotifications()->count();
        $totalCount = $user->notifications()->count();

        return view('notifikasi.index', compact('notifications', 'unreadCount', 'totalCount', 'filter'));
    }

    /**
     * Mark a single notification as read and redirect to its target URL.
     */
    public function readAndRedirect(string $id): RedirectResponse
    {
        $user = auth()->user();
        $notification = $user->notifications()->findOrFail($id);

        if (is_null($notification->read_at)) {
            $notification->markAsRead();
        }

        $targetUrl = $notification->data['url'] ?? route('notifikasi.index');

        return redirect()->to($targetUrl);
    }

    /**
     * Mark a single notification as read.
     */
    public function markAsRead(string $id, Request $request): RedirectResponse|JsonResponse
    {
        $user = auth()->user();
        $notification = $user->notifications()->findOrFail($id);

        if (is_null($notification->read_at)) {
            $notification->markAsRead();
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Notifikasi ditandai sebagai dibaca.',
                'unread_count' => $user->unreadNotifications()->count(),
            ]);
        }

        return redirect()->back()->with('success', 'Notifikasi berhasil ditandai sebagai dibaca.');
    }

    /**
     * Mark all unread notifications as read.
     */
    public function markAllAsRead(Request $request): RedirectResponse|JsonResponse
    {
        $user = auth()->user();
        $user->unreadNotifications->markAsRead();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Semua notifikasi berhasil ditandai sebagai dibaca.',
                'unread_count' => 0,
            ]);
        }

        return redirect()->back()->with('success', 'Semua notifikasi berhasil ditandai sebagai dibaca.');
    }

    /**
     * Delete a notification.
     */
    public function destroy(string $id, Request $request): RedirectResponse|JsonResponse
    {
        $user = auth()->user();
        $notification = $user->notifications()->findOrFail($id);
        $notification->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Notifikasi berhasil dihapus.',
                'unread_count' => $user->unreadNotifications()->count(),
            ]);
        }

        return redirect()->back()->with('success', 'Notifikasi berhasil dihapus.');
    }

    /**
     * Retrieve recent notifications and unread count as JSON.
     */
    public function unreadData(Request $request): JsonResponse
    {
        $user = auth()->user();
        $unreadCount = $user->unreadNotifications()->count();

        $recentNotifications = $user->notifications()->take(5)->get()->map(function ($notification) {
            return [
                'id' => $notification->id,
                'data' => $notification->data,
                'read_at' => $notification->read_at,
                'created_at_human' => $notification->created_at->diffForHumans(),
                'url' => route('notifikasi.read-and-redirect', $notification->id),
            ];
        });

        return response()->json([
            'unread_count' => $unreadCount,
            'notifications' => $recentNotifications,
        ]);
    }
}
