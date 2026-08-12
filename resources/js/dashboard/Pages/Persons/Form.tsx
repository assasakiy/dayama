import React, { useState } from 'react';
import { Head, router } from '@inertiajs/react';
import DashboardLayout from '@dashboard/Layouts/DashboardLayout';
import { Btn } from '@dashboard/Components/ui/btn';
import { Button } from '@dashboard/Components/ui/button';
import { Input } from '@dashboard/Components/ui/input';
import { Tabs, TabsList, TabsTrigger, TabsContent } from '@dashboard/Components/ui/tabs';
import { Select, SelectTrigger, SelectValue, SelectContent, SelectItem } from '@dashboard/Components/ui/select';
import { Switch } from '@dashboard/Components/ui/switch';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogClose, DialogFooter } from '@dashboard/Components/ui/dialog';
import { DuplicateNikDialog } from '@dashboard/Components/DuplicateNikDialog';
import { useNikDuplicateCheck } from '@dashboard/Hooks/useNikDuplicateCheck';
import { Save, ArrowLeft, User, Briefcase, Phone, MapPin, GraduationCap, Wrench, Languages, Users, Award, Plus, Trash2, X, UserPlus, UserCheck, Pencil } from 'lucide-react';

interface Position { id: string; nama: string; slug: string; }
interface Institution { id: string; name: string; slug: string; }
interface ContactType { id: string; nama: string; icon: string | null; }
interface AddressType { id: string; nama: string; }
interface EducationLevel { id: string; nama: string; }
interface SkillItem { id: string; nama: string; }
interface LanguageItem { id: string; nama: string; }
interface RelationshipType { id: string; nama: string; }
interface PersonListItem { id: string; nama_lengkap: string; }

interface PersonPosition {
    id: string; nama: string; slug: string; institution_id: string | null;
    nomor_induk: string | null; tanggal_mulai: string | null; tanggal_selesai: string | null; status: string;
}
interface PersonContact {
    id: string; contact_type_id: string; type: { nama: string; icon: string | null } | null;
    value: string; is_primary: boolean;
}
interface PersonAddress {
    id: string; address_type_id: string; type: { nama: string } | null;
    alamat: string | null; provinsi: string | null; kabupaten_kota: string | null;
    kecamatan: string | null; desa_kelurahan: string | null; kode_pos: string | null;
    latitude: string | null; longitude: string | null; is_primary: boolean;
}
interface PersonEducation {
    id: string; education_level_id: string; level: { nama: string } | null;
    institution_name: string | null; jurusan: string | null;
    tahun_masuk: string | null; tahun_lulus: string | null; status: string | null;
}
interface PersonSkill { id: string; nama: string; level: string | null; }
interface PersonLanguage { id: string; nama: string; }
interface PersonCertificate {
    id: string; nama: string; penerbit: string | null; nomor: string | null;
    tanggal_terbit: string | null; expired_at: string | null; file: string | null;
}
interface PersonFamilyMember {
    id: string; nama_lengkap: string; relationship_type_id: string;
}

interface PersonData {
    id: string; nik: string | null; passport: string | null;
    nama_depan: string; nama_belakang: string | null; nama_lengkap: string;
    gelar_depan: string | null; gelar_belakang: string | null;
    gender: 'L' | 'P' | null; tempat_lahir: string | null; tanggal_lahir: string | null;
    agama: string | null; status_hidup: boolean; photo: string | null; has_user: boolean;
    positions: PersonPosition[]; contacts: PersonContact[]; addresses: PersonAddress[];
    educations: PersonEducation[]; skills: PersonSkill[]; languages: PersonLanguage[];
    certificates: PersonCertificate[]; family_members: PersonFamilyMember[];
}

const AGAMA_OPTIONS = ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu', 'Lainnya'];

type TabValue = 'identitas' | 'jabatan' | 'kontak' | 'alamat' | 'pendidikan' | 'skill' | 'bahasa' | 'keluarga' | 'sertifikat';

export default function PersonForm({
    person, positions, institutions, contact_types, address_types, education_levels,
    skills_list, languages_list, relationship_types, persons_list,
}: {
    person: PersonData; positions: Position[]; institutions: Institution[];
    contact_types: ContactType[]; address_types: AddressType[]; education_levels: EducationLevel[];
    skills_list: SkillItem[]; languages_list: LanguageItem[]; relationship_types: RelationshipType[];
    persons_list: PersonListItem[];
}) {
    const [saving, setSaving] = useState(false);
    const [errors, setErrors] = useState<Record<string, string>>({});
    const [activeTab, setActiveTab] = useState<TabValue>('identitas');
    const [nikDialogOpen, setNikDialogOpen] = useState(false);
    const { duplicates, loading, check, reset } = useNikDuplicateCheck();

    const handleNikBlur = () => {
        if (form.nik && form.nik.length >= 16) {
            check(form.nik);
        }
    };

    const handleNikDialogClose = () => {
        setNikDialogOpen(false);
        reset();
    };

    const [form, setForm] = useState({
        nik: person.nik || '', passport: person.passport || '',
        nama_depan: person.nama_depan || '', nama_belakang: person.nama_belakang || '',
        gelar_depan: person.gelar_depan || '', gelar_belakang: person.gelar_belakang || '',
        gender: person.gender || '', tempat_lahir: person.tempat_lahir || '',
        tanggal_lahir: person.tanggal_lahir || '', agama: person.agama || '',
        status_hidup: person.status_hidup, photo: person.photo || '',
    });

    const update = (field: keyof typeof form, val: any) => setForm(prev => ({ ...prev, [field]: val }));

    const handleSave = () => {
        setSaving(true);
        router.put(`/persons/${person.id}`, form as any, {
            preserveScroll: true,
            onError: (errs) => { setErrors(errs); setSaving(false); },
            onSuccess: () => setSaving(false),
            onFinish: () => setSaving(false),
        });
    };

    // ── Dialogs ─────────────────────────────────────────────────────────────

    const [dialog, setDialog] = useState<{ open: boolean; tab: TabValue; editId?: string }>({ open: false, tab: 'kontak' });
    const openDialog = (tab: TabValue, editId?: string) => setDialog({ open: true, tab, editId });
    const closeDialog = () => setDialog({ open: false, tab: 'kontak', editId: undefined });
    const isEdit = !!dialog.editId;

    React.useEffect(() => {
        if (duplicates.length > 0) {
            setNikDialogOpen(true);
        }
    }, [duplicates]);

    const statusJabatanColor: Record<string, string> = {
        aktif: 'bg-green-50 text-green-700 border-green-200',
        nonaktif: 'bg-muted text-muted-foreground border-border-subtle',
        cuti: 'bg-yellow-50 text-yellow-700 border-yellow-200',
    };

    // ── Tab icons ───────────────────────────────────────────────────────────

    const tabs: { value: TabValue; label: string; icon: React.ReactNode; count?: number }[] = [
        { value: 'identitas', label: 'Identitas', icon: <User className="w-4 h-4" /> },
        { value: 'jabatan', label: 'Jabatan', icon: <Briefcase className="w-4 h-4" />, count: person.positions.length },
        { value: 'kontak', label: 'Kontak', icon: <Phone className="w-4 h-4" />, count: person.contacts.length },
        { value: 'alamat', label: 'Alamat', icon: <MapPin className="w-4 h-4" />, count: person.addresses.length },
        { value: 'pendidikan', label: 'Pendidikan', icon: <GraduationCap className="w-4 h-4" />, count: person.educations.length },
        { value: 'skill', label: 'Skill', icon: <Wrench className="w-4 h-4" />, count: person.skills.length },
        { value: 'bahasa', label: 'Bahasa', icon: <Languages className="w-4 h-4" />, count: person.languages.length },
        { value: 'keluarga', label: 'Keluarga', icon: <Users className="w-4 h-4" />, count: person.family_members.length },
        { value: 'sertifikat', label: 'Sertifikat', icon: <Award className="w-4 h-4" />, count: person.certificates.length },
    ];

    return (
        <DashboardLayout>
            <Head title={`Edit Person: ${person.nama_lengkap}`} />

            <div className="space-y-6 max-w-5xl">
                <div className="flex items-center justify-between">
                    <div className="flex items-center gap-3">
                        <Btn variant="ghost" size="sm" onClick={() => router.visit('/persons')} icon={<ArrowLeft className="w-4 h-4" />} />
                        <div>
                            <h1 className="text-xl font-semibold tracking-tight">{person.nama_lengkap}</h1>
                            <p className="text-sm text-muted-foreground">Edit data person</p>
                        </div>
                    </div>
                    <Btn onClick={handleSave} disabled={saving} loading={saving} icon={<Save className="w-4 h-4" />}>
                        {saving ? 'Menyimpan...' : 'Simpan'}
                    </Btn>
                </div>

                <Tabs value={activeTab} onValueChange={v => setActiveTab(v as TabValue)} className="w-full">
                    <TabsList className="w-full justify-start h-auto p-1 mb-4 flex-wrap gap-1">
                        {tabs.map(t => (
                            <TabsTrigger key={t.value} value={t.value} className="gap-2 px-4 py-2">
                                {t.icon} {t.label}
                                {t.count !== undefined && t.count > 0 && (
                                    <span className="ml-1 px-1.5 py-0.5 text-[10px] font-bold rounded-full bg-primary/10 text-primary">{t.count}</span>
                                )}
                            </TabsTrigger>
                        ))}
                    </TabsList>

                    {/* ═══ IDENTITAS ═══ */}
                    <TabsContent value="identitas" className="space-y-6">
                        <div className="rounded-xl border border-border bg-background p-6 space-y-4">
                            <h3 className="font-semibold text-sm text-muted-foreground uppercase tracking-wider">Nama & Gelar</h3>
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <Input label="Gelar Depan" value={form.gelar_depan} onChange={e => update('gelar_depan', e.target.value)} placeholder="Dr., H., Drs." error={errors.gelar_depan} />
                                <Input label="Gelar Belakang" value={form.gelar_belakang} onChange={e => update('gelar_belakang', e.target.value)} placeholder="S.Pd., M.Ag." error={errors.gelar_belakang} />
                                <Input label="Nama Depan" value={form.nama_depan} onChange={e => update('nama_depan', e.target.value)} required error={errors.nama_depan} />
                                <Input label="Nama Belakang" value={form.nama_belakang} onChange={e => update('nama_belakang', e.target.value)} error={errors.nama_belakang} />
                            </div>
                        </div>
                        <div className="rounded-xl border border-border bg-background p-6 space-y-4">
                            <h3 className="font-semibold text-sm text-muted-foreground uppercase tracking-wider">Identitas Diri</h3>
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <Input label="NIK" value={form.nik} onChange={e => update('nik', e.target.value)} onBlur={handleNikBlur} placeholder="16 digit NIK" error={errors.nik} />
                                <Input label="No. Passport" value={form.passport} onChange={e => update('passport', e.target.value)} error={errors.passport} />
                                <div className="space-y-1.5">
                                    <label className="text-sm font-medium">Jenis Kelamin</label>
                                    <div className="flex gap-3">
                                        {[{ val: 'L', label: 'Laki-laki' }, { val: 'P', label: 'Perempuan' }].map(({ val, label }) => (
                                            <button key={val} type="button" onClick={() => update('gender', form.gender === val ? '' : val)}
                                                className={`flex-1 h-9 rounded-md text-sm font-medium border transition-all ${form.gender === val ? 'bg-primary text-primary-foreground border-primary' : 'bg-background border-border-subtle text-muted-foreground hover:border-primary/50'}`}>
                                                {label}
                                            </button>
                                        ))}
                                    </div>
                                </div>
                                <div className="space-y-1.5">
                                    <label className="text-sm font-medium">Agama</label>
                                    <Select value={form.agama} onValueChange={val => update('agama', val)}>
                                        <SelectTrigger><SelectValue placeholder="Pilih agama..." /></SelectTrigger>
                                        <SelectContent>{AGAMA_OPTIONS.map(a => <SelectItem key={a} value={a}>{a}</SelectItem>)}</SelectContent>
                                    </Select>
                                </div>
                                <Input label="Tempat Lahir" value={form.tempat_lahir} onChange={e => update('tempat_lahir', e.target.value)} placeholder="Kota / Kabupaten" error={errors.tempat_lahir} />
                                <div className="space-y-1.5">
                                    <label className="text-sm font-medium">Tanggal Lahir</label>
                                    <input type="date" value={form.tanggal_lahir} onChange={e => update('tanggal_lahir', e.target.value)}
                                        className="w-full h-9 px-3 text-sm rounded-md border border-border-subtle bg-background focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all" />
                                </div>
                            </div>
                            <div className="flex items-center justify-between border border-border rounded-lg p-4">
                                <div>
                                    <p className="text-sm font-medium">Status Hidup</p>
                                    <p className="text-xs text-muted-foreground">Nonaktifkan jika person sudah wafat</p>
                                </div>
                                <Switch checked={form.status_hidup} onCheckedChange={val => update('status_hidup', val)} />
                            </div>
                        </div>
                        <div className="rounded-xl border border-border bg-background p-6 space-y-4">
                            <h3 className="font-semibold text-sm text-muted-foreground uppercase tracking-wider">Akun Pengguna</h3>
                            {person.has_user ? (
                                <div className="flex items-center gap-3 p-4 rounded-lg border border-green-200 bg-green-50 dark:bg-green-950/20 dark:border-green-800">
                                    <UserCheck className="w-5 h-5 text-green-600 dark:text-green-400 shrink-0" />
                                    <div>
                                        <p className="text-sm font-medium text-green-800 dark:text-green-300">Sudah memiliki akun</p>
                                        <p className="text-xs text-green-600 dark:text-green-400">Person ini sudah terhubung dengan akun pengguna.</p>
                                    </div>
                                </div>
                            ) : (
                                <div className="flex items-center justify-between gap-4 p-4 rounded-lg border border-dashed border-border-subtle">
                                    <div>
                                        <p className="text-sm font-medium">Belum memiliki akun</p>
                                        <p className="text-xs text-muted-foreground mt-0.5">Buat akun pengguna untuk person ini agar bisa login ke dashboard.</p>
                                    </div>
                                    <Btn variant="outline" size="sm" onClick={() => router.post(`/persons/${person.id}/create-account`, {}, { preserveScroll: true })} icon={<UserPlus className="w-4 h-4" />}>
                                        Buat Akun
                                    </Btn>
                                </div>
                            )}
                        </div>
                    </TabsContent>

                    {/* ═══ JABATAN ═══ */}
                    <TabsContent value="jabatan" className="space-y-4">
                        <RelationPanel
                            items={person.positions}
                            emptyIcon={<Briefcase className="w-10 h-10 text-muted-foreground/30 mb-3" />}
                            emptyTitle="Belum ada jabatan"
                            emptyDesc="Tambahkan jabatan/posisi organisasi untuk person ini"
                            onAdd={() => openDialog('jabatan')}
                            renderItem={(pos: PersonPosition) => {
                                const inst = institutions.find(i => i.id === pos.institution_id);
                                return (
                                    <div className="flex items-start gap-3 p-4 rounded-lg border border-border-subtle bg-surface-muted/20">
                                        <div className="flex-1 min-w-0">
                                            <div className="flex items-center gap-2 flex-wrap">
                                                <span className="font-medium text-sm">{pos.nama}</span>
                                                <span className={`inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium border ${statusJabatanColor[pos.status] || statusJabatanColor.aktif}`}>{pos.status}</span>
                                            </div>
                                            {inst && <p className="text-xs text-muted-foreground mt-0.5">{inst.name}</p>}
                                            {pos.nomor_induk && <p className="text-xs text-muted-foreground font-mono">NIS/NIP: {pos.nomor_induk}</p>}
                                            {(pos.tanggal_mulai || pos.tanggal_selesai) && (
                                                <p className="text-xs text-muted-foreground mt-0.5">{pos.tanggal_mulai || '?'} → {pos.tanggal_selesai || 'sekarang'}</p>
                                            )}
                                        </div>
                                        <button onClick={() => router.delete(`/persons/${person.id}/positions/${pos.id}`, { data: { institution_id: pos.institution_id }, preserveScroll: true })}
                                            className="p-1.5 rounded-md text-muted-foreground hover:text-destructive hover:bg-destructive/10 transition-colors shrink-0">
                                            <Trash2 className="w-3.5 h-3.5" />
                                        </button>
                                    </div>
                                );
                            }}
                        />
                    </TabsContent>

                    {/* ═══ KONTAK ═══ */}
                    <TabsContent value="kontak" className="space-y-4">
                        <RelationPanel
                            items={person.contacts}
                            emptyIcon={<Phone className="w-10 h-10 text-muted-foreground/30 mb-3" />}
                            emptyTitle="Belum ada kontak"
                            emptyDesc="Tambahkan nomor telepon, email, atau kontak lainnya"
                            onAdd={() => openDialog('kontak')}
                            renderItem={(c: PersonContact) => (
                                <div className="flex items-start gap-3 p-4 rounded-lg border border-border-subtle bg-surface-muted/20">
                                    <div className="flex-1 min-w-0">
                                        <div className="flex items-center gap-2">
                                            <span className="font-medium text-sm">{c.type?.nama || 'Kontak'}</span>
                                            {c.is_primary && <span className="px-1.5 py-0.5 text-[10px] font-medium rounded-full bg-blue-50 text-blue-700 border border-blue-200 dark:bg-blue-950/40 dark:text-blue-300">Utama</span>}
                                        </div>
                                        <p className="text-sm text-foreground mt-0.5">{c.value}</p>
                                    </div>
                                    <button onClick={() => openDialog('kontak', c.id)}
                                        className="p-1.5 rounded-md text-muted-foreground hover:text-foreground hover:bg-surface-muted transition-colors shrink-0">
                                        <Pencil className="w-3.5 h-3.5" />
                                    </button>
                                    <button onClick={() => router.delete(`/persons/${person.id}/contacts/${c.id}`, { preserveScroll: true })}
                                        className="p-1.5 rounded-md text-muted-foreground hover:text-destructive hover:bg-destructive/10 transition-colors shrink-0">
                                        <Trash2 className="w-3.5 h-3.5" />
                                    </button>
                                </div>
                            )}
                        />
                    </TabsContent>

                    {/* ═══ ALAMAT ═══ */}
                    <TabsContent value="alamat" className="space-y-4">
                        <RelationPanel
                            items={person.addresses}
                            emptyIcon={<MapPin className="w-10 h-10 text-muted-foreground/30 mb-3" />}
                            emptyTitle="Belum ada alamat"
                            emptyDesc="Tambahkan alamat person"
                            onAdd={() => openDialog('alamat')}
                            renderItem={(a: PersonAddress) => (
                                <div className="flex items-start gap-3 p-4 rounded-lg border border-border-subtle bg-surface-muted/20">
                                    <div className="flex-1 min-w-0">
                                        <div className="flex items-center gap-2">
                                            <span className="font-medium text-sm">{a.type?.nama || 'Alamat'}</span>
                                            {a.is_primary && <span className="px-1.5 py-0.5 text-[10px] font-medium rounded-full bg-blue-50 text-blue-700 border border-blue-200 dark:bg-blue-950/40 dark:text-blue-300">Utama</span>}
                                        </div>
                                        <p className="text-xs text-foreground mt-0.5">{a.alamat || '—'}</p>
                                        <p className="text-xs text-muted-foreground">{[a.desa_kelurahan, a.kecamatan, a.kabupaten_kota, a.provinsi].filter(Boolean).join(', ') || '—'}</p>
                                        {a.kode_pos && <p className="text-xs text-muted-foreground font-mono">{a.kode_pos}</p>}
                                    </div>
                                    <button onClick={() => openDialog('alamat', a.id)}
                                        className="p-1.5 rounded-md text-muted-foreground hover:text-foreground hover:bg-surface-muted transition-colors shrink-0">
                                        <Pencil className="w-3.5 h-3.5" />
                                    </button>
                                    <button onClick={() => router.delete(`/persons/${person.id}/addresses/${a.id}`, { preserveScroll: true })}
                                        className="p-1.5 rounded-md text-muted-foreground hover:text-destructive hover:bg-destructive/10 transition-colors shrink-0">
                                        <Trash2 className="w-3.5 h-3.5" />
                                    </button>
                                </div>
                            )}
                        />
                    </TabsContent>

                    {/* ═══ PENDIDIKAN ═══ */}
                    <TabsContent value="pendidikan" className="space-y-4">
                        <RelationPanel
                            items={person.educations}
                            emptyIcon={<GraduationCap className="w-10 h-10 text-muted-foreground/30 mb-3" />}
                            emptyTitle="Belum ada riwayat pendidikan"
                            emptyDesc="Tambahkan riwayat pendidikan person"
                            onAdd={() => openDialog('pendidikan')}
                            renderItem={(e: PersonEducation) => (
                                <div className="flex items-start gap-3 p-4 rounded-lg border border-border-subtle bg-surface-muted/20">
                                    <div className="flex-1 min-w-0">
                                        <div className="flex items-center gap-2">
                                            <span className="font-medium text-sm">{e.level?.nama || 'Pendidikan'}</span>
                                            {e.status && <span className="px-1.5 py-0.5 text-[10px] font-medium rounded-full bg-green-50 text-green-700 border border-green-200">{e.status === 'lulus' ? 'Lulus' : 'Belum Lulus'}</span>}
                                        </div>
                                        <p className="text-xs text-foreground mt-0.5">{e.institution_name || '—'}</p>
                                        {(e.jurusan || e.tahun_masuk || e.tahun_lulus) && (
                                            <p className="text-xs text-muted-foreground">{[e.jurusan, e.tahun_masuk && `Masuk ${e.tahun_masuk}`, e.tahun_lulus && `Lulus ${e.tahun_lulus}`].filter(Boolean).join(' · ')}</p>
                                        )}
                                    </div>
                                    <button onClick={() => openDialog('pendidikan', e.id)}
                                        className="p-1.5 rounded-md text-muted-foreground hover:text-foreground hover:bg-surface-muted transition-colors shrink-0">
                                        <Pencil className="w-3.5 h-3.5" />
                                    </button>
                                    <button onClick={() => router.delete(`/persons/${person.id}/educations/${e.id}`, { preserveScroll: true })}
                                        className="p-1.5 rounded-md text-muted-foreground hover:text-destructive hover:bg-destructive/10 transition-colors shrink-0">
                                        <Trash2 className="w-3.5 h-3.5" />
                                    </button>
                                </div>
                            )}
                        />
                    </TabsContent>

                    {/* ═══ SKILL ═══ */}
                    <TabsContent value="skill" className="space-y-4">
                        <RelationPanel
                            items={person.skills}
                            emptyIcon={<Wrench className="w-10 h-10 text-muted-foreground/30 mb-3" />}
                            emptyTitle="Belum ada skill"
                            emptyDesc="Tambahkan keahlian yang dimiliki person"
                            onAdd={() => openDialog('skill')}
                            renderItem={(s: PersonSkill) => (
                                <div className="flex items-start gap-3 p-4 rounded-lg border border-border-subtle bg-surface-muted/20">
                                    <div className="flex-1 min-w-0">
                                        <span className="font-medium text-sm">{s.nama}</span>
                                        {s.level && <span className="ml-2 px-1.5 py-0.5 text-[10px] font-medium rounded-full bg-muted text-muted-foreground">{s.level}</span>}
                                    </div>
                                    <select value={s.level || ''} onChange={e => router.put(`/persons/${person.id}/skills/${s.id}`, { level: e.target.value || null }, { preserveScroll: true })}
                                        className="h-7 px-2 text-xs rounded border border-border-subtle bg-background">
                                        <option value="">Level</option>
                                        <option value="pemula">Pemula</option>
                                        <option value="menengah">Menengah</option>
                                        <option value="mahir">Mahir</option>
                                    </select>
                                    <button onClick={() => router.delete(`/persons/${person.id}/skills/${s.id}`, { preserveScroll: true })}
                                        className="p-1.5 rounded-md text-muted-foreground hover:text-destructive hover:bg-destructive/10 transition-colors shrink-0">
                                        <Trash2 className="w-3.5 h-3.5" />
                                    </button>
                                </div>
                            )}
                        />
                    </TabsContent>

                    {/* ═══ BAHASA ═══ */}
                    <TabsContent value="bahasa" className="space-y-4">
                        <RelationPanel
                            items={person.languages}
                            emptyIcon={<Languages className="w-10 h-10 text-muted-foreground/30 mb-3" />}
                            emptyTitle="Belum ada bahasa"
                            emptyDesc="Tambahkan bahasa yang dikuasai person"
                            onAdd={() => openDialog('bahasa')}
                            renderItem={(l: PersonLanguage) => (
                                <div className="flex items-center justify-between gap-3 p-4 rounded-lg border border-border-subtle bg-surface-muted/20">
                                    <span className="font-medium text-sm">{l.nama}</span>
                                    <button onClick={() => router.delete(`/persons/${person.id}/languages/${l.id}`, { preserveScroll: true })}
                                        className="p-1.5 rounded-md text-muted-foreground hover:text-destructive hover:bg-destructive/10 transition-colors shrink-0">
                                        <Trash2 className="w-3.5 h-3.5" />
                                    </button>
                                </div>
                            )}
                        />
                    </TabsContent>

                    {/* ═══ KELUARGA ═══ */}
                    <TabsContent value="keluarga" className="space-y-4">
                        <RelationPanel
                            items={person.family_members}
                            emptyIcon={<Users className="w-10 h-10 text-muted-foreground/30 mb-3" />}
                            emptyTitle="Belum ada anggota keluarga"
                            emptyDesc="Tambahkan hubungan keluarga"
                            onAdd={() => openDialog('keluarga')}
                            renderItem={(m: PersonFamilyMember) => {
                                const rel = relationship_types.find(r => r.id === m.relationship_type_id);
                                return (
                                    <div className="flex items-center justify-between gap-3 p-4 rounded-lg border border-border-subtle bg-surface-muted/20">
                                        <div>
                                            <span className="font-medium text-sm">{m.nama_lengkap}</span>
                                            <span className="ml-2 px-1.5 py-0.5 text-[10px] font-medium rounded-full bg-muted text-muted-foreground">{rel?.nama || '?'}</span>
                                        </div>
                                        <button onClick={() => router.delete(`/persons/${person.id}/family/${m.id}`, { preserveScroll: true })}
                                            className="p-1.5 rounded-md text-muted-foreground hover:text-destructive hover:bg-destructive/10 transition-colors shrink-0">
                                            <Trash2 className="w-3.5 h-3.5" />
                                        </button>
                                    </div>
                                );
                            }}
                        />
                    </TabsContent>

                    {/* ═══ SERTIFIKAT ═══ */}
                    <TabsContent value="sertifikat" className="space-y-4">
                        <RelationPanel
                            items={person.certificates}
                            emptyIcon={<Award className="w-10 h-10 text-muted-foreground/30 mb-3" />}
                            emptyTitle="Belum ada sertifikat"
                            emptyDesc="Tambahkan sertifikat yang dimiliki person"
                            onAdd={() => openDialog('sertifikat')}
                            renderItem={(c: PersonCertificate) => (
                                <div className="flex items-start gap-3 p-4 rounded-lg border border-border-subtle bg-surface-muted/20">
                                    <div className="flex-1 min-w-0">
                                        <span className="font-medium text-sm">{c.nama}</span>
                                        {c.penerbit && <p className="text-xs text-muted-foreground">{c.penerbit}</p>}
                                        {(c.tanggal_terbit || c.nomor) && (
                                            <p className="text-xs text-muted-foreground">{[c.nomor, c.tanggal_terbit && `Terbit ${c.tanggal_terbit}`, c.expired_at && `Exp ${c.expired_at}`].filter(Boolean).join(' · ')}</p>
                                        )}
                                    </div>
                                    <button onClick={() => openDialog('sertifikat', c.id)}
                                        className="p-1.5 rounded-md text-muted-foreground hover:text-foreground hover:bg-surface-muted transition-colors shrink-0">
                                        <Pencil className="w-3.5 h-3.5" />
                                    </button>
                                    <button onClick={() => router.delete(`/persons/${person.id}/certificates/${c.id}`, { preserveScroll: true })}
                                        className="p-1.5 rounded-md text-muted-foreground hover:text-destructive hover:bg-destructive/10 transition-colors shrink-0">
                                        <Trash2 className="w-3.5 h-3.5" />
                                    </button>
                                </div>
                            )}
                        />
                    </TabsContent>
                </Tabs>
            </div>

            {/* ═══════════════════ DIALOGS ═══════════════════ */}
            <Dialog open={dialog.open} onOpenChange={o => { if (!o) closeDialog(); }}>
                <DialogContent className="flex flex-col p-0 gap-0 max-w-lg">
                    <DialogHeader className="flex flex-row items-center justify-between px-6 py-4 border-b border-border-subtle shrink-0">
                        <DialogTitle className="text-base">
                            {isEdit ? 'Edit' : 'Tambah'} {dialog.tab === 'jabatan' ? 'Jabatan' : dialog.tab === 'kontak' ? 'Kontak' : dialog.tab === 'alamat' ? 'Alamat' : dialog.tab === 'pendidikan' ? 'Pendidikan' : dialog.tab === 'skill' ? 'Skill' : dialog.tab === 'bahasa' ? 'Bahasa' : dialog.tab === 'keluarga' ? 'Anggota Keluarga' : 'Sertifikat'}
                        </DialogTitle>
                        <DialogClose className="p-1 rounded-md text-muted-foreground hover:text-foreground hover:bg-surface-muted transition-colors">
                            <X className="w-4 h-4" />
                        </DialogClose>
                    </DialogHeader>
                    {dialog.tab === 'jabatan' && <JabatanForm personId={person.id} positions={positions} institutions={institutions} onSuccess={closeDialog} />}
                    {dialog.tab === 'kontak' && <ContactForm personId={person.id} contactTypes={contact_types} editId={dialog.editId} editData={dialog.editId ? person.contacts.find(c => c.id === dialog.editId) : undefined} onSuccess={closeDialog} />}
                    {dialog.tab === 'alamat' && <AddressForm personId={person.id} addressTypes={address_types} editId={dialog.editId} editData={dialog.editId ? person.addresses.find(a => a.id === dialog.editId) : undefined} onSuccess={closeDialog} />}
                    {dialog.tab === 'pendidikan' && <EducationForm personId={person.id} educationLevels={education_levels} editId={dialog.editId} editData={dialog.editId ? person.educations.find(e => e.id === dialog.editId) : undefined} onSuccess={closeDialog} />}
                    {dialog.tab === 'skill' && <SkillForm personId={person.id} skillsList={skills_list} onSuccess={closeDialog} />}
                    {dialog.tab === 'bahasa' && <LanguageForm personId={person.id} languagesList={languages_list} onSuccess={closeDialog} />}
                    {dialog.tab === 'keluarga' && <FamilyForm personId={person.id} relationshipTypes={relationship_types} personsList={persons_list} onSuccess={closeDialog} />}
                    {dialog.tab === 'sertifikat' && <CertificateForm personId={person.id} editId={dialog.editId} editData={dialog.editId ? person.certificates.find(c => c.id === dialog.editId) : undefined} onSuccess={closeDialog} />}
                </DialogContent>
            </Dialog>

            <DuplicateNikDialog
                nik={form.nik}
                duplicates={duplicates}
                open={nikDialogOpen}
                onOpenChange={setNikDialogOpen}
                onCreateNew={handleNikDialogClose}
            />
        </DashboardLayout>
    );
}

// ═══ Shared Panel component ═══

function RelationPanel<T>({ items, emptyIcon, emptyTitle, emptyDesc, onAdd, renderItem }: {
    items: T[]; emptyIcon: React.ReactNode; emptyTitle: string; emptyDesc: string;
    onAdd: () => void; renderItem: (item: T) => React.ReactNode;
}) {
    return (
        <div className="rounded-xl border border-border bg-background p-6">
            <div className="flex items-center justify-between mb-5">
                <div>
                    <h3 className="font-semibold">{emptyTitle}</h3>
                    <p className="text-sm text-muted-foreground mt-0.5">{emptyDesc}</p>
                </div>
                <Btn variant="outline" size="sm" onClick={onAdd} icon={<Plus className="w-4 h-4" />}>Tambah</Btn>
            </div>
            {items.length === 0 ? (
                <div className="flex flex-col items-center justify-center py-10 text-center border-2 border-dashed border-border rounded-lg">
                    {emptyIcon}
                    <p className="text-sm font-medium text-muted-foreground">{emptyTitle}</p>
                    <p className="text-xs text-muted-foreground/70 mt-1">{emptyDesc}</p>
                </div>
            ) : (
                <div className="space-y-3">{items.map((item, i) => <React.Fragment key={i}>{renderItem(item)}</React.Fragment>)}</div>
            )}
        </div>
    );
}

// ═══ Jabatan Form ═══

function JabatanForm({ personId, positions, institutions, onSuccess }: {
    personId: string; positions: Position[]; institutions: Institution[]; onSuccess: () => void;
}) {
    const [form, setForm] = useState({ position_id: '', institution_id: '', nomor_induk: '', tanggal_mulai: '', tanggal_selesai: '', status: 'aktif' });
    const handleSubmit = (e: React.FormEvent) => { e.preventDefault(); router.post(`/persons/${personId}/positions`, form, { preserveScroll: true, onSuccess }); };
    return (
        <form onSubmit={handleSubmit}>
            <div className="space-y-4 px-6 py-5">
                <div className="space-y-1.5">
                    <label className="text-sm font-medium">Jabatan <span className="text-destructive">*</span></label>
                    <Select value={form.position_id} onValueChange={v => setForm(p => ({ ...p, position_id: v }))}>
                        <SelectTrigger><SelectValue placeholder="Pilih jabatan..." /></SelectTrigger>
                        <SelectContent>{positions.map(p => <SelectItem key={p.id} value={p.id}>{p.nama}</SelectItem>)}</SelectContent>
                    </Select>
                </div>
                <div className="space-y-1.5">
                    <label className="text-sm font-medium">Institusi <span className="text-muted-foreground font-normal">(opsional)</span></label>
                    <Select value={form.institution_id} onValueChange={v => setForm(p => ({ ...p, institution_id: v }))}>
                        <SelectTrigger><SelectValue placeholder="Pilih institusi..." /></SelectTrigger>
                        <SelectContent>{institutions.map(i => <SelectItem key={i.id} value={i.id}>{i.name}</SelectItem>)}</SelectContent>
                    </Select>
                </div>
                <div className="grid grid-cols-2 gap-3">
                    <div className="space-y-1.5">
                        <label className="text-sm font-medium">Mulai</label>
                        <input type="date" value={form.tanggal_mulai} onChange={e => setForm(p => ({ ...p, tanggal_mulai: e.target.value }))}
                            className="w-full h-9 px-3 text-sm rounded-md border border-border-subtle bg-background focus:outline-none focus:ring-2 focus:ring-primary/30 transition-all" />
                    </div>
                    <div className="space-y-1.5">
                        <label className="text-sm font-medium">Selesai</label>
                        <input type="date" value={form.tanggal_selesai} onChange={e => setForm(p => ({ ...p, tanggal_selesai: e.target.value }))}
                            className="w-full h-9 px-3 text-sm rounded-md border border-border-subtle bg-background focus:outline-none focus:ring-2 focus:ring-primary/30 transition-all" />
                    </div>
                </div>
                <Input label="Nomor Induk (NIS / NIP)" value={form.nomor_induk} onChange={e => setForm(p => ({ ...p, nomor_induk: e.target.value }))} placeholder="Opsional" />
                <div className="space-y-1.5">
                    <label className="text-sm font-medium">Status</label>
                    <Select value={form.status} onValueChange={v => setForm(p => ({ ...p, status: v }))}>
                        <SelectTrigger><SelectValue /></SelectTrigger>
                        <SelectContent>
                            <SelectItem value="aktif">Aktif</SelectItem>
                            <SelectItem value="nonaktif">Nonaktif</SelectItem>
                            <SelectItem value="cuti">Cuti</SelectItem>
                        </SelectContent>
                    </Select>
                </div>
            </div>
            <DialogFooter className="flex items-center justify-end gap-2 px-6 py-4 border-t border-border-subtle">
                <Button type="button" variant="outline" onClick={onSuccess}>Batal</Button>
                <Btn type="submit" disabled={!form.position_id} icon={<Plus className="w-4 h-4" />}>Tambah</Btn>
            </DialogFooter>
        </form>
    );
}

// ═══ Contact Form ═══

function ContactForm({ personId, contactTypes, editId, editData, onSuccess }: {
    personId: string; contactTypes: ContactType[]; editId?: string; editData?: any; onSuccess: () => void;
}) {
    const [form, setForm] = useState({ contact_type_id: editData?.contact_type_id || '', value: editData?.value || '', is_primary: editData?.is_primary || false });
    const isEdit = !!editId;
    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        if (isEdit) { router.put(`/persons/${personId}/contacts/${editId}`, form, { preserveScroll: true, onSuccess }); }
        else { router.post(`/persons/${personId}/contacts`, form, { preserveScroll: true, onSuccess }); }
    };
    return (
        <form onSubmit={submit}>
            <div className="space-y-4 px-6 py-5">
                <div className="space-y-1.5">
                    <label className="text-sm font-medium">Tipe Kontak <span className="text-destructive">*</span></label>
                    <Select value={form.contact_type_id} onValueChange={v => setForm(p => ({ ...p, contact_type_id: v }))}>
                        <SelectTrigger><SelectValue placeholder="Pilih tipe..." /></SelectTrigger>
                        <SelectContent>{contactTypes.map(t => <SelectItem key={t.id} value={t.id}>{t.nama}</SelectItem>)}</SelectContent>
                    </Select>
                </div>
                <Input label="Nilai / Alamat" value={form.value} onChange={e => setForm(p => ({ ...p, value: e.target.value }))} required placeholder="0812xxxx / email@example.com" />
                <div className="flex items-center gap-3">
                    <Switch checked={form.is_primary} onCheckedChange={v => setForm(p => ({ ...p, is_primary: v }))} />
                    <span className="text-sm">Jadikan kontak utama</span>
                </div>
            </div>
            <DialogFooter className="flex items-center justify-end gap-2 px-6 py-4 border-t border-border-subtle">
                <Button type="button" variant="outline" onClick={onSuccess}>Batal</Button>
                <Btn type="submit" disabled={!form.contact_type_id || !form.value} icon={isEdit ? <Save className="w-4 h-4" /> : <Plus className="w-4 h-4" />}>{isEdit ? 'Simpan' : 'Tambah'}</Btn>
            </DialogFooter>
        </form>
    );
}

// ═══ Address Form ═══

function AddressForm({ personId, addressTypes, editId, editData, onSuccess }: {
    personId: string; addressTypes: AddressType[]; editId?: string; editData?: any; onSuccess: () => void;
}) {
    const [form, setForm] = useState({ address_type_id: editData?.address_type_id || '', alamat: editData?.alamat || '', provinsi: editData?.provinsi || '', kabupaten_kota: editData?.kabupaten_kota || '', kecamatan: editData?.kecamatan || '', desa_kelurahan: editData?.desa_kelurahan || '', kode_pos: editData?.kode_pos || '', latitude: editData?.latitude || '', longitude: editData?.longitude || '', is_primary: editData?.is_primary || false });
    const isEdit = !!editId;
    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        if (isEdit) { router.put(`/persons/${personId}/addresses/${editId}`, form, { preserveScroll: true, onSuccess }); }
        else { router.post(`/persons/${personId}/addresses`, form, { preserveScroll: true, onSuccess }); }
    };
    return (
        <form onSubmit={submit}>
            <div className="space-y-4 px-6 py-5 max-h-[60vh] overflow-y-auto">
                <div className="space-y-1.5">
                    <label className="text-sm font-medium">Tipe Alamat <span className="text-destructive">*</span></label>
                    <Select value={form.address_type_id} onValueChange={v => setForm(p => ({ ...p, address_type_id: v }))}>
                        <SelectTrigger><SelectValue placeholder="Pilih tipe..." /></SelectTrigger>
                        <SelectContent>{addressTypes.map(t => <SelectItem key={t.id} value={t.id}>{t.nama}</SelectItem>)}</SelectContent>
                    </Select>
                </div>
                <div className="space-y-1.5">
                    <label className="text-sm font-medium">Alamat Lengkap</label>
                    <textarea value={form.alamat} onChange={e => setForm(p => ({ ...p, alamat: e.target.value }))} rows={2}
                        className="w-full px-3 py-2 text-sm rounded-md border border-border-subtle bg-background focus:outline-none focus:ring-2 focus:ring-primary/30 transition-all resize-none" />
                </div>
                <div className="grid grid-cols-2 gap-3">
                    <Input label="Provinsi" value={form.provinsi} onChange={e => setForm(p => ({ ...p, provinsi: e.target.value }))} />
                    <Input label="Kabupaten/Kota" value={form.kabupaten_kota} onChange={e => setForm(p => ({ ...p, kabupaten_kota: e.target.value }))} />
                    <Input label="Kecamatan" value={form.kecamatan} onChange={e => setForm(p => ({ ...p, kecamatan: e.target.value }))} />
                    <Input label="Desa/Kelurahan" value={form.desa_kelurahan} onChange={e => setForm(p => ({ ...p, desa_kelurahan: e.target.value }))} />
                    <Input label="Kode Pos" value={form.kode_pos} onChange={e => setForm(p => ({ ...p, kode_pos: e.target.value }))} />
                </div>
                <div className="flex items-center gap-3">
                    <Switch checked={form.is_primary} onCheckedChange={v => setForm(p => ({ ...p, is_primary: v }))} />
                    <span className="text-sm">Jadikan alamat utama</span>
                </div>
            </div>
            <DialogFooter className="flex items-center justify-end gap-2 px-6 py-4 border-t border-border-subtle">
                <Button type="button" variant="outline" onClick={onSuccess}>Batal</Button>
                <Btn type="submit" disabled={!form.address_type_id} icon={isEdit ? <Save className="w-4 h-4" /> : <Plus className="w-4 h-4" />}>{isEdit ? 'Simpan' : 'Tambah'}</Btn>
            </DialogFooter>
        </form>
    );
}

// ═══ Education Form ═══

function EducationForm({ personId, educationLevels, editId, editData, onSuccess }: {
    personId: string; educationLevels: EducationLevel[]; editId?: string; editData?: any; onSuccess: () => void;
}) {
    const [form, setForm] = useState({ education_level_id: editData?.education_level_id || '', institution_name: editData?.institution_name || '', jurusan: editData?.jurusan || '', tahun_masuk: editData?.tahun_masuk || '', tahun_lulus: editData?.tahun_lulus || '', status: editData?.status || '' });
    const isEdit = !!editId;
    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        if (isEdit) { router.put(`/persons/${personId}/educations/${editId}`, form, { preserveScroll: true, onSuccess }); }
        else { router.post(`/persons/${personId}/educations`, form, { preserveScroll: true, onSuccess }); }
    };
    return (
        <form onSubmit={submit}>
            <div className="space-y-4 px-6 py-5">
                <div className="space-y-1.5">
                    <label className="text-sm font-medium">Jenjang <span className="text-destructive">*</span></label>
                    <Select value={form.education_level_id} onValueChange={v => setForm(p => ({ ...p, education_level_id: v }))}>
                        <SelectTrigger><SelectValue placeholder="Pilih jenjang..." /></SelectTrigger>
                        <SelectContent>{educationLevels.map(l => <SelectItem key={l.id} value={l.id}>{l.nama}</SelectItem>)}</SelectContent>
                    </Select>
                </div>
                <Input label="Nama Institusi" value={form.institution_name} onChange={e => setForm(p => ({ ...p, institution_name: e.target.value }))} placeholder="SDN 1 Jakarta" />
                <Input label="Jurusan" value={form.jurusan} onChange={e => setForm(p => ({ ...p, jurusan: e.target.value }))} placeholder="IPA (opsional)" />
                <div className="grid grid-cols-2 gap-3">
                    <Input label="Tahun Masuk" value={form.tahun_masuk} onChange={e => setForm(p => ({ ...p, tahun_masuk: e.target.value }))} placeholder="2015" />
                    <Input label="Tahun Lulus" value={form.tahun_lulus} onChange={e => setForm(p => ({ ...p, tahun_lulus: e.target.value }))} placeholder="2020" />
                </div>
                <div className="space-y-1.5">
                    <label className="text-sm font-medium">Status</label>
                    <Select value={form.status} onValueChange={v => setForm(p => ({ ...p, status: v }))}>
                        <SelectTrigger><SelectValue placeholder="Pilih status..." /></SelectTrigger>
                        <SelectContent>
                            <SelectItem value="lulus">Lulus</SelectItem>
                            <SelectItem value="belum_lulus">Belum Lulus</SelectItem>
                        </SelectContent>
                    </Select>
                </div>
            </div>
            <DialogFooter className="flex items-center justify-end gap-2 px-6 py-4 border-t border-border-subtle">
                <Button type="button" variant="outline" onClick={onSuccess}>Batal</Button>
                <Btn type="submit" disabled={!form.education_level_id} icon={isEdit ? <Save className="w-4 h-4" /> : <Plus className="w-4 h-4" />}>{isEdit ? 'Simpan' : 'Tambah'}</Btn>
            </DialogFooter>
        </form>
    );
}

// ═══ Skill Form ═══

function SkillForm({ personId, skillsList, onSuccess }: {
    personId: string; skillsList: SkillItem[]; onSuccess: () => void;
}) {
    const [skillId, setSkillId] = useState('');
    const [level, setLevel] = useState('');
    const submit = (e: React.FormEvent) => { e.preventDefault(); router.post(`/persons/${personId}/skills`, { skill_id: skillId, level: level || null }, { preserveScroll: true, onSuccess }); };
    return (
        <form onSubmit={submit}>
            <div className="space-y-4 px-6 py-5">
                <div className="space-y-1.5">
                    <label className="text-sm font-medium">Skill <span className="text-destructive">*</span></label>
                    <Select value={skillId} onValueChange={setSkillId}>
                        <SelectTrigger><SelectValue placeholder="Pilih skill..." /></SelectTrigger>
                        <SelectContent>{skillsList.map(s => <SelectItem key={s.id} value={s.id}>{s.nama}</SelectItem>)}</SelectContent>
                    </Select>
                </div>
                <div className="space-y-1.5">
                    <label className="text-sm font-medium">Level <span className="text-muted-foreground font-normal">(opsional)</span></label>
                    <Select value={level} onValueChange={setLevel}>
                        <SelectTrigger><SelectValue placeholder="Pilih level..." /></SelectTrigger>
                        <SelectContent>
                            <SelectItem value="">—</SelectItem>
                            <SelectItem value="pemula">Pemula</SelectItem>
                            <SelectItem value="menengah">Menengah</SelectItem>
                            <SelectItem value="mahir">Mahir</SelectItem>
                        </SelectContent>
                    </Select>
                </div>
            </div>
            <DialogFooter className="flex items-center justify-end gap-2 px-6 py-4 border-t border-border-subtle">
                <Button type="button" variant="outline" onClick={onSuccess}>Batal</Button>
                <Btn type="submit" disabled={!skillId} icon={<Plus className="w-4 h-4" />}>Tambah</Btn>
            </DialogFooter>
        </form>
    );
}

// ═══ Language Form ═══

function LanguageForm({ personId, languagesList, onSuccess }: {
    personId: string; languagesList: LanguageItem[]; onSuccess: () => void;
}) {
    const [languageId, setLanguageId] = useState('');
    const submit = (e: React.FormEvent) => { e.preventDefault(); router.post(`/persons/${personId}/languages`, { language_id: languageId }, { preserveScroll: true, onSuccess }); };
    return (
        <form onSubmit={submit}>
            <div className="space-y-4 px-6 py-5">
                <div className="space-y-1.5">
                    <label className="text-sm font-medium">Bahasa <span className="text-destructive">*</span></label>
                    <Select value={languageId} onValueChange={setLanguageId}>
                        <SelectTrigger><SelectValue placeholder="Pilih bahasa..." /></SelectTrigger>
                        <SelectContent>{languagesList.map(l => <SelectItem key={l.id} value={l.id}>{l.nama}</SelectItem>)}</SelectContent>
                    </Select>
                </div>
            </div>
            <DialogFooter className="flex items-center justify-end gap-2 px-6 py-4 border-t border-border-subtle">
                <Button type="button" variant="outline" onClick={onSuccess}>Batal</Button>
                <Btn type="submit" disabled={!languageId} icon={<Plus className="w-4 h-4" />}>Tambah</Btn>
            </DialogFooter>
        </form>
    );
}

// ═══ Family Form ═══

function FamilyForm({ personId, relationshipTypes, personsList, onSuccess }: {
    personId: string; relationshipTypes: RelationshipType[]; personsList: PersonListItem[]; onSuccess: () => void;
}) {
    const [form, setForm] = useState({ related_person_id: '', relationship_type_id: '' });
    const submit = (e: React.FormEvent) => { e.preventDefault(); router.post(`/persons/${personId}/family`, form, { preserveScroll: true, onSuccess }); };
    return (
        <form onSubmit={submit}>
            <div className="space-y-4 px-6 py-5">
                <div className="space-y-1.5">
                    <label className="text-sm font-medium">Anggota Keluarga <span className="text-destructive">*</span></label>
                    <Select value={form.related_person_id} onValueChange={v => setForm(p => ({ ...p, related_person_id: v }))}>
                        <SelectTrigger><SelectValue placeholder="Cari person..." /></SelectTrigger>
                        <SelectContent>{personsList.filter(p => p.id !== personId).map(p => <SelectItem key={p.id} value={p.id}>{p.nama_lengkap}</SelectItem>)}</SelectContent>
                    </Select>
                </div>
                <div className="space-y-1.5">
                    <label className="text-sm font-medium">Hubungan <span className="text-destructive">*</span></label>
                    <Select value={form.relationship_type_id} onValueChange={v => setForm(p => ({ ...p, relationship_type_id: v }))}>
                        <SelectTrigger><SelectValue placeholder="Pilih hubungan..." /></SelectTrigger>
                        <SelectContent>{relationshipTypes.map(r => <SelectItem key={r.id} value={r.id}>{r.nama}</SelectItem>)}</SelectContent>
                    </Select>
                </div>
            </div>
            <DialogFooter className="flex items-center justify-end gap-2 px-6 py-4 border-t border-border-subtle">
                <Button type="button" variant="outline" onClick={onSuccess}>Batal</Button>
                <Btn type="submit" disabled={!form.related_person_id || !form.relationship_type_id} icon={<Plus className="w-4 h-4" />}>Tambah</Btn>
            </DialogFooter>
        </form>
    );
}

// ═══ Certificate Form ═══

function CertificateForm({ personId, editId, editData, onSuccess }: {
    personId: string; editId?: string; editData?: any; onSuccess: () => void;
}) {
    const [form, setForm] = useState({ nama: editData?.nama || '', penerbit: editData?.penerbit || '', nomor: editData?.nomor || '', tanggal_terbit: editData?.tanggal_terbit || '', expired_at: editData?.expired_at || '', file: editData?.file || '' });
    const isEdit = !!editId;
    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        if (isEdit) { router.put(`/persons/${personId}/certificates/${editId}`, form, { preserveScroll: true, onSuccess }); }
        else { router.post(`/persons/${personId}/certificates`, form, { preserveScroll: true, onSuccess }); }
    };
    return (
        <form onSubmit={submit}>
            <div className="space-y-4 px-6 py-5">
                <Input label="Nama Sertifikat" value={form.nama} onChange={e => setForm(p => ({ ...p, nama: e.target.value }))} required placeholder="TOEFL / Sertifikat Tahfidz / dll" />
                <Input label="Penerbit" value={form.penerbit} onChange={e => setForm(p => ({ ...p, penerbit: e.target.value }))} placeholder="Lembaga / Institusi" />
                <Input label="Nomor Sertifikat" value={form.nomor} onChange={e => setForm(p => ({ ...p, nomor: e.target.value }))} placeholder="Opsional" />
                <div className="grid grid-cols-2 gap-3">
                    <div className="space-y-1.5">
                        <label className="text-sm font-medium">Tanggal Terbit</label>
                        <input type="date" value={form.tanggal_terbit} onChange={e => setForm(p => ({ ...p, tanggal_terbit: e.target.value }))}
                            className="w-full h-9 px-3 text-sm rounded-md border border-border-subtle bg-background focus:outline-none focus:ring-2 focus:ring-primary/30 transition-all" />
                    </div>
                    <div className="space-y-1.5">
                        <label className="text-sm font-medium">Tanggal Expired</label>
                        <input type="date" value={form.expired_at} onChange={e => setForm(p => ({ ...p, expired_at: e.target.value }))}
                            className="w-full h-9 px-3 text-sm rounded-md border border-border-subtle bg-background focus:outline-none focus:ring-2 focus:ring-primary/30 transition-all" />
                    </div>
                </div>
            </div>
            <DialogFooter className="flex items-center justify-end gap-2 px-6 py-4 border-t border-border-subtle">
                <Button type="button" variant="outline" onClick={onSuccess}>Batal</Button>
                <Btn type="submit" disabled={!form.nama} icon={isEdit ? <Save className="w-4 h-4" /> : <Plus className="w-4 h-4" />}>{isEdit ? 'Simpan' : 'Tambah'}</Btn>
            </DialogFooter>
        </form>
    );
}
