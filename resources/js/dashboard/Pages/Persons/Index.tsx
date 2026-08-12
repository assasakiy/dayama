import React, { useState, useCallback } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import DashboardLayout from '@dashboard/Layouts/DashboardLayout';
import { Search, Users, UserCheck, Pencil, GraduationCap, Briefcase } from 'lucide-react';

interface Position {
    id: string;
    nama: string;
    slug: string;
}

interface PersonItem {
    id: string;
    nama_lengkap: string;
    gender: 'L' | 'P' | null;
    tempat_lahir: string | null;
    tanggal_lahir: string | null;
    nik: string | null;
    photo: string | null;
    status_hidup: boolean;
    positions_count: number;
    created_at: string;
    has_user: boolean;
}

interface PaginatedPersons {
    data: PersonItem[];
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
    gender?: string;
    position?: string;
}

function PersonAvatar({ person }: { person: PersonItem }) {
    const safeName = person.nama_lengkap || 'Unknown';
    const initials = safeName.split(' ').map((n) => n[0]).join('').slice(0, 2).toUpperCase();
    const colors = ['from-violet-500 to-purple-500', 'from-blue-500 to-cyan-500', 'from-green-500 to-emerald-500', 'from-orange-500 to-amber-500', 'from-pink-500 to-rose-500'];
    const color = colors[safeName.charCodeAt(0) % colors.length];

    if (person.photo) {
        return <img src={person.photo} alt={person.nama_lengkap} className="w-8 h-8 rounded-full object-cover ring-2 ring-background" />;
    }
    return (
        <div className={`w-8 h-8 rounded-full bg-gradient-to-tr ${color} flex items-center justify-center text-white text-xs font-bold ring-2 ring-background shrink-0`}>
            {initials}
        </div>
    );
}

function formatDate(d: string | null | undefined) {
    if (!d) return '—';
    return new Date(d).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
}

function ageFromDate(d: string | null | undefined) {
    if (!d) return null;
    const diff = Date.now() - new Date(d).getTime();
    return Math.floor(diff / (365.25 * 24 * 60 * 60 * 1000));
}

export default function PersonsIndex({ persons, positions, filters }: { persons: PaginatedPersons; positions: Position[]; filters: Filters }) {
    const [searchInput, setSearchInput] = useState(filters.search || '');

    const applySearch = useCallback(() => {
        router.get('/persons', { ...filters, search: searchInput || undefined }, { preserveState: true, replace: true });
    }, [searchInput, filters]);

    const applyFilter = (key: string, value: string) => {
        router.get('/persons', { ...filters, [key]: value || undefined }, { preserveState: true, replace: true });
    };

    const stats = {
        total: persons.total,
        laki: 0,
        perempuan: 0,
        punya_akun: 0,
    };
    persons.data.forEach((p) => {
        if (p.gender === 'L') stats.laki++;
        if (p.gender === 'P') stats.perempuan++;
        if (p.has_user) stats.punya_akun++;
    });

    return (
        <DashboardLayout>
            <Head title="Data Person" />
            <div className="space-y-5">
                <div>
                    <h1 className="text-xl font-semibold tracking-tight">Data Person</h1>
                    <p className="text-sm text-muted-foreground mt-0.5">
                        Cari dan telusuri data person. Kelola data lengkap dari menu Guru & Staf atau Siswa.
                    </p>
                </div>

                {/* Stats */}
                <div className="grid grid-cols-2 md:grid-cols-4 gap-3">
                    <div className="bg-background border border-border-subtle rounded-xl p-4">
                        <div className="flex items-center gap-3">
                            <div className="w-9 h-9 rounded-lg bg-primary/10 flex items-center justify-center shrink-0">
                                <Users className="w-4 h-4 text-primary" />
                            </div>
                            <div>
                                <p className="text-xs text-muted-foreground">Total</p>
                                <p className="text-lg font-bold">{persons.total}</p>
                            </div>
                        </div>
                    </div>
                    <div className="bg-background border border-border-subtle rounded-xl p-4">
                        <div className="flex items-center gap-3">
                            <div className="w-9 h-9 rounded-lg bg-blue-50 flex items-center justify-center shrink-0">
                                <Users className="w-4 h-4 text-blue-600" />
                            </div>
                            <div>
                                <p className="text-xs text-muted-foreground">Laki-laki</p>
                                <p className="text-lg font-bold">{stats.laki}</p>
                            </div>
                        </div>
                    </div>
                    <div className="bg-background border border-border-subtle rounded-xl p-4">
                        <div className="flex items-center gap-3">
                            <div className="w-9 h-9 rounded-lg bg-pink-50 flex items-center justify-center shrink-0">
                                <Users className="w-4 h-4 text-pink-600" />
                            </div>
                            <div>
                                <p className="text-xs text-muted-foreground">Perempuan</p>
                                <p className="text-lg font-bold">{stats.perempuan}</p>
                            </div>
                        </div>
                    </div>
                    <div className="bg-background border border-border-subtle rounded-xl p-4">
                        <div className="flex items-center gap-3">
                            <div className="w-9 h-9 rounded-lg bg-green-50 flex items-center justify-center shrink-0">
                                <UserCheck className="w-4 h-4 text-green-600" />
                            </div>
                            <div>
                                <p className="text-xs text-muted-foreground">Punya Akun</p>
                                <p className="text-lg font-bold">{stats.punya_akun}</p>
                            </div>
                        </div>
                    </div>
                </div>

                {/* Filters */}
                <div className="flex items-center gap-2 flex-wrap">
                    <div className="relative flex-1 min-w-[220px] max-w-sm">
                        <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-muted-foreground" />
                        <input
                            type="text"
                            value={searchInput}
                            onChange={(e) => setSearchInput(e.target.value)}
                            onKeyDown={(e) => e.key === 'Enter' && applySearch()}
                            placeholder="Cari nama atau NIK..."
                            className="w-full h-9 pl-9 pr-3 text-sm rounded-md border border-border-subtle bg-background focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"
                        />
                    </div>
                    <select
                        value={filters.gender || ''}
                        onChange={(e) => applyFilter('gender', e.target.value)}
                        className="h-9 px-3 text-sm rounded-md border border-border-subtle bg-background text-foreground focus:outline-none focus:ring-2 focus:ring-primary/30 transition-all"
                    >
                        <option value="">Semua Gender</option>
                        <option value="L">Laki-laki</option>
                        <option value="P">Perempuan</option>
                    </select>
                    <select
                        value={filters.position || ''}
                        onChange={(e) => applyFilter('position', e.target.value)}
                        className="h-9 px-3 text-sm rounded-md border border-border-subtle bg-background text-foreground focus:outline-none focus:ring-2 focus:ring-primary/30 transition-all"
                    >
                        <option value="">Semua Jabatan</option>
                        {positions.map((p) => <option key={p.id} value={p.slug}>{p.nama}</option>)}
                    </select>
                </div>

                {/* Table */}
                <div className="bg-background border border-border-subtle rounded-xl overflow-hidden shadow-sm">
                    <table className="w-full text-sm">
                        <thead>
                            <tr className="border-b border-border-subtle bg-surface-muted/40">
                                <th className="text-left px-4 py-3 font-medium text-xs uppercase tracking-wider text-muted-foreground">Nama</th>
                                <th className="text-left px-4 py-3 font-medium text-xs uppercase tracking-wider text-muted-foreground hidden md:table-cell">Gender</th>
                                <th className="text-left px-4 py-3 font-medium text-xs uppercase tracking-wider text-muted-foreground hidden lg:table-cell">TTL</th>
                                <th className="text-left px-4 py-3 font-medium text-xs uppercase tracking-wider text-muted-foreground hidden lg:table-cell">NIK</th>
                                <th className="text-left px-4 py-3 font-medium text-xs uppercase tracking-wider text-muted-foreground hidden xl:table-cell">Jabatan</th>
                                <th className="text-right px-4 py-3 font-medium text-xs uppercase tracking-wider text-muted-foreground">Detail</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-border-subtle">
                            {persons.data.map((person) => {
                                const age = ageFromDate(person.tanggal_lahir);
                                return (
                                    <tr key={person.id} className="hover:bg-surface-muted/30 transition-colors">
                                        <td className="px-4 py-3">
                                            <div className="flex items-center gap-3">
                                                <PersonAvatar person={person} />
                                                <div className="min-w-0">
                                                    <div className="flex items-center gap-1.5">
                                                        <span className="font-medium truncate">{person.nama_lengkap}</span>
                                                        {person.has_user && (
                                                            <span title="Punya akun" className="shrink-0 text-blue-500">
                                                                <UserCheck className="w-3.5 h-3.5" />
                                                            </span>
                                                        )}
                                                        {!person.status_hidup && (
                                                            <span className="shrink-0 px-1.5 py-0.5 bg-muted text-muted-foreground rounded text-[10px] font-medium">Almarhum</span>
                                                        )}
                                                    </div>
                                                    <p className="text-xs text-muted-foreground">{person.nik || '—'}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td className="px-4 py-3 hidden md:table-cell">
                                            <span className={`inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium border ${person.gender === 'L' ? 'bg-blue-50 text-blue-700 border-blue-200' : person.gender === 'P' ? 'bg-pink-50 text-pink-700 border-pink-200' : 'bg-muted text-muted-foreground border-border-subtle'}`}>
                                                {person.gender === 'L' ? 'Laki-laki' : person.gender === 'P' ? 'Perempuan' : '—'}
                                            </span>
                                        </td>
                                        <td className="px-4 py-3 text-xs text-muted-foreground hidden lg:table-cell">
                                            {person.tempat_lahir && <span>{person.tempat_lahir}, </span>}
                                            {person.tanggal_lahir ? (
                                                <span>{formatDate(person.tanggal_lahir)}{age !== null ? ` (${age} th)` : ''}</span>
                                            ) : '—'}
                                        </td>
                                        <td className="px-4 py-3 text-xs font-mono text-muted-foreground hidden lg:table-cell">
                                            {person.nik || '—'}
                                        </td>
                                        <td className="px-4 py-3 hidden xl:table-cell">
                                            <span className="text-xs text-muted-foreground">
                                                {person.positions_count > 0 ? `${person.positions_count} jabatan` : '—'}
                                            </span>
                                        </td>
                                        <td className="px-4 py-3 text-right">
                                            <Link
                                                href={`/persons/${person.id}/edit`}
                                                className="inline-flex items-center gap-1.5 h-8 px-3 text-xs border border-border-subtle rounded-md text-muted-foreground hover:text-foreground hover:border-primary/50 transition-all"
                                            >
                                                <Pencil className="w-3 h-3" />
                                                Detail
                                            </Link>
                                        </td>
                                    </tr>
                                );
                            })}
                        </tbody>
                    </table>

                    {persons.data.length === 0 && (
                        <div className="flex flex-col items-center justify-center py-16 text-center">
                            <Users className="w-12 h-12 text-muted-foreground/20 mb-4" />
                            <p className="text-sm font-medium">Data tidak ditemukan</p>
                            <p className="text-xs text-muted-foreground mt-1">Coba ubah filter pencarian.</p>
                        </div>
                    )}
                </div>

                {/* Pagination */}
                {persons.last_page > 1 && (
                    <div className="flex items-center justify-end md:justify-between gap-4 text-sm">
                        <p className="text-muted-foreground text-xs hidden md:block">
                            Menampilkan {persons.from}–{persons.to} dari {persons.total} orang
                        </p>
                        <div className="flex items-center gap-1">
                            {persons.links.map((link, i) => {
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
        </DashboardLayout>
    );
}
