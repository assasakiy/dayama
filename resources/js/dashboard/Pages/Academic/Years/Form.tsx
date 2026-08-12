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
    is_active: boolean;
}

interface Props {
    year?: AcademicYear;
}

function Form({ year }: Props) {
    const isEditing = !!year;
    const [nama, setNama] = useState(year?.nama || '');
    const [isActive, setIsActive] = useState(year?.is_active || false);
    const [saving, setSaving] = useState(false);
    const [errors, setErrors] = useState<Record<string, string>>({});

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        setSaving(true);

        const data = { nama, is_active: isActive };
        const url = isEditing ? `/academic/years/${year.id}` : '/academic/years';
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
            <Head title={isEditing ? 'Edit Tahun Ajaran' : 'Tambah Tahun Ajaran'} />

            <div className="max-w-2xl mx-auto">
                <Link
                    href="/academic/years"
                    className="inline-flex items-center gap-2 text-sm text-muted-foreground hover:text-foreground transition-colors mb-6"
                >
                    <ArrowLeft className="w-4 h-4" />
                    Kembali
                </Link>

                <h1 className="text-2xl font-bold mb-1">
                    {isEditing ? 'Edit Tahun Ajaran' : 'Tambah Tahun Ajaran'}
                </h1>
                <p className="text-muted-foreground text-sm mb-8">
                    {isEditing
                        ? 'Ubah informasi tahun ajaran'
                        : 'Buat tahun ajaran baru untuk akademik'}
                </p>

                <form onSubmit={handleSubmit} className="space-y-6">
                    <div className="bg-background rounded-xl border border-border-subtle p-6 space-y-5">
                        <div>
                            <label className="block text-sm font-medium mb-1.5">
                                Nama Tahun Ajaran
                            </label>
                            <Input
                                value={nama}
                                onChange={(e) => setNama(e.target.value)}
                                placeholder="Contoh: 2026/2027"
                                className={errors.nama ? 'border-destructive' : ''}
                            />
                            {errors.nama && (
                                <p className="text-xs text-destructive mt-1">{errors.nama}</p>
                            )}
                        </div>

                        <div className="flex items-center justify-between">
                            <div>
                                <label className="text-sm font-medium">Aktif</label>
                                <p className="text-xs text-muted-foreground mt-0.5">
                                    Tahun ajaran yang aktif akan digunakan sebagai default
                                </p>
                            </div>
                            <Switch checked={isActive} onCheckedChange={setIsActive} />
                        </div>
                    </div>

                    <div className="flex items-center justify-end gap-3">
                        <Link href="/academic/years">
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
