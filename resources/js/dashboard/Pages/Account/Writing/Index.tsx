import React from 'react';
import { useForm, usePage } from '@inertiajs/react';
import AccountSettingsLayout from '../../../Layouts/AccountSettingsLayout';
import { Card, CardHeader, CardTitle, CardContent, CardDescription } from '../../../Components/ui/card';
import { PenTool, FileText, Type, Save } from 'lucide-react';
import { Switch } from '../../../Components/ui/switch';
import { Btn } from '../../../Components/ui/btn';

export default function WritingIndex() {
    const { preferences } = usePage<any>().props as any;
    const { data, setData, put, processing, recentlySuccessful, isDirty } = useForm({
        default_editor: preferences?.default_editor || 'rich_text', // 'markdown', 'rich_text'
        auto_save: preferences?.auto_save ?? true,
        spell_check: preferences?.spell_check ?? true,
    });

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        put('/account/writing', { preserveScroll: true });
    };

    return (
        <AccountSettingsLayout 
            title="Writing Preferences" 
            description="Customize your authoring and editing experience."
        >
            <form onSubmit={submit} className="space-y-6">
                
                <Card>
                    <CardHeader className="border-b border-border-subtle pb-4">
                        <CardTitle className="text-sm font-semibold flex items-center gap-2">
                            <span className="w-6 h-6 rounded bg-surface-muted flex items-center justify-center">
                                <PenTool className="w-3.5 h-3.5 text-muted-foreground" />
                            </span>
                            Editor Settings
                        </CardTitle>
                        <CardDescription className="text-xs mt-1.5 ml-8">
                            Configure the default behavior of the post editor.
                        </CardDescription>
                    </CardHeader>
                    <CardContent className="pt-6 space-y-6">
                        
                        <div className="flex flex-col gap-4">
                            <label className="text-sm font-medium">Default Editor Mode</label>
                            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4 max-w-lg">
                                <button
                                    type="button"
                                    onClick={() => setData('default_editor', 'rich_text')}
                                    className={`flex items-start p-4 border rounded-xl gap-4 transition-all text-left ${
                                        data.default_editor === 'rich_text' 
                                        ? 'border-primary bg-primary/5 ring-1 ring-primary/20' 
                                        : 'border-border bg-background hover:bg-surface-muted'
                                    }`}
                                >
                                    <Type className={`w-6 h-6 shrink-0 mt-0.5 ${data.default_editor === 'rich_text' ? 'text-primary' : 'text-muted-foreground'}`} />
                                    <div>
                                        <h4 className={`text-sm font-semibold ${data.default_editor === 'rich_text' ? 'text-primary' : 'text-foreground'}`}>Rich Text (WYSIWYG)</h4>
                                        <p className="text-xs text-muted-foreground mt-1">A visual editor similar to Word or Google Docs. Great for beginners.</p>
                                    </div>
                                </button>
                                <button
                                    type="button"
                                    onClick={() => setData('default_editor', 'markdown')}
                                    className={`flex items-start p-4 border rounded-xl gap-4 transition-all text-left ${
                                        data.default_editor === 'markdown' 
                                        ? 'border-primary bg-primary/5 ring-1 ring-primary/20' 
                                        : 'border-border bg-background hover:bg-surface-muted'
                                    }`}
                                >
                                    <FileText className={`w-6 h-6 shrink-0 mt-0.5 ${data.default_editor === 'markdown' ? 'text-primary' : 'text-muted-foreground'}`} />
                                    <div>
                                        <h4 className={`text-sm font-semibold ${data.default_editor === 'markdown' ? 'text-primary' : 'text-foreground'}`}>Markdown</h4>
                                        <p className="text-xs text-muted-foreground mt-1">Write using Markdown syntax. Fast and preferred by developers.</p>
                                    </div>
                                </button>
                            </div>
                        </div>

                        <div className="h-px bg-border-subtle my-4" />

                        <div className="flex items-center justify-end md:justify-between w-full">
                            <div className="space-y-0.5">
                                <h3 className="text-sm font-medium">Auto-save Drafts</h3>
                                <p className="text-sm text-muted-foreground">Automatically save your progress every 30 seconds while writing.</p>
                            </div>
                            <Switch 
                                checked={data.auto_save}
                                onCheckedChange={(checked) => setData('auto_save', checked)}
                            />
                        </div>

                        <div className="flex items-center justify-end md:justify-between w-full">
                            <div className="space-y-0.5">
                                <h3 className="text-sm font-medium">Enable Spell Check</h3>
                                <p className="text-sm text-muted-foreground">Highlight spelling errors within the editor.</p>
                            </div>
                            <Switch 
                                checked={data.spell_check}
                                onCheckedChange={(checked) => setData('spell_check', checked)}
                            />
                        </div>

                    </CardContent>
                    <div className="px-6 py-4 border-t border-border-subtle flex justify-end gap-3 bg-surface-muted/10 rounded-b-lg">
                        {recentlySuccessful && (
                            <span className="text-sm text-green-600 self-center font-medium">Preferences saved.</span>
                        )}
                        <Btn 
                            type="submit"
                            loading={processing}
                            disabled={!isDirty || processing}
                            icon={<Save className="w-4 h-4" />}
                        >
                            Save Preferences
                        </Btn>
                    </div>
                </Card>

            </form>
        </AccountSettingsLayout>
    );
}
