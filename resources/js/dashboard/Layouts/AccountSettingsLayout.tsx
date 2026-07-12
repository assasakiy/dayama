import React from 'react';
import { Link, usePage } from '@inertiajs/react';
import DashboardLayout from './DashboardLayout';
import { User, Shield, Bell, Palette, Settings2, Share2, Download, Trash2, PenTool, LayoutDashboard, ChevronLeft } from 'lucide-react';
import { usePermissions } from '@dashboard/hooks/usePermissions';

interface AccountSettingsLayoutProps {
    children: React.ReactNode;
    title?: string;
    description?: string;
}

export interface MenuItem {
    label: string;
    href: string;
    icon: React.ElementType;
    destructive?: boolean;
    permission?: string;
}

export interface MenuGroup {
    title: string;
    items: MenuItem[];
}

export const menuGroups: MenuGroup[] = [
    {
        title: 'Personal',
        items: [
            { label: 'Profile', href: '/dashboard/account/profile', icon: User },
            { label: 'Account Details', href: '/dashboard/account/details', icon: Settings2 },
        ]
    },
    {
        title: 'Security & Privacy',
        items: [
            { label: 'Security', href: '/dashboard/account/security', icon: Shield },
            { label: 'Connected Accounts', href: '/dashboard/account/connected', icon: Share2 },
            { label: 'Export Data', href: '/dashboard/account/export', icon: Download },
            { label: 'Delete Account', href: '/dashboard/account/delete', icon: Trash2, destructive: true },
        ]
    },
    {
        title: 'Preferences',
        items: [
            { label: 'Appearance', href: '/dashboard/account/appearance', icon: Palette },
            { label: 'Writing Preferences', href: '/dashboard/account/writing', icon: PenTool, permission: 'posts.create' },
            { label: 'Notifications', href: '/dashboard/account/notifications', icon: Bell },
        ]
    }
];

export default function AccountSettingsLayout({ children, title = 'Settings', description }: AccountSettingsLayoutProps) {
    const { url } = usePage();

    const isActive = (href: string) => {
        return url.startsWith(href);
    };

    const isIndex = url === '/dashboard/account' || url === '/dashboard/account/';
    const { can } = usePermissions();

    const filteredMenuGroups = menuGroups.map(group => ({
        ...group,
        items: group.items.filter((item: any) => !item.permission || can(item.permission))
    })).filter(group => group.items.length > 0);

    return (
        <DashboardLayout>
            <div className="flex flex-col md:flex-row md:gap-8 pb-10">
                {/* Inner Sidebar */}
                <aside className={`w-full md:w-64 shrink-0 ${isIndex ? 'block md:hidden' : 'hidden md:block'}`}>
                    <nav className="space-y-8 md:sticky md:top-0 pt-2 md:pt-0">
                        {filteredMenuGroups.map((group, index) => (
                            <div key={index}>
                                <h3 className="text-xs font-semibold text-muted-foreground uppercase tracking-wider mb-3 px-2">
                                    {group.title}
                                </h3>
                                <div className="space-y-1">
                                    {group.items.map((item) => {
                                        const active = isActive(item.href);
                                        return (
                                            <Link
                                                key={item.href}
                                                href={item.href}
                                                className={`flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-all ${
                                                    active 
                                                        ? 'bg-primary/10 text-primary' 
                                                        : item.destructive 
                                                            ? 'text-destructive hover:bg-destructive/10' 
                                                            : 'text-muted-foreground hover:bg-surface-muted hover:text-foreground'
                                                }`}
                                            >
                                                <item.icon className="w-4 h-4" />
                                                {item.label}
                                            </Link>
                                        );
                                    })}
                                </div>
                            </div>
                        ))}
                    </nav>
                </aside>

                {/* Content Area */}
                <div className={`flex-1 w-full ${isIndex ? 'hidden md:block' : 'block'}`}>
                    
                    {/* Mobile Breadcrumb */}
                    <div className="md:hidden mb-5">
                        <div className="bg-background border border-border-subtle rounded-xl shadow-sm p-3 flex items-center gap-2 text-sm text-muted-foreground">
                            <Link href="/dashboard/account" className="flex items-center gap-1 hover:text-primary transition-colors">
                                <ChevronLeft className="w-4 h-4 -ml-1" />
                                <span className="font-medium">Settings</span>
                            </Link>
                            <span className="text-border">/</span>
                            <span className="font-semibold text-foreground">{title}</span>
                        </div>
                    </div>

                    <div className="hidden md:block mb-6">
                        <h1 className="text-2xl font-bold tracking-tight">{title}</h1>
                        {description && (
                            <p className="text-muted-foreground mt-1 text-sm">
                                {description}
                            </p>
                        )}
                    </div>

                    {children}
                </div>
            </div>
        </DashboardLayout>
    );
}
