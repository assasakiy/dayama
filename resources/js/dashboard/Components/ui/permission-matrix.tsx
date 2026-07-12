import React, { useState, useMemo } from 'react';
import { Search, ChevronDown, ChevronRight, CheckSquare, Square, MinusSquare } from 'lucide-react';

interface PermissionItem {
    id: string;
    name: string;
    module?: string;
    action?: string;
    scope?: string;
    description?: string;
}

interface GroupedPermissions {
    [module: string]: PermissionItem[];
}

interface PermissionMatrixProps {
    groupedPermissions: GroupedPermissions;
    selected: string[];
    onChange: (selected: string[]) => void;
}

const MODULE_LABELS: Record<string, string> = {
    dashboard: 'Dashboard',
    posts: 'Posts',
    pages: 'Pages',
    media: 'Media',
    comments: 'Comments',
    categories: 'Categories',
    tags: 'Tags',
    users: 'Users',
    roles: 'Roles',
    settings: 'Settings',
    analytics: 'Analytics',
    other: 'Other',
};

const ACTION_COLORS: Record<string, string> = {
    view: 'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-950/40 dark:text-blue-300 dark:border-blue-800',
    create: 'bg-green-50 text-green-700 border-green-200 dark:bg-green-950/40 dark:text-green-300 dark:border-green-800',
    edit: 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950/40 dark:text-amber-300 dark:border-amber-800',
    delete: 'bg-red-50 text-red-700 border-red-200 dark:bg-red-950/40 dark:text-red-300 dark:border-red-800',
    publish: 'bg-purple-50 text-purple-700 border-purple-200 dark:bg-purple-950/40 dark:text-purple-300 dark:border-purple-800',
    moderate: 'bg-orange-50 text-orange-700 border-orange-200 dark:bg-orange-950/40 dark:text-orange-300 dark:border-orange-800',
    upload: 'bg-teal-50 text-teal-700 border-teal-200 dark:bg-teal-950/40 dark:text-teal-300 dark:border-teal-800',
    restore: 'bg-cyan-50 text-cyan-700 border-cyan-200 dark:bg-cyan-950/40 dark:text-cyan-300 dark:border-cyan-800',
    update: 'bg-indigo-50 text-indigo-700 border-indigo-200 dark:bg-indigo-950/40 dark:text-indigo-300 dark:border-indigo-800',
    reply: 'bg-sky-50 text-sky-700 border-sky-200 dark:bg-sky-950/40 dark:text-sky-300 dark:border-sky-800',
};

const SCOPE_LABELS: Record<string, string> = { own: 'Own', all: 'All', assigned: 'Assigned' };

function TriCheckbox({ state, onChange }: { state: 'all' | 'some' | 'none'; onChange: () => void }) {
    if (state === 'all') return (
        <button type="button" onClick={onChange} className="w-4 h-4 rounded flex items-center justify-center bg-primary border border-primary text-primary-foreground shrink-0 transition-all hover:bg-primary/80">
            <CheckSquare className="w-3 h-3" />
        </button>
    );
    if (state === 'some') return (
        <button type="button" onClick={onChange} className="w-4 h-4 rounded flex items-center justify-center bg-primary/20 border border-primary shrink-0 transition-all hover:bg-primary/30">
            <MinusSquare className="w-3 h-3 text-primary" />
        </button>
    );
    return (
        <button type="button" onClick={onChange} className="w-4 h-4 rounded flex items-center justify-center border border-border-subtle shrink-0 hover:border-primary transition-all">
            <Square className="w-3 h-3 text-muted-foreground opacity-0 hover:opacity-50" />
        </button>
    );
}

export default function PermissionMatrix({ groupedPermissions, selected, onChange }: PermissionMatrixProps) {
    const [search, setSearch] = useState('');
    const [collapsed, setCollapsed] = useState<Set<string>>(new Set());
    const allPermNames = useMemo(
        () => Object.values(groupedPermissions).flat().map((p) => p.name),
        [groupedPermissions]
    );

    const filtered = useMemo(() => {
        if (!search.trim()) return groupedPermissions;
        const q = search.toLowerCase();
        const result: GroupedPermissions = {};
        for (const [mod, perms] of Object.entries(groupedPermissions)) {
            const matched = perms.filter(
                (p) => p.name.toLowerCase().includes(q) || (p.action || '').toLowerCase().includes(q)
            );
            if (matched.length > 0) result[mod] = matched;
        }
        return result;
    }, [groupedPermissions, search]);

    const toggleModule = (module: string) => {
        const perms = (filtered[module] || []).map((p) => p.name);
        const allSel = perms.every((n) => selected.includes(n));
        if (allSel) {
            onChange(selected.filter((s) => !perms.includes(s)));
        } else {
            const next = new Set(selected);
            perms.forEach((p) => next.add(p));
            onChange(Array.from(next));
        }
    };

    const togglePerm = (permName: string) => {
        if (selected.includes(permName)) {
            onChange(selected.filter((s) => s !== permName));
        } else {
            onChange([...selected, permName]);
        }
    };

    const toggleCollapse = (module: string) => {
        setCollapsed((prev) => {
            const next = new Set(prev);
            next.has(module) ? next.delete(module) : next.add(module);
            return next;
        });
    };

    const selectAll = () => onChange(allPermNames);
    const clearAll = () => onChange([]);

    const getModuleState = (module: string): 'all' | 'some' | 'none' => {
        const perms = (filtered[module] || []).map((p) => p.name);
        const selCount = perms.filter((p) => selected.includes(p)).length;
        if (selCount === 0) return 'none';
        if (selCount === perms.length) return 'all';
        return 'some';
    };

    const getActionLabel = (perm: PermissionItem): string => {
        if (perm.action) {
            return perm.scope ? `${perm.action} (${SCOPE_LABELS[perm.scope] || perm.scope})` : perm.action;
        }
        return perm.name;
    };

    const getActionColor = (perm: PermissionItem): string => {
        const action = perm.action?.split('.')[0] || '';
        return ACTION_COLORS[action] || 'bg-surface-muted text-muted-foreground border-border-subtle';
    };

    return (
        <div className="flex flex-col gap-2">
            {/* Toolbar */}
            <div className="flex items-center gap-2">
                <div className="relative flex-1">
                    <Search className="absolute left-2.5 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-muted-foreground" />
                    <input
                        type="text"
                        value={search}
                        onChange={(e) => setSearch(e.target.value)}
                        placeholder="Search permissions..."
                        className="w-full h-8 pl-8 pr-3 text-xs rounded-md border border-border-subtle bg-background focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"
                    />
                </div>
                <button type="button" onClick={selectAll} className="h-8 px-3 text-xs font-medium text-primary hover:bg-primary/10 rounded-md transition-colors border border-transparent hover:border-primary/20">
                    Select All
                </button>
                <button type="button" onClick={clearAll} className="h-8 px-3 text-xs font-medium text-muted-foreground hover:bg-surface-muted rounded-md transition-colors border border-transparent hover:border-border-subtle">
                    Clear
                </button>
            </div>

            {/* Selected count */}
            <p className="text-xs text-muted-foreground px-0.5">
                {selected.length} of {allPermNames.length} permission{allPermNames.length !== 1 ? 's' : ''} selected
            </p>

            {/* Module groups */}
            <div className="border border-border-subtle rounded-lg overflow-hidden divide-y divide-border-subtle">
                {Object.entries(filtered).length === 0 && (
                    <div className="py-8 text-center text-sm text-muted-foreground">No permissions match your search.</div>
                )}
                {Object.entries(filtered).map(([module, perms]) => {
                    const state = getModuleState(module);
                    const isCollapsed = collapsed.has(module);
                    return (
                        <div key={module}>
                            {/* Module header */}
                            <div className="flex items-center gap-2 px-3 py-2.5 bg-surface-muted/50 hover:bg-surface-muted transition-colors">
                                <TriCheckbox state={state} onChange={() => toggleModule(module)} />
                                <span className="flex-1 text-xs font-semibold uppercase tracking-wider text-foreground">
                                    {MODULE_LABELS[module] || module}
                                </span>
                                <span className="text-xs text-muted-foreground mr-1">
                                    {perms.filter((p) => selected.includes(p.name)).length}/{perms.length}
                                </span>
                                <button
                                    type="button"
                                    onClick={() => toggleCollapse(module)}
                                    className="p-0.5 rounded text-muted-foreground hover:text-foreground transition-colors"
                                >
                                    {isCollapsed ? <ChevronRight className="w-3.5 h-3.5" /> : <ChevronDown className="w-3.5 h-3.5" />}
                                </button>
                            </div>

                            {/* Permission chips */}
                            {!isCollapsed && (
                                <div className="flex flex-wrap gap-1.5 px-4 py-3 bg-background">
                                    {perms.map((perm) => {
                                        const active = selected.includes(perm.name);
                                        return (
                                            <button
                                                key={perm.id}
                                                type="button"
                                                onClick={() => togglePerm(perm.name)}
                                                title={perm.name}
                                                className={`inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-medium border transition-all ${
                                                    active
                                                        ? 'ring-2 ring-primary/40 ' + getActionColor(perm)
                                                        : 'opacity-50 ' + getActionColor(perm)
                                                }`}
                                            >
                                                {active && <CheckSquare className="w-3 h-3 shrink-0" />}
                                                {getActionLabel(perm)}
                                            </button>
                                        );
                                    })}
                                </div>
                            )}
                        </div>
                    );
                })}
            </div>
        </div>
    );
}
