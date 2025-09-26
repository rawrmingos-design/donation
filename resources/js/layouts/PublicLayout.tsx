import { Head } from '@inertiajs/react';
import PublicNavigation from '@/components/PublicNavigation';
import PublicFooter from '@/components/PublicFooter';

interface PublicLayoutProps {
    title: string;
    currentPage?: string;
    children: React.ReactNode;
}

export default function PublicLayout({ title, currentPage, children }: PublicLayoutProps) {
    return (
        <div className="min-h-screen bg-gray-900">
            <Head title={title} />
            <PublicNavigation currentPage={currentPage} />
            <main>
                {children}
            </main>
            <PublicFooter />
        </div>
    );
}
