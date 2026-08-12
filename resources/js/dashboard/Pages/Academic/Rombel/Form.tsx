import React, { useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import DashboardLayout from '@dashboard/Layouts/DashboardLayout';
import { Input } from '@dashboard/Components/ui/input';
import { Btn } from '@dashboard/Components/ui/btn';
import { ArrowLeft, Save } from 'lucide-react';

interface AcademicYear {
    id: string;
    nama: string;
    is_active: boolean;
}

interface Person {
    id: string;
    nama_lengkap: string;
}

interface Classroom {
    id: string;
    nama: string;
    tingkat: string | null;
    academic_year_id: string;
    wali_kelas_person_id: string | null;
}

interface Props {
    rombel?: Classroom;
    academicYears: AcademicYear[];
    teachers: Person[];
}

function Form({ rombel, academicYears, teachers }: Props) {
    const isEditing = !!rombel;
    const [nama, setNama] = useState(rombel?.nama || '');
    const [tingkat, setTingkat] = useState(rombel?.tingkat || '');
    const [academicYearId, setAcademicYearId] = useState(rombel?.academic_year_id || '');
    const [waliKelasPersonId, setWaliKelasPersonId] = useState(rombel?.wali_kelas_person_id || '');
    const [saving, setSaving] = useState(false);
    const [errors, setErrors] = useState<Record<string, string>>({});

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        setSaving(true);

        const data = { nama, tingkat, academic_year_id: academicYearId, wali_kelas_person_id: waliKelasPersonId || null };
        const url = isEditing ? `/academic/rombel/${rombel.id}` : '/academic/rombel';
        const method = isEditing ? 'put' : 'post';

        router[method](url, data, {
            onSuccess: () => setSaving(false),
            onError: (err) => {
                setErrors(err);
                setSaving(false);
            },
        });
    };

    return (
        <>
            <Head title={isEditing ? 'Edit Rombel' : 'Tambah Rombel'} />

            <div className="max-w-2xl mx-auto">
                <Link
                    href="/academic/rombel"
                    className="inline-flex items-center gap-2 text-sm text-muted-foreground hover:text-foreground transition-colors mb-6"
                >
                    <ArrowLeft className="w-4 h-4" />
                    Kembali
                </Link>

                <h1 className="text-2xl font-bold mb-1">
                    {isEditing ? 'Edit Rombel' : 'Tambah Rombel'}
                </h1>
                <p className="text-muted-foreground text-sm mb-8">
                    {isEditing ? 'Ubah informasi rombongan belajar' : 'Buat rombongan belajar baru'}
                </p>

                <form onSubmit={handleSubmit} className="space-y-6">
                    <div className="bg-background rounded-xl border border-border-subtle p-6 space-y-5">
                        <div>
                            <label className="block text-sm font-medium mb-1.5">Nama Rombel</label>
                            <Input
                                value={nama}
                                onChange={(e) => setNama(e.target.value)}
                                placeholder="Contoh: X-A"
                                className={errors.nama ? 'border-destructive' : ''}
                            />
                            {errors.nama && <p className="text-xs text-destructive mt-1">{errors.nama}</p>}
                        </div>

                        <div>
                            <label className="block text-sm font-medium mb-1.5">Tingkat</label>
                            <Input
                                value={tingkat}
                                onChange={(e) => setTingkat(e.target.value)}
                                placeholder="Contoh: 10, XI, atau 1"
                                className={errors.tingkat ? 'border-destructive' : ''}
                            />
                            {errors.tingkat && <p className="text-xs text-destructive mt-1">{errors.tingkat}</p>}
                        </div>

                        <div>
                            <label className="block text-sm font-medium mb-1.5">Tahun Ajaran</label>
                            <select
                                value={academicYearId}
                                onChange={(e) => setAcademicYearId(e.target.value)}
                                className={`flex h-9 w-full rounded-md border border-border-subtle bg-background px-3 py-1.5 text-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-primary ${errors.academic_year_id ? 'border-destructive' : ''}`}
                            >
                                <option value="">Pilih Tahun Ajaran</option>
                                {academicYears.map((ay) => (
                                    <option key={ay.id} value={ay.id}>
                                        {ay.nama} {ay.is_active ? '(Aktif)' : ''}
                                    </option>
                                ))}
                            </select>
                            {errors.academic_year_id && <p className="text-xs text-destructive mt-1">{errors.academic_year_id}</p>}
                        </div>

                        <div>
                            <label className="block text-sm font-medium mb-1.5">Wali Kelas</label>
                            <select
                                value={waliKelasPersonId}
                                onChange={(e) => setWaliKelasPersonId(e.target.value)}
                                className={`flex h-9 w-full rounded-md border border-border-subtle bg-background px-3 py-1.5 text-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-primary ${errors.wali_kelas_person_id ? 'border-destructive' : ''}`}
                            >
                                <option value="">Pilih Wali Kelas (Opsional)</option>
                                {teachers.map((t) => (
                                    <option key={t.id} value={t.id}>
                                        {t.nama_lengkap}
                                    </option>
                                ))}
                            </select>
                            {errors.wali_kelas_person_id && <p className="text-xs text-destructive mt-1">{errors.wali_kelas_person_id}</p>}
                        </div>
                    </div>

                    <div className="flex items-center justify-end gap-3">
                        <Link href="/academic/rombel">
                            <Btn variant="outline" type="button">Batal</Btn>
                        </Link>
                        <Btn variant="primary" type="submit" disabled={saving}>
                            <Save className="w-4 h-4 mr-2" />
                            {saving ? 'Menyimpan...' : 'Simpan'}
                        </Btn>
                    </div>
                </form>
            </div>
        </>
    );
}

Form.layout = (page: React.ReactNode) => <DashboardLayout>{page}</DashboardLayout>;

export default Form;
