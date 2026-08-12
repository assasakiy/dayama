import React, { useState } from 'react';
import { Head, router } from '@inertiajs/react';
import DashboardLayout from '@dashboard/Layouts/DashboardLayout';
import { Plus, Pencil, Trash2, Building2 } from 'lucide-react';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogFooter } from '@dashboard/Components/ui/dialog';
import { Input } from '@dashboard/Components/ui/input';
import { Textarea } from '@dashboard/Components/ui/textarea';
import { Switch } from '@dashboard/Components/ui/switch';
import { Btn } from '@dashboard/Components/ui/btn';
import ConfirmDialog from '@dashboard/Components/ui/confirm-dialog';

interface Department {
    id: string;
    name: string;
    code: string | null;
    description: string | null;
    sort_order: number;
    is_active: boolean;
    parent: { id: string; name: string } | null;
    head_employee: { id: string; person: { nama_lengkap: string } } | null;
}

interface EmployeeOption {
    id: string;
    name: string;
}

interface DepartmentOption {
    id: string;
    name: string;
}

interface Props {
    departments: Department[];
    allDepartments: DepartmentOption[];
    employees: EmployeeOption[];
}

function Index({ departments, allDepartments, employees }: Props) {
    const [deleteTarget, setDeleteTarget] = useState<{ id: string; name: string } | null>(null);
    const [showModal, setShowModal] = useState(false);
    const [editingDept, setEditingDept] = useState<Department | null>(null);
    const [name, setName] = useState('');
    const [code, setCode] = useState('');
    const [description, setDescription] = useState('');
    const [parentId, setParentId] = useState('');
    const [headEmployeeId, setHeadEmployeeId] = useState('');
    const [sortOrder, setSortOrder] = useState('');
    const [isActive, setIsActive] = useState(true);
    const [saving, setSaving] = useState(false);
    const [errors, setErrors] = useState<Record<string, string>>({});

    const openCreate = () => {
        setEditingDept(null);
        setName('');
        setCode('');
        setDescription('');
        setParentId('');
        setHeadEmployeeId('');
        setSortOrder('');
        setIsActive(true);
        setErrors({});
        setShowModal(true);
    };

    const openEdit = (dept: Department) => {
        setEditingDept(dept);
        setName(dept.name);
        setCode(dept.code || '');
        setDescription(dept.description || '');
        setParentId(dept.parent?.id || '');
        setHeadEmployeeId(dept.head_employee?.id || '');
        setSortOrder(dept.sort_order?.toString() || '');
        setIsActive(dept.is_active);
        setErrors({});
        setShowModal(true);
    };

    const handleDelete = () => {
        if (!deleteTarget) return;
        router.delete(`/hr/departments/${deleteTarget.id}`, {
            onSuccess: () => setDeleteTarget(null),
        });
    };

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        setSaving(true);

        const data = {
            name,
            code: code || null,
            description: description || null,
            parent_id: parentId || null,
            head_employee_id: headEmployeeId || null,
            sort_order: sortOrder ? parseInt(sortOrder, 10) : 0,
            is_active: isActive,
        };

        const url = editingDept ? `/hr/departments/${editingDept.id}` : '/hr/departments';
        const method = editingDept ? 'put' : 'post';

        router[method](url, data, {
            preserveScroll: true,
            onSuccess: () => {
                setShowModal(false);
                setSaving(false);
            },
            onError: (err) => {
                setErrors(err);
                setSaving(false);
            },
        });
    };

    return (
        <>
            <Head title="Departemen" />

            <div className="flex items-center justify-between mb-6">
                <div>
                    <h1 className="text-2xl font-bold">Departemen</h1>
                    <p className="text-muted-foreground text-sm mt-1">
                        Kelola struktur departemen organisasi
                    </p>
                </div>
                <Btn variant="primary" onClick={openCreate}>
                    <Plus className="w-4 h-4 mr-2" />
                    Tambah Departemen
                </Btn>
            </div>

            <div className="bg-background rounded-xl border border-border-subtle overflow-hidden">
                <table className="w-full">
                    <thead>
                        <tr className="border-b border-border-subtle bg-surface/50">
                            <th className="text-left px-6 py-3 text-xs font-semibold uppercase tracking-widest text-muted-foreground/60">Departemen</th>
                            <th className="text-left px-6 py-3 text-xs font-semibold uppercase tracking-widest text-muted-foreground/60">Kode</th>
                            <th className="text-left px-6 py-3 text-xs font-semibold uppercase tracking-widest text-muted-foreground/60">Parent</th>
                            <th className="text-left px-6 py-3 text-xs font-semibold uppercase tracking-widest text-muted-foreground/60">Kepala</th>
                            <th className="text-center px-6 py-3 text-xs font-semibold uppercase tracking-widest text-muted-foreground/60">Urutan</th>
                            <th className="text-center px-6 py-3 text-xs font-semibold uppercase tracking-widest text-muted-foreground/60">Status</th>
                            <th className="text-right px-6 py-3 text-xs font-semibold uppercase tracking-widest text-muted-foreground/60">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        {departments.length === 0 ? (
                            <tr>
                                <td colSpan={7} className="px-6 py-12 text-center text-muted-foreground">
                                    <Building2 className="w-10 h-10 mx-auto mb-3 opacity-40" />
                                    <p>Belum ada departemen.</p>
                                    <button onClick={openCreate} className="text-primary text-sm mt-1 inline-block hover:underline">
                                        Tambah departemen baru
                                    </button>
                                </td>
                            </tr>
                        ) : (
                            departments.map((dept) => (
                                <tr key={dept.id} className="border-b border-border-subtle last:border-0 hover:bg-surface/30 transition-colors">
                                    <td className="px-6 py-4">
                                        <div className="flex items-center gap-3">
                                            <div className="w-9 h-9 rounded-lg bg-primary/10 flex items-center justify-center">
                                                <Building2 className="w-4 h-4 text-primary" />
                                            </div>
                                            <div>
                                                <p className="font-medium text-sm">{dept.name}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td className="px-6 py-4 text-sm text-muted-foreground">{dept.code || '—'}</td>
                                    <td className="px-6 py-4 text-sm text-muted-foreground">{dept.parent?.name || '—'}</td>
                                    <td className="px-6 py-4 text-sm text-muted-foreground">{dept.head_employee?.person?.nama_lengkap || '—'}</td>
                                    <td className="px-6 py-4 text-sm text-center text-muted-foreground">{dept.sort_order}</td>
                                    <td className="px-6 py-4 text-center">
                                        <span className={`inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium ${
                                            dept.is_active
                                                ? 'bg-green-50 text-green-700 border border-green-200 dark:bg-green-950/40 dark:text-green-300'
                                                : 'bg-gray-50 text-gray-500 border border-gray-200 dark:bg-gray-900/40 dark:text-gray-400'
                                        }`}>
                                            {dept.is_active ? 'Aktif' : 'Tidak Aktif'}
                                        </span>
                                    </td>
                                    <td className="px-6 py-4 text-right">
                                        <div className="flex items-center justify-end gap-1">
                                            <button
                                                onClick={() => openEdit(dept)}
                                                className="p-2 rounded-lg text-muted-foreground hover:bg-surface-muted hover:text-foreground transition-colors"
                                            >
                                                <Pencil className="w-4 h-4" />
                                            </button>
                                            <button
                                                onClick={() => setDeleteTarget({ id: dept.id, name: dept.name })}
                                                className="p-2 rounded-lg text-muted-foreground hover:bg-destructive/10 hover:text-destructive transition-colors"
                                            >
                                                <Trash2 className="w-4 h-4" />
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            ))
                        )}
                    </tbody>
                </table>
            </div>

            <Dialog open={showModal} onOpenChange={(open) => { if (!open) setShowModal(false); }}>
                <DialogContent>
                    <DialogHeader>
                        <DialogTitle>{editingDept ? 'Edit Departemen' : 'Tambah Departemen'}</DialogTitle>
                    </DialogHeader>
                    <form onSubmit={handleSubmit} className="px-6 py-4 space-y-5 overflow-y-auto">
                        <div>
                            <label className="block text-sm font-medium mb-1.5">
                                Nama Departemen <span className="text-destructive">*</span>
                            </label>
                            <Input
                                value={name}
                                onChange={(e) => setName(e.target.value)}
                                placeholder="Nama departemen"
                                className={errors.name ? 'border-destructive' : ''}
                            />
                            {errors.name && <p className="text-xs text-destructive mt-1">{errors.name}</p>}
                        </div>
                        <div className="grid grid-cols-2 gap-4">
                            <div>
                                <label className="block text-sm font-medium mb-1.5">Kode</label>
                                <Input value={code} onChange={(e) => setCode(e.target.value)} placeholder="Kode (opsional)" className={errors.code ? 'border-destructive' : ''} />
                                {errors.code && <p className="text-xs text-destructive mt-1">{errors.code}</p>}
                            </div>
                            <div>
                                <label className="block text-sm font-medium mb-1.5">Urutan</label>
                                <Input type="number" value={sortOrder} onChange={(e) => setSortOrder(e.target.value)} placeholder="0" min={0} />
                            </div>
                        </div>
                        <div>
                            <label className="block text-sm font-medium mb-1.5">Deskripsi</label>
                            <Textarea value={description} onChange={(e) => setDescription(e.target.value)} placeholder="Deskripsi departemen (opsional)" />
                        </div>
                        <div className="grid grid-cols-2 gap-4">
                            <div>
                                <label className="block text-sm font-medium mb-1.5">Parent Departemen</label>
                                <select
                                    value={parentId}
                                    onChange={(e) => setParentId(e.target.value)}
                                    className="flex h-10 w-full rounded-lg border border-border-subtle bg-background px-3 py-2 text-sm"
                                >
                                    <option value="">Tidak ada parent</option>
                                    {allDepartments.filter((d) => d.id !== editingDept?.id).map((d) => (
                                        <option key={d.id} value={d.id}>{d.name}</option>
                                    ))}
                                </select>
                            </div>
                            <div>
                                <label className="block text-sm font-medium mb-1.5">Kepala Departemen</label>
                                <select
                                    value={headEmployeeId}
                                    onChange={(e) => setHeadEmployeeId(e.target.value)}
                                    className="flex h-10 w-full rounded-lg border border-border-subtle bg-background px-3 py-2 text-sm"
                                >
                                    <option value="">Pilih Kepala</option>
                                    {employees.map((emp) => (
                                        <option key={emp.id} value={emp.id}>{emp.name}</option>
                                    ))}
                                </select>
                            </div>
                        </div>
                        <div className="flex items-center justify-between">
                            <div>
                                <label className="text-sm font-medium">Status Aktif</label>
                                <p className="text-xs text-muted-foreground mt-0.5">Nonaktifkan jika departemen tidak digunakan</p>
                            </div>
                            <Switch checked={isActive} onCheckedChange={setIsActive} />
                        </div>
                    </form>
                    <DialogFooter>
                        <Btn variant="outline" type="button" onClick={() => setShowModal(false)}>Batal</Btn>
                        <Btn variant="primary" type="submit" onClick={handleSubmit} disabled={saving}>
                            {saving ? 'Menyimpan...' : 'Simpan'}
                        </Btn>
                    </DialogFooter>
                </DialogContent>
            </Dialog>

            <ConfirmDialog
                open={!!deleteTarget}
                onOpenChange={() => setDeleteTarget(null)}
                onConfirm={handleDelete}
                title="Hapus Departemen"
                description={`Apakah Anda yakin ingin menghapus departemen "${deleteTarget?.name}"?`}
            />
        </>
    );
}

Index.layout = (page: React.ReactNode) => <DashboardLayout>{page}</DashboardLayout>;

export default Index;
