import React, { useState, useMemo } from 'react';
import { Head, router, useForm, usePage } from '@inertiajs/react';
import DashboardLayout from '@dashboard/Layouts/DashboardLayout';
import {
    Settings, FileText, Search, Image as ImageIcon, Mail, Shield,
    Palette, Globe, LayoutTemplate, Save, Info, Lock,
    Eye, EyeOff, ChevronRight, ArrowLeft, PanelsTopLeft, Bell, X, Check
} from 'lucide-react';
import { Btn } from '@dashboard/Components/ui/btn';
import { Switch } from '@dashboard/Components/ui/switch';
import { Select, SelectTrigger, SelectValue, SelectContent, SelectItem } from '@dashboard/Components/ui/select';
import MediaPicker from '@dashboard/Components/MediaPicker';

const ICONS: Record<string, React.ReactNode> = {
    PanelsTopLeft: <PanelsTopLeft className="w-3.5 h-3.5" />,
    Globe:         <Globe className="w-3.5 h-3.5" />,
    Lock:          <Lock className="w-3.5 h-3.5" />,
    Bell:          <Bell className="w-3.5 h-3.5" />,
};

type SettingField = {
    key: string;
    value: any;
    type: string;
    is_env: boolean;
    is_locked: boolean;
    description: string | null;
};

type Group = {
    key: string;
    name: string;
    icon: string;
    description: string;
};

const SETTINGS_TABS = [
    { key: 'branding', name: 'Branding', icon: 'PanelsTopLeft', description: 'Kelola identitas merek dan optimasi pencarian.' },
    { key: 'visual', name: 'Visual & Tema', icon: 'Globe', description: 'Personalisasi tampilan dan lokalisasi.' },
    { key: 'system', name: 'Sistem & Keamanan', icon: 'Lock', description: 'Konfigurasi teknis, keamanan, dan media.' },
    { key: 'contact', name: 'Kontak & Sosial', icon: 'Bell', description: 'Informasi kontak publik dan tautan.' },
];

const SUB_GROUPS: Record<string, { title: string, description: string }> = {
    identity: { title: 'Identitas Situs', description: 'Logo utama, nama, dan tagline situs web.' },
    seo: { title: 'SEO & Meta Tag', description: 'Pengaturan mesin pencari dan integrasi analitik.' },
    appearance: { title: 'Tampilan', description: 'Skema warna, prasetel, dan warna teks.' },
    localization: { title: 'Lokalisasi & Waktu', description: 'Bahasa, format tanggal, dan zona waktu.' },
    security: { title: 'Keamanan', description: 'Autentikasi, perlindungan login, dan pemeliharaan.' },
    media: { title: 'Media & Penyimpanan', description: 'Batas unggahan dan optimasi gambar.' },
    mail: { title: 'Konfigurasi Email', description: 'Pengaturan server SMTP untuk email keluar.' },
    domains: { title: 'Pengaturan Domain', description: 'Multi-domain dan konfigurasi URL.' },
    pages: { title: 'Halaman Utama', description: 'Pengaturan beranda dan blog.' },
    other: { title: 'Pengaturan Lainnya', description: 'Konfigurasi tambahan.' },
};

const PRESET_COLORS: Record<string, { primary: string, secondary: string, accent: string, heading: string, body: string, muted: string }> = {
    green:  { primary: '#15803D', secondary: '#0F766E', accent: '#D4A017', heading: '#0F172A', body: '#334155', muted: '#64748B' },
    orange: { primary: '#EA580C', secondary: '#D97706', accent: '#0891B2', heading: '#1C1917', body: '#44403C', muted: '#78716C' },
    blue:   { primary: '#2563EB', secondary: '#7C3AED', accent: '#059669', heading: '#0F172A', body: '#334155', muted: '#64748B' },
};

const PRESET_META: Record<string, { label: string, desc: string, icon: string }> = {
    green:  { label: 'Hijau Alami', desc: 'Hijau segar khas pesantren', icon: '🌿' },
    orange: { label: 'Orange Hangat', desc: 'Semangat & kreativitas', icon: '🔥' },
    blue:   { label: 'Biru Profesional', desc: 'Modern & terpercaya', icon: '🌊' },
};

export default function SettingsShow({
    groups,
    context,
    fields,
    isSingleGroup,
}: {
    groups: Group[];
    context: string;
    fields: SettingField[];
    defaultActiveTab: string;
    isSingleGroup: boolean;
}) {
    const [activeTab, setActiveTab] = useState(isSingleGroup ? groups[0]?.key : 'branding');
    
    const { data, setData, put, processing, isDirty, transform } = useForm<Record<string, any>>(() => {
        const initial: Record<string, any> = {};
        for (const field of fields) {
            let val = field.value;
            if (field.type === 'boolean' && typeof val === 'string') {
                val = val === 'true' || val === '1';
            }
            initial[field.key] = val ?? '';
        }
        return initial;
    });
    
    transform((data) => ({
        settings: data,
    }));
    
    const [showPasswords, setShowPasswords] = useState<Record<string, boolean>>({});
    const [mediaPickerField, setMediaPickerField] = useState<string | null>(null);

    const handleMediaSelect = (media: any) => {
        if (mediaPickerField) {
            if (media instanceof File) {
                handleFieldChange(mediaPickerField, media);
                handleFieldChange(mediaPickerField + '_media_id', null);
            } else {
                handleFieldChange(mediaPickerField + '_media_id', media.id);
                handleFieldChange(mediaPickerField + '_preview', media.original_url);
            }
            setMediaPickerField(null);
        }
    };

    const handleFieldChange = (key: string, value: any) => {
        setData((prevData: Record<string, any>) => {
            const next = { ...prevData, [key]: value };
            if (key === 'mail.encryption') {
                if (value === 'tls') next['mail.port'] = 587;
                else if (value === 'ssl') next['mail.port'] = 465;
            }
            return next;
        });
    };

    const handlePresetSelect = (preset: string) => {
        setData((prev: Record<string, any>) => {
            const next: Record<string, any> = { ...prev, 'appearance.color_preset': preset };
            if (preset !== 'custom' && PRESET_COLORS[preset]) {
                const c = PRESET_COLORS[preset];
                next['appearance.primary_color'] = c.primary;
                next['appearance.secondary_color'] = c.secondary;
                next['appearance.accent_color'] = c.accent;
                next['appearance.heading_color'] = c.heading;
                next['appearance.body_color'] = c.body;
                next['appearance.muted_color'] = c.muted;
            }
            return next;
        });
    };

    const handleSave = () => {
        let url = `/settings/${context}`;
        if (context === 'landing') {
            url = `/landing/settings`;
        }
        
        if (isSingleGroup) {
            url += `/${activeTab}`;
        }

        put(url, {
            preserveScroll: true,
        });
    };

    const labelFrom = (key: string) => {
        const short = key.split('.').pop() ?? key;
        return short.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
    };

    const getFieldTab = (fieldKey: string) => {
        if (fieldKey.startsWith('seo.') || fieldKey === 'general.site_name' || fieldKey === 'general.tagline' || fieldKey === 'general.site_description' || fieldKey.includes('logo') || fieldKey.includes('favicon')) {
            return 'branding';
        }
        if (fieldKey.startsWith('appearance.') || fieldKey === 'general.timezone' || fieldKey === 'general.language' || fieldKey === 'general.date_format') {
            return 'visual';
        }
        if (fieldKey.startsWith('security.') || fieldKey.startsWith('media.')) {
            return 'system';
        }
        if (fieldKey.startsWith('contact.') || fieldKey.startsWith('social.')) {
            return 'contact';
        }
        return 'system';
    };

    const getFieldSubGroup = (fieldKey: string) => {
        if (fieldKey.startsWith('seo.')) return 'seo';
        if (fieldKey.includes('logo') || fieldKey.includes('favicon') || fieldKey === 'general.site_name' || fieldKey === 'general.tagline' || fieldKey === 'general.site_description') return 'identity';
        if (fieldKey.startsWith('appearance.')) return 'appearance';
        if (fieldKey === 'general.timezone' || fieldKey === 'general.language' || fieldKey === 'general.date_format') return 'localization';
        if (fieldKey.startsWith('security.')) return 'security';
        if (fieldKey.startsWith('media.')) return 'media';
        if (fieldKey.startsWith('mail.')) return 'mail';
        if (fieldKey.startsWith('domains.')) return 'domains';
        if (fieldKey.startsWith('pages.')) return 'pages';
        return 'other';
    };

    const renderSelect = (key: string, value: any, disabled: boolean, options: { value: string, label: string }[]) => {
        return (
            <Select disabled={disabled} value={String(value || '')} onValueChange={v => handleFieldChange(key, v)}>
                <SelectTrigger className={disabled ? 'opacity-70 bg-surface-muted cursor-not-allowed' : ''}>
                    <SelectValue placeholder="Pilih opsi..." />
                </SelectTrigger>
                <SelectContent>
                    {options.map(opt => (
                        <SelectItem key={opt.value} value={opt.value}>{opt.label}</SelectItem>
                    ))}
                </SelectContent>
            </Select>
        );
    };

    const currentPreset = data['appearance.color_preset'] || 'green';
    const isCustom = currentPreset === 'custom';

    const previewUrl = (key: string) => {
        const v = data[key];
        const preview = data[key + '_preview'];
        if (preview) return preview;
        if (v instanceof File) return URL.createObjectURL(v);
        return v || '';
    };

    const { domain_main } = usePage().props as { domain_main?: string };
    const brandDomain = domain_main || 'test-blog.test';

    const brandPreviewName = data['general.site_name'] || 'Nama Situs';
    const brandPreviewTagline = data['general.tagline'] || 'Tagline situs';
    const brandPreviewDesc = data['general.site_description'] || '';

    const renderBrandingFields = (identityFields: SettingField[], seoFields: SettingField[]) => {
        const logoField = identityFields.find(f => f.key === 'general.logo_url');
        const faviconField = identityFields.find(f => f.key === 'general.favicon_url');
        const logoUrl = previewUrl('general.logo_url');
        const faviconUrl = previewUrl('general.favicon_url');
        const hasLogo = !!logoUrl;

        return (
            <>
                <div className="mb-8">
                    <h3 className="text-base font-semibold mb-1">Pratinjau Brand</h3>
                    <p className="text-xs text-muted-foreground mb-4">Pratinjau langsung tampilan brand Anda di situs.</p>

                    <div className="rounded-xl border border-border-subtle overflow-hidden bg-white shadow-sm">
                        <div className="p-5 sm:p-6" style={{ background: 'linear-gradient(135deg, var(--color-primary), var(--color-secondary))' }}>
                            <div className="flex items-center gap-4">
                                <div className="w-12 h-12 rounded-xl bg-white/20 backdrop-blur-sm flex items-center justify-center overflow-hidden shrink-0 shadow-sm border border-white/10">
                                    {hasLogo ? (
                                        <img src={logoUrl} alt="" className="w-full h-full object-contain" />
                                    ) : (
                                        <PanelsTopLeft className="w-6 h-6 text-white/80" />
                                    )}
                                </div>
                                <div className="min-w-0">
                                    <div className="text-white font-bold text-lg truncate">{brandPreviewName}</div>
                                    <div className="text-white/70 text-xs truncate">{brandPreviewTagline}</div>
                                </div>
                            </div>
                        </div>
                        {brandPreviewDesc && (
                            <div className="px-5 sm:px-6 py-4 text-sm text-muted-foreground leading-relaxed border-b border-border-subtle">
                                {brandPreviewDesc}
                            </div>
                        )}
                        <div className="px-5 sm:px-6 py-3 flex items-center gap-3 text-xs text-muted-foreground/60 bg-surface-muted/20">
                            <Globe className="w-3.5 h-3.5" />
                            {brandDomain}
                        </div>
                    </div>
                </div>

                <div className="mb-8">
                    <h3 className="text-base font-semibold mb-1">Identitas Situs</h3>
                    <p className="text-xs text-muted-foreground mb-4">Logo utama, nama, dan tagline situs web.</p>

                    <div className="grid grid-cols-1 md:grid-cols-2 gap-5">
                        {['general.site_name', 'general.tagline', 'general.site_description'].map(key => {
                            const field = identityFields.find(f => f.key === key);
                            if (!field) return null;
                            const isFullWidth = key === 'general.site_description';
                            return (
                                <div key={key} className={isFullWidth ? 'md:col-span-2' : ''}>
                                    <label className="block text-sm font-medium mb-1.5">{labelFrom(key)}</label>
                                    {renderField(field)}
                                    {field.description && (
                                        <p className="mt-1 text-xs text-muted-foreground flex items-start gap-1">
                                            <Info className="w-3 h-3 shrink-0 mt-0.5" />
                                            {field.description}
                                        </p>
                                    )}
                                </div>
                            );
                        })}
                    </div>

                    <div className="mt-5">
                        <label className="block text-sm font-medium mb-3">Logo & Favicon</label>
                        <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            {logoField && (
                                <div className="rounded-lg border border-border-subtle overflow-hidden">
                                    <div className="p-4 bg-surface-muted/30 border-b border-border-subtle flex items-center gap-3">
                                        {logoUrl ? (
                                            <img src={logoUrl} alt="" className="w-10 h-10 rounded-lg object-contain bg-background border border-border-subtle" />
                                        ) : (
                                            <div className="w-10 h-10 rounded-lg bg-surface-muted border border-border-subtle flex items-center justify-center">
                                                <ImageIcon className="w-5 h-5 text-muted-foreground/50" />
                                            </div>
                                        )}
                                        <div>
                                            <div className="text-sm font-medium">Logo</div>
                                            <div className="text-xs text-muted-foreground">Tampil di header situs</div>
                                        </div>
                                    </div>
                                    <div className="p-3">
                                        {renderField(logoField)}
                                    </div>
                                </div>
                            )}
                            {faviconField && (
                                <div className="rounded-lg border border-border-subtle overflow-hidden">
                                    <div className="p-4 bg-surface-muted/30 border-b border-border-subtle flex items-center gap-3">
                                        {faviconUrl ? (
                                            <img src={faviconUrl} alt="" className="w-8 h-8 rounded object-contain bg-background border border-border-subtle" />
                                        ) : (
                                            <div className="w-8 h-8 rounded bg-surface-muted border border-border-subtle flex items-center justify-center">
                                                <Search className="w-4 h-4 text-muted-foreground/50" />
                                            </div>
                                        )}
                                        <div>
                                            <div className="text-sm font-medium">Favicon</div>
                                            <div className="text-xs text-muted-foreground">Ikon tab browser</div>
                                        </div>
                                    </div>
                                    <div className="p-3">
                                        {renderField(faviconField)}
                                    </div>
                                </div>
                            )}
                        </div>
                    </div>
                </div>

                {seoFields.length > 0 && (
                    <div>
                        <h3 className="text-base font-semibold mb-1">SEO & Meta Tag</h3>
                        <p className="text-xs text-muted-foreground mb-4">Pengaturan mesin pencari dan integrasi analitik.</p>

                        <div className="grid grid-cols-1 md:grid-cols-2 gap-5">
                            {seoFields.map(field => {
                                const isFullWidth = field.type === 'boolean' || field.type === 'text' || field.key.includes('description');
                                return (
                                    <div key={field.key} className={isFullWidth ? 'md:col-span-2' : ''}>
                                        <label className="block text-sm font-medium mb-1.5">{labelFrom(field.key)}</label>
                                        {renderField(field)}
                                        {field.description && (
                                            <p className="mt-1 text-xs text-muted-foreground flex items-start gap-1">
                                                <Info className="w-3 h-3 shrink-0 mt-0.5" />
                                                {field.description}
                                            </p>
                                        )}
                                    </div>
                                );
                            })}
                        </div>
                    </div>
                )}
            </>
        );
    };

    const renderField = (field: SettingField) => {
        const value = data[field.key];
        const isAppearanceColor = field.key.startsWith('appearance.') && field.key !== 'appearance.color_preset' && field.key.includes('color');
        const disabled = field.is_env || field.is_locked || (isAppearanceColor && !isCustom);
        const baseClass = `w-full rounded-md border px-3 py-2 text-sm transition-colors focus:outline-none focus:ring-1 focus:ring-primary ${
            disabled
                ? 'bg-surface-muted text-muted-foreground border-border-subtle cursor-not-allowed opacity-70'
                : 'bg-background border-border-subtle hover:border-primary/50 focus:border-primary'
        }`;

        if (field.type === 'boolean') {
            return (
                <label className="flex items-center gap-3 cursor-pointer group">
                    <Switch
                        checked={!!value}
                        onCheckedChange={(checked) => handleFieldChange(field.key, checked)}
                        disabled={disabled}
                    />
                    <span className="text-sm text-muted-foreground group-hover:text-foreground transition-colors">
                        {!!value ? 'Aktif' : 'Nonaktif'}
                    </span>
                </label>
            );
        }

        if (field.key === 'general.logo_url' || field.key === 'general.favicon_url' || field.key === 'seo.og_image_url') {
            const previewUrl = data[field.key + '_preview'] || (value instanceof File ? URL.createObjectURL(value) : value);
            return (
                <div className="space-y-3">
                    {previewUrl ? (
                        <div className="relative rounded-lg overflow-hidden border border-border-subtle group bg-surface-muted/30">
                            <img src={previewUrl} alt={labelFrom(field.key)} className="w-full h-40 object-contain bg-surface-muted/30" />
                            {!disabled && (
                                <button
                                    type="button"
                                    onClick={() => {
                                        handleFieldChange(field.key, '');
                                        handleFieldChange(field.key + '_preview', '');
                                        handleFieldChange(field.key + '_media_id', null);
                                    }}
                                    className="absolute top-2 right-2 p-1.5 rounded-full bg-background/80 backdrop-blur-sm text-muted-foreground hover:text-foreground opacity-0 group-hover:opacity-100 transition-opacity"
                                >
                                    <X className="w-4 h-4" />
                                </button>
                            )}
                        </div>
                    ) : (
                        <button
                            type="button"
                            onClick={() => !disabled && setMediaPickerField(field.key)}
                            disabled={disabled}
                            className={`w-full h-28 border-2 border-dashed border-border-subtle rounded-lg flex flex-col items-center justify-center gap-2 transition-all ${
                                disabled ? 'bg-surface-muted text-muted-foreground cursor-not-allowed opacity-70' : 'text-muted-foreground hover:text-foreground hover:border-primary hover:bg-surface-muted/30 cursor-pointer'
                            }`}
                        >
                            <ImageIcon className="w-5 h-5" />
                            <span className="text-sm font-medium">Pilih atau unggah gambar</span>
                        </button>
                    )}
                </div>
            );
        }

        if (field.key === 'mail.encryption') {
            return renderSelect(field.key, value, disabled, [
                { value: 'tls', label: 'TLS' },
                { value: 'ssl', label: 'SSL' },
                { value: 'none', label: 'None' },
            ]);
        }
        if (field.key === 'mail.driver') {
            return renderSelect(field.key, value, disabled, [
                { value: 'smtp', label: 'SMTP' },
                { value: 'mailgun', label: 'Mailgun' },
                { value: 'postmark', label: 'Postmark' },
                { value: 'ses', label: 'Amazon SES' },
                { value: 'sendmail', label: 'Sendmail' },
                { value: 'log', label: 'Log (Local)' },
            ]);
        }
        if (field.key === 'general.language') {
             return renderSelect(field.key, value, disabled, [
                { value: 'en', label: 'Inggris' },
                { value: 'id', label: 'Indonesia' },
            ]);
        }
        if (field.key === 'general.timezone') {
             return renderSelect(field.key, value, disabled, [
                { value: 'UTC', label: 'UTC' },
                { value: 'Asia/Jakarta', label: 'Asia/Jakarta (WIB)' },
                { value: 'Asia/Makassar', label: 'Asia/Makassar (WITA)' },
                { value: 'Asia/Jayapura', label: 'Asia/Jayapura (WIT)' },
            ]);
        }
        if (field.key === 'general.date_format') {
             return renderSelect(field.key, value, disabled, [
                { value: 'Y-m-d', label: 'YYYY-MM-DD (2026-12-31)' },
                { value: 'd/m/Y', label: 'DD/MM/YYYY (31/12/2026)' },
                { value: 'M d, Y', label: 'MMM DD, YYYY (Dec 31, 2026)' },
                { value: 'd M Y', label: 'DD MMM YYYY (31 Dec 2026)' },
            ]);
        }
        if (field.key === 'media.disk') {
             return renderSelect(field.key, value, disabled, [
                { value: 'public', label: 'Local (Public)' },
                { value: 's3', label: 'Amazon S3' },
            ]);
        }
        if (field.key === 'security.login_attempts') {
             return renderSelect(field.key, value, disabled, [
                { value: '3', label: '3 Attempts' },
                { value: '5', label: '5 Attempts' },
                { value: '10', label: '10 Attempts' },
                { value: '0', label: 'Unlimited' },
            ]);
        }
        if (field.key === 'seo.robots') {
             return renderSelect(field.key, value, disabled, [
                { value: 'index, follow', label: 'Index, Follow (Disarankan)' },
                { value: 'noindex, nofollow', label: 'No Index, No Follow (Privat)' },
            ]);
        }

        if (field.type === 'integer') {
            return (
                <input
                    type="number"
                    value={value ?? ''}
                    disabled={disabled}
                    onChange={e => handleFieldChange(field.key, e.target.value)}
                    className={baseClass}
                />
            );
        }

        if (field.type === 'json' || field.type === 'array') {
            const strVal = typeof value === 'string' ? value : JSON.stringify(value ?? [], null, 2);
            return (
                <textarea
                    value={strVal}
                    disabled={disabled}
                    rows={4}
                    onChange={e => {
                        try {
                            handleFieldChange(field.key, JSON.parse(e.target.value));
                        } catch {
                            handleFieldChange(field.key, e.target.value);
                        }
                    }}
                    className={`${baseClass} font-mono text-xs resize-y`}
                    placeholder="Nilai JSON"
                />
            );
        }

        if (field.key.includes('color')) {
            return (
                <div className="flex items-center gap-3">
                    <input
                        type="color"
                        value={value || '#6366f1'}
                        disabled={disabled}
                        onChange={e => handleFieldChange(field.key, e.target.value)}
                        className="h-9 w-9 rounded-md border border-border-subtle cursor-pointer p-0"
                    />
                    <input
                        type="text"
                        value={value || ''}
                        disabled={disabled}
                        onChange={e => handleFieldChange(field.key, e.target.value)}
                        className={`${baseClass} flex-1`}
                        placeholder="#6366f1"
                    />
                </div>
            );
        }

        if (field.key.includes('password') || field.key.includes('secret')) {
            const shown = showPasswords[field.key];
            return (
                <div className="relative">
                    <input
                        type={shown ? 'text' : 'password'}
                        value={value ?? ''}
                        disabled={disabled}
                        onChange={e => handleFieldChange(field.key, e.target.value)}
                        className={`${baseClass} pr-10`}
                        placeholder={disabled ? '(diatur di .env)' : ''}
                    />
                    {!disabled && (
                        <button
                            type="button"
                            onClick={() => setShowPasswords(p => ({ ...p, [field.key]: !p[field.key] }))}
                            className="absolute right-2 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground transition-colors"
                        >
                            {shown ? <EyeOff className="w-4 h-4" /> : <Eye className="w-4 h-4" />}
                        </button>
                    )}
                </div>
            );
        }
        
        if (field.key.includes('description') || field.type === 'text') {
             return (
                <textarea
                    value={value ?? ''}
                    disabled={disabled}
                    rows={3}
                    onChange={e => handleFieldChange(field.key, e.target.value)}
                    className={`${baseClass} resize-y`}
                    placeholder={disabled ? '(dikelola via .env)' : ''}
                />
            );
        }

        return (
            <input
                type="text"
                value={value ?? ''}
                disabled={disabled}
                onChange={e => handleFieldChange(field.key, e.target.value)}
                className={baseClass}
                placeholder={disabled ? '(managed via .env)' : ''}
            />
        );
    };

    const availableTabs = useMemo(() => {
        if (isSingleGroup) return [];
        
        const tabsWithFields = new Set<string>(fields.map(f => getFieldTab(f.key)));
        return SETTINGS_TABS.filter(t => tabsWithFields.has(t.key));
    }, [fields, isSingleGroup]);
    
    const currentFields = useMemo(() => {
        if (isSingleGroup) return fields;
        return fields.filter(f => getFieldTab(f.key) === activeTab);
    }, [fields, activeTab, isSingleGroup]);

    const activeGroupMeta = isSingleGroup 
        ? groups.find(g => g.key === activeTab) 
        : SETTINGS_TABS.find(t => t.key === activeTab);

    const hasEnvFields = currentFields.some(f => f.is_env);
    
    const isMultiDomainEnabled = data['domains.multi_domain_enabled'] === true;
    const useCustomSmtp = data['mail.use_custom_smtp'] === true;
    const isCustomSeoEnabled = data['seo.custom_seo_enabled'] === true;

    const editableFields = currentFields.filter(f => {
        if (f.is_env || f.is_locked) return false;
        if (f.key === 'seo.sitemap_enabled') return false;

        if (activeTab === 'domains' && f.key !== 'domains.multi_domain_enabled') {
            return isMultiDomainEnabled;
        }
        if (activeTab === 'mail' && f.key !== 'mail.use_custom_smtp') {
             return useCustomSmtp;
        }
        if (getFieldSubGroup(f.key) === 'seo' && f.key !== 'seo.custom_seo_enabled') {
             return isCustomSeoEnabled;
        }

        return true;
    });

    const envFields = currentFields.filter(f => f.is_env && f.key !== 'seo.sitemap_enabled');

    const groupedEditableFields = useMemo(() => {
        const grouped: Record<string, SettingField[]> = {};
        editableFields.forEach(field => {
            const subGroup = getFieldSubGroup(field.key);
            if (!grouped[subGroup]) grouped[subGroup] = [];
            grouped[subGroup].push(field);
        });
        
        const order = ['identity', 'seo', 'appearance', 'localization', 'security', 'media', 'mail', 'domains', 'pages', 'other'];
        const sortedGroups: Record<string, SettingField[]> = {};
        order.forEach(key => {
            if (grouped[key]) sortedGroups[key] = grouped[key];
        });
        
        return sortedGroups;
    }, [editableFields]);

    const groupedEnvFields = useMemo(() => {
        const grouped: Record<string, SettingField[]> = {};
        envFields.forEach(field => {
            const subGroup = getFieldSubGroup(field.key);
            if (!grouped[subGroup]) grouped[subGroup] = [];
            grouped[subGroup].push(field);
        });
        
        const order = ['identity', 'seo', 'appearance', 'localization', 'security', 'media', 'mail', 'domains', 'pages', 'other'];
        const sortedGroups: Record<string, SettingField[]> = {};
        order.forEach(key => {
            if (grouped[key]) sortedGroups[key] = grouped[key];
        });
        
        return sortedGroups;
    }, [envFields]);

    const renderAppearanceFields = (fields: SettingField[]) => {
        const brandFields = fields.filter(f => f.key !== 'appearance.color_preset');
        const presetD = data['appearance.color_preset'] || 'green';

        return (
            <div className="space-y-8">
                <div>
                    <label className="block text-sm font-medium mb-3">Prasetel Warna</label>
                    <div className="grid grid-cols-2 md:grid-cols-4 gap-3">
                        {Object.entries(PRESET_META).map(([key, meta]) => {
                            const active = presetD === key;
                            return (
                                <button
                                    key={key}
                                    type="button"
                                    onClick={() => handlePresetSelect(key)}
                                    className={`relative flex flex-col items-center gap-2 p-4 rounded-xl border-2 transition-all text-center ${
                                        active
                                            ? 'border-primary bg-primary/5 shadow-sm'
                                            : 'border-border-subtle hover:border-primary/50 hover:bg-surface-muted/50'
                                    }`}
                                >
                                    <span className="text-2xl">{meta.icon}</span>
                                    <div>
                                        <div className="text-sm font-semibold">{meta.label}</div>
                                        <div className="text-xs text-muted-foreground">{meta.desc}</div>
                                    </div>
                                    {active && (
                                        <span className="absolute top-2 right-2 w-5 h-5 rounded-full bg-primary text-primary-foreground flex items-center justify-center">
                                            <Check className="w-3 h-3" />
                                        </span>
                                    )}
                                </button>
                            );
                        })}
                        <button
                            type="button"
                            onClick={() => handlePresetSelect('custom')}
                            className={`relative flex flex-col items-center gap-2 p-4 rounded-xl border-2 transition-all text-center ${
                                presetD === 'custom'
                                    ? 'border-primary bg-primary/5 shadow-sm'
                                    : 'border-dashed border-border-subtle hover:border-primary/50 hover:bg-surface-muted/50'
                            }`}
                        >
                            <span className="text-2xl">🎨</span>
                            <div>
                                <div className="text-sm font-semibold">Kustom</div>
                                <div className="text-xs text-muted-foreground">Atur manual</div>
                            </div>
                            {presetD === 'custom' && (
                                <span className="absolute top-2 right-2 w-5 h-5 rounded-full bg-primary text-primary-foreground flex items-center justify-center">
                                    <Check className="w-3 h-3" />
                                </span>
                            )}
                        </button>
                    </div>
                </div>

                {isCustom && (
                    <div>
                        <label className="block text-sm font-medium mb-3">Warna Brand</label>
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-5">
                            {brandFields.filter(f => ['appearance.primary_color', 'appearance.secondary_color', 'appearance.accent_color'].includes(f.key)).map(field => (
                                <div key={field.key}>
                                    <label className="block text-xs font-medium mb-1.5 text-muted-foreground">
                                        {labelFrom(field.key)}
                                    </label>
                                    {renderField(field)}
                                    {field.description && (
                                        <p className="mt-1 text-xs text-muted-foreground">{field.description}</p>
                                    )}
                                </div>
                            ))}
                        </div>
                    </div>
                )}

                <div>
                    <label className="block text-sm font-medium mb-3">Warna Teks</label>
                    <p className="text-xs text-muted-foreground mb-3">
                        {isCustom ? 'Warna teks untuk heading, body, dan teks redup.' : 'Gunakan mode Kustom untuk mengatur warna teks.'}
                    </p>
                    <div className="grid grid-cols-1 md:grid-cols-3 gap-5">
                        {brandFields.filter(f => ['appearance.heading_color', 'appearance.body_color', 'appearance.muted_color'].includes(f.key)).map(field => (
                            <div key={field.key}>
                                <label className="block text-xs font-medium mb-1.5 text-muted-foreground">
                                    {labelFrom(field.key)}
                                </label>
                                {renderField(field)}
                            </div>
                        ))}
                    </div>
                </div>

                <div>
                    <label className="block text-sm font-medium mb-3">Pratinjau</label>
                    <div className="grid grid-cols-1 md:grid-cols-3 gap-3">
                        {['primary', 'secondary', 'accent'].map(role => {
                            const colors: Record<string, string> = {};
                            brandFields.forEach(f => {
                                const short = f.key.split('.').pop() || '';
                                colors[short.replace('_color', '')] = data[f.key] || '#ccc';
                            });

                            return (
                                <div key={role} className="rounded-lg overflow-hidden border border-border-subtle">
                                    <div
                                        className="h-12 flex items-center justify-center text-xs font-bold"
                                        style={{ backgroundColor: colors[role] || '#ccc', color: role === 'accent' ? '#422006' : '#fff' }}
                                    >
                                        {role}
                                    </div>
                                    <div className="p-3 space-y-1.5 text-xs">
                                        <div className="flex justify-between">
                                            <span className="text-muted-foreground">Dasar</span>
                                            <code className="font-mono">{colors[role]}</code>
                                        </div>
                                        <div className="flex justify-between">
                                            <span className="text-muted-foreground">Hover</span>
                                            <code className="font-mono" style={{ color: colors[role] }}>
                                                digelapkan
                                            </code>
                                        </div>
                                    </div>
                                </div>
                            );
                        })}
                    </div>
                </div>
            </div>
        );
    };

    return (
        <DashboardLayout>
            <Head title={`${context} Pengaturan`} />
            <div className="space-y-5">
                
                <div className="flex items-center justify-end md:justify-between w-full mb-6">
                    <div className="hidden md:block">
                        <h1 className="text-xl font-semibold tracking-tight capitalize">
                            {context} Pengaturan
                        </h1>
                        <p className="text-sm text-muted-foreground mt-0.5 hidden lg:block">
                            Kelola branding, operasi, dan konfigurasi untuk {context}.
                        </p>
                    </div>
                    {isDirty && (
                        <Btn 
                            onClick={handleSave} 
                            disabled={processing}
                            loading={processing}
                            icon={<Save className="w-4 h-4" />}
                            variant="primary"
                            className="ms-auto"
                        >
                            Simpan Perubahan
                        </Btn>
                    )}
                </div>

                {!isSingleGroup && availableTabs.length > 1 && (
                    <div className="flex w-full flex-wrap p-1 bg-surface-muted rounded-xl border border-border-subtle gap-0.5 sm:gap-1 mb-6">
                        {availableTabs.map(tab => (
                            <button
                                key={tab.key}
                                onClick={() => setActiveTab(tab.key)}
                                className={`flex-1 flex items-center justify-center gap-2 px-4 py-2 rounded-lg text-xs font-medium transition-all whitespace-nowrap ${
                                    activeTab === tab.key
                                        ? 'bg-background text-primary shadow-sm border border-border-subtle font-semibold'
                                        : 'text-muted-foreground hover:text-foreground hover:bg-background/80'
                                }`}
                            >
                                {ICONS[tab.icon] ?? <Settings className="w-3.5 h-3.5" />}
                                {tab.name}
                            </button>
                        ))}
                    </div>
                )}

                <div className="space-y-6">
                    {activeGroupMeta?.description && (
                        <div className="mb-4">
                            <h2 className="text-lg font-semibold tracking-tight">{activeGroupMeta.name}</h2>
                            <p className="text-sm text-muted-foreground mt-1">{activeGroupMeta.description}</p>
                        </div>
                    )}

                    {activeTab === 'branding' ? (
                        (() => {
                            const identityF = editableFields.filter(f => getFieldSubGroup(f.key) === 'identity');
                            const seoF = editableFields.filter(f => getFieldSubGroup(f.key) === 'seo');
                            return (
                                <div className="bg-background rounded-xl border border-border-subtle shadow-sm overflow-hidden p-6">
                                    {renderBrandingFields(identityF, seoF)}
                                    {!isCustomSeoEnabled && (
                                        <div className="bg-primary/5 border border-primary/20 rounded-md p-4 mt-8">
                                            <p className="text-sm text-primary">
                                                <strong>Catatan:</strong> SEO Kustom dinonaktifkan. Sistem akan secara otomatis membuat tag SEO (Judul, Deskripsi, Gambar) menggunakan nilai yang diberikan di kartu <strong>Identitas Situs</strong>. Aktifkan toggle di atas untuk menyesuaikannya secara manual.
                                            </p>
                                        </div>
                                    )}
                                </div>
                            );
                        })()
                    ) : (
                        Object.entries(groupedEditableFields).map(([subGroupId, fields]) => (
                            <div key={subGroupId} className="bg-background rounded-xl border border-border-subtle shadow-sm overflow-hidden p-6">
                                <div className="mb-5 pb-4 border-b border-border-subtle flex justify-between items-start">
                                    <div>
                                        <h3 className="text-base font-semibold">{SUB_GROUPS[subGroupId]?.title || 'Pengaturan'}</h3>
                                        {SUB_GROUPS[subGroupId]?.description && (
                                            <p className="text-xs text-muted-foreground mt-1">{SUB_GROUPS[subGroupId].description}</p>
                                        )}
                                    </div>
                                </div>
                                
                                {subGroupId === 'appearance' ? (
                                    renderAppearanceFields(fields)
                                ) : (
                                    <fieldset>
                                        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                                            {fields.map(field => {
                                                const type = field.type;
                                                const key = field.key;
                                                
                                                const isFullWidth = type === 'json' || type === 'array' || type === 'boolean' || 
                                                                    key.includes('use_custom_smtp') || key.includes('description') || key.includes('text');
                                                
                                                return (
                                                    <div key={field.key} className={isFullWidth ? 'md:col-span-2' : ''}>
                                                        <label className="block text-sm font-medium mb-1.5">
                                                            {labelFrom(field.key)}
                                                        </label>
                                                        {renderField(field)}
                                                        {field.description && (
                                                            <p className="mt-1.5 text-xs text-muted-foreground flex items-start gap-1">
                                                                <Info className="w-3 h-3 shrink-0 mt-0.5" />
                                                                {field.description}
                                                            </p>
                                                        )}
                                                    </div>
                                                );
                                            })}
                                        </div>
                                    </fieldset>
                                )}
                                
                                {!isCustomSeoEnabled && subGroupId === 'seo' && (
                                    <div className="bg-primary/5 border border-primary/20 rounded-md p-4 mt-8">
                                        <p className="text-sm text-primary">
                                            <strong>Note:</strong> Custom SEO is disabled. The system will automatically generate SEO tags (Title, Description, Image) using the values provided in the <strong>Site Identity</strong> card. Enable the toggle above to customize them manually.
                                        </p>
                                    </div>
                                )}
                            </div>
                        ))
                    )}

                    {!isMultiDomainEnabled && activeTab === 'domains' && (
                        <div className="bg-primary/5 border border-primary/20 rounded-md p-4 mt-8">
                            <p className="text-sm text-primary">
                                                <strong>Catatan:</strong> Aktifkan <em>Multi Domain</em> di atas untuk mengonfigurasi URL untuk subdomain terpisah seperti blog, auth, dan dashboard.
                            </p>
                        </div>
                    )}

                    {!useCustomSmtp && isSingleGroup && context === 'global' && activeGroupMeta?.key === 'mail' && (
                        <div className="bg-primary/5 border border-primary/20 rounded-md p-4 mt-8">
                            <p className="text-sm text-primary">
                                <strong>Catatan:</strong> SMTP Kustom dinonaktifkan. Sistem akan menggunakan pengaturan email default yang ditentukan di file <code>.env</code> Anda. Aktifkan toggle di atas untuk menimpanya.
                            </p>
                        </div>
                    )}

                    {hasEnvFields && (
                        <div className="border-t border-border-subtle pt-8 mt-8">
                            <div className="flex items-center gap-2 mb-4">
                                <Lock className="w-4 h-4 text-muted-foreground" />
                                <h3 className="text-sm font-semibold text-muted-foreground uppercase tracking-wider">
                                    Dikelola Environment (Hanya Baca)
                                </h3>
                            </div>
                            <div className="bg-surface-muted/50 border border-border-subtle rounded-xl p-4 mb-6">
                                <p className="text-xs text-muted-foreground">
                                Nilai-nilai ini dimuat dari file <code className="bg-surface-muted text-foreground px-1 py-0.5 rounded font-mono">.env</code> Anda.
                                Untuk mengubahnya, edit file <code className="bg-surface-muted text-foreground px-1 py-0.5 rounded font-mono">.env</code> Anda dan restart server.
                                </p>
                            </div>
                            
                            {Object.entries(groupedEnvFields).map(([subGroupId, fields]) => (
                                <div key={subGroupId} className="bg-background rounded-xl border border-border-subtle overflow-hidden p-6 mb-6">
                                    <div className="mb-4 pb-3 border-b border-border-subtle">
                                        <h4 className="text-sm font-bold text-foreground">{SUB_GROUPS[subGroupId]?.title || 'Pengaturan'}</h4>
                                    </div>
                                    <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
                                        {fields.map(field => {
                                            const isFullWidth = field.type === 'json' || field.type === 'array' || field.type === 'boolean';
                                            
                                            return (
                                                <div key={field.key} className={isFullWidth ? 'md:col-span-2' : ''}>
                                                    <label className="block text-sm font-medium text-muted-foreground mb-1.5 flex items-center gap-1">
                                                        <Lock className="w-3.5 h-3.5" />
                                                        {labelFrom(field.key)}
                                                    </label>
                                                    {renderField(field)}
                                                    {field.description && (
                                                        <p className="mt-1.5 text-xs text-muted-foreground flex items-start gap-1">
                                                            <Info className="w-3 h-3 shrink-0 mt-0.5" />
                                                            {field.description}
                                                        </p>
                                                    )}
                                                </div>
                                            );
                                        })}
                                    </div>
                                </div>
                            ))}
                        </div>
                    )}

                    {currentFields.length === 0 && (
                        <div className="text-center py-12 text-muted-foreground text-sm font-medium bg-background rounded-2xl border border-border-subtle shadow-sm">
                            Tidak ada pengaturan untuk tab ini.
                        </div>
                    )}
                </div>
            </div>

            <MediaPicker 
                open={!!mediaPickerField} 
                onOpenChange={(open) => !open && setMediaPickerField(null)} 
                onSelect={handleMediaSelect} 
                title="Pilih Gambar" 
            />
        </DashboardLayout>
    );
}
