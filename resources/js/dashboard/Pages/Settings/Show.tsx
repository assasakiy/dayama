import React, { useState } from 'react';
import { Head, router, useForm } from '@inertiajs/react';
import DashboardLayout from '@dashboard/Layouts/DashboardLayout';
import {
    Settings, FileText, Search, Image, Mail, Shield,
    Palette, Globe, LayoutTemplate, Save, Info, Lock,
    Eye, EyeOff, ChevronRight
} from 'lucide-react';
import { Btn } from '@dashboard/Components/ui/btn';
import { Switch } from '@dashboard/Components/ui/switch';
import { Select, SelectTrigger, SelectValue, SelectContent, SelectItem } from '@dashboard/Components/ui/select';

// Icon mapping for group icons (Lucide names from seeder)
const ICONS: Record<string, React.ReactNode> = {
    Settings:      <Settings className="w-4 h-4" />,
    FileText:      <FileText className="w-4 h-4" />,
    Search:        <Search className="w-4 h-4" />,
    Image:         <Image className="w-4 h-4" />,
    Mail:          <Mail className="w-4 h-4" />,
    Shield:        <Shield className="w-4 h-4" />,
    Palette:       <Palette className="w-4 h-4" />,
    Globe:         <Globe className="w-4 h-4" />,
    LayoutTemplate:<LayoutTemplate className="w-4 h-4" />,
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

export default function SettingsShow({
    group,
    fields,
}: {
    group: Group;
    fields: SettingField[];
}) {
    const activeGroup = group.key;
    const { data, setData, put, processing, isDirty, transform } = useForm<Record<string, any>>(() => {
        const initial: Record<string, any> = {};
        for (const field of fields) {
            initial[field.key] = field.value ?? '';
        }
        return initial;
    });
    
    transform((data) => ({
        settings: data,
    }));
    
    const [showPasswords, setShowPasswords] = useState<Record<string, boolean>>({});

    const currentFields = fields;
    const currentValues = data;

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
        put(`/dashboard/settings/${activeGroup}`, {
            preserveScroll: true,
        });
    };

    // Get short label from key (e.g. 'general.site_name' → 'Site Name')
    const labelFrom = (key: string) => {
        const short = key.split('.').pop() ?? key;
        return short.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
    };

    const renderField = (field: SettingField) => {
        const value = currentValues[field.key];
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

        if (field.key === 'mail.encryption') {
            return (
                <Select disabled={disabled} value={value || 'none'} onValueChange={v => handleFieldChange(field.key, v)}>
                    <SelectTrigger className={disabled ? 'opacity-70 bg-surface-muted cursor-not-allowed' : ''}>
                        <SelectValue placeholder="Select encryption..." />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem value="tls">TLS</SelectItem>
                        <SelectItem value="ssl">SSL</SelectItem>
                        <SelectItem value="none">None</SelectItem>
                    </SelectContent>
                </Select>
            );
        }

        if (field.key === 'mail.driver') {
            return (
                <Select disabled={disabled} value={value || 'smtp'} onValueChange={v => handleFieldChange(field.key, v)}>
                    <SelectTrigger className={disabled ? 'opacity-70 bg-surface-muted cursor-not-allowed' : ''}>
                        <SelectValue placeholder="Select driver..." />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem value="smtp">SMTP</SelectItem>
                        <SelectItem value="mailgun">Mailgun</SelectItem>
                        <SelectItem value="postmark">Postmark</SelectItem>
                        <SelectItem value="ses">Amazon SES</SelectItem>
                        <SelectItem value="sendmail">Sendmail</SelectItem>
                        <SelectItem value="log">Log (Local)</SelectItem>
                    </SelectContent>
                </Select>
            );
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

        // String fields — detect color, password
        if (field.key.includes('color')) {
            return (
                <div className="flex items-center gap-3">
                    <input
                        type="color"
                        value={value || '#6366f1'}
                        disabled={disabled}
                        onChange={e => handleFieldChange(field.key, e.target.value)}
                        className="h-9 w-9 rounded-md border border-border-subtle cursor-pointer"
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
                            className="absolute right-2 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground"
                        >
                            {shown ? <EyeOff className="w-4 h-4" /> : <Eye className="w-4 h-4" />}
                        </button>
                    )}
                </div>
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

    const activeGroupMeta = group;
    const hasEnvFields = currentFields.some(f => f.is_env);
    
    // Reactive check for multi-domain toggle
    const isMultiDomainEnabled = currentValues['domains.multi_domain_enabled'] === true;
    const useCustomSmtp = currentValues['mail.use_custom_smtp'] === true;

    const editableFields = currentFields.filter(f => {
        if (f.is_env || f.is_locked) return false;

        // Hide domain URLs if multi-domain is disabled
        if (activeGroup === 'domains' && f.key !== 'domains.multi_domain_enabled') {
            return isMultiDomainEnabled;
        }

        // Hide advanced homepage/blog assignments if multi-domain is disabled
        if (activeGroup === 'pages') {
            const advancedPageFields = ['pages.homepage_type', 'pages.homepage_page_id', 'pages.blog_page_id'];
            if (advancedPageFields.includes(f.key)) {
                return isMultiDomainEnabled;
            }
        }

        if (activeGroup === 'mail' && f.key !== 'mail.use_custom_smtp') {
             return useCustomSmtp;
        }

        return true;
    }).sort((a, b) => {
        // Domains Order
        if (a.key === 'domains.multi_domain_enabled') return -1;
        if (b.key === 'domains.multi_domain_enabled') return 1;

        // Pages Order
        if (a.key === 'pages.homepage_type') return -1;
        if (b.key === 'pages.homepage_type') return 1;

        // Mail Order explicitly to group related items
        if (activeGroup === 'mail') {
            const mailOrder = [
                'mail.use_custom_smtp',
                'mail.driver',
                'mail.host',
                'mail.encryption',
                'mail.port',
                'mail.username',
                'mail.password',
                'mail.from_name',
                'mail.from_email',
            ];
            const getOrder = (key: string) => {
                const idx = mailOrder.indexOf(key);
                return idx === -1 ? 999 : idx;
            };
            return getOrder(a.key) - getOrder(b.key);
        }

        return 0; // Keep existing order for the rest
    });

    const envFields = currentFields.filter(f => f.is_env);

    return (
        <DashboardLayout>
            <Head title={`${group.name} Settings`} />
            <div className="space-y-5">
                {/* Header */}
                <div>
                    <h1 className="text-xl font-semibold text-foreground">{group.name} Settings</h1>
                    <p className="text-sm text-muted-foreground mt-0.5">
                        {group.description || `Configure your site's ${group.name.toLowerCase()} behavior.`}
                    </p>
                </div>

                <div className="flex gap-6 items-start">
                    {/* Main Content */}
                    <div className="flex-1 min-w-0">
                        <div className="bg-background border border-border-subtle rounded-lg">
                            {/* Group Header */}
                            <div className="px-6 py-4 border-b border-border-subtle flex items-center justify-between">
                                <div>
                                    <h2 className="text-base font-semibold text-foreground flex items-center gap-2">
                                        {ICONS[activeGroupMeta?.icon ?? ''] ?? <Settings className="w-4 h-4" />}
                                        {activeGroupMeta?.name}
                                    </h2>
                                    {activeGroupMeta?.description && (
                                        <p className="text-xs text-muted-foreground mt-0.5">{activeGroupMeta.description}</p>
                                    )}
                                </div>
                                {editableFields.length > 0 && (
                                    <Btn
                                        onClick={handleSave}
                                        disabled={processing || !isDirty}
                                        loading={processing}
                                        icon={<Save className="w-3.5 h-3.5" />}
                                        variant="primary"
                                        size="sm"
                                    >
                                        Save Changes
                                    </Btn>
                                )}
                            </div>

                            {/* Fields */}
                            <div className="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                                {/* Editable Fields */}
                                {editableFields.map(field => {
                                    const type = field.type;
                                    const key = field.key;
                                    
                                    const isFullWidth = type === 'json' || type === 'array' || type === 'boolean' || 
                                                        key.includes('use_custom_smtp') || key.includes('description') || key.includes('text');
                                    
                                    return (
                                        <div key={field.key} className={isFullWidth ? 'md:col-span-2' : ''}>
                                            <label className="block text-sm font-medium text-foreground mb-1.5">
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

                                {!isMultiDomainEnabled && activeGroup === 'domains' && (
                                    <div className="md:col-span-2 bg-primary/5 border border-primary/20 rounded-md p-4 mt-2">
                                        <p className="text-sm text-primary">
                                            <strong>Note:</strong> Enable <em>Multi Domain Enabled</em> above to configure URLs for separate subdomains like blog, auth, and dashboard.
                                        </p>
                                    </div>
                                )}

                                {!useCustomSmtp && activeGroup === 'mail' && (
                                    <div className="md:col-span-2 bg-primary/5 border border-primary/20 rounded-md p-4 mt-2">
                                        <p className="text-sm text-primary">
                                            <strong>Note:</strong> Custom SMTP is disabled. The system will use the default mail settings defined in your <code>.env</code> file. Enable the toggle above to override them.
                                        </p>
                                    </div>
                                )}

                                {!isMultiDomainEnabled && activeGroup === 'pages' && (
                                    <div className="md:col-span-2 bg-primary/5 border border-primary/20 rounded-md p-4 mt-2">
                                        <p className="text-sm text-primary">
                                            <strong>Note:</strong> Advanced homepage configurations are currently hidden because Multi-Domain Mode is disabled. Only the basic required pages are shown below.
                                        </p>
                                    </div>
                                )}


                                {/* Env-managed Fields (read-only section) */}
                                {hasEnvFields && (
                                    <div className="md:col-span-2 border-t border-border-subtle pt-6 mt-2">
                                        <div className="flex items-center gap-2 mb-4">
                                            <Lock className="w-3.5 h-3.5 text-muted-foreground" />
                                            <h3 className="text-xs font-semibold text-muted-foreground uppercase tracking-wider">
                                                Environment-Managed (Read-only)
                                            </h3>
                                        </div>
                                        <div className="bg-surface-muted/50 border border-border-subtle rounded-md p-3 mb-6">
                                            <p className="text-xs text-muted-foreground">
                                                These values are loaded from your <code className="bg-surface-muted px-1 py-0.5 rounded text-xs">.env</code> file.
                                                To change them, edit your <code className="bg-surface-muted px-1 py-0.5 rounded text-xs">.env</code> file and restart the server.
                                            </p>
                                        </div>
                                        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                                            {envFields.map(field => {
                                                const isFullWidth = field.type === 'json' || field.type === 'array' || field.type === 'boolean';
                                                
                                                return (
                                                    <div key={field.key} className={isFullWidth ? 'md:col-span-2' : ''}>
                                                        <label className="block text-sm font-medium text-muted-foreground mb-1.5 flex items-center gap-1">
                                                            <Lock className="w-3 h-3" />
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
                                )}

                                {currentFields.length === 0 && (
                                    <div className="md:col-span-2 text-center py-12 text-muted-foreground text-sm">
                                        No settings found for this group.
                                    </div>
                                )}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </DashboardLayout>
    );
}

