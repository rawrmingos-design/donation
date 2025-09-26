import React, { useState } from 'react';
import { Head, Link } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { useNotifications, Notification } from '../../hooks/useNotifications';

const NotificationsIndex: React.FC = () => {
    const { 
        notifications, 
        loading, 
        unreadCount, 
        markAsRead, 
        markAllAsRead, 
        deleteNotification, 
        loadMore, 
        pagination 
    } = useNotifications();

    const [filter, setFilter] = useState<'all' | 'unread'>('all');

    const filteredNotifications = filter === 'unread' 
        ? notifications.filter(n => !n.read_at)
        : notifications;

    const handleMarkAsRead = async (notificationId: number) => {
        await markAsRead(notificationId);
    };

    const handleMarkAllAsRead = async () => {
        await markAllAsRead();
    };

    const handleDelete = async (notificationId: number) => {
        await deleteNotification(notificationId);
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
            month: 'long',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    };

    const getColorClasses = (color: string) => {
        switch (color) {
            case 'success':
                return 'border-l-green-500 bg-green-50';
            case 'danger':
                return 'border-l-red-500 bg-red-50';
            case 'warning':
                return 'border-l-yellow-500 bg-yellow-50';
            case 'info':
                return 'border-l-blue-500 bg-blue-50';
            default:
                return 'border-l-gray-500 bg-gray-50';
        }
    };

    const NotificationCard: React.FC<{ notification: Notification }> = ({ notification }) => {
        const CardContent = () => (
            <div
                className={`p-6 border-l-4 rounded-lg shadow-sm hover:shadow-md transition-shadow ${
                    !notification.read_at ? 'bg-blue-50 border-l-blue-500' : 'bg-white border-l-gray-300'
                } ${getColorClasses(notification.data.color)}`}
            >
                <div className="flex items-start justify-between">
                    <div className="flex items-start space-x-4 flex-1">
                        {/* Icon */}
                        <div className="text-3xl flex-shrink-0">
                            {notification.data.icon}
                        </div>

                        {/* Content */}
                        <div className="flex-1">
                            <div className="flex items-start justify-between">
                                <h3 className={`text-lg font-semibold ${
                                    !notification.read_at ? 'text-gray-900' : 'text-gray-700'
                                }`}>
                                    {notification.data.title}
                                </h3>
                                
                                {!notification.read_at && (
                                    <div className="w-3 h-3 bg-blue-500 rounded-full flex-shrink-0 ml-2 mt-1"></div>
                                )}
                            </div>

                            <p className="text-gray-600 mt-2 leading-relaxed">
                                {notification.data.message}
                            </p>

                            {/* Additional Details */}
                            <div className="mt-4 space-y-2">
                                {notification.data.campaign_title && (
                                    <div className="text-sm">
                                        <span className="font-medium text-gray-700">Kampanye: </span>
                                        <span className="text-gray-600">{notification.data.campaign_title}</span>
                                    </div>
                                )}

                                {notification.data.formatted_amount && (
                                    <div className="text-sm">
                                        <span className="font-medium text-gray-700">Jumlah: </span>
                                        <span className="text-green-600 font-semibold">{notification.data.formatted_amount}</span>
                                    </div>
                                )}

                                {notification.data.reference_number && (
                                    <div className="text-sm">
                                        <span className="font-medium text-gray-700">Nomor Referensi: </span>
                                        <span className="text-gray-600 font-mono">{notification.data.reference_number}</span>
                                    </div>
                                )}

                                {notification.data.notes && (
                                    <div className="text-sm bg-yellow-50 border border-yellow-200 rounded p-3 mt-3">
                                        <span className="font-medium text-yellow-800">Catatan: </span>
                                        <span className="text-yellow-700">{notification.data.notes}</span>
                                    </div>
                                )}
                            </div>

                            <div className="flex items-center justify-between mt-4 pt-4 border-t border-gray-200">
                                <span className="text-sm text-gray-500">
                                    {formatTimeAgo(notification.created_at)}
                                </span>

                                <div className="flex items-center space-x-2">
                                    {!notification.read_at && (
                                        <button
                                            onClick={(e) => {
                                                e.preventDefault();
                                                e.stopPropagation();
                                                handleMarkAsRead(notification.id);
                                            }}
                                            className="text-sm text-blue-600 hover:text-blue-800 font-medium"
                                        >
                                            Tandai Dibaca
                                        </button>
                                    )}
                                    
                                    <button
                                        onClick={(e) => {
                                            e.preventDefault();
                                            e.stopPropagation();
                                            handleDelete(notification.id);
                                        }}
                                        className="text-sm text-red-600 hover:text-red-800 font-medium"
                                    >
                                        Hapus
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        );

        if (notification.data.action_url) {
            return (
                <Link href={notification.data.action_url}>
                    <CardContent />
                </Link>
            );
        }

        return <CardContent />;
    };

    return (
        <AppLayout>
            <Head title="Notifikasi" />

            <div className="min-h-screen bg-[oklch(0.205 0 0)]">
                <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                    {/* Header */}
                    <div className="mb-8">
                        <div className="flex items-center justify-between">
                            <div>
                                <h1 className="text-3xl font-bold text-white">🔔 Notifikasi</h1>
                                <p className="text-gray-400 mt-2">
                                    Kelola semua notifikasi Anda di sini
                                </p>
                            </div>

                            {unreadCount > 0 && (
                                <button
                                    onClick={handleMarkAllAsRead}
                                    className="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium"
                                >
                                    Tandai Semua Dibaca ({unreadCount})
                                </button>
                            )}
                        </div>

                        {/* Filter Tabs */}
                        <div className="mt-6 border-b border-gray-200">
                            <nav className="flex space-x-8">
                                <button
                                    onClick={() => setFilter('all')}
                                    className={`py-2 px-1 border-b-2 font-medium text-sm ${
                                        filter === 'all'
                                            ? 'border-blue-500 text-blue-600'
                                            : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                                    }`}
                                >
                                    Semua ({notifications.length})
                                </button>
                                <button
                                    onClick={() => setFilter('unread')}
                                    className={`py-2 px-1 border-b-2 font-medium text-sm ${
                                        filter === 'unread'
                                            ? 'border-blue-500 text-blue-600'
                                            : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                                    }`}
                                >
                                    Belum Dibaca ({unreadCount})
                                </button>
                            </nav>
                        </div>
                    </div>

                    {/* Notifications List */}
                    <div className="space-y-4 flex flex-col gap-4">
                        {loading && notifications.length === 0 ? (
                            <div className="text-center py-12">
                                <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div>
                                <p className="text-gray-100 mt-4">Memuat notifikasi...</p>
                            </div>
                        ) : filteredNotifications.length === 0 ? (
                            <div className="text-center py-12">
                                <div className="text-6xl mb-4">📭</div>
                                <h3 className="text-lg font-medium text-gray-400 mb-2">
                                    {filter === 'unread' ? 'Tidak ada notifikasi yang belum dibaca' : 'Belum ada notifikasi'}
                                </h3>
                                <p className="text-gray-500">
                                    {filter === 'unread' 
                                        ? 'Semua notifikasi sudah dibaca' 
                                        : 'Notifikasi akan muncul di sini ketika ada aktivitas baru'
                                    }
                                </p>
                            </div>
                        ) : (
                            <>
                                {filteredNotifications.map((notification) => (
                                    <NotificationCard key={notification.id} notification={notification} />
                                ))}

                                {/* Load More Button */}
                                {pagination && pagination.has_more && filter === 'all' && (
                                    <div className="text-center py-6">
                                        <button
                                            onClick={loadMore}
                                            disabled={loading}
                                            className="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium disabled:opacity-50 disabled:cursor-not-allowed"
                                        >
                                            {loading ? 'Memuat...' : 'Muat Lebih Banyak'}
                                        </button>
                                    </div>
                                )}
                            </>
                        )}
                    </div>
                </div>
            </div>
        </AppLayout>
    );
};

export default NotificationsIndex;
