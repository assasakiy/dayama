import React, { useState, useEffect } from 'react';
import { Link, usePage, router } from '@inertiajs/react';
import {
    LayoutDashboard,
    FileText,
    FolderTree,
    Tags,
    MessageSquare,
    CheckCircle2,
    Clock,
    Trash2,
    Image as ImageIcon,
    Users,
    Settings,
    Settings2,
    Menu,
    LogOut,
    Search,
    Bell,
    Plus,
    PanelLeftClose,
    PanelLeftOpen,
    User,
    Moon,
    Sun,
    Newspaper,
    Globe,
    Shield,
    ShieldCheck,
    KeyRound,
    Layers,
    ChevronDown,
    ChevronRight,
    Activity,
    Bookmark,
    BookOpen,
    Mail,
    LayoutTemplate,
    Palette,
} from 'lucide-react';
import { Toaster } from '../Components/ui/toaster';
import { GlobalToast } from '../Components/GlobalToast';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '../Components/ui/dropdown-menu';
import {
    Tooltip,
    TooltipContent,
    TooltipProvider,
    TooltipTrigger,
} from '../Components/ui/tooltip';
import { Badge } from '@dashboard/Components/ui/badge';
import { BottomSheet } from '@dashboard/Components/ui/bottom-sheet';
import { usePermissions } from '../hooks/usePermissions';

interface MenuItem {
    label: string;
    href?: string;
    icon?: React.ComponentType<{ className?: string }>;
    permission?: string;
    items?: MenuItem[];
}

interface MenuGroup {
    label: string;
    icon?: React.ComponentType<{ className?: string }>;
    items: MenuItem[];
    key: string;
}

const menuGroups: MenuGroup[] = [
    {
        label: 'Dashboard',
        key: 'dashboard',
        items: [
            { label: 'Dashboard', href: '/', icon: LayoutDashboard },
        ]
    },
    {
        label: 'Media',
        key: 'media',
        items: [
            { label: 'Media', href: '/media', icon: ImageIcon, permission: 'media.view' },
        ]
    },
    {
        label: 'Post Management',
        key: 'post',
        items: [
            { label: 'All Posts', href: '/posts', icon: Newspaper, permission: 'posts.view' },
            { label: 'Drafts', href: '/posts?status=draft', icon: Clock, permission: 'posts.view' },
            { label: 'Published', href: '/posts?status=published', icon: CheckCircle2, permission: 'posts.view' },
            { label: 'Trash', href: '/posts?status=trash', icon: Trash2, permission: 'posts.view' },
            { label: 'Categories', href: '/categories', icon: FolderTree, permission: 'categories.view' },
            { label: 'Tags', href: '/tags', icon: Tags, permission: 'tags.view' },
            { label: 'Comments', href: '/comments', icon: MessageSquare, permission: 'comments.view' },
        ],
    },
    {
        label: 'My Content',
        key: 'my-content',
        items: [
            { label: 'My Bookmarks',    href: '/bookmarks', icon: Bookmark,  permission: 'bookmarks.view.own' },
            { label: 'Reading History', href: '/history',   icon: BookOpen,  permission: 'reading_history.view.own' },
        ],
    },
    {
        label: 'Activity',
        key: 'activity',
        items: [
            { label: 'Activity Logs', href: '/activity-logs', icon: Activity, permission: 'activity_logs.view' },
        ],
    },
    {
        label: 'Management Settings',
        key: 'system',
        items: [
            { label: 'Global Settings', href: '/settings/global', icon: Globe, permission: 'settings.view' },
            { label: 'Blog Settings', href: '/settings/blog', icon: Newspaper, permission: 'settings.view' },
            { label: 'Landing Settings', href: '/settings/landing', icon: LayoutTemplate, permission: 'settings.view' },
        ],
    },
    {
        label: 'Mail Management',
        key: 'mail-management',
        items: [
            { label: 'Mail (SMTP)', href: '/settings/global/mail', icon: Mail, permission: 'settings.view' },
            { label: 'Email Templates', href: '/email-templates', icon: LayoutTemplate, permission: 'settings.view' },
        ],
    },
    {
        label: 'Access Management',
        key: 'access',
        items: [
            { label: 'Users', href: '/users', icon: Users, permission: 'users.view' },
            { label: 'Roles', href: '/roles', icon: ShieldCheck, permission: 'roles.view' },
            { label: 'Permissions', href: '/permissions', icon: KeyRound, permission: 'roles.view' },
            { label: 'Permission Groups', href: '/permission-groups', icon: Layers, permission: 'roles.view' },
        ],
    }
];

export default function DashboardLayout({ children }: { children: React.ReactNode }) {
    const { url, props } = usePage<any>();
    const user = (props as { auth?: { user?: { name: string; email: string; avatar_url?: string; unread_notifications?: any[]; unread_notifications_count?: number } } }).auth?.user;
    const { can } = usePermissions();
    const settings = props.settings?.general || {};
    const siteName = settings.site_name || 'ModernBlog';
    const logoUrl = settings.logo_url || null;

    const [sidebarCollapsed, setSidebarCollapsed] = useState(false);
    const [mobileDrawerOpen, setMobileDrawerOpen] = useState(false);
    const [isUserMenuOpen, setIsUserMenuOpen] = useState(false);
    const [isDarkMode, setIsDarkMode] = useState(false);
    const [expandedGroups, setExpandedGroups] = useState<Record<string, boolean>>({ content: true });

    const toggleGroup = (key: string) => {
        setExpandedGroups(prev => ({ ...prev, [key]: !prev[key] }));
    };

    // Filter items based on permissions (recursive)
    const filterItems = (items: MenuItem[]): MenuItem[] => {
        return items.map(item => {
            if (item.items) {
                return { ...item, items: filterItems(item.items) };
            }
            return item;
        }).filter(item => {
            if (item.items) return item.items.length > 0;
            return !item.permission || can(item.permission);
        });
    };

    const filteredGroups = menuGroups.map(group => ({
        ...group,
        items: filterItems(group.items)
    })).filter(group => group.items.length > 0);

    // Load state from local storage on mount
    useEffect(() => {
        const stored = localStorage.getItem('dashboard:sidebar-collapsed');
        if (stored !== null) {
            setSidebarCollapsed(stored === 'true');
        }

        // Auto-expand groups that contain the active url
        const initialExpanded: Record<string, boolean> = { post: true };
        
        // Helper to check if a menu item or its children match the URL
        const matchesUrl = (items: MenuItem[]): boolean => {
            return items.some(item => {
                if (item.href && url.startsWith(item.href)) return true;
                if (item.items) return matchesUrl(item.items);
                return false;
            });
        };

        try {
            filteredGroups.forEach(group => {
                if (!['dashboard', 'media'].includes(group.key) && matchesUrl(group.items)) {
                    initialExpanded[group.key] = true;
                }
                
                // Also expand sub-menus
                const expandSubMenus = (items: MenuItem[]) => {
                    items.forEach(item => {
                        if (item.items && matchesUrl(item.items)) {
                            initialExpanded[item.label] = true;
                            expandSubMenus(item.items);
                        }
                    });
                };
                expandSubMenus(group.items);
            });
            setExpandedGroups(prev => ({ ...prev, ...initialExpanded }));
        } catch (e) {
            console.error(e);
        }

        const isDark = document.documentElement.classList.contains('dark');
        setIsDarkMode(isDark);
    }, [url]); // Added url as dependency


    const toggleSidebar = () => {
        const newState = !sidebarCollapsed;
        setSidebarCollapsed(newState);
        localStorage.setItem('dashboard:sidebar-collapsed', String(newState));
    };

    const toggleDarkMode = () => {
        const html = document.documentElement;
        if (html.classList.contains('dark')) {
            html.classList.remove('dark');
            setIsDarkMode(false);
        } else {
            html.classList.add('dark');
            setIsDarkMode(true);
        }
    };

    const isActive = (href: string) => {
        if (href === '/') return url === '/';
        return url.startsWith(href);
    };

    const handleLogout = (e: React.FormEvent) => {
        e.preventDefault();
        router.post('/logout');
    };

    const renderMenuItem = (item: MenuItem, level: number = 0) => {
        if (item.items) {
            const isSubExpanded = expandedGroups[item.label] || false;
            const isActive = item.items.some(sub => sub.href && url.startsWith(sub.href));
            
            return (
                <div key={item.label} className="pt-1">
                    <button
                        type="button"
                        onClick={() => toggleGroup(item.label)}
                        className={`flex items-center justify-between w-full py-2 rounded-lg text-sm transition-all group
                            ${isActive ? 'bg-primary/5 text-primary font-medium' : 'text-muted-foreground hover:bg-surface-muted hover:text-foreground'}
                            ${sidebarCollapsed ? 'lg:justify-center px-3' : ''}
                        `}
                        style={{ paddingLeft: sidebarCollapsed ? '' : `${12 + level * 16}px`, paddingRight: sidebarCollapsed ? '' : '12px' }}
                    >
                        <div className="flex items-center gap-3">
                            {item.icon && <item.icon className={`shrink-0 ${level === 0 ? 'w-5 h-5' : 'w-4 h-4'} ${isActive ? 'text-primary' : ''}`} />}
                            <span className={`whitespace-nowrap transition-all duration-200 ${sidebarCollapsed ? 'lg:opacity-0 lg:w-0 lg:hidden' : 'opacity-100'}`}>
                                {item.label}
                            </span>
                        </div>
                        {!sidebarCollapsed && (
                            isSubExpanded ? (
                                <ChevronDown className="w-3.5 h-3.5 opacity-50" />
                            ) : (
                                <ChevronRight className="w-3.5 h-3.5 opacity-50" />
                            )
                        )}
                    </button>
                    {(isSubExpanded || sidebarCollapsed) && (
                        <div className="space-y-0.5 mt-0.5">
                            {item.items.map(subItem => renderMenuItem(subItem, level + 1))}
                        </div>
                    )}
                </div>
            );
        }

        const active = item.href?.includes('?') 
            ? url === item.href || (item.href && url.startsWith(item.href + '&'))
            : url === item.href || (item.href && url.startsWith(item.href + '/') && !url.includes('?status='));
        
        return (
            <Link
                key={item.href || item.label}
                href={item.href || '#'}
                onClick={() => setMobileDrawerOpen(false)}
                className={`flex items-center gap-3 py-2.5 rounded-lg text-sm transition-all relative group
                    ${active ? 'bg-primary/10 text-primary font-medium' : 'text-muted-foreground hover:bg-surface-muted hover:text-foreground'}
                    ${sidebarCollapsed ? 'lg:justify-center px-3' : 'justify-start'}
                `}
                style={{ paddingLeft: sidebarCollapsed ? '' : `${12 + level * 16}px`, paddingRight: sidebarCollapsed ? '' : '12px' }}
            >
                {item.icon && <item.icon className={`shrink-0 transition-transform group-hover:scale-110 ${active ? 'text-primary' : ''} ${level === 0 ? 'w-5 h-5' : 'w-4 h-4'}`} />}
                <span className={`whitespace-nowrap transition-all duration-200 ${sidebarCollapsed ? 'lg:opacity-0 lg:w-0 lg:hidden' : 'opacity-100'}`}>
                    {item.label}
                </span>
            </Link>
        );
    };

    return (
        <TooltipProvider delayDuration={200}>
            <div className="min-h-screen bg-surface font-sans text-foreground">
                <GlobalToast />
                <Toaster />
                
                {/* Mobile Overlay */}
                {mobileDrawerOpen && (
                    <div 
                        className="fixed inset-0 z-40 bg-black/50 backdrop-blur-sm lg:hidden transition-opacity"
                        onClick={() => setMobileDrawerOpen(false)} 
                    />
                )}

                {/* Sidebar */}
                <aside
                    className={`fixed inset-y-0 left-0 z-50 flex flex-col bg-background border-r border-border-subtle shadow-sm transform transition-all duration-300 ease-in-out w-64
                        ${mobileDrawerOpen ? 'translate-x-0' : '-translate-x-full'}
                        ${sidebarCollapsed ? 'lg:translate-x-0 lg:w-20' : 'lg:translate-x-0 lg:w-64'}
                    `}
                >
                    {/* Logo Area */}
                    <div className={`flex items-center h-16 border-b border-border-subtle shrink-0 ${sidebarCollapsed ? 'lg:justify-center px-4' : 'px-4'}`}>
                        <Link href="/" className="flex items-center gap-3 overflow-hidden whitespace-nowrap">
                            {logoUrl ? (
                                <img src={logoUrl} alt={siteName} className="shrink-0 w-8 h-8 object-contain" />
                            ) : (
                                <span className="shrink-0 w-8 h-8 rounded-lg bg-gradient-to-br from-primary to-primary/80 flex items-center justify-center text-primary-foreground text-sm font-bold shadow-sm">
                                    {siteName.charAt(0)}
                                </span>
                            )}
                            <span className={`font-bold text-lg transition-opacity duration-200 ${sidebarCollapsed ? 'lg:opacity-0 lg:w-0' : 'opacity-100'}`}>
                                {siteName}
                            </span>
                        </Link>
                    </div>

                    {/* Navigation */}
                    <nav className="flex-1 overflow-y-auto p-3 space-y-2 scrollbar-hide">
                        {filteredGroups.map(group => {
                            if (['dashboard', 'media'].includes(group.key)) {
                                const item = group.items[0];
                                const active = item.href ? (url.startsWith(item.href) && (item.href !== '/' || url === '/')) : false;
                                return (
                                    <Link
                                        key={item.href}
                                        href={item.href}
                                        onClick={() => setMobileDrawerOpen(false)}
                                        className={`flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-all relative group
                                            ${active ? 'bg-primary/10 text-primary font-medium' : 'text-muted-foreground hover:bg-surface-muted hover:text-foreground'}
                                            ${sidebarCollapsed ? 'lg:justify-center' : 'justify-start'}
                                        `}
                                    >
                                        {item.icon && <item.icon className={`shrink-0 transition-transform group-hover:scale-110 ${active ? 'text-primary w-5 h-5' : 'w-5 h-5'}`} />}
                                        <span className={`whitespace-nowrap transition-all duration-200 ${sidebarCollapsed ? 'lg:opacity-0 lg:w-0 lg:hidden' : 'opacity-100'}`}>
                                            {item.label}
                                        </span>
                                    </Link>
                                );
                            }

                            const isGroupActive = group.items.some(item => item.href && url.startsWith(item.href));
                            const isExpanded = expandedGroups[group.key] || false;
                            
                            return (
                                <div key={group.key} className="pt-2">
                                    <button
                                        type="button"
                                        onClick={() => toggleGroup(group.key)}
                                        className={`flex items-center justify-between w-full px-3 pb-2 pt-1 group ${sidebarCollapsed ? 'lg:hidden' : ''}`}
                                    >
                                        <span className="text-[11px] font-bold uppercase tracking-wider text-muted-foreground/60 group-hover:text-muted-foreground transition-colors text-left">
                                            {group.label}
                                        </span>
                                        {isExpanded ? (
                                            <ChevronDown className="w-3.5 h-3.5 text-muted-foreground/40 group-hover:text-muted-foreground transition-colors" />
                                        ) : (
                                            <ChevronRight className="w-3.5 h-3.5 text-muted-foreground/40 group-hover:text-muted-foreground transition-colors" />
                                        )}
                                    </button>

                                    {/* Sub-items */}
                                    {(isExpanded || sidebarCollapsed) && (
                                        <div className="space-y-0.5">
                                            {group.items.map(item => renderMenuItem(item, 0))}
                                        </div>
                                    )}
                                </div>
                            );
                        })}
                    </nav>

                    {/* Sidebar Footer */}
                    <div className="shrink-0 p-4 border-t border-border-subtle">
                        <button
                            onClick={toggleSidebar}
                            className={`hidden lg:flex items-center gap-3 w-full p-2 rounded-lg text-muted-foreground hover:bg-surface-muted hover:text-foreground transition-all ${sidebarCollapsed ? 'justify-center' : 'justify-start'}`}
                        >
                            {sidebarCollapsed ? <PanelLeftOpen className="w-5 h-5" /> : <PanelLeftClose className="w-5 h-5" />}
                            {!sidebarCollapsed && <span className="text-sm font-medium">Collapse</span>}
                        </button>
                    </div>
                </aside>

                {/* Main Content Area */}
                <div className={`flex flex-col h-screen overflow-hidden transition-all duration-300 ease-in-out
                    ${sidebarCollapsed ? 'lg:pl-20' : 'lg:pl-64'}
                `}>    
                    {/* Header */}
                    <header className="shrink-0 z-30 h-16 bg-background/80 border-b border-border-subtle backdrop-blur-xl flex items-center justify-between px-4 lg:px-8 shadow-sm">
                        
                        {/* Left Section: Mobile Menu Toggle & Search */}
                        <div className="flex items-center gap-4 flex-1">
                            <button 
                                className="lg:hidden p-2 rounded-lg text-muted-foreground hover:bg-surface-muted transition-colors" 
                                onClick={() => setMobileDrawerOpen(true)}
                            >
                                <Menu className="w-6 h-6" />
                            </button>
                            
                            <div className="hidden sm:flex relative group w-full max-w-md">
                                <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground group-focus-within:text-primary transition-colors" />
                                <input 
                                    type="text" 
                                    placeholder="Search..." 
                                    className="w-full h-10 pl-10 pr-4 rounded-full bg-surface-muted border-transparent focus:bg-background focus:border-primary focus:ring-2 focus:ring-primary/20 text-sm transition-all outline-none"
                                />
                            </div>
                        </div>

                        {/* Right Section: Actions */}
                        <div className="flex items-center gap-3 sm:gap-4 shrink-0">
                            
                            {/* New Post Button */}
                            <Link 
                                href="/posts/create"
                                className="hidden sm:flex items-center gap-2 bg-primary hover:bg-primary/90 text-primary-foreground px-4 py-2 rounded-full text-sm font-medium shadow-sm transition-transform active:scale-95"
                            >
                                <Plus className="w-4 h-4" />
                                <span>New Post</span>
                            </Link>

                            {/* Mobile New Post Icon */}
                            <Link 
                                href="/posts/create"
                                className="sm:hidden p-2 rounded-full bg-primary/10 text-primary hover:bg-primary/20 transition-colors"
                            >
                                <Plus className="w-5 h-5" />
                            </Link>

                            {/* Notifications */}
                            <DropdownMenu>
                                <DropdownMenuTrigger asChild>
                                    <button className="relative p-2 rounded-full text-muted-foreground hover:bg-surface-muted hover:text-foreground transition-colors focus:outline-none focus:ring-2 focus:ring-primary/20">
                                        <Bell className="w-5 h-5" />
                                        {(user?.unread_notifications_count ?? 0) > 0 && (
                                            <span className="absolute top-1 right-1.5 flex h-2.5 w-2.5">
                                                <span className="animate-ping absolute inline-flex h-full w-full rounded-full bg-destructive opacity-75"></span>
                                                <span className="relative inline-flex rounded-full h-2.5 w-2.5 bg-destructive border-2 border-background"></span>
                                            </span>
                                        )}
                                    </button>
                                </DropdownMenuTrigger>
                                <DropdownMenuContent align="end" className="w-80 p-0 rounded-xl overflow-hidden shadow-lg border-border-subtle">
                                    <div className="p-4 border-b border-border-subtle flex items-center justify-between bg-surface/50">
                                        <span className="font-semibold text-sm">Notifications</span>
                                        {(user?.unread_notifications_count ?? 0) > 0 && (
                                            <Badge variant="secondary" className="rounded-full bg-primary/10 text-primary hover:bg-primary/20">{user?.unread_notifications_count} New</Badge>
                                        )}
                                    </div>
                                    <div className="max-h-80 overflow-y-auto">
                                        {(user?.unread_notifications?.length ?? 0) > 0 ? (
                                            user?.unread_notifications?.map((notification: any) => (
                                                <Link href={notification.data.url ?? '#'} key={notification.id}>
                                                    <div className="p-4 border-b border-border-subtle hover:bg-surface-muted transition-colors cursor-pointer group last:border-0">
                                                        <div className="flex gap-3">
                                                            <div className="w-2 h-2 rounded-full bg-primary mt-1.5 shrink-0" />
                                                            <div>
                                                                <p className="text-sm font-medium text-foreground group-hover:text-primary transition-colors">{notification.data.message}</p>
                                                                <p className="text-xs text-muted-foreground mt-1">{new Date(notification.created_at).toLocaleDateString()}</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </Link>
                                            ))
                                        ) : (
                                            <div className="p-4 text-center text-sm text-muted-foreground">
                                                No new notifications
                                            </div>
                                        )}
                                    </div>
                                    <div className="p-2 border-t border-border-subtle bg-surface/50">
                                        <Link href="/notifications" className="block text-center w-full py-2 text-sm text-primary font-medium hover:bg-primary/10 rounded-lg transition-colors">
                                            View all notifications
                                        </Link>
                                    </div>
                                </DropdownMenuContent>
                            </DropdownMenu>

                            {/* User Profile - Desktop Dropdown */}
                            <div className="hidden md:block">
                                <DropdownMenu>
                                    <DropdownMenuTrigger asChild>
                                        <button className="flex items-center gap-2 p-1 pl-2 pr-1 rounded-full hover:bg-surface-muted transition-colors focus:outline-none focus:ring-2 focus:ring-primary/20">
                                            <span className="hidden sm:block text-sm font-medium text-muted-foreground px-1">
                                                {user?.name || 'Admin'}
                                            </span>
                                            <div className={`w-8 h-8 rounded-full flex items-center justify-center text-white shadow-sm ring-2 ring-background overflow-hidden ${!user?.avatar_url ? 'bg-gradient-to-tr from-indigo-500 to-purple-500' : 'bg-transparent'}`}>
                                                {user?.avatar_url ? (
                                                    <img src={user.avatar_url} alt={user.name} className="w-full h-full object-cover" />
                                                ) : (
                                                    <User className="w-4 h-4" />
                                                )}
                                            </div>
                                        </button>
                                    </DropdownMenuTrigger>
                                    <DropdownMenuContent align="end" className="w-56 rounded-xl shadow-lg border-border-subtle p-2">
                                        <div className="px-2 py-2 mb-2 border-b border-border-subtle">
                                            <p className="text-sm font-medium">{user?.name || 'Admin User'}</p>
                                            <p className="text-xs text-muted-foreground truncate">{user?.email || 'admin@modernblog.com'}</p>
                                        </div>
                                        <Link href="/account/profile" className="w-full">
                                            <DropdownMenuItem className="rounded-lg cursor-pointer">
                                                <User className="w-4 h-4 mr-2 text-muted-foreground" />
                                                Personal
                                            </DropdownMenuItem>
                                        </Link>
                                        <Link href="/account/security" className="w-full">
                                            <DropdownMenuItem className="rounded-lg cursor-pointer">
                                                <Shield className="w-4 h-4 mr-2 text-muted-foreground" />
                                                Security & Privacy
                                            </DropdownMenuItem>
                                        </Link>
                                        <Link href="/account/appearance" className="w-full">
                                            <DropdownMenuItem className="rounded-lg cursor-pointer">
                                                <Settings className="w-4 h-4 mr-2 text-muted-foreground" />
                                                Preferences
                                            </DropdownMenuItem>
                                        </Link>
                                        <DropdownMenuSeparator />
                                        <DropdownMenuItem 
                                            className="rounded-lg cursor-pointer text-destructive focus:bg-destructive/10 focus:text-destructive w-full"
                                            onClick={() => router.post('/logout')}
                                        >
                                            <LogOut className="w-4 h-4 mr-2" />
                                            Logout
                                        </DropdownMenuItem>
                                    </DropdownMenuContent>
                                </DropdownMenu>
                            </div>

                            {/* User Profile - Mobile Bottom Sheet Trigger */}
                            <button 
                                className="md:hidden flex items-center gap-2 p-1 pl-2 pr-1 rounded-full hover:bg-surface-muted transition-colors focus:outline-none focus:ring-2 focus:ring-primary/20"
                                onClick={() => setIsUserMenuOpen(true)}
                            >
                                <div className={`w-8 h-8 rounded-full flex items-center justify-center text-white shadow-sm ring-2 ring-background overflow-hidden ${!user?.avatar_url ? 'bg-gradient-to-tr from-indigo-500 to-purple-500' : 'bg-transparent'}`}>
                                    {user?.avatar_url ? (
                                        <img src={user.avatar_url} alt={user.name} className="w-full h-full object-cover" />
                                    ) : (
                                        <User className="w-4 h-4" />
                                    )}
                                </div>
                            </button>

                        </div>
                    </header>

                    {/* Page Content */}
                    <main className="flex-1 overflow-y-auto bg-surface/50 scroll-smooth relative">
                        <div className="mx-auto max-w-7xl animate-in fade-in duration-500 slide-in-from-bottom-4 p-4 lg:p-8">
                            {children}
                        </div>
                    </main>
                </div>
                
                {/* Mobile User Profile Bottom Sheet */}
                <BottomSheet open={isUserMenuOpen} onOpenChange={setIsUserMenuOpen}>
                    <div className="mb-6 px-1 flex flex-col items-center text-center">
                        <div className={`w-16 h-16 rounded-full mb-3 flex items-center justify-center text-white shadow-sm ring-4 ring-background overflow-hidden ${!user?.avatar_url ? 'bg-gradient-to-tr from-indigo-500 to-purple-500' : 'bg-transparent'}`}>
                            {user?.avatar_url ? (
                                <img src={user.avatar_url} alt={user.name} className="w-full h-full object-cover" />
                            ) : (
                                <User className="w-8 h-8" />
                            )}
                        </div>
                        <p className="text-lg font-bold">{user?.name || 'Admin User'}</p>
                        <p className="text-sm text-muted-foreground">{user?.email || 'admin@modernblog.com'}</p>
                    </div>
                    
                    <div className="space-y-1">
                        <Link href="/account/profile" onClick={() => setIsUserMenuOpen(false)} className="w-full flex items-center px-4 py-3 text-sm rounded-xl hover:bg-surface-muted transition-colors text-foreground">
                            <User className="w-5 h-5 mr-3 text-muted-foreground" /> Personal
                        </Link>
                        <Link href="/account/security" onClick={() => setIsUserMenuOpen(false)} className="w-full flex items-center px-4 py-3 text-sm rounded-xl hover:bg-surface-muted transition-colors text-foreground">
                            <Shield className="w-5 h-5 mr-3 text-muted-foreground" /> Security & Privacy
                        </Link>
                        <Link href="/account" onClick={() => setIsUserMenuOpen(false)} className="w-full flex items-center px-4 py-3 text-sm rounded-xl hover:bg-surface-muted transition-colors text-foreground">
                            <Settings2 className="w-5 h-5 mr-3 text-muted-foreground" /> Account Settings
                        </Link>
                        <Link href="/account/appearance" onClick={() => setIsUserMenuOpen(false)} className="w-full flex items-center px-4 py-3 text-sm rounded-xl hover:bg-surface-muted transition-colors text-foreground">
                            <Settings className="w-5 h-5 mr-3 text-muted-foreground" /> Preferences
                        </Link>
                        <button onClick={() => { setIsUserMenuOpen(false); router.post('/logout'); }} className="w-full flex items-center px-4 py-3 text-sm rounded-xl text-destructive hover:bg-destructive/10 transition-colors">
                            <LogOut className="w-5 h-5 mr-3" /> Logout
                        </button>
                    </div>
                </BottomSheet>
            </div>
        </TooltipProvider>
    );
}