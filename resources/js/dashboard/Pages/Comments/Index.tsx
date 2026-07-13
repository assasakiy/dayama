import React, { useState } from 'react';
import { Head, Link, router, usePage } from '@inertiajs/react';
import { SmartLink } from '@dashboard/Components/ui/SmartLink';
import DashboardLayout from '@dashboard/Layouts/DashboardLayout';
import ConfirmDialog from '@dashboard/Components/ui/confirm-dialog';
import {
    Trash2,
    MessageSquare,
    ExternalLink,
    User,
    CalendarDays,
    CheckCircle2,
    Clock,
    XCircle,
    ShieldAlert,
    Pin,
    PinOff,
    Network,
    Bot,
    Info,
    CornerDownRight
} from 'lucide-react';

const STATUS_TABS = [
    { label: 'All', value: 'all', href: '/comments' },
    { label: 'Review', value: 'review', href: '/comments?status=review' },
    { label: 'Published', value: 'published', href: '/comments?status=published' },
    { label: 'Spam', value: 'spam', href: '/comments?status=spam' },
    { label: 'Rejected', value: 'rejected', href: '/comments?status=rejected' },
];

const statusBadge = (status: string) => {
    const map: Record<string, { icon: React.ReactNode; className: string; label: string }> = {
        review: { icon: <Clock className="w-3 h-3" />, className: 'bg-warning/10 text-warning border-warning/20', label: 'Review' },
        published: { icon: <CheckCircle2 className="w-3 h-3" />, className: 'bg-success/10 text-success border-success/20', label: 'Published' },
        spam: { icon: <ShieldAlert className="w-3 h-3" />, className: 'bg-danger/10 text-danger border-danger/20', label: 'Spam' },
        rejected: { icon: <XCircle className="w-3 h-3" />, className: 'bg-muted-foreground/10 text-muted-foreground border-muted-foreground/20', label: 'Rejected' },
    };
    const config = map[status] ?? map.review;
    return (
        <span className={`inline-flex items-center gap-1 px-2 py-0.5 text-xs font-medium rounded-full border ${config.className}`}>
            {config.icon}
            {config.label}
        </span>
    );
};

export default function CommentIndex({ comments, currentStatus }: { comments: any; currentStatus: string }) {
    const { blog_url } = usePage().props as any;
    const [deleteId, setDeleteId] = useState<string | null>(null);

    const handleDelete = () => {
        if (!deleteId) return;
        router.delete(`/comments/${deleteId}`, {
            preserveScroll: true,
        });
        setDeleteId(null);
    };

    const handleStatusChange = (commentId: string, status: string) => {
        router.patch(`/comments/${commentId}/status`, { status }, {
            preserveScroll: true,
        });
    };

    const handlePinChange = (commentId: string) => {
        router.patch(`/comments/${commentId}/pin`, {}, {
            preserveScroll: true,
        });
    };

    return (
        <DashboardLayout>
            <Head title="Comments" />
            <div className="space-y-5">
                <div className="hidden md:block">
                    <h1 className="text-xl font-semibold tracking-tight">Comments</h1>
                    <p className="hidden lg:block text-sm text-muted-foreground mt-0.5">Manage reader comments</p>
                </div>

                {/* Filter Tabs */}
                <div className="flex items-center gap-1 p-1 bg-surface-muted/50 rounded-lg w-fit">
                    {STATUS_TABS.map((tab) => (
                        <Link
                            key={tab.value}
                            href={tab.href}
                            className={`px-3 py-1.5 text-xs font-medium rounded-md transition-all ${
                                currentStatus === tab.value
                                    ? 'bg-background text-foreground shadow-sm'
                                    : 'text-muted-foreground hover:text-foreground'
                            }`}
                        >
                            {tab.label}
                        </Link>
                    ))}
                </div>

                <div className="bg-background border border-border-subtle rounded-lg overflow-x-auto">
                    <table className="w-full text-sm min-w-[1000px]">
                        <thead>
                            <tr className="border-b border-border-subtle bg-surface-muted/50">
                                <th className="text-left px-4 py-3 font-medium">Content</th>
                                <th className="text-left px-4 py-3 font-medium">Author</th>
                                <th className="text-left px-4 py-3 font-medium">Post</th>
                                <th className="text-left px-4 py-3 font-medium">Status</th>
                                <th className="text-left px-4 py-3 font-medium">Date</th>
                                <th className="text-right px-4 py-3 font-medium">Actions</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-border-subtle">
                            {comments.data.map((comment: any) => (
                                <tr key={comment.id} className="hover:bg-surface-muted/30 transition-colors">
                                    <td className="px-4 py-3 text-muted-foreground max-w-xs">
                                        <div className="flex flex-col gap-1.5" style={{ paddingLeft: `${comment.depth * 1}rem` }}>
                                            <div className="flex items-start gap-2">
                                                {comment.depth > 0 ? (
                                                    <CornerDownRight className="w-3.5 h-3.5 mt-0.5 shrink-0 text-muted-foreground/50" />
                                                ) : (
                                                    <MessageSquare className="w-3.5 h-3.5 mt-0.5 shrink-0" />
                                                )}
                                                <span className="truncate block flex-1">{comment.content}</span>
                                                {comment.is_pinned && (
                                                    <Pin className="w-3.5 h-3.5 mt-0.5 shrink-0 text-primary rotate-45" />
                                                )}
                                            </div>
                                            {comment.replies_count > 0 && (
                                                <span className="text-[10px] bg-surface-muted border border-border-subtle rounded-md px-1.5 py-0.5 w-fit">
                                                    {comment.replies_count} replies
                                                </span>
                                            )}
                                        </div>
                                    </td>
                                    <td className="px-4 py-3">
                                        <div className="flex flex-col gap-1">
                                            <span className="inline-flex items-center gap-1.5 text-muted-foreground">
                                                <User className="w-3.5 h-3.5" />
                                                <span className="font-medium text-foreground">{comment.author?.name ?? comment.guest_name ?? 'Anonymous'}</span>
                                                {comment.created_as_guest && (
                                                    <span className="text-[9px] bg-surface-muted border border-border-subtle px-1 rounded uppercase tracking-wider">Guest</span>
                                                )}
                                            </span>
                                            {comment.guest_email && (
                                                <span className="text-xs text-muted-foreground/70 ml-5 truncate">{comment.guest_email}</span>
                                            )}
                                            {comment.guest_ip && (
                                                <span className="text-[10px] text-muted-foreground/50 ml-5 inline-flex items-center gap-1" title={comment.guest_user_agent}>
                                                    <Network className="w-3 h-3" /> {comment.guest_ip}
                                                </span>
                                            )}
                                        </div>
                                    </td>
                                    <td className="px-4 py-3 text-muted-foreground max-w-[200px] truncate">
                                        {comment.post && (
                                            <SmartLink href={`${blog_url}/post/${comment.post.slug}`} className="inline-flex items-center gap-1.5 hover:text-primary transition-colors">
                                                <ExternalLink className="w-3 h-3 shrink-0" />
                                                <span className="truncate">{comment.post.title}</span>
                                            </SmartLink>
                                        )}
                                    </td>
                                    <td className="px-4 py-3">
                                        <div className="flex flex-col gap-1.5 items-start">
                                            {statusBadge(comment.status)}
                                            {comment.moderation_score !== null && (
                                                <div className="flex items-center gap-1 text-[10px] text-muted-foreground/80 mt-1" title={comment.moderation_flags ? JSON.stringify(comment.moderation_flags) : 'No flags'}>
                                                    <Info className="w-3 h-3" /> Score: {comment.moderation_score}
                                                </div>
                                            )}
                                        </div>
                                    </td>
                                    <td className="px-4 py-3 text-muted-foreground min-w-[140px]">
                                        <div className="flex flex-col gap-1.5 items-start">
                                            <span className="inline-flex items-center gap-1.5 text-xs">
                                                <CalendarDays className="w-3 h-3" />
                                                {new Date(comment.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}
                                            </span>
                                            {comment.moderated_at && (
                                                <span className="inline-flex items-center gap-1 text-[10px] text-muted-foreground/70" title={`Moderated at ${new Date(comment.moderated_at).toLocaleString()}`}>
                                                    {comment.moderator ? (
                                                        <><User className="w-3 h-3" /> {comment.moderator.name}</>
                                                    ) : (
                                                        <><Bot className="w-3 h-3" /> System (Auto)</>
                                                    )}
                                                </span>
                                            )}
                                        </div>
                                    </td>
                                    <td className="px-4 py-3 text-right">
                                        <div className="flex items-center justify-end gap-1">
                                            {comment.status !== 'published' && (
                                                <button
                                                    onClick={() => handleStatusChange(comment.id, 'published')}
                                                    className="p-1.5 rounded-md text-muted-foreground hover:text-success hover:bg-success/10 transition-colors"
                                                    title="Publish"
                                                >
                                                    <CheckCircle2 className="w-3.5 h-3.5" />
                                                </button>
                                            )}
                                            {comment.status === 'published' && (
                                                <button
                                                    onClick={() => handlePinChange(comment.id)}
                                                    className={`p-1.5 rounded-md transition-colors ${comment.is_pinned ? 'text-primary bg-primary/10 hover:bg-primary/20' : 'text-muted-foreground hover:text-primary hover:bg-primary/10'}`}
                                                    title={comment.is_pinned ? "Unpin" : "Pin"}
                                                >
                                                    {comment.is_pinned ? <PinOff className="w-3.5 h-3.5" /> : <Pin className="w-3.5 h-3.5" />}
                                                </button>
                                            )}
                                            {comment.status !== 'rejected' && comment.status !== 'spam' && (
                                                <button
                                                    onClick={() => handleStatusChange(comment.id, 'rejected')}
                                                    className="p-1.5 rounded-md text-muted-foreground hover:text-warning hover:bg-warning/10 transition-colors"
                                                    title="Reject"
                                                >
                                                    <XCircle className="w-3.5 h-3.5" />
                                                </button>
                                            )}
                                            {comment.status !== 'spam' && (
                                                <button
                                                    onClick={() => handleStatusChange(comment.id, 'spam')}
                                                    className="p-1.5 rounded-md text-muted-foreground hover:text-danger hover:bg-danger/10 transition-colors"
                                                    title="Mark as Spam"
                                                >
                                                    <ShieldAlert className="w-3.5 h-3.5" />
                                                </button>
                                            )}
                                            <button
                                                onClick={() => setDeleteId(comment.id)}
                                                className="p-1.5 rounded-md text-muted-foreground hover:text-danger hover:bg-danger/10 transition-colors"
                                                title="Delete"
                                            >
                                                <Trash2 className="w-3.5 h-3.5" />
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>

                    {comments.data.length === 0 && (
                        <div className="flex flex-col items-center justify-center py-16 text-center">
                            <MessageSquare className="w-12 h-12 text-muted-foreground/30 mb-4" />
                            <p className="text-sm font-medium text-foreground">No comments found</p>
                            <p className="text-xs text-muted-foreground mt-1">
                                {currentStatus === 'all'
                                    ? 'Comments from readers will appear here.'
                                    : `No ${currentStatus} comments at this time.`}
                            </p>
                        </div>
                    )}
                </div>
            </div>

            <ConfirmDialog
                open={!!deleteId}
                onOpenChange={(open) => { if (!open) setDeleteId(null); }}
                title="Delete Comment"
                message="Are you sure you want to delete this comment? This action cannot be undone."
                confirmLabel="Delete"
                variant="danger"
                onConfirm={handleDelete}
            />
        </DashboardLayout>
    );
}
