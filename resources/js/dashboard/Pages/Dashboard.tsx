import React from 'react';
import { Head, Link, usePage } from '@inertiajs/react';
import { SmartLink } from '@dashboard/Components/ui/SmartLink';
import DashboardLayout from '@dashboard/Layouts/DashboardLayout';
import {
    FileText,
    CheckCircle2,
    Clock,
    MessageSquare,
    FolderTree,
    Users,
    ChevronRight,
} from 'lucide-react';

interface Stats {
    posts: number;
    categories: number;
    tags: number;
    comments: number;
    users: number;
    published_posts: number;
    draft_posts: number;
    pending_comments: number;
}

interface Post {
    id: string;
    title: string;
    status: string;
    author: { name: string };
    category: { name: string } | null;
    published_at: string;
    thumbnail_url: string | null;
}

interface Comment {
    id: string;
    content: string;
    author: { name: string } | null;
    post: { title: string; slug: string };
    created_at: string;
}

interface Props {
    stats: Stats;
    recent_posts: Post[];
    recent_comments: Comment[];
}

const statCards = [
    { label: 'Total Postingan', key: 'posts' as const, icon: FileText, color: 'text-primary' },
    { label: 'Terbit', key: 'published_posts' as const, icon: CheckCircle2, color: 'text-success' },
    { label: 'Draf', key: 'draft_posts' as const, icon: Clock, color: 'text-warning' },
    { label: 'Komentar', key: 'comments' as const, icon: MessageSquare, color: 'text-info' },
    { label: 'Kategori', key: 'categories' as const, icon: FolderTree, color: 'text-foreground' },
    { label: 'Pengguna', key: 'users' as const, icon: Users, color: 'text-muted-foreground' },
];

export default function Dashboard({ stats, recent_posts, recent_comments }: Props) {
    const { blog_url } = usePage().props as any;
    return (
        <DashboardLayout>
            <Head title="Dasbor" />
            <div className="space-y-6">
                <div>
                    <h1 className="text-2xl font-semibold tracking-tight">Ringkasan</h1>
                    <p className="text-sm text-muted-foreground mt-1">Sekilas tentang blog Anda</p>
                </div>

                {/* Stats grid */}
                <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                    {statCards.map((s) => {
                        const Icon = s.icon;
                        return (
                            <div key={s.key} className="bg-background border border-border-subtle rounded-lg p-4 hover:shadow-elevated transition-shadow">
                                <div className="flex items-center gap-3 mb-3">
                                    <div className={`w-8 h-8 rounded-lg bg-surface-muted flex items-center justify-center ${s.color}`}>
                                        <Icon className="w-4 h-4" />
                                    </div>
                                </div>
                                <p className="text-2xl font-bold tracking-tight">{stats[s.key]}</p>
                                <p className="text-xs text-muted-foreground mt-0.5">{s.label}</p>
                            </div>
                        );
                    })}
                </div>

                <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    {/* Recent Posts */}
                    <div className="bg-background border border-border-subtle rounded-lg">
                        <div className="flex items-center justify-end md:justify-between px-5 py-3.5 border-b border-border-subtle w-full">
                            <h2 className="text-sm font-semibold flex items-center gap-2">
                                <FileText className="w-4 h-4 text-muted-foreground" />
                                Postingan Terbaru
                            </h2>
                            <Link href="/posts" className="text-xs text-primary hover:underline flex items-center gap-1">
                                Lihat Semua <ChevronRight className="w-3 h-3" />
                            </Link>
                        </div>
                        <div className="divide-y divide-border-subtle">
                            {recent_posts.length === 0 ? (
                                <div className="px-5 py-8 text-center text-sm text-muted-foreground">
                                    Belum ada postingan. Mulai menulis!
                                </div>
                            ) : (
                                recent_posts.map((p) => (
                                    <div key={p.id} className="px-5 py-3 flex items-center gap-3 hover:bg-surface-muted/30 transition-colors">
                                        {p.thumbnail_url ? (
                                            <img src={p.thumbnail_url} alt="" className="w-10 h-10 rounded object-cover shrink-0" />
                                        ) : (
                                            <div className="w-10 h-10 rounded bg-surface-muted flex items-center justify-center shrink-0">
                                                <FileText className="w-4 h-4 text-muted-foreground" />
                                            </div>
                                        )}
                                        <div className="min-w-0 flex-1">
                                            <Link href={`/posts/${p.id}/edit`} className="text-sm font-medium truncate block hover:text-primary transition-colors">
                                                {p.title}
                                            </Link>
                                            <p className="text-xs text-muted-foreground">{p.author?.name} &middot; {p.category?.name}</p>
                                        </div>
                                        <span className={`chip text-xs ${p.status === 'published' ? 'text-success' : 'text-warning'}`}>{p.status}</span>
                                    </div>
                                ))
                            )}
                        </div>
                    </div>

                    {/* Recent Comments */}
                    <div className="bg-background border border-border-subtle rounded-lg">
                        <div className="flex items-center justify-end md:justify-between px-5 py-3.5 border-b border-border-subtle w-full">
                            <h2 className="text-sm font-semibold flex items-center gap-2">
                                <MessageSquare className="w-4 h-4 text-muted-foreground" />
                                Komentar Terbaru
                            </h2>
                            <Link href="/comments" className="text-xs text-primary hover:underline flex items-center gap-1">
                                Lihat Semua <ChevronRight className="w-3 h-3" />
                            </Link>
                        </div>
                        <div className="divide-y divide-border-subtle">
                            {recent_comments.length === 0 ? (
                                <div className="px-5 py-8 text-center text-sm text-muted-foreground">
                                    Belum ada komentar.
                                </div>
                            ) : (
                                recent_comments.map((c) => (
                                    <div key={c.id} className="px-5 py-3 hover:bg-surface-muted/30 transition-colors">
                                        <p className="text-sm truncate">{c.content.slice(0, 80)}...</p>
                                        <p className="text-xs text-muted-foreground mt-0.5">
                                            {c.author?.name ?? 'Anonim'} pada{' '}
                                            <SmartLink href={`${blog_url}/post/${c.post?.slug}`} className="hover:text-primary transition-colors">{c.post?.title}</SmartLink>
                                        </p>
                                    </div>
                                ))
                            )}
                        </div>
                    </div>
                </div>
            </div>
        </DashboardLayout>
    );
}
