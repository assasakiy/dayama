import React, { useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import DashboardLayout from '@dashboard/Layouts/DashboardLayout';
import { Input } from '@dashboard/Components/ui/input';
import { Switch } from '@dashboard/Components/ui/switch';
import { Btn } from '@dashboard/Components/ui/btn';
import { ArrowLeft, Save } from 'lucide-react';

interface AcademicYear {
    id: string;
    nama: string;
}

interface Semester {
    id: string;
    academic_year_id: string;
    name: string;
    start_date: string;
    end_date: string;
    is_active: boolean;
}

interface Props {
    semester?: Semester;
    academicYears: AcademicYear[];
}

function Form({ semester, academicYears }: Props) {
    const isEditing = !!semester;
    const [academicYearId, setAcademicYearId] = useState(semester?.academic_year_id || '');
    const [name, setName] = useState(semester?.name || '');
    const [startDate, setStartDate] = useState(semester?.start_date?.split('T')[0] || '');
    const [endDate, setEndDate] = useState(semester?.end_date?.split('T')[0] || '');
    const [isActive, setIsActive] = useState(semester?.is_active || false);
    const [saving, setSaving] = useState(false);
    const [errors, setErrors] = useState<Record<string, string>>({});

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        setSaving(true);

        const data = {
            academic_year_id: academicYearId,
            name,
            start_date: startDate,
            end_date: endDate,
            is_active: isActive,
        };
        const url = isEditing ? `/academic/semesters/${semester.id}` : '/academic/semesters';
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
            <Head title={isEditing ? 'Edit Semester' : 'Tambah Semester'} />

            <div className="max-w-2xl mx-auto">
                <Link href="/academic/semesters" className="inline-flex items-center gap-2 text-sm text-muted-foreground hover:text-foreground transition-colors mb-6">
                    <ArrowLeft className="w-4 h-4" />
                    Kembali
                </Link>

                <h1 className="text-2xl font-bold mb-1">{isEditing ? 'Edit Semester' : 'Tambah Semester'}</h1>
                <p className="text-muted-foreground text-sm mb-8">
                    {isEditing ? 'Ubah informasi semester' : 'Buat semester baru'}
                </p>

                <form onSubmit={handleSubmit} className="space-y-6">
                    <div className="bg-background rounded-xl border border-border-subtle p-6 space-y-5">
                        <div>
                            <label className="block text-sm font-medium mb-1.5">Tahun Ajaran</label>
                            <select
                                value={academicYearId}
                                onChange={(e) => setAcademicYearId(e.target.value)}
                                className={`w-full h-10 px-3 rounded-lg border ${errors.academic_year_id ? 'border-destructive' : 'border-border-subtle'} bg-background text-sm focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-colors`}
                            >
                                <option value="">Pilih tahun ajaran</option>
                                {academicYears.map((ay) => (
                                    <option key={ay.id} value={ay.id}>{ay.nama}</option>
                                ))}
                            </select>
                            {errors.academic_year_id && <p className="text-xs text-destructive mt-1">{errors.academic_year_id}</p>}
                        </div>

                        <div>
                            <label className="block text-sm font-medium mb-1.5">Nama Semester</label>
                            <Input
                                value={name}
                                onChange={(e) => setName(e.target.value)}
                                placeholder="Contoh: Ganjil 2026/2027"
                                className={errors.name ? 'border-destructive' : ''}
                            />
                            {errors.name && <p className="text-xs text-destructive mt-1">{errors.name}</p>}
                        </div>

                        <div className="grid grid-cols-2 gap-4">
                            <div>
                                <label className="block text-sm font-medium mb-1.5">Tanggal Mulai</label>
                                <Input
                                    type="date"
                                    value={startDate}
                                    onChange={(e) => setStartDate(e.target.value)}
                                    className={errors.start_date ? 'border-destructive' : ''}
                                />
                                {errors.start_date && <p className="text-xs text-destructive mt-1">{errors.start_date}</p>}
                            </div>
                            <div>
                                <label className="block text-sm font-medium mb-1.5">Tanggal Selesai</label>
                                <Input
                                    type="date"
                                    value={endDate}
                                    onChange={(e) => setEndDate(e.target.value)}
                                    className={errors.end_date ? 'border-destructive' : ''}
                                />
                                {errors.end_date && <p className="text-xs text-destructive mt-1">{errors.end_date}</p>}
                            </div>
                        </div>

                        <div className="flex items-center justify-between">
                            <div>
                                <label className="text-sm font-medium">Aktif</label>
                                <p className="text-xs text-muted-foreground mt-0.5">Semester aktif akan digunakan sebagai default</p>
                            </div>
                            <Switch checked={isActive} onCheckedChange={setIsActive} />
                        </div>
                    </div>

                    <div className="flex items-center justify-end gap-3">
                        <Link href="/academic/semesters"><Btn variant="outline" type="button">Batal</Btn></Link>
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
