import React, { useState } from 'react';
import { Head, router } from '@inertiajs/react';
import DashboardLayout from '@dashboard/Layouts/DashboardLayout';
import {
    Settings, FileText, Search, Image, Mail, Shield,
    Palette, Globe, LayoutTemplate, Save, Info, Lock,
    Eye, EyeOff, ChevronRight
} from 'lucide-react';

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

export default function SettingsIndex({
    groups,
    settings,
}: {
    groups: Group[];
    settings: Record<string, SettingField[]>;
}) {
    const [activeGroup, setActiveGroup] = useState(groups[0]?.key ?? 'general');
    const [formValues, setFormValues] = useState<Record<string, Record<string, any>>>(() => {
        const initial: Record<string, Record<string, any>> = {};
        for (const group of groups) {
            initial[group.key] = {};
            for (const field of (settings[group.key] ?? [])) {
                initial[group.key][field.key] = field.value ?? '';
            }
        }
        return initial;
    });
    const [saving, setSaving] = useState(false);
    const [showPasswords, setShowPasswords] = useState<Record<string, boolean>>({});

    const currentFields = settings[activeGroup] ?? [];
    const currentValues = formValues[activeGroup] ?? {};

    const handleFieldChange = (key: string, value: any) => {
        setFormValues(prev => ({
            ...prev,
            [activeGroup]: { ...prev[activeGroup], [key]: value },
        }));
    };

    const handleSave = () => {
        setSaving(true);
        router.put(`/settings/${activeGroup}`, {
            settings: currentValues,
        }, {
            preserveScroll: true,
            onFinish: () => setSaving(false),
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
                    <div className="relative">
                        <input
                            type="checkbox"
                            className="sr-only"
                            checked={!!value}
                            disabled={disabled}
                            onChange={e => handleFieldChange(field.key, e.target.checked)}
                        />
                        <div className={`w-10 h-6 rounded-full transition-colors ${!!value ? 'bg-primary' : 'bg-border-subtle'} ${disabled ? 'opacity-60' : ''}`}>
                            <div className={`absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow transition-transform ${!!value ? 'translate-x-4' : 'translate-x-0'}`} />
                        </div>
                    </div>
                    <span className="text-sm text-muted-foreground group-hover:text-foreground transition-colors">
                        {!!value ? 'Enabled' : 'Disabled'}
                    </span>
                </label>
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

    const activeGroupMeta = groups.find(g => g.key === activeGroup);
    const hasEnvFields = currentFields.some(f => f.is_env);
    
    const editableFields = currentFields.filter(f => {
        if (f.is_env || f.is_locked) return false;
        return true;
    }).sort((a, b) => {
        return 0; // Keep existing order
    });

    const envFields = currentFields.filter(f => f.is_env);

    return (
        <DashboardLayout>
            <Head title="Settings" />
            <div className="space-y-5">
                {/* Header */}
                <div>
                    <h1 className="text-xl font-semibold text-foreground">System Settings</h1>
                    <p className="text-sm text-muted-foreground mt-0.5">
                        Configure your site's behavior, appearance, and integrations.
                    </p>
                </div>

                <div className="flex gap-6 items-start">
                    {/* Sidebar */}
                    <div className="w-52 shrink-0">
                        <nav className="bg-background border border-border-subtle rounded-lg overflow-hidden">
                            {groups.map(group => (
                                <button
                                    key={group.key}
                                    onClick={() => setActiveGroup(group.key)}
                                    className={`w-full flex items-center gap-2.5 px-3.5 py-2.5 text-sm text-left transition-colors ${
                                        activeGroup === group.key
                                            ? 'bg-primary/10 text-primary font-medium border-r-2 border-primary'
                                            : 'text-muted-foreground hover:bg-surface-muted hover:text-foreground'
                                    }`}
                                >
                                    <span className="shrink-0">{ICONS[group.icon] ?? <Settings className="w-4 h-4" />}</span>
                                    <span className="truncate">{group.name}</span>
                                    {activeGroup === group.key && <ChevronRight className="w-3.5 h-3.5 ml-auto shrink-0" />}
                                </button>
                            ))}
                        </nav>
                    </div>

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
                                    <button
                                        onClick={handleSave}
                                        disabled={saving}
                                        className="flex items-center gap-1.5 h-9 px-4 bg-primary text-primary-foreground rounded-md text-sm font-medium hover:opacity-90 transition-opacity disabled:opacity-60"
                                    >
                                        <Save className="w-3.5 h-3.5" />
                                        {saving ? 'Saving…' : 'Save Changes'}
                                    </button>
                                )}
                            </div>

                            {/* Fields */}
                            <div className="p-6 space-y-6">
                                {/* Editable Fields */}
                                {editableFields.map(field => (
                                    <div key={field.key}>
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
                                ))}




                                {/* Env-managed Fields (read-only section) */}
                                {hasEnvFields && (
                                    <div className="border-t border-border-subtle pt-6">
                                        <div className="flex items-center gap-2 mb-4">
                                            <Lock className="w-3.5 h-3.5 text-muted-foreground" />
                                            <h3 className="text-xs font-semibold text-muted-foreground uppercase tracking-wider">
                                                Environment-Managed (Read-only)
                                            </h3>
                                        </div>
                                        <div className="bg-surface-muted/50 border border-border-subtle rounded-md p-3 mb-4">
                                            <p className="text-xs text-muted-foreground">
                                                These values are loaded from your <code className="bg-surface-muted px-1 py-0.5 rounded text-xs">.env</code> file.
                                                To change them, edit your <code className="bg-surface-muted px-1 py-0.5 rounded text-xs">.env</code> file and restart the server.
                                            </p>
                                        </div>
                                        <div className="space-y-4">
                                            {envFields.map(field => (
                                                <div key={field.key}>
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
                                            ))}
                                        </div>
                                    </div>
                                )}

                                {currentFields.length === 0 && (
                                    <div className="text-center py-12 text-muted-foreground text-sm">
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

