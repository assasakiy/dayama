import React, { useState } from 'react';
import { Head, Link, useForm } from '@inertiajs/react';
import DashboardLayout from '@dashboard/Layouts/DashboardLayout';
import { ArrowLeft, Save, Info, AlertTriangle } from 'lucide-react';

interface EmailTemplate {
    id: string;
    key: string;
    name: string;
    subject: string;
    body: string;
    variables: string[];
    is_active: boolean;
}

export default function EmailTemplateForm({ template }: { template: EmailTemplate }) {
    const { data, setData, put, processing, errors } = useForm({
        subject: template.subject || '',
        body: template.body || '',
        is_active: template.is_active,
    });

    const [previewKey, setPreviewKey] = useState(0);

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        put(`/email-templates/${template.id}`, {
            preserveScroll: true,
            onSuccess: () => setPreviewKey(k => k + 1)
        });
    };

    return (
        <DashboardLayout>
            <Head title={`Edit ${template.name}`} />
            <div className="space-y-6 max-w-7xl">
                <div className="flex items-center justify-between">
                    <div>
                        <div className="flex items-center gap-2 mb-1 text-sm text-muted-foreground">
                            <Link href="/email-templates" className="hover:text-foreground flex items-center gap-1 transition-colors">
                                <ArrowLeft className="w-3.5 h-3.5" /> Back to templates
                            </Link>
                        </div>
                        <h1 className="text-2xl font-semibold text-foreground tracking-tight">Edit: {template.name}</h1>
                        <p className="text-sm text-muted-foreground font-mono mt-1 px-2 py-0.5 bg-surface-muted inline-block rounded">
                            {template.key}
                        </p>
                    </div>
                    <button
                        type="submit"
                        form="template-form"
                        disabled={processing}
                        className="inline-flex items-center gap-2 h-9 px-4 bg-primary text-primary-foreground rounded-md text-sm font-medium hover:bg-primary/90 transition-colors disabled:opacity-70"
                    >
                        <Save className="w-4 h-4" />
                        {processing ? 'Saving...' : 'Save Template'}
                    </button>
                </div>

                <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    {/* Form Section */}
                    <div className="space-y-6">
                        <form id="template-form" onSubmit={submit} className="bg-background border border-border-subtle rounded-lg p-6 space-y-5 shadow-sm">
                            
                            <div>
                                <label className="block text-sm font-medium text-foreground mb-1.5">Template Subject</label>
                                <input
                                    type="text"
                                    value={data.subject}
                                    onChange={e => setData('subject', e.target.value)}
                                    className="w-full rounded-md border border-border-subtle bg-background px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-primary"
                                />
                                {errors.subject && <p className="text-red-500 text-xs mt-1">{errors.subject}</p>}
                            </div>

                            <div>
                                <label className="block text-sm font-medium text-foreground mb-1.5 flex items-center justify-between">
                                    <span>HTML Body</span>
                                </label>
                                <textarea
                                    value={data.body}
                                    onChange={e => setData('body', e.target.value)}
                                    rows={20}
                                    className="w-full rounded-md border border-border-subtle bg-background px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-primary font-mono"
                                />
                                {errors.body && <p className="text-red-500 text-xs mt-1">{errors.body}</p>}
                            </div>

                            <div className="flex items-center gap-3 pt-2">
                                <label className="relative inline-flex items-center cursor-pointer">
                                    <input
                                        type="checkbox"
                                        checked={data.is_active}
                                        onChange={e => setData('is_active', e.target.checked)}
                                        className="sr-only peer"
                                    />
                                    <div className="w-9 h-5 bg-surface-muted peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-border-subtle after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-emerald-500 border border-border-subtle"></div>
                                    <span className="ml-3 text-sm font-medium text-foreground">Active Template</span>
                                </label>
                            </div>
                        </form>

                        <div className="bg-primary/5 border border-primary/20 rounded-lg p-4">
                            <h3 className="text-sm font-semibold text-primary flex items-center gap-2 mb-2">
                                <Info className="w-4 h-4" /> Available Variables
                            </h3>
                            <p className="text-xs text-muted-foreground mb-3">
                                You can use the following variables in the subject or body. Wrap them in double curly braces like <code>{`{{ variable }}`}</code>.
                            </p>
                            <div className="flex flex-wrap gap-2">
                                {template.variables?.map(v => (
                                    <span key={v} className="px-2 py-1 bg-background border border-border-subtle rounded text-xs font-mono text-foreground">
                                        {`{{ ${v} }}`}
                                    </span>
                                ))}
                                <span className="px-2 py-1 bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 rounded text-xs font-mono">
                                    {`{{ brand_name }}`}
                                </span>
                                <span className="px-2 py-1 bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 rounded text-xs font-mono">
                                    {`{{ footer_brand }}`}
                                </span>
                                {(!template.variables || template.variables.length === 0) && (
                                    <span className="text-xs text-muted-foreground">No variables available for this template.</span>
                                )}
                            </div>
                        </div>
                    </div>

                    {/* Preview Section */}
                    <div className="space-y-3">
                        <div className="flex items-center justify-between">
                            <h3 className="text-sm font-medium text-foreground">Live Preview</h3>
                            <button
                                type="button"
                                onClick={() => setPreviewKey(k => k + 1)}
                                className="text-xs text-primary hover:underline"
                            >
                                Reload Preview
                            </button>
                        </div>
                        <div className="bg-white border border-border-subtle rounded-lg overflow-hidden shadow-sm h-[calc(100vh-12rem)] flex flex-col">
                            <div className="bg-surface-muted border-b border-border-subtle px-4 py-3 shrink-0">
                                <div className="text-sm font-medium text-black">
                                    Subject: <span className="font-normal">{data.subject}</span>
                                </div>
                            </div>
                            <iframe
                                key={previewKey}
                                src={`/email-templates/${template.id}/preview`}
                                className="w-full flex-1 bg-white"
                                sandbox="allow-same-origin"
                                title="Email Preview"
                            />
                        </div>
                        <p className="text-xs text-muted-foreground flex items-center gap-1.5 mt-2">
                            <AlertTriangle className="w-3.5 h-3.5" />
                            Preview relies on saved data. Save changes to update the preview window.
                        </p>
                    </div>
                </div>
            </div>
        </DashboardLayout>
    );
}
