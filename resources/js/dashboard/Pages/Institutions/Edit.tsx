import React, { useState } from 'react';
import { Head, router } from '@inertiajs/react';
import DashboardLayout from '@dashboard/Layouts/DashboardLayout';
import { Btn } from '@dashboard/Components/ui/btn';
import { Button } from '@dashboard/Components/ui/button';
import { Input } from '@dashboard/Components/ui/input';
import { Textarea } from '@dashboard/Components/ui/textarea';
import { Switch } from '@dashboard/Components/ui/switch';
import { Select, SelectTrigger, SelectValue, SelectContent, SelectItem } from '@dashboard/Components/ui/select';
import { Tabs, TabsList, TabsTrigger, TabsContent } from '@dashboard/Components/ui/tabs';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogClose, DialogFooter } from '@dashboard/Components/ui/dialog';
import {
    Save, ArrowLeft, Image as ImageIcon, Plus, Trash2, Info, LayoutTemplate,
    Star, MapPin, Hash, ScrollText, Phone, Pencil, X,
} from 'lucide-react';
import MediaPicker from '@dashboard/Components/MediaPicker';
import { IconPicker } from '@dashboard/Components/ui/icon-picker';

interface Facility { name: string; description: string; icon: string; }
interface InstitutionType { id: string; nama: string; }
interface ContactType { id: string; nama: string; icon: string | null; }
interface ContactItem { id: string; contact_type_id: string; type: ContactType | null; value: string; is_primary: boolean; }
interface Legality {
    id?: string; nspp?: string; npsn?: string; kode_registrasi?: string;
    nomor_ijop?: string; tanggal_ijop?: string; nomor_akta_yayasan?: string;
    npwp?: string; tahun_berdiri_masehi?: string; tahun_berdiri_hijriyah?: string;
}
interface AddressItem {
    id?: string; alamat_jalan?: string; rt?: string; rw?: string; kode_pos?: string;
    provinsi?: string; kabupaten_kota?: string; kecamatan?: string; desa_kelurahan?: string;
    latitude?: string; longitude?: string;
}

interface Institution {
    id: string; name: string; slug: string; short_description: string | null;
    content: string | null; facilities: Facility[] | null; extracurriculars: string[] | null;
    logo_url: string | null; cover_url: string | null; registration_url: string | null;
    is_active: boolean; institution_type_id: string | null; kode: string | null;
    alamat: string | null; status: string; type: InstitutionType | null;
    legality: Legality | null; address: AddressItem | null;
    institution_contacts: ContactItem[];
}

export default function InstitutionEdit({ institution, institutionTypes, contact_types }: {
    institution: Institution; institutionTypes: InstitutionType[]; contact_types: ContactType[];
}) {
    const [saving, setSaving] = useState(false);
    const [formData, setFormData] = useState({
        name: institution.name || '', slug: institution.slug || '',
        short_description: institution.short_description || '', content: institution.content || '',
        facilities: institution.facilities || [], extracurriculars: institution.extracurriculars || [],
        logo_url: institution.logo_url || '', cover_url: institution.cover_url || '',
        registration_url: institution.registration_url || '', is_active: institution.is_active ?? true,
        institution_type_id: institution.institution_type_id || '', kode: institution.kode || '',
        alamat: institution.alamat || '', status: institution.status || 'draft',
    });
    const [mediaPickerFor, setMediaPickerFor] = useState<'logo' | 'cover' | null>(null);

    // EMIS forms
    const [legality, setLegality] = useState<Legality>(institution.legality || {});
    const [address, setAddress] = useState<AddressItem>(institution.address || {});
    const [contacts, setContacts] = useState<ContactItem[]>(institution.institution_contacts || []);

    // Dialog state
    const [contactDialog, setContactDialog] = useState(false);
    const [editContactId, setEditContactId] = useState<string | null>(null);
    const [contactForm, setContactForm] = useState({ contact_type_id: '', value: '', is_primary: false });

    const contactsWithTypes = contacts.map(c => ({
        ...c,
        type: c.type || contact_types.find(ct => ct.id === c.contact_type_id) || null,
    }));

    const statusOptions = [
        { value: 'draft', label: 'Draft' },
        { value: 'menunggu_kelengkapan', label: 'Menunggu Kelengkapan' },
        { value: 'lengkap', label: 'Lengkap' },
        { value: 'terverifikasi', label: 'Terverifikasi' },
    ];

    const updateField = (field: keyof typeof formData, value: any) => setFormData(prev => ({ ...prev, [field]: value }));

    const saveAll = () => {
        setSaving(true);
        router.put(`/institutions/${institution.id}`, formData as any, {
            preserveScroll: true,
            onFinish: () => setSaving(false),
        });
    };

    const saveLegality = () => {
        router.put(`/institutions/${institution.id}/legality`, legality as any, { preserveScroll: true });
    };

    const saveAddress = () => {
        router.put(`/institutions/${institution.id}/address`, address as any, { preserveScroll: true });
    };

    const saveContact = (e: React.FormEvent) => {
        e.preventDefault();
        const isEdit = !!editContactId;
        if (isEdit) {
            router.put(`/institutions/${institution.id}/contacts/${editContactId}`, contactForm, { preserveScroll: true, onSuccess: () => { setContactDialog(false); setEditContactId(null); setContactForm({ contact_type_id: '', value: '', is_primary: false }); } });
        } else {
            router.post(`/institutions/${institution.id}/contacts`, contactForm, { preserveScroll: true, onSuccess: () => { setContactDialog(false); setContactForm({ contact_type_id: '', value: '', is_primary: false }); } });
        }
    };

    const editContact = (c: ContactItem) => {
        setEditContactId(c.id);
        setContactForm({ contact_type_id: c.contact_type_id, value: c.value, is_primary: c.is_primary });
        setContactDialog(true);
    };

    const addFacility = () => { const f = [...formData.facilities]; f.push({ name: '', description: '', icon: 'home' }); updateField('facilities', f); };
    const updateFacility = (i: number, field: keyof Facility, val: string) => { const f = [...formData.facilities]; f[i] = { ...f[i], [field]: val }; updateField('facilities', f); };
    const removeFacility = (i: number) => { const f = [...formData.facilities]; f.splice(i, 1); updateField('facilities', f); };
    const addExtracurricular = () => { const e = [...formData.extracurriculars]; e.push(''); updateField('extracurriculars', e); };
    const updateExtracurricular = (i: number, val: string) => { const e = [...formData.extracurriculars]; e[i] = val; updateField('extracurriculars', e); };
    const removeExtracurricular = (i: number) => { const e = [...formData.extracurriculars]; e.splice(i, 1); updateField('extracurriculars', e); };

    return (
        <DashboardLayout>
            <Head title={`Edit Lembaga: ${institution.name}`} />

            <div className="space-y-6 max-w-5xl">
                <div className="flex items-center justify-between">
                    <div className="flex items-center gap-3">
                        <Btn variant="ghost" size="sm" onClick={() => router.visit('/institutions')} icon={<ArrowLeft className="w-4 h-4" />} />
                        <div>
                            <h1 className="text-2xl font-bold text-foreground">Edit: {institution.name}</h1>
                            <p className="text-sm text-muted-foreground">/{institution.slug}</p>
                        </div>
                    </div>
                    <Btn onClick={saveAll} disabled={saving} icon={<Save className="w-4 h-4" />}>
                        {saving ? 'Menyimpan...' : 'Simpan Perubahan'}
                    </Btn>
                </div>

                <Tabs defaultValue="general" className="w-full">
                    <TabsList className="w-full justify-start h-auto p-1 mb-4 flex-wrap gap-1">
                        <TabsTrigger value="general" className="gap-2 px-4 py-2"><Info className="w-4 h-4" /> Informasi Umum</TabsTrigger>
                        <TabsTrigger value="alamat" className="gap-2 px-4 py-2"><MapPin className="w-4 h-4" /> Alamat & Status</TabsTrigger>
                        <TabsTrigger value="content" className="gap-2 px-4 py-2"><LayoutTemplate className="w-4 h-4" /> Profil</TabsTrigger>
                        <TabsTrigger value="features" className="gap-2 px-4 py-2"><Star className="w-4 h-4" /> Fasilitas</TabsTrigger>
                        <TabsTrigger value="legalitas" className="gap-2 px-4 py-2"><ScrollText className="w-4 h-4" /> Legalitas</TabsTrigger>
                        <TabsTrigger value="alamat-emis" className="gap-2 px-4 py-2"><MapPin className="w-4 h-4" /> Alamat EMIS</TabsTrigger>
                        <TabsTrigger value="kontak" className="gap-2 px-4 py-2"><Phone className="w-4 h-4" /> Kontak</TabsTrigger>
                    </TabsList>

                    {/* GENERAL TAB */}
                    <TabsContent value="general" className="space-y-6">
                        <div className="rounded-xl border border-border bg-background p-6 space-y-6">
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div className="space-y-2">
                                    <label className="text-sm font-medium">Nama Lembaga</label>
                                    <Input value={formData.name} onChange={e => updateField('name', e.target.value)} />
                                </div>
                                <div className="space-y-2">
                                    <label className="text-sm font-medium">Slug URL</label>
                                    <Input value={formData.slug} onChange={e => updateField('slug', e.target.value)} />
                                </div>
                                <div className="space-y-2">
                                    <label className="text-sm font-medium">Jenis Lembaga</label>
                                    <Select value={formData.institution_type_id} onValueChange={val => updateField('institution_type_id', val)}>
                                        <SelectTrigger><SelectValue placeholder="Pilih jenis..." /></SelectTrigger>
                                        <SelectContent>{institutionTypes.map(t => <SelectItem key={t.id} value={t.id}>{t.nama}</SelectItem>)}</SelectContent>
                                    </Select>
                                </div>
                                <div className="space-y-2">
                                    <label className="text-sm font-medium">Kode Lembaga</label>
                                    <div className="relative">
                                        <Hash className="absolute left-2.5 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground" />
                                        <Input className="pl-8" value={formData.kode} onChange={e => updateField('kode', e.target.value)} placeholder="Misal: MA-DAYAMA" />
                                    </div>
                                </div>
                                <div className="space-y-2 md:col-span-2">
                                    <label className="text-sm font-medium">Deskripsi Singkat</label>
                                    <Textarea value={formData.short_description} onChange={e => updateField('short_description', e.target.value)} rows={3} />
                                    <p className="text-xs text-muted-foreground">Muncul di hero section dan list lembaga.</p>
                                </div>
                                <div className="space-y-2">
                                    <label className="text-sm font-medium">URL Pendaftaran (Link PSB)</label>
                                    <Input value={formData.registration_url} onChange={e => updateField('registration_url', e.target.value)} placeholder="/layanan/psb" />
                                </div>
                                <div className="flex items-center justify-between border border-border p-4 rounded-lg">
                                    <div>
                                        <label className="text-base font-semibold text-foreground">Tampilkan di Website</label>
                                        <p className="text-sm text-muted-foreground">Lembaga ini muncul di halaman Pendidikan</p>
                                    </div>
                                    <Switch checked={formData.is_active} onCheckedChange={checked => updateField('is_active', checked)} />
                                </div>
                            </div>
                        </div>
                        <div className="rounded-xl border border-border bg-background p-6">
                            <h3 className="font-semibold text-foreground mb-4">Media</h3>
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <MediaField label="Cover Image" url={formData.cover_url} onSelect={() => setMediaPickerFor('cover')} onRemove={() => updateField('cover_url', '')} />
                                <MediaField label="Logo Lembaga" url={formData.logo_url} onSelect={() => setMediaPickerFor('logo')} onRemove={() => updateField('logo_url', '')} />
                            </div>
                        </div>
                    </TabsContent>

                    {/* ALAMAT & STATUS TAB */}
                    <TabsContent value="alamat" className="space-y-6">
                        <div className="rounded-xl border border-border bg-background p-6 space-y-6">
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div className="space-y-2">
                                    <label className="text-sm font-medium">Status Lembaga</label>
                                    <Select value={formData.status} onValueChange={val => updateField('status', val)}>
                                        <SelectTrigger><SelectValue /></SelectTrigger>
                                        <SelectContent>{statusOptions.map(opt => <SelectItem key={opt.value} value={opt.value}>{opt.label}</SelectItem>)}</SelectContent>
                                    </Select>
                                </div>
                                <div className="space-y-2">
                                    <label className="text-sm font-medium">Kode Lembaga</label>
                                    <Input value={formData.kode} onChange={e => updateField('kode', e.target.value)} placeholder="Misal: MA-DAYAMA" />
                                </div>
                                <div className="space-y-2 md:col-span-2">
                                    <label className="text-sm font-medium">Alamat</label>
                                    <Textarea value={formData.alamat} onChange={e => updateField('alamat', e.target.value)} rows={3} placeholder="Jl. Contoh No. 123, Kelurahan, Kecamatan, Kota, Provinsi" />
                                </div>
                            </div>
                        </div>
                    </TabsContent>

                    {/* CONTENT TAB */}
                    <TabsContent value="content">
                        <div className="rounded-xl border border-border bg-background p-6 space-y-4">
                            <div className="space-y-2">
                                <label className="text-sm font-medium">Profil / Sistem Pendidikan (HTML/Text Lengkap)</label>
                                <Textarea value={formData.content} onChange={e => updateField('content', e.target.value)} rows={15} />
                                <p className="text-xs text-muted-foreground">Teks utama yang menjelaskan sistem pendidikan atau profil lengkap lembaga. Mendukung format HTML sederhana.</p>
                            </div>
                        </div>
                    </TabsContent>

                    {/* FEATURES TAB */}
                    <TabsContent value="features" className="space-y-6">
                        <div className="rounded-xl border border-border bg-background p-6">
                            <div className="flex items-center justify-between mb-4">
                                <div>
                                    <h3 className="font-semibold text-foreground">Fasilitas</h3>
                                    <p className="text-sm text-muted-foreground">Daftar fasilitas yang dimiliki lembaga ini.</p>
                                </div>
                                <Btn variant="outline" size="sm" onClick={addFacility} icon={<Plus className="w-4 h-4" />}>Tambah Fasilitas</Btn>
                            </div>
                            <div className="space-y-4">
                                {formData.facilities.map((facility, i) => (
                                    <div key={i} className="relative rounded-lg border border-border p-4 bg-muted/20 grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <button onClick={() => removeFacility(i)} className="absolute top-2 right-2 text-muted-foreground hover:text-destructive transition-colors"><Trash2 className="w-4 h-4" /></button>
                                        <div className="space-y-1.5">
                                            <label className="text-xs font-medium">Nama Fasilitas</label>
                                            <Input value={facility.name} onChange={e => updateFacility(i, 'name', e.target.value)} placeholder="Misal: Asrama Putra & Putri" />
                                        </div>
                                        <div className="space-y-1.5">
                                            <IconPicker label="Ikon Fasilitas" value={facility.icon} onChange={val => updateFacility(i, 'icon', val)} />
                                        </div>
                                        <div className="space-y-1.5 md:col-span-2">
                                            <label className="text-xs font-medium">Deskripsi Singkat</label>
                                            <Input value={facility.description} onChange={e => updateFacility(i, 'description', e.target.value)} />
                                        </div>
                                    </div>
                                ))}
                            </div>
                        </div>
                        <div className="rounded-xl border border-border bg-background p-6">
                            <div className="flex items-center justify-between mb-4">
                                <div>
                                    <h3 className="font-semibold text-foreground">Kegiatan Ekstrakurikuler</h3>
                                    <p className="text-sm text-muted-foreground">Daftar kegiatan pengembangan diri santri.</p>
                                </div>
                                <Btn variant="outline" size="sm" onClick={addExtracurricular} icon={<Plus className="w-4 h-4" />}>Tambah Ekskul</Btn>
                            </div>
                            <div className="space-y-3">
                                {formData.extracurriculars.map((item, i) => (
                                    <div key={i} className="flex items-center gap-3">
                                        <Input value={item} onChange={e => updateExtracurricular(i, e.target.value)} placeholder="Nama ekstrakurikuler" />
                                        <Btn variant="outline" size="sm" onClick={() => removeExtracurricular(i)} className="text-destructive" icon={<Trash2 className="w-4 h-4" />} />
                                    </div>
                                ))}
                            </div>
                        </div>
                    </TabsContent>

                    {/* LEGALITAS TAB */}
                    <TabsContent value="legalitas" className="space-y-4">
                        <div className="rounded-xl border border-border bg-background p-6">
                            <div className="flex items-center justify-between mb-5">
                                <div>
                                    <h3 className="font-semibold">Data Legalitas</h3>
                                    <p className="text-sm text-muted-foreground mt-0.5">Informasi legal dan administrasi lembaga (EMIS)</p>
                                </div>
                                <Btn onClick={saveLegality} icon={<Save className="w-4 h-4" />}>Simpan Legalitas</Btn>
                            </div>
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <Input label="NSPP" value={legality.nspp || ''} onChange={e => setLegality(p => ({ ...p, nspp: e.target.value }))} />
                                <Input label="NPSN" value={legality.npsn || ''} onChange={e => setLegality(p => ({ ...p, npsn: e.target.value }))} />
                                <Input label="Kode Registrasi" value={legality.kode_registrasi || ''} onChange={e => setLegality(p => ({ ...p, kode_registrasi: e.target.value }))} />
                                <Input label="Nomor IZOP" value={legality.nomor_ijop || ''} onChange={e => setLegality(p => ({ ...p, nomor_ijop: e.target.value }))} />
                                <div className="space-y-1.5">
                                    <label className="text-sm font-medium">Tanggal IZOP</label>
                                    <input type="date" value={legality.tanggal_ijop || ''} onChange={e => setLegality(p => ({ ...p, tanggal_ijop: e.target.value }))}
                                        className="w-full h-9 px-3 text-sm rounded-md border border-border-subtle bg-background focus:outline-none focus:ring-2 focus:ring-primary/30 transition-all" />
                                </div>
                                <Input label="Nomor Akta Yayasan" value={legality.nomor_akta_yayasan || ''} onChange={e => setLegality(p => ({ ...p, nomor_akta_yayasan: e.target.value }))} />
                                <Input label="NPWP" value={legality.npwp || ''} onChange={e => setLegality(p => ({ ...p, npwp: e.target.value }))} />
                                <Input label="Tahun Berdiri (Masehi)" value={legality.tahun_berdiri_masehi || ''} onChange={e => setLegality(p => ({ ...p, tahun_berdiri_masehi: e.target.value }))} placeholder="1998" />
                                <Input label="Tahun Berdiri (Hijriyah)" value={legality.tahun_berdiri_hijriyah || ''} onChange={e => setLegality(p => ({ ...p, tahun_berdiri_hijriyah: e.target.value }))} placeholder="1419" />
                            </div>
                        </div>
                    </TabsContent>

                    {/* ALAMAT EMIS TAB */}
                    <TabsContent value="alamat-emis" className="space-y-4">
                        <div className="rounded-xl border border-border bg-background p-6">
                            <div className="flex items-center justify-between mb-5">
                                <div>
                                    <h3 className="font-semibold">Alamat (EMIS)</h3>
                                    <p className="text-sm text-muted-foreground mt-0.5">Data alamat lengkap untuk keperluan EMIS</p>
                                </div>
                                <Btn onClick={saveAddress} icon={<Save className="w-4 h-4" />}>Simpan Alamat</Btn>
                            </div>
                            <div className="space-y-4">
                                <div className="space-y-1.5">
                                    <label className="text-sm font-medium">Alamat Jalan</label>
                                    <textarea value={address.alamat_jalan || ''} onChange={e => setAddress(p => ({ ...p, alamat_jalan: e.target.value }))} rows={2}
                                        className="w-full px-3 py-2 text-sm rounded-md border border-border-subtle bg-background focus:outline-none focus:ring-2 focus:ring-primary/30 transition-all resize-none" />
                                </div>
                                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <Input label="RT" value={address.rt || ''} onChange={e => setAddress(p => ({ ...p, rt: e.target.value }))} />
                                    <Input label="RW" value={address.rw || ''} onChange={e => setAddress(p => ({ ...p, rw: e.target.value }))} />
                                    <Input label="Desa/Kelurahan" value={address.desa_kelurahan || ''} onChange={e => setAddress(p => ({ ...p, desa_kelurahan: e.target.value }))} />
                                    <Input label="Kecamatan" value={address.kecamatan || ''} onChange={e => setAddress(p => ({ ...p, kecamatan: e.target.value }))} />
                                    <Input label="Kabupaten/Kota" value={address.kabupaten_kota || ''} onChange={e => setAddress(p => ({ ...p, kabupaten_kota: e.target.value }))} />
                                    <Input label="Provinsi" value={address.provinsi || ''} onChange={e => setAddress(p => ({ ...p, provinsi: e.target.value }))} />
                                    <Input label="Kode Pos" value={address.kode_pos || ''} onChange={e => setAddress(p => ({ ...p, kode_pos: e.target.value }))} />
                                </div>
                                <div className="grid grid-cols-2 gap-4">
                                    <Input label="Latitude" value={address.latitude || ''} onChange={e => setAddress(p => ({ ...p, latitude: e.target.value }))} />
                                    <Input label="Longitude" value={address.longitude || ''} onChange={e => setAddress(p => ({ ...p, longitude: e.target.value }))} />
                                </div>
                            </div>
                        </div>
                    </TabsContent>

                    {/* KONTAK TAB */}
                    <TabsContent value="kontak" className="space-y-4">
                        <div className="rounded-xl border border-border bg-background p-6">
                            <div className="flex items-center justify-between mb-5">
                                <div>
                                    <h3 className="font-semibold">Kontak Lembaga</h3>
                                    <p className="text-sm text-muted-foreground mt-0.5">Nomor telepon, email, dan kontak lainnya</p>
                                </div>
                                <Btn variant="outline" size="sm" onClick={() => { setEditContactId(null); setContactForm({ contact_type_id: '', value: '', is_primary: false }); setContactDialog(true); }} icon={<Plus className="w-4 h-4" />}>Tambah Kontak</Btn>
                            </div>
                            {contactsWithTypes.length === 0 ? (
                                <div className="flex flex-col items-center justify-center py-10 text-center border-2 border-dashed border-border rounded-lg">
                                    <Phone className="w-10 h-10 text-muted-foreground/30 mb-3" />
                                    <p className="text-sm font-medium text-muted-foreground">Belum ada kontak</p>
                                    <p className="text-xs text-muted-foreground/70 mt-1">Tambahkan kontak untuk lembaga ini</p>
                                </div>
                            ) : (
                                <div className="space-y-3">
                                    {contactsWithTypes.map((c) => (
                                        <div key={c.id} className="flex items-start gap-3 p-4 rounded-lg border border-border-subtle bg-surface-muted/20">
                                            <div className="flex-1 min-w-0">
                                                <div className="flex items-center gap-2">
                                                    <span className="font-medium text-sm">{c.type?.nama || 'Kontak'}</span>
                                                    {c.is_primary && <span className="px-1.5 py-0.5 text-[10px] font-medium rounded-full bg-blue-50 text-blue-700 border border-blue-200 dark:bg-blue-950/40 dark:text-blue-300">Utama</span>}
                                                </div>
                                                <p className="text-sm text-foreground mt-0.5">{c.value}</p>
                                            </div>
                                            <button onClick={() => editContact(c)} className="p-1.5 rounded-md text-muted-foreground hover:text-foreground hover:bg-surface-muted transition-colors shrink-0"><Pencil className="w-3.5 h-3.5" /></button>
                                            <button onClick={() => router.delete(`/institutions/${institution.id}/contacts/${c.id}`, { preserveScroll: true })} className="p-1.5 rounded-md text-muted-foreground hover:text-destructive hover:bg-destructive/10 transition-colors shrink-0"><Trash2 className="w-3.5 h-3.5" /></button>
                                        </div>
                                    ))}
                                </div>
                            )}
                        </div>
                    </TabsContent>
                </Tabs>
            </div>

            {/* Contact Dialog */}
            <Dialog open={contactDialog} onOpenChange={o => { if (!o) setContactDialog(false); }}>
                <DialogContent className="flex flex-col p-0 gap-0 max-w-md">
                    <DialogHeader className="flex flex-row items-center justify-between px-6 py-4 border-b border-border-subtle shrink-0">
                        <DialogTitle className="text-base">{editContactId ? 'Edit' : 'Tambah'} Kontak</DialogTitle>
                        <DialogClose className="p-1 rounded-md text-muted-foreground hover:text-foreground hover:bg-surface-muted transition-colors"><X className="w-4 h-4" /></DialogClose>
                    </DialogHeader>
                    <form onSubmit={saveContact}>
                        <div className="space-y-4 px-6 py-5">
                            <div className="space-y-1.5">
                                <label className="text-sm font-medium">Tipe Kontak <span className="text-destructive">*</span></label>
                                <Select value={contactForm.contact_type_id} onValueChange={v => setContactForm(p => ({ ...p, contact_type_id: v }))}>
                                    <SelectTrigger><SelectValue placeholder="Pilih tipe..." /></SelectTrigger>
                                    <SelectContent>{contact_types.map(t => <SelectItem key={t.id} value={t.id}>{t.nama}</SelectItem>)}</SelectContent>
                                </Select>
                            </div>
                            <Input label="Nilai / Alamat" value={contactForm.value} onChange={e => setContactForm(p => ({ ...p, value: e.target.value }))} required placeholder="0812xxxx / email@example.com" />
                            <div className="flex items-center gap-3">
                                <Switch checked={contactForm.is_primary} onCheckedChange={v => setContactForm(p => ({ ...p, is_primary: v }))} />
                                <span className="text-sm">Jadikan kontak utama</span>
                            </div>
                        </div>
                        <DialogFooter className="flex items-center justify-end gap-2 px-6 py-4 border-t border-border-subtle">
                            <Button type="button" variant="outline" onClick={() => setContactDialog(false)}>Batal</Button>
                            <Btn type="submit" disabled={!contactForm.contact_type_id || !contactForm.value} icon={editContactId ? <Save className="w-4 h-4" /> : <Plus className="w-4 h-4" />}>
                                {editContactId ? 'Simpan' : 'Tambah'}
                            </Btn>
                        </DialogFooter>
                    </form>
                </DialogContent>
            </Dialog>

            {/* Media Picker Dialog */}
            {mediaPickerFor && (
                <MediaPicker open={true} onOpenChange={() => setMediaPickerFor(null)} onSelect={(media: any) => {
                    if (media?.original_url) {
                        if (mediaPickerFor === 'logo') updateField('logo_url', media.original_url);
                        if (mediaPickerFor === 'cover') updateField('cover_url', media.original_url);
                    }
                    setMediaPickerFor(null);
                }} />
            )}
        </DashboardLayout>
    );
}

function MediaField({ label, url, onSelect, onRemove }: { label: string; url: string | null; onSelect: () => void; onRemove: () => void }) {
    return (
        <div className="space-y-2">
            <label className="text-sm font-medium">{label}</label>
            {url ? (
                <div className="relative group rounded-lg overflow-hidden border border-border">
                    <img src={url} alt={label} className="w-full h-40 object-cover" />
                    <div className="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2">
                        <Btn variant="outline" size="sm" onClick={onSelect} className="text-white border-white hover:bg-white hover:text-black">Ganti</Btn>
                        <Btn variant="outline" size="sm" onClick={onRemove} className="text-white border-white hover:bg-white hover:text-black hover:bg-destructive hover:border-destructive hover:text-destructive-foreground">Hapus</Btn>
                    </div>
                </div>
            ) : (
                <button onClick={onSelect} className="w-full h-40 border-2 border-dashed border-border rounded-lg flex flex-col items-center justify-center text-muted-foreground hover:bg-muted hover:border-primary/50 transition-colors">
                    <ImageIcon className="w-8 h-8 mb-2 opacity-50" />
                    <span className="text-sm font-medium">Pilih {label}</span>
                </button>
            )}
        </div>
    );
}
