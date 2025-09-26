import { Breadcrumbs } from '@/components/breadcrumbs';
import { SidebarTrigger } from '@/components/ui/sidebar';
import { type BreadcrumbItem as BreadcrumbItemType } from '@/types';
import NotificationBell from '@/components/Notifications/NotificationBell';
import { Link, usePage } from '@inertiajs/react';

export function AppSidebarHeader({ breadcrumbs = [] }: { breadcrumbs?: BreadcrumbItemType[] }) {
    const { auth } = usePage().props as any;
    
    return (
        <header className="flex h-14 sm:h-16 shrink-0 items-center justify-between gap-2 border-b border-sidebar-border/50 px-4 sm:px-6 transition-[width,height] ease-linear group-has-data-[collapsible=icon]/sidebar-wrapper:h-12 bg-white/50 backdrop-blur-sm dark:bg-gray-900/50">
            <div className="flex items-center gap-2 min-w-0 flex-1">
                <SidebarTrigger className="-ml-1 p-2 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition-colors" />
                <div className="hidden sm:block min-w-0 flex-1">
                    <Breadcrumbs breadcrumbs={breadcrumbs} />
                </div>
                {/* Mobile: Show current page title instead of full breadcrumbs */}
                <div className="sm:hidden min-w-0 flex-1">
                    {breadcrumbs.length > 0 && (
                        <h1 className="text-lg font-semibold text-gray-900 dark:text-white truncate">
                            {breadcrumbs[breadcrumbs.length - 1].title}
                        </h1>
                    )}
                </div>
            </div>
            
            <div className="flex items-center gap-2 sm:gap-3">
                {/* Quick Actions for Mobile */}
                <div className="sm:hidden flex items-center gap-1">
                    <Link
                        href="/campaigns"
                        className="p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition-colors"
                        title="Jelajahi Kampanye"
                    >
                        <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                    </Link>
                </div>
                
                {/* User Avatar for Mobile */}
                <div className="sm:hidden flex items-center">
                    <div className="w-8 h-8 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                        <span className="text-white text-sm font-medium">
                            {auth?.user?.name?.charAt(0)?.toUpperCase() || 'U'}
                        </span>
                    </div>
                </div>
                
                {/* Desktop Notification */}
                <div className="hidden sm:block">
                    <NotificationBell />
                </div>
                
                {/* Mobile Notification - Simplified */}
                <div className="sm:hidden">
                    <NotificationBell />
                </div>
            </div>
        </header>
    );
}
