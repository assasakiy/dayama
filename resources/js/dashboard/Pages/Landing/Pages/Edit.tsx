import React, { useState } from 'react';
import { Head, router } from '@inertiajs/react';
import DashboardLayout from '@dashboard/Layouts/DashboardLayout';
import { Tabs, TabsList, TabsTrigger, TabsContent } from '@dashboard/Components/ui/tabs';
import { Input } from '@dashboard/Components/ui/input';
import { Textarea } from '@dashboard/Components/ui/textarea';
import { Btn } from '@dashboard/Components/ui/btn';
import { Switch } from '@dashboard/Components/ui/switch';
import { IconPicker } from '@dashboard/Components/ui/icon-picker';
import MediaPicker from '@dashboard/Components/MediaPicker';
import {
    Save,
    ArrowLeft,
    Image as ImageIcon,
    Type,
    Star,
    BookOpen,
    Plus,
    Trash2,
    GripVertical,
    Settings,
} from 'lucide-react';

interface PageData {
    id: string;
    name: string;
    slug: string;
    sections: Record<string, any>;
    is_active: boolean;
    cta_id?: string | null;
    stat_group_id?: string | null;
    faq_category?: string | null;
}

// Section metadata for display purposes
const SECTION_META: Record<string, { label: string; icon: React.ReactNode; description: string }> = {
    hero: { label: 'Hero', icon: <Star className="w-4 h-4" />, description: 'Bagian utama dengan judul besar, deskripsi, dan tombol aksi.' },
    features: { label: 'Keunggulan', icon: <Star className="w-4 h-4" />, description: 'Daftar fitur/keunggulan yang ditampilkan.' },
    about: { label: 'Tentang', icon: <BookOpen className="w-4 h-4" />, description: 'Informasi sejarah atau deskripsi singkat.' },
    programs: { label: 'Program', icon: <BookOpen className="w-4 h-4" />, description: 'Daftar program pendidikan.' },
};

export default function PageEdit({ page, ctas = [], statGroups = [], faqCategories = [] }: { page: PageData, ctas?: any[], statGroups?: any[], faqCategories?: string[] }) {
    const [sections, setSections] = useState<Record<string, any>>(page.sections || {});
    const [general, setGeneral] = useState({
        cta_id: page.cta_id || '',
        stat_group_id: page.stat_group_id || '',
        faq_category: page.faq_category || '',
    });
    const [saving, setSaving] = useState(false);
    const [mediaPicker, setMediaPicker] = useState<{ sectionKey: string; field: string } | null>(null);

    const sectionKeys = ['komponen_umum', ...Object.keys(sections)];

    const updateField = (sectionKey: string, field: string, value: any) => {
        setSections(prev => ({
            ...prev,
            [sectionKey]: { ...prev[sectionKey], [field]: value },
        }));
    };

    const updateItemField = (sectionKey: string, itemIndex: number, field: string, value: any) => {
        setSections(prev => {
            const items = [...(prev[sectionKey]?.items || [])];
            items[itemIndex] = { ...items[itemIndex], [field]: value };
            return { ...prev, [sectionKey]: { ...prev[sectionKey], items } };
        });
    };

    const addItem = (sectionKey: string) => {
        setSections(prev => {
            const items = [...(prev[sectionKey]?.items || [])];
            items.push({ icon: '', title: '', description: '' });
            return { ...prev, [sectionKey]: { ...prev[sectionKey], items } };
        });
    };

    const removeItem = (sectionKey: string, index: number) => {
        setSections(prev => {
            const items = [...(prev[sectionKey]?.items || [])];
            items.splice(index, 1);
            return { ...prev, [sectionKey]: { ...prev[sectionKey], items } };
        });
    };

    const saveSection = (sectionKey: string) => {
        setSaving(true);
        router.patch(`/landing/pages/${page.id}/section`, {
            section_key: sectionKey,
            section_data: sections[sectionKey],
        }, {
            preserveScroll: true,
            onFinish: () => setSaving(false),
        });
    };

    const saveAll = () => {
        setSaving(true);
        router.put(`/landing/pages/${page.id}`, {
            name: page.name,
            sections,
            is_active: page.is_active,
            cta_id: general.cta_id || null,
            stat_group_id: general.stat_group_id || null,
            faq_category: general.faq_category || null,
        }, {
            preserveScroll: true,
            onFinish: () => setSaving(false),
        });
    };

    const renderTextField = (sectionKey: string, field: string, label: string, multiline = false) => {
        const value = sections[sectionKey]?.[field] ?? '';
        return (
            <div className="space-y-1.5">
                <label className="text-sm font-medium text-foreground">{label}</label>
                {multiline ? (
                    <Textarea
                        value={value}
                        onChange={e => updateField(sectionKey, field, e.target.value)}
                        rows={3}
                        className="resize-none"
                    />
                ) : (
                    <Input
                        value={value}
                        onChange={e => updateField(sectionKey, field, e.target.value)}
                    />
                )}
            </div>
        );
    };

    const renderImageField = (sectionKey: string, field: string, label: string) => {
        const value = sections[sectionKey]?.[field] ?? '';
        return (
            <div className="space-y-1.5">
                <label className="text-sm font-medium text-foreground">{label}</label>
                <div className="flex items-center gap-3">
                    <Input
                        value={value || ''}
                        onChange={e => updateField(sectionKey, field, e.target.value)}
                        placeholder="URL gambar atau pilih dari Media Library"
                        className="flex-1"
                    />
                    <Btn variant="outline" size="sm" onClick={() => setMediaPicker({ sectionKey, field })} icon={<ImageIcon className="w-4 h-4" />} />
                </div>
                {value && (
                    <div className="mt-2 rounded-lg border border-border overflow-hidden max-w-xs">
                        <img src={value} alt={label} className="w-full h-32 object-cover" />
                    </div>
                )}
            </div>
        );
    };

    const renderHeroSection = (sectionKey: string) => (
        <div className="space-y-5">
            {renderTextField(sectionKey, 'badge', 'Teks Badge (Label Kecil di Atas)')}
            {renderTextField(sectionKey, 'title', 'Judul Utama')}
            {renderTextField(sectionKey, 'highlight', 'Teks Highlight (berwarna)')}
            {renderTextField(sectionKey, 'subtitle', 'Deskripsi Singkat', true)}
            {renderImageField(sectionKey, 'image', 'Gambar Hero')}

            <div className="border-t border-border pt-5">
                <h4 className="text-sm font-semibold text-foreground mb-3">Tombol Aksi</h4>
                <div className="grid grid-cols-2 gap-4">
                    {renderTextField(sectionKey, 'button1_text', 'Teks Tombol 1')}
                    {renderTextField(sectionKey, 'button1_url', 'URL Tombol 1 (kosongkan untuk default)')}
                    {renderTextField(sectionKey, 'button2_text', 'Teks Tombol 2')}
                    {renderTextField(sectionKey, 'button2_url', 'URL Tombol 2 (kosongkan untuk default)')}
                </div>
            </div>
        </div>
    );

    const renderItemsSection = (sectionKey: string) => {
        const items = sections[sectionKey]?.items || [];
        return (
            <div className="space-y-5">
                {renderTextField(sectionKey, 'label', 'Label')}
                {renderTextField(sectionKey, 'heading', 'Judul Section')}
                {renderTextField(sectionKey, 'description', 'Deskripsi', true)}

                <div className="border-t border-border pt-5">
                    <div className="flex items-center justify-between mb-3">
                        <h4 className="text-sm font-semibold text-foreground">Daftar Item</h4>
                        <Btn variant="outline" size="sm" onClick={() => addItem(sectionKey)} icon={<Plus className="w-3.5 h-3.5" />}>
                            Tambah Item
                        </Btn>
                    </div>

                    <div className="space-y-3">
                        {items.map((item: any, i: number) => (
                            <div key={i} className="relative rounded-lg border border-border p-4 bg-muted/30">
                                <button
                                    onClick={() => removeItem(sectionKey, i)}
                                    className="absolute top-3 right-3 text-muted-foreground hover:text-destructive transition-colors"
                                >
                                    <Trash2 className="w-4 h-4" />
                                </button>
                                <div className="grid grid-cols-1 md:grid-cols-3 gap-3">
                                    <div className="space-y-1.5">
                                        <IconPicker
                                            label="Ikon Item"
                                            value={item.icon || ''}
                                            onChange={val => updateItemField(sectionKey, i, 'icon', val)}
                                        />
                                    </div>
                                    <div className="space-y-1.5">
                                        <label className="text-xs font-medium text-muted-foreground">Judul</label>
                                        <Input
                                            value={item.title || ''}
                                            onChange={e => updateItemField(sectionKey, i, 'title', e.target.value)}
                                        />
                                    </div>
                                    <div className="space-y-1.5">
                                        <label className="text-xs font-medium text-muted-foreground">Deskripsi</label>
                                        <Input
                                            value={item.description || ''}
                                            onChange={e => updateItemField(sectionKey, i, 'description', e.target.value)}
                                        />
                                    </div>
                                    {sectionKey === 'programs' && (
                                        <div className="space-y-1.5">
                                            <label className="text-xs font-medium text-muted-foreground">URL Target (Opsional)</label>
                                            <Input
                                                value={item.url || ''}
                                                onChange={e => updateItemField(sectionKey, i, 'url', e.target.value)}
                                                placeholder="/pendidikan/program"
                                            />
                                        </div>
                                    )}
                                </div>
                            </div>
                        ))}

                        {items.length === 0 && (
                            <p className="text-sm text-muted-foreground text-center py-6">
                                Belum ada item. Klik "Tambah Item" untuk memulai.
                            </p>
                        )}
                    </div>
                </div>
            </div>
        );
    };

    const renderStatsSection = (sectionKey: string) => {
        const items = sections[sectionKey]?.items || [];
        return (
            <div className="space-y-5">
                <div className="border-t border-border pt-5">
                    <div className="flex items-center justify-between mb-3">
                        <h4 className="text-sm font-semibold text-foreground">Daftar Statistik</h4>
                        <Btn variant="outline" size="sm" onClick={() => addItem(sectionKey)} icon={<Plus className="w-3.5 h-3.5" />}>
                            Tambah Stat
                        </Btn>
                    </div>

                    <div className="space-y-3">
                        {items.map((item: any, i: number) => (
                            <div key={i} className="relative rounded-lg border border-border p-4 bg-muted/30 grid grid-cols-2 gap-4">
                                <button
                                    onClick={() => removeItem(sectionKey, i)}
                                    className="absolute top-3 right-3 text-muted-foreground hover:text-destructive transition-colors"
                                >
                                    <Trash2 className="w-4 h-4" />
                                </button>
                                <div className="space-y-1.5">
                                    <label className="text-sm font-medium text-foreground">Angka / Nilai</label>
                                    <Input
                                        value={item.number || ''}
                                        onChange={e => updateItemField(sectionKey, i, 'number', e.target.value)}
                                        placeholder="Misal: 500+"
                                    />
                                </div>
                                <div className="space-y-1.5">
                                    <label className="text-sm font-medium text-foreground">Label</label>
                                    <Input
                                        value={item.label || ''}
                                        onChange={e => updateItemField(sectionKey, i, 'label', e.target.value)}
                                        placeholder="Misal: Santri Aktif"
                                    />
                                </div>
                            </div>
                        ))}
                    </div>
                </div>
            </div>
        );
    };

    const renderAboutSection = (sectionKey: string) => (
        <div className="space-y-5">
            {renderTextField(sectionKey, 'badge', 'Teks Badge')}
            {renderTextField(sectionKey, 'heading', 'Judul (gunakan <br> untuk baris baru)')}
            {renderTextField(sectionKey, 'description1', 'Paragraf 1', true)}
            {renderTextField(sectionKey, 'description2', 'Paragraf 2', true)}
            
            <div className="grid grid-cols-2 gap-4">
                {renderTextField(sectionKey, 'button1_text', 'Teks Tombol 1')}
                {renderTextField(sectionKey, 'button1_url', 'URL Tombol 1')}
                {renderTextField(sectionKey, 'button2_text', 'Teks Tombol 2')}
                {renderTextField(sectionKey, 'button2_url', 'URL Tombol 2')}
            </div>

            {renderImageField(sectionKey, 'image', 'Gambar Representasi')}
        </div>
    );

    const renderSectionContent = (sectionKey: string) => {
        if (sectionKey === 'hero') return renderHeroSection(sectionKey);
        if (sectionKey === 'features' || sectionKey === 'programs') return renderItemsSection(sectionKey);
        if (sectionKey === 'about') return renderAboutSection(sectionKey);
        if (sectionKey === 'stats') return renderStatsSection(sectionKey);

        // Generic fallback: render all fields as text inputs
        const sectionData = sections[sectionKey] || {};
        return (
            <div className="space-y-4">
                {Object.entries(sectionData).map(([field, value]) => {
                    if (typeof value === 'object') return null;
                    return renderTextField(sectionKey, field, field.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase()));
                })}
            </div>
        );
    };

    const renderGeneralSection = () => (
        <div className="space-y-6">
            <div className="space-y-1.5">
                <label className="text-sm font-medium text-foreground">Call to Action (CTA)</label>
                <select
                    value={general.cta_id}
                    onChange={e => setGeneral(prev => ({ ...prev, cta_id: e.target.value }))}
                    className="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                >
                    <option value="">-- Tidak ada CTA --</option>
                    {ctas.map(cta => (
                        <option key={cta.id} value={cta.id}>{cta.name}</option>
                    ))}
                </select>
                <p className="text-xs text-muted-foreground mt-1">Pilih CTA yang akan ditampilkan di bagian bawah halaman ini.</p>
            </div>

            <div className="space-y-1.5">
                <label className="text-sm font-medium text-foreground">Statistik (Stats)</label>
                <select
                    value={general.stat_group_id}
                    onChange={e => setGeneral(prev => ({ ...prev, stat_group_id: e.target.value }))}
                    className="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                >
                    <option value="">-- Tidak ada Statistik --</option>
                    {statGroups.map(group => (
                        <option key={group.id} value={group.id}>{group.name}</option>
                    ))}
                </select>
                <p className="text-xs text-muted-foreground mt-1">Grup statistik yang akan ditampilkan (biasanya untuk Beranda / Profil).</p>
            </div>

            <div className="space-y-1.5">
                <label className="text-sm font-medium text-foreground">Kategori FAQ</label>
                <select
                    value={general.faq_category}
                    onChange={e => setGeneral(prev => ({ ...prev, faq_category: e.target.value }))}
                    className="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                >
                    <option value="">-- Pilih Kategori --</option>
                    <option value="umum">Umum</option>
                    <option value="psb">Pendaftaran (PSB)</option>
                    <option value="donasi">Donasi</option>
                    {faqCategories.map(cat => (
                        !['umum', 'psb', 'donasi'].includes(cat) && <option key={cat} value={cat}>{cat}</option>
                    ))}
                </select>
                <p className="text-xs text-muted-foreground mt-1">Pilih kategori FAQ jika halaman ini menampilkan modul FAQ (contoh: halaman Layanan).</p>
            </div>
        </div>
    );

    return (
        <DashboardLayout>
            <Head title={`Edit: ${page.name}`} />

            <div className="space-y-6">
                {/* Header */}
                <div className="flex items-center justify-between">
                    <div className="flex items-center gap-3">
                        <Btn variant="ghost" size="sm" onClick={() => router.visit('/landing/pages')} icon={<ArrowLeft className="w-4 h-4" />} />
                        <div>
                            <h1 className="text-2xl font-bold text-foreground">Edit: {page.name}</h1>
                            <p className="text-sm text-muted-foreground">/{page.slug} • {sectionKeys.length} section</p>
                        </div>
                    </div>
                    <Btn onClick={saveAll} disabled={saving} icon={<Save className="w-4 h-4" />}>
                        {saving ? 'Menyimpan...' : 'Simpan Semua'}
                    </Btn>
                </div>

                {/* Tabs */}
                {sectionKeys.length > 0 ? (
                    <Tabs defaultValue={sectionKeys[0]} className="w-full">
                        <TabsList className="w-full justify-start flex-wrap h-auto p-1 mb-4">
                            {sectionKeys.map(key => {
                                if (key === 'komponen_umum') {
                                    return (
                                        <TabsTrigger key={key} value={key} className="gap-2 px-4 py-2">
                                            <Settings className="w-4 h-4" /> Komponen Umum
                                        </TabsTrigger>
                                    );
                                }
                                const meta = SECTION_META[key];
                                return (
                                    <TabsTrigger key={key} value={key} className="gap-2 px-4 py-2">
                                        {meta?.icon}
                                        {meta?.label || key.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase())}
                                    </TabsTrigger>
                                );
                            })}
                        </TabsList>

                        {sectionKeys.map(key => {
                            if (key === 'komponen_umum') {
                                return (
                                    <TabsContent key={key} value={key}>
                                        <div className="rounded-xl border border-border bg-background p-6 mt-4">
                                            <p className="text-sm text-muted-foreground mb-5">Pengaturan komponen dinamis yang akan merender data dari modul lain.</p>
                                            {renderGeneralSection()}
                                            <div className="flex justify-end mt-6 pt-4 border-t border-border">
                                                <Btn onClick={saveAll} disabled={saving} icon={<Save className="w-4 h-4" />}>
                                                    {saving ? 'Menyimpan...' : `Simpan Semua`}
                                                </Btn>
                                            </div>
                                        </div>
                                    </TabsContent>
                                );
                            }
                            
                            const meta = SECTION_META[key];
                            return (
                                <TabsContent key={key} value={key}>
                                    <div className="rounded-xl border border-border bg-background p-6 mt-4">
                                        {meta && (
                                            <p className="text-sm text-muted-foreground mb-5">{meta.description}</p>
                                        )}
                                        {renderSectionContent(key)}

                                        <div className="flex justify-end mt-6 pt-4 border-t border-border">
                                            <Btn variant="outline" onClick={() => saveSection(key)} disabled={saving} icon={<Save className="w-4 h-4" />}>
                                                {saving ? 'Menyimpan...' : `Simpan ${meta?.label || key}`}
                                            </Btn>
                                        </div>
                                    </div>
                                </TabsContent>
                            );
                        })}
                    </Tabs>
                ) : (
                    <div className="text-center py-16 text-muted-foreground rounded-xl border border-border bg-background">
                        <Type className="w-12 h-12 mx-auto mb-4 opacity-40" />
                        <p className="text-lg font-medium">Belum ada section</p>
                        <p className="text-sm">Halaman ini belum memiliki konten section.</p>
                    </div>
                )}
            </div>

            {/* Media Picker Dialog */}
            {mediaPicker && (
                <MediaPicker
                    open={true}
                    onOpenChange={() => setMediaPicker(null)}
                    onSelect={(media: any) => {
                        if (media?.original_url) {
                            updateField(mediaPicker.sectionKey, mediaPicker.field, media.original_url);
                            if (media.id) {
                                updateField(mediaPicker.sectionKey, `${mediaPicker.field}_media_id`, media.id);
                            }
                        }
                        setMediaPicker(null);
                    }}
                />
            )}
        </DashboardLayout>
    );
}
