import React from 'react';
import { useNotifications } from '../../hooks/useNotifications';
import NotificationItem from './NotificationItem';
import { Link } from '@inertiajs/react';

interface NotificationDropdownProps {
    onClose: () => void;
}

const NotificationDropdown: React.FC<NotificationDropdownProps> = ({ onClose }) => {
    const { 
        notifications, 
        loading, 
        unreadCount, 
        markAllAsRead, 
        loadMore, 
        pagination 
    } = useNotifications();

    const handleMarkAllAsRead = async () => {
        await markAllAsRead();
    };

    return (
        <div className="absolute right-0 mt-2 w-80 sm:w-96 bg-white dark:bg-gray-800 rounded-lg shadow-xl border border-gray-200 dark:border-gray-700 z-50 max-h-80 sm:max-h-96 overflow-hidden">
            {/* Header */}
            <div className="px-3 sm:px-4 py-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 rounded-t-lg">
                <div className="flex items-center justify-between">
                    <h3 className="text-base sm:text-lg font-semibold text-gray-900 dark:text-white">
                        🔔 Notifikasi
                    </h3>
                    {unreadCount > 0 && (
                        <button
                            onClick={handleMarkAllAsRead}
                            className="text-xs sm:text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 font-medium px-2 py-1 rounded hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors"
                        >
                            <span className="hidden sm:inline">Tandai Semua Dibaca</span>
                            <span className="sm:hidden">Tandai Semua</span>
                        </button>
                    )}
                </div>
                {unreadCount > 0 && (
                    <p className="text-xs sm:text-sm text-gray-600 dark:text-gray-400 mt-1">
                        {unreadCount} notifikasi belum dibaca
                    </p>
                )}
            </div>

            {/* Notifications List */}
            <div className="max-h-60 sm:max-h-80 overflow-y-auto">
                {loading && notifications.length === 0 ? (
                    <div className="px-3 sm:px-4 py-6 sm:py-8 text-center">
                        <div className="animate-spin rounded-full h-6 sm:h-8 w-6 sm:w-8 border-b-2 border-blue-600 mx-auto"></div>
                        <p className="text-gray-500 dark:text-gray-400 mt-2 text-sm">Memuat notifikasi...</p>
                    </div>
                ) : notifications.length === 0 ? (
                    <div className="px-3 sm:px-4 py-6 sm:py-8 text-center">
                        <div className="text-3xl sm:text-4xl mb-2">📭</div>
                        <p className="text-gray-500 dark:text-gray-400 text-sm">Belum ada notifikasi</p>
                    </div>
                ) : (
                    <>
                        {notifications.map((notification) => (
                            <NotificationItem
                                key={notification.id}
                                notification={notification}
                                onClose={onClose}
                            />
                        ))}
                        
                        {/* Load More Button */}
                        {pagination && pagination.has_more && (
                            <div className="px-3 sm:px-4 py-3 border-t border-gray-200 dark:border-gray-700">
                                <button
                                    onClick={loadMore}
                                    disabled={loading}
                                    className="w-full text-center text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 font-medium py-2 disabled:opacity-50 text-sm hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded transition-colors"
                                >
                                    {loading ? 'Memuat...' : 'Muat Lebih Banyak'}
                                </button>
                            </div>
                        )}
                    </>
                )}
            </div>

            {/* Footer */}
            <div className="px-3 sm:px-4 py-3 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 rounded-b-lg">
                <Link
                    href="/notifications"
                    onClick={onClose}
                    className="block text-center text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 font-medium text-sm hover:bg-blue-50 dark:hover:bg-blue-900/20 py-2 rounded transition-colors"
                >
                    Lihat Semua Notifikasi
                </Link>
            </div>
        </div>
    );
};

export default NotificationDropdown;
