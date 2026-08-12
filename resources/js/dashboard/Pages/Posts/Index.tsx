import React, { useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import DashboardLayout from '@dashboard/Layouts/DashboardLayout';
import ConfirmDialog from '@dashboard/Components/ui/confirm-dialog';
import {
    Plus,
    Pencil,
    Trash2,
    FileText,
    Eye,
    CheckCircle2,
    Clock,
    XCircle,
    RotateCcw,
} from 'lucide-react';

export default function PostIndex({ posts }: { posts: any }) {
    const [deleteTarget, setDeleteTarget] = useState<{ id: string; title: string, force?: boolean } | null>(null);
    const [showEmptyTrashConfirm, setShowEmptyTrashConfirm] = useState(false);

    const isTrash = typeof window !== 'undefined' && new URLSearchParams(window.location.search).get('status') === 'trash';

    const handleDelete = () => {
        if (!deleteTarget) return;
        if (deleteTarget.force) {
            router.delete(`/posts/${deleteTarget.id}/force-delete`, { preserveScroll: true });
        } else {
            router.delete(`/posts/${deleteTarget.id}`, { preserveScroll: true });
        }
        setDeleteTarget(null);
    };

    const handleRestore = (id: string) => {
        router.post(`/posts/${id}/restore`, {}, { preserveScroll: true });
    };

    const handleEmptyTrash = () => {
        router.delete('/posts/empty-trash', { preserveScroll: true });
        setShowEmptyTrashConfirm(false);
    };

    const statusIcon = (status: string) => {
        switch (status) {
            case 'published': return <CheckCircle2 className="w-3.5 h-3.5 text-success" />;
            case 'draft': return <Clock className="w-3.5 h-3.5 text-warning" />;
            default: return <XCircle className="w-3.5 h-3.5 text-muted-foreground" />;
        }
    };

    return (
        <DashboardLayout>
            <Head title="Postingan" />
            <div className="space-y-5">
                <div className="flex items-center justify-end md:justify-between w-full">
                    <div className="hidden md:block">
                        <h1 className="text-xl font-semibold tracking-tight">{isTrash ? 'Sampah' : 'Postingan'}</h1>
                        <p className="hidden lg:block text-sm text-muted-foreground mt-0.5">
                            {isTrash ? 'Kelola postingan yang dihapus' : 'Kelola postingan blog'}
                        </p>
                    </div>
                    {!isTrash ? (
                        <Link
                            href="/posts/create"
                            className="ml-auto inline-flex items-center gap-2 h-9 px-4 bg-primary text-primary-foreground rounded-md text-sm font-medium hover:bg-primary/90 transition-all shadow-sm"
                        >
                            <Plus className="w-4 h-4" />
                            Posting Baru
                        </Link>
                    ) : posts.data.length > 0 ? (
                        <button
                            onClick={() => setShowEmptyTrashConfirm(true)}
                            className="ml-auto inline-flex items-center gap-2 h-9 px-4 bg-danger text-danger-foreground rounded-md text-sm font-medium hover:bg-danger/90 transition-all shadow-sm"
                        >
                            <Trash2 className="w-4 h-4" />
Kosongkan Sampah
                        </button>
                    ) : null}
                </div>

                <div className="bg-background border border-border-subtle rounded-lg overflow-hidden">
                    <table className="w-full text-sm">
                        <thead>
                            <tr className="border-b border-border-subtle bg-surface-muted/50">
                                <th className="text-left px-4 py-3 font-medium w-12">Gambar</th>
                                <th className="text-left px-4 py-3 font-medium">Judul</th>
                                <th className="text-left px-4 py-3 font-medium hidden md:table-cell">Penulis</th>
                                <th className="text-left px-4 py-3 font-medium hidden sm:table-cell">Status</th>
                                <th className="text-left px-4 py-3 font-medium hidden lg:table-cell">Tanggal</th>
                                <th className="text-right px-4 py-3 font-medium">Aksi</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-border-subtle">
                            {posts.data.map((post: any) => (
                                <tr key={post.id} className="hover:bg-surface-muted/30 transition-colors">
                                    <td className="px-4 py-3">
                                        {post.thumbnail_url ? (
                                            <img src={post.thumbnail_url} alt="" className="w-10 h-10 rounded object-cover" />
                                        ) : (
                                            <div className="w-10 h-10 rounded bg-surface-muted flex items-center justify-center">
                                                <FileText className="w-4 h-4 text-muted-foreground" />
                                            </div>
                                        )}
                                    </td>
                                    <td className="px-4 py-3">
                                        <Link href={`/posts/${post.id}/edit`} className="font-medium hover:text-primary transition-colors">
                                            {post.title}
                                        </Link>
                                        {post.category && (
                                            <p className="text-xs text-muted-foreground mt-0.5">{post.category.name}</p>
                                        )}
                                    </td>
                                    <td className="px-4 py-3 text-muted-foreground hidden md:table-cell">{post.author?.name}</td>
                                    <td className="px-4 py-3 hidden sm:table-cell">
                                        <span className={`inline-flex items-center gap-1.5 text-xs font-medium ${
                                            post.status === 'published' ? 'text-success' : post.status === 'draft' ? 'text-warning' : 'text-muted-foreground'
                                        }`}>
                                            {statusIcon(post.status)}
                                            {post.status}
                                        </span>
                                    </td>
                                    <td className="px-4 py-3 text-muted-foreground hidden lg:table-cell text-xs">
                                        {new Date(post.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}
                                    </td>
                                    <td className="px-4 py-3 text-right">
                                        <div className="flex items-center justify-end gap-1">
                                            {isTrash ? (
                                                <>
                                                    <button
                                                        onClick={() => handleRestore(post.id)}
                                                        className="p-1.5 rounded-md text-muted-foreground hover:text-success hover:bg-success/10 transition-colors"
                                                        title="Pulihkan"
                                                    >
                                                        <RotateCcw className="w-3.5 h-3.5" />
                                                    </button>
                                                    <button
                                                        onClick={() => setDeleteTarget({ id: post.id, title: post.title, force: true })}
                                                        className="p-1.5 rounded-md text-muted-foreground hover:text-danger hover:bg-danger/10 transition-colors"
                                                        title="Hapus Permanen"
                                                    >
                                                        <Trash2 className="w-3.5 h-3.5" />
                                                    </button>
                                                </>
                                            ) : (
                                                <>
                                                    <Link
                                                        href={`/post/${post.slug}`}
                                                        className="p-1.5 rounded-md text-muted-foreground hover:text-foreground hover:bg-surface-muted transition-colors"
                                                        title="Lihat"
                                                    >
                                                        <Eye className="w-3.5 h-3.5" />
                                                    </Link>
                                                    <Link
                                                        href={`/posts/${post.id}/edit`}
                                                        className="p-1.5 rounded-md text-muted-foreground hover:text-foreground hover:bg-surface-muted transition-colors"
                                                        title="Edit"
                                                    >
                                                        <Pencil className="w-3.5 h-3.5" />
                                                    </Link>
                                                    <button
                                                        onClick={() => setDeleteTarget({ id: post.id, title: post.title })}
                                                        className="p-1.5 rounded-md text-muted-foreground hover:text-danger hover:bg-danger/10 transition-colors"
                                                        title="Pindah ke Sampah"
                                                    >
                                                        <Trash2 className="w-3.5 h-3.5" />
                                                    </button>
                                                </>
                                            )}
                                        </div>
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>

                    {posts.data.length === 0 && (
                        <div className="flex flex-col items-center justify-center py-16 text-center">
                            <FileText className="w-12 h-12 text-muted-foreground/30 mb-4" />
                            <p className="text-sm font-medium text-foreground">{isTrash ? 'Sampah kosong' : 'Belum ada postingan'}</p>
                            <p className="text-xs text-muted-foreground mt-1">
                                {isTrash ? 'Tidak ada postingan di sampah.' : 'Mulai dengan membuat postingan pertama.'}
                            </p>
                            {!isTrash && (
                                <Link
                                    href="/posts/create"
                                    className="ml-auto inline-flex items-center gap-2 mt-4 h-8 px-3 bg-primary text-primary-foreground rounded-md text-xs font-medium hover:bg-primary/90 transition-all"
                                >
                                    <Plus className="w-3.5 h-3.5" />
                                    Buat Postingan
                                </Link>
                            )}
                        </div>
                    )}
                </div>

                {/* Pagination */}
                {posts.links && posts.links.length > 3 && (
                    <div className="flex items-center justify-end md:justify-between text-sm w-full">
                        <p className="text-xs text-muted-foreground">
                            Menampilkan {posts.from} hingga {posts.to} dari {posts.total} postingan
                        </p>
                        <div className="flex items-center gap-1">
                            {posts.links.map((link: any, i: number) => (
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

            <ConfirmDialog
                open={!!deleteTarget}
                onOpenChange={(open) => { if (!open) setDeleteTarget(null); }}
                title={deleteTarget?.force ? "Hapus Postingan Permanen" : "Pindah ke Sampah"}
message={deleteTarget ? `Apakah Anda yakin ingin ${deleteTarget.force ? 'menghapus permanen' : 'memindahkan'} "${deleteTarget.title}"${deleteTarget.force ? '? Tindakan ini tidak dapat dibatalkan.' : ' ke sampah?'}` : ''}
                                                confirmLabel="Hapus"
                variant="danger"
                onConfirm={handleDelete}
            />

            <ConfirmDialog
                open={showEmptyTrashConfirm}
                onOpenChange={setShowEmptyTrashConfirm}
                title="Kosongkan Sampah"
                message="Apakah Anda yakin ingin menghapus permanen semua postingan di sampah? Tindakan ini tidak dapat dibatalkan."
                confirmLabel="Kosongkan Sampah"
                variant="danger"
                onConfirm={handleEmptyTrash}
            />
        </DashboardLayout>
    );
}
