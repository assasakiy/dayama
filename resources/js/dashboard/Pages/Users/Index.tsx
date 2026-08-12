import React, { useState, useCallback } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import DashboardLayout from '@dashboard/Layouts/DashboardLayout';
import { Button } from '@dashboard/Components/ui/button';
import { Input } from '@dashboard/Components/ui/input';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogClose, DialogFooter } from '@dashboard/Components/ui/dialog';
import ConfirmDialog from '@dashboard/Components/ui/confirm-dialog';
import { Btn } from '@dashboard/Components/ui/btn';
import {
    Plus, Pencil, Trash2, Eye, X, ShieldCheck, Search, Lock,
    CheckCircle2, XCircle, Clock, Filter, UserX, Users as UsersIcon,
    ChevronLeft, ChevronRight, Copy,
} from 'lucide-react';

interface Role {
    id: string;
    name: string;
    display_name?: string;
    color?: string;
    icon?: string;
    scope?: string | null;
}

interface Institution {
    id: string;
    name: string;
}

interface UserItem {
    id: string;
    name: string;
    username?: string;
    email: string;
    avatar_url?: string;
    status: 'active' | 'inactive' | 'banned';
    email_verified_at?: string | null;
    last_login_at?: string | null;
    posts_count: number;
    comments_count: number;
    created_at: string;
    roles: Role[];
    is_primary_super_admin: boolean;
    is_protected: boolean;
    is_verified: boolean;
    highest_rank: number;
    can: {
        update: boolean;
        delete: boolean;
    };
}

interface PaginatedUsers {
    data: UserItem[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from: number;
    to: number;
    links: { url: string | null; label: string; active: boolean }[];
}

interface Filters {
    search?: string;
    role?: string;
    status?: string;
    verified?: string;
}

const STATUS_CONFIG = {
    active:   { label: 'Aktif',   className: 'bg-green-50 text-green-700 border-green-200 dark:bg-green-950/40 dark:text-green-300' },
    inactive: { label: 'Nonaktif', className: 'bg-warning/10 text-warning border-warning/20 dark:bg-yellow-950/40 dark:text-yellow-300' },
    banned:   { label: 'Diblokir',   className: 'bg-red-50 text-red-700 border-red-200 dark:bg-red-950/40 dark:text-red-300' },
};

function UserAvatar({ user }: { user: UserItem }) {
    const safeName = user.name || 'Pengguna Tidak Diketahui';
    const initials = safeName.split(' ').map((n) => n[0]).join('').slice(0, 2).toUpperCase();
    const colors = ['from-violet-500 to-purple-500', 'from-blue-500 to-cyan-500', 'from-green-500 to-emerald-500', 'from-orange-500 to-amber-500', 'from-pink-500 to-rose-500'];
    const color = colors[safeName.charCodeAt(0) % colors.length];

    if (user.avatar_url) {
        return <img src={user.avatar_url} alt={user.name} className="w-8 h-8 rounded-full object-cover ring-2 ring-background" />;
    }
    return (
        <div className={`w-8 h-8 rounded-full bg-gradient-to-tr ${color} flex items-center justify-center text-white text-xs font-bold ring-2 ring-background shrink-0`}>
            {initials}
        </div>
    );
}

export default function UserIndex({ users, roles, filters, institutions }: { users: PaginatedUsers; roles: Role[]; filters: Filters; institutions?: Institution[] }) {
    const [showCreate, setShowCreate] = useState(false);
    const [deleteTarget, setDeleteTarget] = useState<{ id: string; name: string } | null>(null);
    const [selected, setSelected] = useState<string[]>([]);
    const [showBulkRoleModal, setShowBulkRoleModal] = useState(false);
    const [bulkRole, setBulkRole] = useState('');

    // Create form state
    const [name, setName] = useState('');
    const [email, setEmail] = useState('');
    const [password, setPassword] = useState('');
    const [selectedRoles, setSelectedRoles] = useState<string[]>([]);
    const [institutionId, setInstitutionId] = useState('');
    const [errors, setErrors] = useState<Record<string, string>>({});
    const [submitting, setSubmitting] = useState(false);

    const hasLembagaRole = roles.some((r) => r.scope === 'lembaga' && selectedRoles.includes(r.name));

    // Filter state
    const [searchInput, setSearchInput] = useState(filters.search || '');

    const resetForm = () => {
        setName(''); setEmail(''); setPassword('');
        setSelectedRoles([]); setInstitutionId(''); setErrors({}); setSubmitting(false);
    };

    const toggleRole = (roleName: string) => {
        setSelectedRoles((prev) => prev.includes(roleName) ? prev.filter((r) => r !== roleName) : [...prev, roleName]);
    };

    const handleCreate = (e: React.FormEvent) => {
        e.preventDefault();
        setSubmitting(true);
        router.post('/users', { name, email, password, roles: selectedRoles, institution_id: hasLembagaRole ? institutionId || undefined : undefined }, {
            onError: (errs) => { setErrors(errs); setSubmitting(false); },
            onSuccess: () => { setShowCreate(false); resetForm(); },
            onFinish: () => setSubmitting(false),
        });
    };

    const handleDelete = () => {
        if (!deleteTarget) return;
        router.delete(`/users/${deleteTarget.id}`, { preserveScroll: true });
        setDeleteTarget(null);
    };

    const applySearch = useCallback(() => {
        router.get('/users', { ...filters, search: searchInput || undefined }, { preserveState: true, replace: true });
    }, [searchInput, filters]);

    const applyFilter = (key: string, value: string) => {
        router.get('/users', { ...filters, [key]: value || undefined }, { preserveState: true, replace: true });
    };

    const toggleSelect = (id: string) => {
        setSelected((prev) => prev.includes(id) ? prev.filter((s) => s !== id) : [...prev, id]);
    };
    const toggleAll = () => {
        setSelected(selected.length === users.data.length ? [] : users.data.map((u) => u.id));
    };

    const handleBulkDelete = () => {
        router.delete('/users', { data: { ids: selected }, preserveScroll: true });
        setSelected([]);
    };

    const handleBulkRole = () => {
        if (!bulkRole) return;
        router.post('/users/bulk-role', { ids: selected, role: bulkRole }, {
            onSuccess: () => { setShowBulkRoleModal(false); setSelected([]); setBulkRole(''); },
        });
    };

    const formatDate = (dateStr: string | null | undefined) => {
        if (!dateStr) return '—';
        return new Date(dateStr).toLocaleDateString(undefined, { month: 'short', day: 'numeric', year: 'numeric' });
    };

    return (
        <DashboardLayout>
            <Head title="Pengguna" />
            <div className="space-y-5">

                {/* Header */}
                <div className="flex items-center justify-end md:justify-between gap-4 flex-wrap w-full">
                    <div className="hidden md:block">
                        <h1 className="text-xl font-semibold tracking-tight">Pengguna</h1>
                        <p className="hidden lg:block text-sm text-muted-foreground mt-0.5">
                            Total {users.total} pengguna
                        </p>
                    </div>
                    <button
                        onClick={() => setShowCreate(true)}
                        className="inline-flex items-center gap-2 h-9 px-4 bg-primary text-primary-foreground rounded-md text-sm font-medium hover:bg-primary/90 active:bg-primary/80 transition-all shadow-sm"
                    >
                        <Plus className="w-4 h-4" />
                        Pengguna Baru
                    </button>
                </div>

                {/* Filters bar */}
                <div className="flex items-center gap-2 flex-wrap">
                    <div className="relative flex-1 min-w-[220px] max-w-sm">
                        <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-muted-foreground" />
                        <input
                            type="text"
                            value={searchInput}
                            onChange={(e) => setSearchInput(e.target.value)}
                            onKeyDown={(e) => e.key === 'Enter' && applySearch()}
                            placeholder="Cari pengguna..."
                            className="w-full h-9 pl-9 pr-3 text-sm rounded-md border border-border-subtle bg-background focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"
                        />
                    </div>

                    <select
                        value={filters.role || ''}
                        onChange={(e) => applyFilter('role', e.target.value)}
                        className="h-9 px-3 text-sm rounded-md border border-border-subtle bg-background text-foreground focus:outline-none focus:ring-2 focus:ring-primary/30 transition-all"
                    >
                        <option value="">Semua Peran</option>
                        {roles.map((r) => <option key={r.id} value={r.name}>{r.display_name || r.name}</option>)}
                    </select>

                    <select
                        value={filters.status || ''}
                        onChange={(e) => applyFilter('status', e.target.value)}
                        className="h-9 px-3 text-sm rounded-md border border-border-subtle bg-background text-foreground focus:outline-none focus:ring-2 focus:ring-primary/30 transition-all"
                    >
                        <option value="">Semua Status</option>
                        <option value="active">Aktif</option>
                        <option value="inactive">Nonaktif</option>
                        <option value="banned">Diblokir</option>
                    </select>

                    <select
                        value={filters.verified || ''}
                        onChange={(e) => applyFilter('verified', e.target.value)}
                        className="h-9 px-3 text-sm rounded-md border border-border-subtle bg-background text-foreground focus:outline-none focus:ring-2 focus:ring-primary/30 transition-all"
                    >
                        <option value="">Semua Pengguna</option>
                        <option value="verified">Email Terverifikasi</option>
                        <option value="unverified">Belum Terverifikasi</option>
                    </select>
                </div>

                {/* Bulk actions toolbar */}
                {selected.length > 0 && (
                    <div className="flex items-center gap-3 px-4 py-2.5 bg-primary/5 border border-primary/20 rounded-lg">
                        <span className="text-sm font-medium text-primary">{selected.length} terpilih</span>
                        <div className="flex items-center gap-2 ml-auto">
                            <button
                                onClick={() => setShowBulkRoleModal(true)}
                                className="inline-flex items-center gap-1.5 h-8 px-3 text-xs font-medium rounded-md bg-background border border-border-subtle hover:bg-surface-muted transition-colors"
                            >
                                <ShieldCheck className="w-3.5 h-3.5" />
                                Tetapkan Peran
                            </button>
                            <button
                                onClick={handleBulkDelete}
                                className="inline-flex items-center gap-1.5 h-8 px-3 text-xs font-medium rounded-md bg-destructive/10 text-destructive border border-destructive/20 hover:bg-destructive/20 transition-colors"
                            >
                                <Trash2 className="w-3.5 h-3.5" />
                                Hapus
                            </button>
                            <button onClick={() => setSelected([])} className="p-1.5 text-muted-foreground hover:text-foreground transition-colors">
                                <X className="w-4 h-4" />
                            </button>
                        </div>
                    </div>
                )}

                {/* Table */}
                <div className="bg-background border border-border-subtle rounded-xl overflow-hidden shadow-sm">
                    <table className="w-full text-sm">
                        <thead>
                            <tr className="border-b border-border-subtle bg-surface-muted/40">
                                <th className="w-10 px-4 py-3">
                                    <input
                                        type="checkbox"
                                        checked={selected.length === users.data.length && users.data.length > 0}
                                        onChange={toggleAll}
                                        className="w-4 h-4 rounded border-border-subtle accent-primary cursor-pointer"
                                    />
                                </th>
                                <th className="text-left px-4 py-3 font-medium text-xs uppercase tracking-wider text-muted-foreground">Pengguna</th>
                                <th className="text-left px-4 py-3 font-medium text-xs uppercase tracking-wider text-muted-foreground hidden md:table-cell">Peran</th>
                                <th className="text-left px-4 py-3 font-medium text-xs uppercase tracking-wider text-muted-foreground hidden lg:table-cell">Status</th>
                                <th className="text-left px-4 py-3 font-medium text-xs uppercase tracking-wider text-muted-foreground hidden xl:table-cell">Aktivitas</th>
                                <th className="text-left px-4 py-3 font-medium text-xs uppercase tracking-wider text-muted-foreground hidden lg:table-cell">Bergabung</th>
                                <th className="text-right px-4 py-3 font-medium text-xs uppercase tracking-wider text-muted-foreground">Aksi</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-border-subtle">
                            {users.data.map((user) => {
                                const statusCfg = STATUS_CONFIG[user.status] || STATUS_CONFIG.active;
                                return (
                                    <tr key={user.id} className={`hover:bg-surface-muted/30 transition-colors ${selected.includes(user.id) ? 'bg-primary/5' : ''}`}>
                                        <td className="px-4 py-3">
                                            {user.can.delete ? (
                                                <input
                                                    type="checkbox"
                                                    checked={selected.includes(user.id)}
                                                    onChange={() => toggleSelect(user.id)}
                                                    className="w-4 h-4 rounded border-border-subtle accent-primary cursor-pointer"
                                                />
                                            ) : (
                                                <span className="w-4 h-4 block" />
                                            )}
                                        </td>
                                        <td className="px-4 py-3">
                                            <div className="flex items-center gap-3">
                                                <UserAvatar user={user} />
                                                <div className="min-w-0">
                                                    <div className="flex items-center gap-1.5">
                                                        <Link
                                                            href={`/users/${user.id}`}
                                                            className="font-medium hover:text-primary transition-colors truncate block"
                                                        >
                                                            {user.name}
                                                        </Link>
                                                        {user.is_verified && (
                                                            <span title="Pengguna Terverifikasi" className="shrink-0 text-blue-500">
                                                                <CheckCircle2 className="w-3.5 h-3.5 fill-blue-500 text-white" />
                                                            </span>
                                                        )}
                                                    </div>
                                                    <p className="text-xs text-muted-foreground truncate">{user.email}</p>
                                                </div>
                                                {user.is_primary_super_admin ? (
                                                    <span title="Super Admin Utama" className="hidden sm:flex shrink-0 px-1.5 py-0.5 bg-red-100 text-red-700 border border-red-200 rounded text-[10px] font-bold uppercase tracking-wider">
                                                        Utama
                                                    </span>
                                                ) : user.is_protected ? (
                                                    <span title="Akun Terlindungi" className="hidden sm:flex shrink-0 px-1.5 py-0.5 bg-warning/15 text-warning border border-warning/20 rounded text-[10px] font-bold uppercase tracking-wider gap-1 items-center">
                                                        <Lock className="w-3 h-3" /> Terlindungi
                                                    </span>
                                                ) : user.email_verified_at ? (
                                                    <span title="Email terverifikasi" className="hidden sm:flex shrink-0"><CheckCircle2 className="w-3.5 h-3.5 text-green-500" /></span>
                                                ) : (
                                                    <span title="Email belum terverifikasi" className="hidden sm:flex shrink-0"><XCircle className="w-3.5 h-3.5 text-warning" /></span>
                                                )}
                                            </div>
                                        </td>
                                        <td className="px-4 py-3 hidden md:table-cell">
                                            <div className="flex flex-wrap gap-1">
                                                {user.roles.length > 0 ? (
                                                    user.roles.map((r) => (
                                                        <span
                                                            key={r.id}
                                                            className="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-primary/10 text-primary"
                                                            style={r.color ? { backgroundColor: r.color + '20', color: r.color } : {}}
                                                        >
                                                            <ShieldCheck className="w-2.5 h-2.5" />
                                                            {r.display_name || r.name}
                                                        </span>
                                                    ))
                                                ) : (
                                                    <span className="text-xs text-muted-foreground">Tidak ada peran</span>
                                                )}
                                            </div>
                                        </td>
                                        <td className="px-4 py-3 hidden lg:table-cell">
                                            <span className={`inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium border ${statusCfg.className}`}>
                                                {statusCfg.label}
                                            </span>
                                        </td>
                                        <td className="px-4 py-3 hidden xl:table-cell">
                                            <div className="text-xs text-muted-foreground space-y-0.5">
                                                <div>{user.posts_count} postingan</div>
                                                <div className="flex items-center gap-1 text-muted-foreground/70">
                                                    <Clock className="w-3 h-3" />
                                                    {user.last_login_at ? formatDate(user.last_login_at) : 'Tidak Pernah'}
                                                </div>
                                            </div>
                                        </td>
                                        <td className="px-4 py-3 text-muted-foreground text-xs hidden lg:table-cell">
                                            {formatDate(user.created_at)}
                                        </td>
                                        <td className="px-4 py-3 text-right">
                                            <div className="flex items-center justify-end gap-1">
                                                <Link
                                                    href={`/users/${user.id}`}
                                                    className="p-1.5 rounded-md text-muted-foreground hover:text-foreground hover:bg-surface-muted transition-colors"
                                                    title="Lihat"
                                                >
                                                    <Eye className="w-3.5 h-3.5" />
                                                </Link>
                                                {user.can.update && (
                                                    <Link
                                                        href={`/users/${user.id}/edit`}
                                                        className="p-1.5 rounded-md text-muted-foreground hover:text-foreground hover:bg-surface-muted transition-colors"
                                                        title="Edit"
                                                    >
                                                        <Pencil className="w-3.5 h-3.5" />
                                                    </Link>
                                                )}
                                                {user.can.delete && (
                                                    <button
                                                        onClick={() => setDeleteTarget({ id: user.id, name: user.name })}
                                                        className="p-1.5 rounded-md text-muted-foreground hover:text-destructive hover:bg-destructive/10 transition-colors"
                                                        title="Hapus"
                                                    >
                                                        <Trash2 className="w-3.5 h-3.5" />
                                                    </button>
                                                )}
                                            </div>
                                        </td>
                                    </tr>
                                );
                            })}
                        </tbody>
                    </table>

                    {users.data.length === 0 && (
                        <div className="flex flex-col items-center justify-center py-16 text-center">
                            <UsersIcon className="w-12 h-12 text-muted-foreground/20 mb-4" />
                            <p className="text-sm font-medium">Tidak ada pengguna</p>
                            <p className="text-xs text-muted-foreground mt-1">Coba sesuaikan pencarian atau filter Anda.</p>
                        </div>
                    )}
                </div>

                {/* Pagination */}
                {users.last_page > 1 && (
                    <div className="flex items-center justify-end md:justify-between gap-4 text-sm w-full">
                        <p className="text-muted-foreground text-xs">
                            Menampilkan {users.from}–{users.to} dari {users.total} pengguna
                        </p>
                        <div className="flex items-center gap-1">
                            {users.links.map((link, i) => {
                                if (!link.url) return (
                                    <span key={i} className="px-2.5 py-1.5 text-xs rounded-md text-muted-foreground opacity-50 select-none" dangerouslySetInnerHTML={{ __html: link.label }} />
                                );
                                return (
                                    <Link
                                        key={i}
                                        href={link.url}
                                        className={`px-2.5 py-1.5 text-xs rounded-md transition-colors ${link.active ? 'bg-primary text-primary-foreground font-medium' : 'text-foreground hover:bg-surface-muted'}`}
                                        dangerouslySetInnerHTML={{ __html: link.label }}
                                    />
                                );
                            })}
                        </div>
                    </div>
                )}
            </div>

            {/* Create user dialog */}
            <Dialog open={showCreate} onOpenChange={(open) => { setShowCreate(open); if (!open) resetForm(); }}>
                <DialogContent className="flex flex-col p-0 gap-0 max-h-[90vh] max-w-md">
                    <DialogHeader className="flex flex-row items-center justify-between px-6 py-4 border-b border-border-subtle mb-0 shrink-0">
                        <DialogTitle className="text-base">Buat Pengguna</DialogTitle>
                        <DialogClose className="p-1 rounded-md text-muted-foreground hover:text-foreground hover:bg-surface-muted transition-colors">
                            <X className="w-4 h-4" />
                        </DialogClose>
                    </DialogHeader>
                    <form onSubmit={handleCreate} className="flex flex-col flex-1 min-h-0">
                        <div className="space-y-4 px-6 py-4 overflow-y-auto flex-1">
                            <Input label="Nama Lengkap" value={name} onChange={(e) => setName(e.target.value)} error={errors.name} required placeholder="John Doe" />
                            <Input label="Email" type="email" value={email} onChange={(e) => setEmail(e.target.value)} error={errors.email} required placeholder="john@example.com" />
                            <Input label="Kata Sandi" type="password" value={password} onChange={(e) => setPassword(e.target.value)} error={errors.password} required placeholder="Min. 8 karakter" />
                            <div className="space-y-1.5">
                                <label className="text-sm font-medium">Peran</label>
                                <div className="flex flex-wrap gap-2 pt-1">
                                    {roles.map((role) => {
                                        const active = selectedRoles.includes(role.name);
                                        return (
                                            <button
                                                key={role.id}
                                                type="button"
                                                onClick={() => toggleRole(role.name)}
                                                className={`inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md text-xs font-medium border transition-all ${active ? 'bg-primary text-primary-foreground border-primary shadow-sm' : 'bg-background text-muted-foreground border-border-subtle hover:border-primary/50 hover:text-foreground'}`}
                                                style={active && role.color ? { backgroundColor: role.color, borderColor: role.color } : {}}
                                            >
                                                <ShieldCheck className="w-3 h-3" />
                                                {role.display_name || role.name}
                                            </button>
                                        );
                                    })}
                                </div>
                                {errors.roles && <p className="text-xs text-destructive">{errors.roles}</p>}
                            </div>

                            {hasLembagaRole && institutions && institutions.length > 0 && (
                                <div className="space-y-1.5">
                                    <label className="text-sm font-medium">Lembaga <span className="text-destructive">*</span></label>
                                    <select
                                        value={institutionId}
                                        onChange={(e) => setInstitutionId(e.target.value)}
                                        className="flex w-full h-9 rounded-sm border border-border-subtle bg-background px-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary"
                                    >
                                        <option value="">Pilih lembaga...</option>
                                        {institutions.map((inst) => (
                                            <option key={inst.id} value={inst.id}>{inst.name}</option>
                                        ))}
                                    </select>
                                    {errors.institution_id && <p className="text-xs text-destructive">{errors.institution_id}</p>}
                                </div>
                            )}
                        </div>
                        <DialogFooter className="flex items-center justify-end gap-2 px-6 py-4 border-t border-border-subtle mt-0 shrink-0">
                            <Button type="button" variant="outline" onClick={() => setShowCreate(false)}>Batal</Button>
                            <Btn
                                type="submit"
                                disabled={!name || !email || !password || submitting}
                                loading={submitting}
                                icon={<Plus className="w-4 h-4" />}
                                className="h-9 px-4 shadow-sm"
                            >
                                Buat Pengguna
                            </Btn>
                        </DialogFooter>
                    </form>
                </DialogContent>
            </Dialog>

            {/* Bulk assign role dialog */}
            <Dialog open={showBulkRoleModal} onOpenChange={setShowBulkRoleModal}>
                <DialogContent className="max-w-sm p-0 gap-0">
                    <DialogHeader className="flex flex-row items-center justify-between px-6 py-4 border-b border-border-subtle shrink-0">
                        <DialogTitle className="text-base">Tetapkan Peran ke {selected.length} Pengguna</DialogTitle>
                        <DialogClose className="p-1 rounded-md text-muted-foreground hover:text-foreground hover:bg-surface-muted transition-colors">
                            <X className="w-4 h-4" />
                        </DialogClose>
                    </DialogHeader>
                    <div className="px-6 py-4 space-y-3">
                        <p className="text-sm text-muted-foreground">Pilih peran untuk ditetapkan ke pengguna terpilih.</p>
                        <div className="flex flex-wrap gap-2">
                            {roles.map((role) => (
                                <button
                                    key={role.id}
                                    type="button"
                                    onClick={() => setBulkRole(role.name)}
                                    className={`inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md text-xs font-medium border transition-all ${bulkRole === role.name ? 'bg-primary text-primary-foreground border-primary shadow-sm' : 'bg-background text-muted-foreground border-border-subtle hover:border-primary/50'}`}
                                >
                                    <ShieldCheck className="w-3 h-3" />
                                    {role.display_name || role.name}
                                </button>
                            ))}
                        </div>
                    </div>
                    <DialogFooter className="flex items-center justify-end gap-2 px-6 py-4 border-t border-border-subtle">
                            <Button type="button" variant="outline" onClick={() => setShowBulkRoleModal(false)}>Batal</Button>
                            <Btn
                                onClick={handleBulkRole}
                                disabled={!bulkRole}
                                icon={<ShieldCheck className="w-4 h-4" />}
                                className="h-9 px-4 shadow-sm"
                            >
                                Tetapkan Peran
                            </Btn>
                    </DialogFooter>
                </DialogContent>
            </Dialog>

            <ConfirmDialog
                open={!!deleteTarget}
                onOpenChange={(open) => { if (!open) setDeleteTarget(null); }}
                title="Hapus Pengguna"
                message={deleteTarget ? `Apakah Anda yakin ingin menghapus "${deleteTarget.name}"? Tindakan ini tidak dapat dibatalkan.` : ''}
                confirmLabel="Hapus"
                variant="danger"
                onConfirm={handleDelete}
            />
        </DashboardLayout>
    );
}

function formatDate(dateStr: string | null | undefined): string {
    if (!dateStr) return '—';
    return new Date(dateStr).toLocaleDateString(undefined, { month: 'short', day: 'numeric', year: 'numeric' });
}
