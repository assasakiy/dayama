import React, { useState, useMemo } from 'react';
import { Head, router, useForm } from '@inertiajs/react';
import DashboardLayout from '@dashboard/Layouts/DashboardLayout';
import {
    Settings, FileText, Search, Image as ImageIcon, Mail, Shield,
    Palette, Globe, LayoutTemplate, Save, Info, Lock,
    Eye, EyeOff, ChevronRight, ArrowLeft, PanelsTopLeft, Bell, X
} from 'lucide-react';
import { Btn } from '@dashboard/Components/ui/btn';
import { Switch } from '@dashboard/Components/ui/switch';
import { Select, SelectTrigger, SelectValue, SelectContent, SelectItem } from '@dashboard/Components/ui/select';
import MediaPicker from '@dashboard/Components/MediaPicker';

// Icon mapping for tabs
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

// Hardcoded Custom Tabs for Settings (combining fields dynamically)
const SETTINGS_TABS = [
    { key: 'branding', name: 'Branding', icon: 'PanelsTopLeft', description: 'Manage brand identity and search optimization.' },
    { key: 'visual', name: 'Visual & Theme', icon: 'Globe', description: 'Personalize appearance and localization.' },
    { key: 'system', name: 'System & Security', icon: 'Lock', description: 'Technical configuration, security, and media.' },
    { key: 'contact', name: 'Contact & Socials', icon: 'Bell', description: 'Public contact information and links.' },
];

const SUB_GROUPS: Record<string, { title: string, description: string }> = {
    identity: { title: 'Site Identity', description: 'Main logo, name, and tagline of the website.' },
    seo: { title: 'SEO & Meta Tags', description: 'Search engine settings and analytics integration.' },
    appearance: { title: 'Appearance', description: 'Primary colors and basic layout.' },
    localization: { title: 'Localization & Time', description: 'Language, date format, and timezone.' },
    security: { title: 'Security', description: 'Authentication, login protection, and maintenance.' },
    media: { title: 'Media & Storage', description: 'Upload limits and image optimization.' },
    mail: { title: 'Mail Configuration', description: 'SMTP server settings for outgoing emails.' },
    domains: { title: 'Domain Settings', description: 'Multi-domain and URL configurations.' },
    pages: { title: 'Main Pages', description: 'Homepage and blog settings.' },
    other: { title: 'Other Settings', description: 'Additional configurations.' },
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
            // Provide a default for boolean 'false' strings
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

    const handleSave = () => {
        put(`/settings/${context}${isSingleGroup ? `/${activeTab}` : ''}`, {
            preserveScroll: true,
        });
    };

    // Get short label from key (e.g. 'general.site_name' → 'Site Name')
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
        return 'system'; // fallback
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
                    <SelectValue placeholder="Select an option..." />
                </SelectTrigger>
                <SelectContent>
                    {options.map(opt => (
                        <SelectItem key={opt.value} value={opt.value}>{opt.label}</SelectItem>
                    ))}
                </SelectContent>
            </Select>
        );
    };

    const renderField = (field: SettingField) => {
        const value = data[field.key];
        const disabled = field.is_env || field.is_locked;
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
                        {!!value ? 'Enabled' : 'Disabled'}
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
                            <span className="text-sm font-medium">Select or upload image</span>
                        </button>
                    )}
                </div>
            );
        }

        // Dropdowns mapped to Select
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
                { value: 'en', label: 'English' },
                { value: 'id', label: 'Indonesian' },
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
                { value: 'index, follow', label: 'Index, Follow (Recommended)' },
                { value: 'noindex, nofollow', label: 'No Index, No Follow (Private)' },
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
                    placeholder="JSON value"
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
                        placeholder={disabled ? '(set in .env)' : ''}
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
                    placeholder={disabled ? '(managed via .env)' : ''}
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
        if (isSingleGroup) return []; // Mail handles itself
        
        const tabsWithFields = new Set<string>(fields.map(f => getFieldTab(f.key)));
        return SETTINGS_TABS.filter(t => tabsWithFields.has(t.key));
    }, [fields, isSingleGroup]);
    
    const currentFields = useMemo(() => {
        if (isSingleGroup) return fields; // For Mail etc
        return fields.filter(f => getFieldTab(f.key) === activeTab);
    }, [fields, activeTab, isSingleGroup]);

    const activeGroupMeta = isSingleGroup 
        ? groups.find(g => g.key === activeTab) 
        : SETTINGS_TABS.find(t => t.key === activeTab);

    const hasEnvFields = currentFields.some(f => f.is_env);
    
    const isMultiDomainEnabled = data['domains.multi_domain_enabled'] === true;
    const useCustomSmtp = data['mail.use_custom_smtp'] === true;
    
    // Add check for Custom SEO Enabled
    const isCustomSeoEnabled = data['seo.custom_seo_enabled'] === true;

    const editableFields = currentFields.filter(f => {
        if (f.is_env || f.is_locked) return false;
        if (f.key === 'seo.sitemap_enabled') return false; // Hide sitemap as requested

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

    return (
        <DashboardLayout>
            <Head title={`${context} Settings`} />
            <div className="space-y-5">
                
                <div className="flex items-center justify-end md:justify-between w-full mb-6">
                    <div className="hidden md:block">
                        <h1 className="text-xl font-semibold tracking-tight capitalize">
                            {context} Settings
                        </h1>
                        <p className="text-sm text-muted-foreground mt-0.5 hidden lg:block">
                            Manage the branding, operations, and configuration for {context}.
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
                            Save Changes
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

                    {Object.entries(groupedEditableFields).map(([subGroupId, fields]) => (
                        <div key={subGroupId} className="bg-background rounded-xl border border-border-subtle shadow-sm overflow-hidden p-6">
                            <div className="mb-5 pb-4 border-b border-border-subtle flex justify-between items-start">
                                <div>
                                    <h3 className="text-base font-semibold">{SUB_GROUPS[subGroupId]?.title || 'Settings'}</h3>
                                    {SUB_GROUPS[subGroupId]?.description && (
                                        <p className="text-xs text-muted-foreground mt-1">{SUB_GROUPS[subGroupId].description}</p>
                                    )}
                                </div>
                                {subGroupId === 'seo' && (
                                    <div className="mt-1">
                                    </div>
                                )}
                            </div>
                            
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
                            
                            {!isCustomSeoEnabled && subGroupId === 'seo' && (
                                <div className="bg-primary/5 border border-primary/20 rounded-md p-4 mt-8">
                                    <p className="text-sm text-primary">
                                        <strong>Note:</strong> Custom SEO is disabled. The system will automatically generate SEO tags (Title, Description, Image) using the values provided in the <strong>Site Identity</strong> card. Enable the toggle above to customize them manually.
                                    </p>
                                </div>
                            )}
                        </div>
                    ))}

                    {!isMultiDomainEnabled && activeTab === 'domains' && (
                        <div className="bg-primary/5 border border-primary/20 rounded-md p-4 mt-8">
                            <p className="text-sm text-primary">
                                <strong>Note:</strong> Enable <em>Multi Domain Enabled</em> above to configure URLs for separate subdomains like blog, auth, and dashboard.
                            </p>
                        </div>
                    )}

                    {!useCustomSmtp && isSingleGroup && context === 'global' && activeGroupMeta?.key === 'mail' && (
                        <div className="bg-primary/5 border border-primary/20 rounded-md p-4 mt-8">
                            <p className="text-sm text-primary">
                                <strong>Note:</strong> Custom SMTP is disabled. The system will use the default mail settings defined in your <code>.env</code> file. Enable the toggle above to override them.
                            </p>
                        </div>
                    )}

                    {hasEnvFields && (
                        <div className="border-t border-border-subtle pt-8 mt-8">
                            <div className="flex items-center gap-2 mb-4">
                                <Lock className="w-4 h-4 text-muted-foreground" />
                                <h3 className="text-sm font-semibold text-muted-foreground uppercase tracking-wider">
                                    Environment-Managed (Read-only)
                                </h3>
                            </div>
                            <div className="bg-surface-muted/50 border border-border-subtle rounded-xl p-4 mb-6">
                                <p className="text-xs text-muted-foreground">
                                    These values are loaded from your <code className="bg-surface-muted text-foreground px-1 py-0.5 rounded font-mono">.env</code> file.
                                    To change them, edit your <code className="bg-surface-muted text-foreground px-1 py-0.5 rounded font-mono">.env</code> file and restart the server.
                                </p>
                            </div>
                            
                            {Object.entries(groupedEnvFields).map(([subGroupId, fields]) => (
                                <div key={subGroupId} className="bg-background rounded-xl border border-border-subtle overflow-hidden p-6 mb-6">
                                    <div className="mb-4 pb-3 border-b border-border-subtle">
                                        <h4 className="text-sm font-bold text-foreground">{SUB_GROUPS[subGroupId]?.title || 'Settings'}</h4>
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
                            No settings found for this tab.
                        </div>
                    )}
                </div>
            </div>

            <MediaPicker 
                open={!!mediaPickerField} 
                onOpenChange={(open) => !open && setMediaPickerField(null)} 
                onSelect={handleMediaSelect} 
                title="Select Image" 
            />
        </DashboardLayout>
    );
}
