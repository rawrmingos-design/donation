<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Get user notifications
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $perPage = $request->get('per_page', 15);
        
        $notifications = $user->notifications()
            ->paginate($perPage);

        return response()->json([
            'notifications' => $notifications->items(),
            'pagination' => [
                'current_page' => $notifications->currentPage(),
                'last_page' => $notifications->lastPage(),
                'per_page' => $notifications->perPage(),
                'total' => $notifications->total(),
                'has_more' => $notifications->hasMorePages(),
            ],
            'unread_count' => $this->notificationService->getUnreadCount($user),
        ]);
    }

    /**
     * Get unread notifications count
     */
    public function unreadCount()
    {
        $user = Auth::user();
        return response()->json([
            'unread_count' => $this->notificationService->getUnreadCount($user),
        ]);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(Request $request, $id)
    {
        $user = Auth::user();
        $success = $this->notificationService->markAsRead($id, $user);

        if ($success) {
            return response()->json([
                'message' => 'Notification marked as read',
                'unread_count' => $this->notificationService->getUnreadCount($user),
            ]);
        }

        return response()->json(['message' => 'Notification not found'], 404);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        $user = Auth::user();
        $this->notificationService->markAllAsRead($user);

        return response()->json([
            'message' => 'All notifications marked as read',
            'unread_count' => 0,
        ]);
    }

    /**
     * Delete notification
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $notification = $user->notifications()->find($id);

        if ($notification) {
            $notification->delete();
            return response()->json([
                'message' => 'Notification deleted',
                'unread_count' => $this->notificationService->getUnreadCount($user),
            ]);
        }

        return response()->json(['message' => 'Notification not found'], 404);
    }
}
