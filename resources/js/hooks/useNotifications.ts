import { useState, useEffect, useCallback } from 'react';
import axios from 'axios';

export interface Notification {
    id: number;
    type: string;
    data: {
        title: string;
        message: string;
        icon: string;
        color: string;
        action_url?: string;
        withdrawal_id?: number;
        campaign_title?: string;
        formatted_amount?: string;
        reference_number?: string;
        notes?: string;
    };
    read_at: string | null;
    created_at: string;
}

export interface NotificationPagination {
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    has_more: boolean;
}

export interface NotificationResponse {
    notifications: Notification[];
    pagination: NotificationPagination;
    unread_count: number;
}

export const useNotifications = () => {
    const [notifications, setNotifications] = useState<Notification[]>([]);
    const [unreadCount, setUnreadCount] = useState<number>(0);
    const [loading, setLoading] = useState<boolean>(false);
    const [pagination, setPagination] = useState<NotificationPagination | null>(null);

    // Fetch notifications
    const fetchNotifications = useCallback(async (page: number = 1) => {
        try {
            setLoading(true);
            const response = await axios.get<NotificationResponse>(`/api/notifications?page=${page}`);
            
            if (page === 1) {
                setNotifications(response.data.notifications);
            } else {
                setNotifications(prev => [...prev, ...response.data.notifications]);
            }
            
            setPagination(response.data.pagination);
            setUnreadCount(response.data.unread_count);
        } catch (error) {
            console.error('Error fetching notifications:', error);
        } finally {
            setLoading(false);
        }
    }, []);

    // Fetch unread count only
    const fetchUnreadCount = useCallback(async () => {
        try {
            const response = await axios.get<{ unread_count: number }>('/api/notifications/unread-count');
            setUnreadCount(response.data.unread_count);
        } catch (error) {
            console.error('Error fetching unread count:', error);
        }
    }, []);

    // Mark notification as read
    const markAsRead = useCallback(async (notificationId: number) => {
        try {
            await axios.post(`/api/notifications/${notificationId}/mark-as-read`);
            
            // Update local state
            setNotifications(prev => 
                prev.map(notification => 
                    notification.id === notificationId 
                        ? { ...notification, read_at: new Date().toISOString() }
                        : notification
                )
            );
            
            // Update unread count
            setUnreadCount(prev => Math.max(0, prev - 1));
        } catch (error) {
            console.error('Error marking notification as read:', error);
        }
    }, []);

    // Mark all notifications as read
    const markAllAsRead = useCallback(async () => {
        try {
            await axios.post('/api/notifications/mark-all-as-read');
            
            // Update local state
            setNotifications(prev => 
                prev.map(notification => ({ 
                    ...notification, 
                    read_at: new Date().toISOString() 
                }))
            );
            
            setUnreadCount(0);
        } catch (error) {
            console.error('Error marking all notifications as read:', error);
        }
    }, []);

    // Delete notification
    const deleteNotification = useCallback(async (notificationId: number) => {
        try {
            await axios.delete(`/api/notifications/${notificationId}`);
            
            // Update local state
            const notification = notifications.find(n => n.id === notificationId);
            setNotifications(prev => prev.filter(n => n.id !== notificationId));
            
            // Update unread count if notification was unread
            if (notification && !notification.read_at) {
                setUnreadCount(prev => Math.max(0, prev - 1));
            }
        } catch (error) {
            console.error('Error deleting notification:', error);
        }
    }, [notifications]);

    // Load more notifications
    const loadMore = useCallback(() => {
        if (pagination && pagination.has_more && !loading) {
            fetchNotifications(pagination.current_page + 1);
        }
    }, [pagination, loading, fetchNotifications]);

    // Initial load
    useEffect(() => {
        fetchNotifications();
    }, [fetchNotifications]);

    // Poll for new notifications every 30 seconds
    useEffect(() => {
        const interval = setInterval(() => {
            fetchUnreadCount();
        }, 30000);

        return () => clearInterval(interval);
    }, [fetchUnreadCount]);

    return {
        notifications,
        unreadCount,
        loading,
        pagination,
        fetchNotifications,
        fetchUnreadCount,
        markAsRead,
        markAllAsRead,
        deleteNotification,
        loadMore,
    };
};
