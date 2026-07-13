import React, { useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import DashboardLayout from '@dashboard/Layouts/DashboardLayout';
import { Card, CardHeader, CardTitle, CardContent } from '@dashboard/Components/ui/card';
import {
    ArrowLeft,
    History,
    User,
    CalendarDays,
    Eye,
    ChevronDown,
    ChevronUp,
} from 'lucide-react';

interface Revision {
    id: string;
    revision_number: number;
    title: string;
    excerpt: string | null;
    content: string;
    change_summary: string | null;
    author: { name: string } | null;
    created_at: string;
}

interface Props {
    post: { id: string; title: string };
    revisions: Revision[];
}

export default function PostRevisions({ post, revisions }: Props) {
    const [expandedId, setExpandedId] = useState<string | null>(null);

    const toggle = (id: string) => {
        setExpandedId(prev => prev === id ? null : id);
    };

    const handleRestore = (revisionId: string) => {
        if (confirm('Are you sure you want to restore to this revision? Current unsaved changes will be lost.')) {
            router.post(`/posts/${post.id}/restore-revision/${revisionId}`);
        }
    };

    return (
        <DashboardLayout>
            <Head title={`Revisions - ${post.title}`} />
            <div className="space-y-5">
                <div className="flex items-center gap-3">
                    <Link
                        href={`/posts/${post.id}/edit`}
                        className="p-1.5 rounded-md text-muted-foreground hover:text-foreground hover:bg-surface-muted transition-colors"
                    >
                        <ArrowLeft className="w-4 h-4" />
                    </Link>
                    <div className="hidden md:block">
                        <h1 className="text-xl font-semibold tracking-tight">Revision History</h1>
                        <p className="hidden lg:block text-sm text-muted-foreground mt-0.5">{post.title}</p>
                    </div>
                </div>

                {revisions.length === 0 ? (
                    <div className="flex flex-col items-center justify-center py-20 text-center">
                        <History className="w-12 h-12 text-muted-foreground/30 mb-4" />
                        <p className="text-sm font-medium text-foreground">No revisions yet</p>
                        <p className="text-xs text-muted-foreground mt-1">Revisions are saved automatically each time you update the post.</p>
                    </div>
                ) : (
                    <div className="space-y-3">
                        {revisions.map((rev) => (
                            <Card key={rev.id}>
                                <CardHeader className="cursor-pointer" onClick={() => toggle(rev.id)}>
                                    <div className="flex items-center justify-between w-full">
                                        <CardTitle className="flex items-center gap-2 text-sm">
                                            <span className="w-6 h-6 rounded-full bg-primary/10 text-primary flex items-center justify-center text-xs font-bold">
                                                {rev.revision_number}
                                            </span>
                                            <span className="font-medium">{rev.title}</span>
                                        </CardTitle>
                                        <div className="flex items-center gap-3 text-xs text-muted-foreground">
                                            <span className="hidden sm:inline-flex items-center gap-1.5">
                                                <User className="w-3 h-3" />
                                                {rev.author?.name ?? 'Unknown'}
                                            </span>
                                            <span className="inline-flex items-center gap-1.5">
                                                <CalendarDays className="w-3 h-3" />
                                                {new Date(rev.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric', hour: '2-digit', minute: '2-digit' })}
                                            </span>
                                            {expandedId === rev.id ? <ChevronUp className="w-4 h-4" /> : <ChevronDown className="w-4 h-4" />}
                                        </div>
                                    </div>
                                </CardHeader>
                                {expandedId === rev.id && (
                                    <CardContent className="pt-0 space-y-4">
                                        {rev.change_summary && (
                                            <div>
                                                <h4 className="text-xs font-semibold uppercase tracking-wider text-muted-foreground mb-2">Changes</h4>
                                                <p className="text-sm font-medium text-foreground bg-primary/5 rounded-md p-3 border border-primary/10">{rev.change_summary}</p>
                                            </div>
                                        )}
                                        {rev.excerpt && (
                                            <div>
                                                <h4 className="text-xs font-semibold uppercase tracking-wider text-muted-foreground mb-2">Excerpt</h4>
                                                <p className="text-sm text-muted-foreground bg-surface-muted/50 rounded-md p-3">{rev.excerpt}</p>
                                            </div>
                                        )}
                                        <div>
                                            <h4 className="text-xs font-semibold uppercase tracking-wider text-muted-foreground mb-2">Content</h4>
                                            <div
                                                className="prose-blog max-w-none text-sm bg-surface-muted/50 rounded-md p-4 max-h-80 overflow-y-auto border border-border-subtle"
                                                dangerouslySetInnerHTML={{ __html: rev.content }}
                                            />
                                        </div>
                                        <div className="pt-4 border-t border-border-subtle flex justify-end">
                                            <button
                                                onClick={() => handleRestore(rev.id)}
                                                className="inline-flex items-center gap-2 h-9 px-4 bg-primary text-primary-foreground rounded-md text-sm font-medium hover:bg-primary/90 transition-all shadow-sm"
                                            >
                                                <History className="w-4 h-4" />
                                                Restore to this version
                                            </button>
                                        </div>
                                    </CardContent>
                                )}
                            </Card>
                        ))}
                    </div>
                )}
            </div>
        </DashboardLayout>
    );
}
