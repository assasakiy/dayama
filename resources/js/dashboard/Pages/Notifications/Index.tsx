import React from 'react';
import { Head, Link, router } from '@inertiajs/react';
import DashboardLayout from '@dashboard/Layouts/DashboardLayout';
import {
    Bell,
    CheckCircle2,
    Trash2,
    MessageSquare,
    AtSign,
    CornerDownRight
} from 'lucide-react';

export default function NotificationIndex({ notifications, unreadCount }: { notifications: any; unreadCount: number }) {
    
    const handleMarkAsRead = (id: string) => {
        router.patch(`/notifications/${id}/read`, {}, {
            preserveScroll: true,
        });
    };

    const handleMarkAllAsRead = () => {
        router.post(`/notifications/read-all`, {}, {
            preserveScroll: true,
        });
    };

    const handleDelete = (id: string) => {
        router.delete(`/notifications/${id}`, {
            preserveScroll: true,
        });
    };

    const getIcon = (type: string) => {
        switch (type) {
            case 'App\\Notifications\\NewCommentNotification':
                return <MessageSquare className="w-5 h-5 text-primary" />;
            case 'App\\Notifications\\CommentMentionedNotification':
                return <AtSign className="w-5 h-5 text-indigo-500" />;
            case 'App\\Notifications\\CommentReplyNotification':
                return <CornerDownRight className="w-5 h-5 text-emerald-500" />;
            default:
                return <Bell className="w-5 h-5 text-muted-foreground" />;
        }
    };

    return (
        <DashboardLayout>
            <Head title="Notifications" />
            <div className="space-y-5">
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-xl font-semibold tracking-tight">Notifications</h1>
                        <p className="hidden sm:block text-sm text-muted-foreground mt-0.5">Stay updated with activity on your posts</p>
                    </div>
                    {unreadCount > 0 && (
                        <button
                            onClick={handleMarkAllAsRead}
                            className="btn btn-primary h-9 px-4 text-xs font-semibold rounded-md shadow-sm"
                        >
                            Mark all as read
                        </button>
                    )}
                </div>

                <div className="bg-background border border-border-subtle rounded-lg overflow-hidden shadow-sm">
                    {notifications.data.length > 0 ? (
                        <div className="divide-y divide-border-subtle">
                            {notifications.data.map((notification: any) => (
                                <div key={notification.id} className={`p-4 flex gap-4 items-start transition-colors ${!notification.read_at ? 'bg-primary/5' : 'hover:bg-surface-muted/30'}`}>
                                    <div className="shrink-0 mt-1">
                                        {getIcon(notification.type)}
                                    </div>
                                    <div className="flex-1 min-w-0">
                                        <p className={`text-sm ${!notification.read_at ? 'font-semibold text-foreground' : 'text-muted-foreground'}`}>
                                            {notification.data.message}
                                        </p>
                                        <div className="flex items-center gap-4 mt-2">
                                            <span className="text-xs text-muted-foreground">
                                                {new Date(notification.created_at).toLocaleString()}
                                            </span>
                                            {notification.data.url && (
                                                <a href={notification.data.url} className="text-xs text-primary hover:underline font-medium">
                                                    View Details
                                                </a>
                                            )}
                                        </div>
                                    </div>
                                    <div className="shrink-0 flex items-center gap-2">
                                        {!notification.read_at && (
                                            <button
                                                onClick={() => handleMarkAsRead(notification.id)}
                                                className="p-1.5 rounded-md text-muted-foreground hover:text-success hover:bg-success/10 transition-colors"
                                                title="Mark as read"
                                            >
                                                <CheckCircle2 className="w-4 h-4" />
                                            </button>
                                        )}
                                        <button
                                            onClick={() => handleDelete(notification.id)}
                                            className="p-1.5 rounded-md text-muted-foreground hover:text-danger hover:bg-danger/10 transition-colors"
                                            title="Delete"
                                        >
                                            <Trash2 className="w-4 h-4" />
                                        </button>
                                    </div>
                                </div>
                            ))}
                        </div>
                    ) : (
                        <div className="p-8 text-center">
                            <Bell className="w-8 h-8 mx-auto text-muted-foreground/50 mb-3" />
                            <p className="text-muted-foreground">You have no notifications.</p>
                        </div>
                    )}
                </div>

                {notifications.links && notifications.links.length > 3 && (
                    <div className="flex items-center justify-end md:justify-between text-sm w-full pt-4">
                        <p className="text-xs text-muted-foreground hidden md:block">
                            Showing {notifications.from} to {notifications.to} of {notifications.total} notifications
                        </p>
                        <div className="flex items-center gap-1">
                            {notifications.links.map((link: any, i: number) => (
                                <Link
                                    key={i}
                                    href={link.url || '#'}
                                    preserveScroll
                                    className={`px-2.5 py-1.5 rounded-md text-xs transition-colors ${
                                        link.active
                                            ? 'bg-primary text-primary-foreground'
                                            : link.url
                                            ? 'text-muted-foreground hover:text-foreground hover:bg-surface-muted'
                                            : 'text-muted-foreground/40 cursor-not-allowed'
                                    }`}
                                    dangerouslySetInnerHTML={{ __html: link.label }}
                                />
                            ))}
                        </div>
                    </div>
                )}
            </div>
        </DashboardLayout>
    );
}
