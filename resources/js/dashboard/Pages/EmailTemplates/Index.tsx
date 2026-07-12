import React from 'react';
import { Head, Link } from '@inertiajs/react';
import DashboardLayout from '@dashboard/Layouts/DashboardLayout';
import { LayoutTemplate, Edit } from 'lucide-react';
import { Badge } from '@dashboard/Components/ui/badge';

interface EmailTemplate {
    id: string;
    key: string;
    name: string;
    subject: string;
    is_active: boolean;
    updated_at: string;
}

export default function EmailTemplatesIndex({ templates }: { templates: EmailTemplate[] }) {
    return (
        <DashboardLayout>
            <Head title="Email Templates" />
            <div className="space-y-6">
                <div>
                    <h1 className="text-2xl font-semibold text-foreground tracking-tight">Email Templates</h1>
                    <p className="text-sm text-muted-foreground mt-1">
                        Manage automated emails sent by the system.
                    </p>
                </div>

                <div className="bg-background border border-border-subtle rounded-lg shadow-sm overflow-hidden">
                    <div className="overflow-x-auto">
                        <table className="w-full text-sm text-left">
                            <thead className="bg-surface-muted text-muted-foreground border-b border-border-subtle">
                                <tr>
                                    <th className="px-6 py-4 font-medium">Name / Key</th>
                                    <th className="px-6 py-4 font-medium">Subject</th>
                                    <th className="px-6 py-4 font-medium">Status</th>
                                    <th className="px-6 py-4 font-medium text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-border-subtle">
                                {templates.map(template => (
                                    <tr key={template.id} className="hover:bg-surface-muted/50 transition-colors">
                                        <td className="px-6 py-4">
                                            <div className="font-medium text-foreground">{template.name}</div>
                                            <div className="text-xs text-muted-foreground font-mono mt-0.5">{template.key}</div>
                                        </td>
                                        <td className="px-6 py-4">
                                            <span className="truncate max-w-[250px] inline-block" title={template.subject}>
                                                {template.subject}
                                            </span>
                                        </td>
                                        <td className="px-6 py-4">
                                            {template.is_active ? (
                                                <Badge variant="default" className="bg-emerald-500/10 text-emerald-600 hover:bg-emerald-500/20 border-emerald-500/20">Active</Badge>
                                            ) : (
                                                <Badge variant="secondary" className="text-muted-foreground">Inactive</Badge>
                                            )}
                                        </td>
                                        <td className="px-6 py-4">
                                            <div className="flex justify-end gap-2">
                                                <Link
                                                    href={`/dashboard/email-templates/${template.id}/edit`}
                                                    className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md text-xs font-medium bg-primary/10 text-primary hover:bg-primary/20 transition-colors"
                                                >
                                                    <Edit className="w-3.5 h-3.5" />
                                                    Edit
                                                </Link>
                                            </div>
                                        </td>
                                    </tr>
                                ))}
                                {templates.length === 0 && (
                                    <tr>
                                        <td colSpan={4} className="px-6 py-12 text-center text-muted-foreground">
                                            <LayoutTemplate className="w-12 h-12 mx-auto mb-3 opacity-20" />
                                            <p>No email templates found.</p>
                                        </td>
                                    </tr>
                                )}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </DashboardLayout>
    );
}
