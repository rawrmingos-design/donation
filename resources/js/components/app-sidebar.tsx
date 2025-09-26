import { NavFooter } from '@/components/nav-footer';
import { NavMain } from '@/components/nav-main';
import { NavUser } from '@/components/nav-user';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import { type NavItem, type User } from '@/types';
import { Link, usePage } from '@inertiajs/react';
import { BookOpen, Folder, LayoutGrid, Heart, Users, Plus, Wallet } from 'lucide-react';
import AppLogo from './app-logo';

const getMainNavItems = (userRole: string): NavItem[] => {
    const baseItems: NavItem[] = [
        {
            title: 'Dashboard',
            href: dashboard(),
            icon: LayoutGrid,
        },
        {
            title: 'Kampanye Donasi',
            href: '/campaigns',
            icon: Heart,
        }
    ];

    // Role-specific navigation items
    if (userRole === 'donor') {
        baseItems.push({
            title: 'Jadi Penggalang Dana',
            href: '/fundraiser/application',
            icon: Users,
        });
    } else if (userRole === 'creator') {
        baseItems.push(
            {
                title: 'Buat Kampanye',
                href: '/campaign/create',
                icon: Plus,
            },
            {
                title: 'Penarikan Dana',
                href: '/withdrawals',
                icon: Wallet,
            }
        );
    }

    return baseItems;
};

const footerNavItems: NavItem[] = [
    {
        title: 'Tentang Kami',
        href: '/about',
        icon: BookOpen,
    },
    {
        title: 'Kontak',
        href: '/contact',
        icon: Folder,
    },
];

export function AppSidebar() {
    const { auth } = usePage<{ auth: { user: User } }>().props;
    const mainNavItems = getMainNavItems(auth.user.role);

    return (
        <Sidebar collapsible="icon" variant="inset">
            <SidebarHeader>
                <SidebarMenu>
                    <SidebarMenuItem>
                        <SidebarMenuButton size="lg" asChild>
                            <Link href={dashboard()} prefetch>
                                <AppLogo />
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarHeader>

            <SidebarContent>
                <NavMain items={mainNavItems} />
            </SidebarContent>

            <SidebarFooter>
                <NavFooter items={footerNavItems} className="mt-auto" />
                <NavUser />
            </SidebarFooter>
        </Sidebar>
    );
}
