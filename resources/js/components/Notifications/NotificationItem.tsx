import React from 'react';
import { Link } from '@inertiajs/react';
import { Notification } from '../../hooks/useNotifications';
import { useNotifications } from '../../hooks/useNotifications';

interface NotificationItemProps {
    notification: Notification;
    onClose: () => void;
}

const NotificationItem: React.FC<NotificationItemProps> = ({ notification, onClose }) => {
    const { markAsRead, deleteNotification } = useNotifications();

    const handleClick = () => {
        if (!notification.read_at) {
            markAsRead(notification.id);
        }
        onClose();
    };

    const handleDelete = (e: React.MouseEvent) => {
        e.preventDefault();
        e.stopPropagation();
        deleteNotification(notification.id);
    };

    const formatTimeAgo = (dateString: string) => {
        const date = new Date(dateString);
        const now = new Date();
        const diffInSeconds = Math.floor((now.getTime() - date.getTime()) / 1000);

        if (diffInSeconds < 60) return 'Baru saja';
        if (diffInSeconds < 3600) return `${Math.floor(diffInSeconds / 60)} menit lalu`;
        if (diffInSeconds < 86400) return `${Math.floor(diffInSeconds / 3600)} jam lalu`;
        if (diffInSeconds < 604800) return `${Math.floor(diffInSeconds / 86400)} hari lalu`;
        
        return date.toLocaleDateString('id-ID', {
            day: 'numeric',
            month: 'short',
            year: 'numeric'
        });
    };

    const getColorClasses = (color: string) => {
        switch (color) {
            case 'success':
                return 'border-green-200 bg-green-50';
            case 'danger':
                return 'border-red-200 bg-red-50';
            case 'warning':
                return 'border-yellow-200 bg-yellow-50';
            case 'info':
                return 'border-blue-200 bg-blue-50';
            default:
                return 'border-gray-200 bg-gray-50';
        }
    };

    const NotificationContent = () => (
        <div
            className={`px-4 py-3 border-l-4 hover:bg-gray-50 transition-colors cursor-pointer relative group ${
                !notification.read_at ? 'bg-blue-50 border-l-blue-500' : 'border-l-gray-300'
            } ${getColorClasses(notification.data.color)}`}
        >
            <div className="flex items-start space-x-3">
                {/* Icon */}
                <div className="flex-shrink-0 text-2xl">
                    {notification.data.icon}
                </div>

                {/* Content */}
                <div className="flex-1 min-w-0">
                    <div className="flex items-start justify-between">
                        <div className="flex-1">
                            <p className={`text-sm font-medium ${
                                !notification.read_at ? 'text-gray-900' : 'text-gray-700'
                            }`}>
                                {notification.data.title}
                            </p>
                            <p className="text-sm text-gray-600 mt-1 line-clamp-2">
                                {notification.data.message}
                            </p>
                            
                            {/* Additional Info */}
                            {notification.data.formatted_amount && (
                                <p className="text-sm font-medium text-green-600 mt-1">
                                    {notification.data.formatted_amount}
                                </p>
                            )}
                            
                            {notification.data.reference_number && (
                                <p className="text-xs text-gray-500 mt-1">
                                    Ref: {notification.data.reference_number}
                                </p>
                            )}
                        </div>

                        {/* Unread Indicator */}
                        {!notification.read_at && (
                            <div className="w-2 h-2 bg-blue-500 rounded-full flex-shrink-0 mt-1"></div>
                        )}
                    </div>

                    {/* Timestamp */}
                    <p className="text-xs text-gray-500 mt-2">
                        {formatTimeAgo(notification.created_at)}
                    </p>
                </div>

                {/* Delete Button */}
                <button
                    onClick={handleDelete}
                    className="opacity-0 group-hover:opacity-100 transition-opacity p-1 text-gray-400 hover:text-red-500"
                    title="Hapus notifikasi"
                >
                    <svg className="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fillRule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clipRule="evenodd" />
                    </svg>
                </button>
            </div>
        </div>
    );

    // If notification has action URL, wrap in Link
    if (notification.data.action_url) {
        return (
            <Link href={notification.data.action_url} onClick={handleClick}>
                <NotificationContent />
            </Link>
        );
    }

    // Otherwise, just show as clickable div
    return (
        <div onClick={handleClick}>
            <NotificationContent />
        </div>
    );
};

export default NotificationItem;
