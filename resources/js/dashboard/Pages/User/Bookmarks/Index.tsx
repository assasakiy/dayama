import React from 'react';
import { Head, Link, router, usePage } from '@inertiajs/react';
import { SmartLink } from '@dashboard/Components/ui/SmartLink';
import DashboardLayout from '@dashboard/Layouts/DashboardLayout';
import ConfirmDialog from '@dashboard/Components/ui/confirm-dialog';
import { Bookmark, Clock, ExternalLink, Trash2, BookOpen } from 'lucide-react';

export default function BookmarksIndex({ bookmarks }: { bookmarks: any }) {
    const { blog_url } = usePage().props as any;
    const [removeId, setRemoveId] = React.useState<string | null>(null);

    const handleRemove = () => {
        if (!removeId) return;
        router.delete(`/bookmarks/${removeId}`, { preserveScroll: true });
        setRemoveId(null);
    };

    return (
        <DashboardLayout>
            <Head title="My Bookmarks" />
            <div className="space-y-5">
                <div className="hidden md:block">
                    <h1 className="text-xl font-semibold tracking-tight">My Bookmarks</h1>
                    <p className="hidden lg:block text-sm text-muted-foreground mt-0.5">Articles you've saved for later.</p>
                </div>

                {bookmarks.data.length === 0 ? (
                    <div className="bg-background border border-border-subtle rounded-lg flex flex-col items-center justify-center py-16 text-center">
                        <Bookmark className="w-10 h-10 text-muted-foreground/30 mb-3" />
                        <p className="text-sm font-medium text-foreground">No bookmarks yet</p>
                        <p className="text-xs text-muted-foreground mt-1">Click the bookmark icon on any article to save it here.</p>
                        <SmartLink href={blog_url} className="mt-4 inline-flex items-center gap-1.5 text-xs text-primary hover:underline">
                            <BookOpen className="w-3.5 h-3.5" /> Browse Articles
                        </SmartLink>
                    </div>
                ) : (
                    <div className="bg-background border border-border-subtle rounded-lg overflow-hidden">
                        <table className="w-full text-sm">
                            <thead>
                                <tr className="border-b border-border-subtle bg-surface-muted/50">
                                    <th className="text-left px-4 py-3 font-medium">Article</th>
                                    <th className="text-left px-4 py-3 font-medium hidden md:table-cell">Category</th>
                                    <th className="text-left px-4 py-3 font-medium hidden sm:table-cell">Saved</th>
                                    <th className="text-right px-4 py-3 font-medium">Actions</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-border-subtle">
                                {bookmarks.data.map((bookmark: any) => {
                                    const post = bookmark.post;
                                    if (!post) return null;
                                    return (
                                        <tr key={bookmark.id} className="hover:bg-surface-muted/30 transition-colors">
                                            <td className="px-4 py-3">
                                                <div className="flex items-start gap-3">
                                                    {post.cover && (
                                                        <img src={post.cover} alt="" className="w-10 h-10 rounded object-cover shrink-0 hidden sm:block" />
                                                    )}
                                                    <div className="min-w-0">
                                                        <SmartLink href={`${blog_url}/post/${post.slug}`} className="font-medium hover:text-primary transition-colors line-clamp-1">
                                                            {post.title}
                                                        </SmartLink>
                                                        <div className="flex items-center gap-2 mt-0.5 text-xs text-muted-foreground">
                                                            {post.author && <span>{post.author.name}</span>}
                                                            {post.reading_time && (
                                                                <span className="flex items-center gap-1">
                                                                    <Clock className="w-3 h-3" />{post.reading_time} min
                                                                </span>
                                                            )}
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td className="px-4 py-3 hidden md:table-cell">
                                                {post.category ? (
                                                    <span className="inline-flex items-center px-2 py-0.5 text-[11px] font-medium rounded-full border bg-primary/5 text-primary border-primary/20">
                                                        {post.category.name}
                                                    </span>
                                                ) : <span className="text-muted-foreground/40">—</span>}
                                            </td>
                                            <td className="px-4 py-3 hidden sm:table-cell text-xs text-muted-foreground">
                                                {new Date(bookmark.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}
                                            </td>
                                            <td className="px-4 py-3 text-right">
                                                <div className="flex items-center justify-end gap-1">
                                                    <a
                                                        href={`/post/${post.slug}`}
                                                        target="_blank"
                                                        className="p-1.5 rounded-md text-muted-foreground hover:text-foreground hover:bg-surface-muted transition-colors"
                                                        title="Open article"
                                                    >
                                                        <ExternalLink className="w-3.5 h-3.5" />
                                                    </a>
                                                    <button
                                                        onClick={() => setRemoveId(post.id)}
                                                        className="p-1.5 rounded-md text-muted-foreground hover:text-danger hover:bg-danger/10 transition-colors"
                                                        title="Remove bookmark"
                                                    >
                                                        <Trash2 className="w-3.5 h-3.5" />
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    );
                                })}
                            </tbody>
                        </table>

                        {bookmarks.last_page > 1 && (
                            <div className="px-4 py-3 border-t border-border-subtle flex items-center justify-between">
                                <span className="text-xs text-muted-foreground">
                                    Showing {bookmarks.from}–{bookmarks.to} of {bookmarks.total}
                                </span>
                                <div className="flex items-center gap-1">
                                    {bookmarks.links.map((link: any, i: number) => (
                                        link.url ? (
                                            <Link key={i} href={link.url}
                                                className={`h-8 min-w-[32px] px-2 flex items-center justify-center rounded text-xs font-medium transition-colors ${link.active ? 'bg-primary text-primary-foreground' : 'bg-surface-muted hover:bg-surface text-muted-foreground border border-border-subtle'}`}
                                                dangerouslySetInnerHTML={{ __html: link.label }}
                                            />
                                        ) : (
                                            <span key={i} className="h-8 min-w-[32px] px-2 flex items-center justify-center text-xs text-muted-foreground/40" dangerouslySetInnerHTML={{ __html: link.label }} />
                                        )
                                    ))}
                                </div>
                            </div>
                        )}
                    </div>
                )}
            </div>

            <ConfirmDialog
                open={!!removeId}
                onOpenChange={(open) => { if (!open) setRemoveId(null); }}
                title="Remove Bookmark"
                message="Remove this article from your bookmarks?"
                confirmLabel="Remove"
                variant="danger"
                onConfirm={handleRemove}
            />
        </DashboardLayout>
    );
}
