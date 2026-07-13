import React, { useState, useEffect } from 'react';
import { Head, router } from '@inertiajs/react';
import DashboardLayout from '@dashboard/Layouts/DashboardLayout';
import { Input } from '@dashboard/Components/ui/input';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogClose, DialogFooter } from '@dashboard/Components/ui/dialog';
import { Select, SelectTrigger, SelectValue, SelectContent, SelectItem } from '@dashboard/Components/ui/select';
import { Tabs, TabsList, TabsTrigger, TabsContent } from '@dashboard/Components/ui/tabs';
import { Switch } from '@dashboard/Components/ui/switch';
import ConfirmDialog from '@dashboard/Components/ui/confirm-dialog';
import MediaPicker from '@dashboard/Components/MediaPicker';
import {
    Plus,
    Pencil,
    Trash2,
    FolderTree,
    Layers,
    X,
    Save,
    Image as ImageIcon
} from 'lucide-react';
import { usePermissions } from '@dashboard/hooks/usePermissions';
import { Btn } from '@dashboard/Components/ui/btn';

interface Category {
    id: string;
    name: string;
    title: string | null;
    description: string | null;
    parent: { id: string; name: string } | null;
    color: string | null;
    icon: string | null;
    seo_title: string | null;
    seo_description: string | null;
    meta_keywords: string | null;
    is_visible: boolean;
    sort_order: number;
    posts_count: number;
    created_by?: string | null;
    updated_by?: string | null;
    deleted_at?: string | null;
    created_at: string;
    updated_at?: string;
    image_url?: string | null;
}

interface ParentCategory {
    id: string;
    name: string;
}

export default function CategoryIndex({ categories, parentCategories }: { categories: Category[]; parentCategories: ParentCategory[] }) {
    const [modalOpen, setModalOpen] = useState(false);
    const [editCategory, setEditCategory] = useState<Category | null>(null);
    const [deleteTarget, setDeleteTarget] = useState<{ id: string; name: string } | null>(null);

    const formatDate = (dateString?: string | null) => {
        if (!dateString) return null;
        const d = new Date(dateString);
        return `${d.toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' })} • ${d.toLocaleTimeString('en-GB', { hour: '2-digit', minute: '2-digit' })}`;
    };

    // Form states
    const [name, setName] = useState('');
    const [title, setTitle] = useState('');
    const [description, setDescription] = useState('');
    const [parentId, setParentId] = useState('none');
    
    // Visual states
    const [color, setColor] = useState('#ef4444');
    const [icon, setIcon] = useState('');
    const [isVisible, setIsVisible] = useState(true);
    const [sortOrder, setSortOrder] = useState(0);
    const [image, setImage] = useState<File | null>(null);
    const [imageMediaId, setImageMediaId] = useState<string>('');
    const [removeImage, setRemoveImage] = useState(false);
    const [imagePreview, setImagePreview] = useState<string | null>(null);
    const [isMediaPickerOpen, setIsMediaPickerOpen] = useState(false);

    // SEO states
    const [seoTitle, setSeoTitle] = useState('');
    const [seoDescription, setSeoDescription] = useState('');
    const [metaKeywords, setMetaKeywords] = useState('');

    const [errors, setErrors] = useState<Record<string, string>>({});
    const [submitting, setSubmitting] = useState(false);
    const [activeTab, setActiveTab] = useState('general');

    const { can } = usePermissions();
    const isEdit = !!editCategory;

    const isDirty = isEdit
        ? name !== (editCategory?.name ?? '') ||
          title !== (editCategory?.title ?? '') ||
          description !== (editCategory?.description ?? '') ||
          parentId !== (editCategory?.parent?.id ?? 'none') ||
          color !== (editCategory?.color ?? '#ef4444') ||
          icon !== (editCategory?.icon ?? '') ||
          isVisible !== (editCategory?.is_visible ?? true) ||
          sortOrder !== (editCategory?.sort_order ?? 0) ||
          seoTitle !== (editCategory?.seo_title ?? '') ||
          seoDescription !== (editCategory?.seo_description ?? '') ||
          metaKeywords !== (editCategory?.meta_keywords ?? '') ||
          image !== null ||
          imageMediaId !== '' ||
          removeImage !== false
        : !!name;

    const resetForm = () => {
        setName('');
        setTitle('');
        setDescription('');
        setParentId('none');
        setColor('#ef4444');
        setIcon('');
        setIsVisible(true);
        setSortOrder(0);
        setImage(null);
        setImageMediaId('');
        setRemoveImage(false);
        setImagePreview(null);
        setSeoTitle('');
        setSeoDescription('');
        setMetaKeywords('');
        setErrors({});
        setSubmitting(false);
        setEditCategory(null);
        setActiveTab('general');
    };

    const openCreate = () => {
        resetForm();
        setModalOpen(true);
    };

    const openEdit = (cat: Category) => {
        setEditCategory(cat);
        setName(cat.name);
        setTitle(cat.title ?? '');
        setDescription(cat.description ?? '');
        setParentId(cat.parent?.id ?? 'none');
        setColor(cat.color ?? '#ef4444');
        setIcon(cat.icon ?? '');
        setIsVisible(cat.is_visible);
        setSortOrder(cat.sort_order ?? 0);
        setImagePreview(cat.image_url ?? null);
        setImage(null);
        setImageMediaId('');
        setRemoveImage(false);
        setSeoTitle(cat.seo_title ?? '');
        setSeoDescription(cat.seo_description ?? '');
        setMetaKeywords(cat.meta_keywords ?? '');
        setErrors({});
        setSubmitting(false);
        setActiveTab('general');
        setModalOpen(true);
    };

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        setSubmitting(true);
        
        const data: Record<string, any> = { 
            name, 
            title: title || null,
            description: description || null,
            color: color || null,
            icon: icon || null,
            is_visible: isVisible,
            sort_order: sortOrder,
            seo_title: seoTitle || null,
            seo_description: seoDescription || null,
            meta_keywords: metaKeywords || null,
        };

        if (parentId && parentId !== 'none') {
            data.parent_id = parentId;
        } else {
            data.parent_id = '';
        }
        if (image) data.image = image;
        if (imageMediaId) data.image_media_id = imageMediaId;
        if (removeImage) data.remove_image = true;

        if (isEdit) {
            router.post(`/categories/${editCategory!.id}`, {
                _method: 'put',
                ...data
            }, {
                onError: (errs) => { setErrors(errs); setSubmitting(false); },
                onSuccess: () => { setModalOpen(false); resetForm(); },
                onFinish: () => setSubmitting(false),
            });
        } else {
            router.post('/categories', data, {
                onError: (errs) => { setErrors(errs); setSubmitting(false); },
                onSuccess: () => { setModalOpen(false); resetForm(); },
                onFinish: () => setSubmitting(false),
            });
        }
    };

    const handleDelete = () => {
        if (!deleteTarget) return;
        router.delete(`/categories/${deleteTarget.id}`, { preserveScroll: true });
        setDeleteTarget(null);
    };

    return (
        <DashboardLayout>
            <Head title="Categories" />
            <div className="space-y-5">
                <div className="flex items-center justify-end md:justify-between w-full">
                    <div className="hidden md:block">
                        <h1 className="text-xl font-semibold tracking-tight">Categories</h1>
                        <p className="hidden lg:block text-sm text-muted-foreground mt-0.5">Organize your content and SEO metadata</p>
                    </div>
                    {can('categories.create') && (
                        <button
                            onClick={openCreate}
                            className="inline-flex items-center gap-2 h-9 px-4 bg-primary text-primary-foreground rounded-md text-sm font-medium hover:bg-primary/90 active:bg-primary/80 transition-all shadow-sm"
                        >
                            <Plus className="w-4 h-4" />
                            New Category
                        </button>
                    )}
                </div>

                <div className="bg-background border border-border-subtle rounded-lg overflow-hidden">
                    <table className="w-full text-sm">
                        <thead>
                            <tr className="border-b border-border-subtle bg-surface-muted/50">
                                <th className="text-left px-4 py-3 font-medium">Name</th>
                                <th className="text-left px-4 py-3 font-medium">Visuals</th>
                                <th className="text-left px-4 py-3 font-medium">Posts</th>
                                <th className="text-left px-4 py-3 font-medium">Visibility</th>
                                <th className="text-right px-4 py-3 font-medium">Actions</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-border-subtle">
                            {categories.map((cat) => (
                                <tr key={cat.id} className="hover:bg-surface-muted/30 transition-colors">
                                    <td className="px-4 py-3">
                                        <div className="flex items-center gap-3">
                                            <div 
                                                className="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 border border-border-subtle"
                                                style={{ backgroundColor: cat.color ? `${cat.color}20` : 'var(--surface-muted)' }}
                                            >
                                                {cat.icon ? (
                                                    <span className="text-sm" dangerouslySetInnerHTML={{ __html: cat.icon }}></span>
                                                ) : (
                                                    <FolderTree className="w-4 h-4" style={{ color: cat.color || 'var(--muted-foreground)' }} />
                                                )}
                                            </div>
                                            <div>
                                                <button
                                                    onClick={() => openEdit(cat)}
                                                    className="font-medium hover:text-primary transition-colors flex items-center gap-2"
                                                >
                                                    {cat.name}
                                                </button>
                                                {cat.parent && (
                                                    <p className="text-xs text-muted-foreground mt-0.5">Subcategory of <span className="font-medium">{cat.parent.name}</span></p>
                                                )}
                                                {cat.description && (
                                                    <p className="text-xs text-muted-foreground line-clamp-1 mt-0.5">{cat.description}</p>
                                                )}
                                            </div>
                                        </div>
                                    </td>
                                    <td className="px-4 py-3">
                                        {cat.image_url ? (
                                            <div className="w-12 h-8 rounded bg-surface-muted border border-border-subtle overflow-hidden">
                                                <img src={cat.image_url} alt={cat.name} className="w-full h-full object-cover" />
                                            </div>
                                        ) : (
                                            <span className="text-xs text-muted-foreground">-</span>
                                        )}
                                    </td>
                                    <td className="px-4 py-3">
                                        <span className="inline-flex items-center gap-1.5 text-muted-foreground">
                                            <Layers className="w-3.5 h-3.5" />
                                            {cat.posts_count ?? 0}
                                        </span>
                                    </td>
                                    <td className="px-4 py-3">
                                        {cat.is_visible ? (
                                            <span className="inline-flex items-center gap-1.5 text-xs font-medium text-success bg-success/10 px-2 py-1 rounded-md">
                                                <span className="w-1.5 h-1.5 rounded-full bg-success"></span>
                                                Visible
                                            </span>
                                        ) : (
                                            <span className="inline-flex items-center gap-1.5 text-xs font-medium text-muted-foreground bg-surface-muted px-2 py-1 rounded-md">
                                                <span className="w-1.5 h-1.5 rounded-full bg-muted-foreground"></span>
                                                Hidden
                                            </span>
                                        )}
                                    </td>
                                     <td className="px-4 py-3 text-right">
                                        <div className="flex items-center justify-end gap-1">
                                            {can('categories.edit') && (
                                                <button
                                                    onClick={() => openEdit(cat)}
                                                    className="p-1.5 rounded-md text-muted-foreground hover:text-foreground hover:bg-surface-muted transition-colors"
                                                    title="Edit"
                                                >
                                                    <Pencil className="w-3.5 h-3.5" />
                                                </button>
                                            )}
                                            {can('categories.delete') && (
                                                <button
                                                    onClick={() => setDeleteTarget({ id: cat.id, name: cat.name })}
                                                    className="p-1.5 rounded-md text-muted-foreground hover:text-danger hover:bg-danger/10 transition-colors"
                                                    title="Delete"
                                                >
                                                    <Trash2 className="w-3.5 h-3.5" />
                                                </button>
                                            )}
                                        </div>
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>

                    {categories.length === 0 && (
                        <div className="flex flex-col items-center justify-center py-16 text-center">
                            <FolderTree className="w-12 h-12 text-muted-foreground/30 mb-4" />
                            <p className="text-sm font-medium text-foreground">No categories yet</p>
                            <p className="text-xs text-muted-foreground mt-1">Create your first category to organize posts.</p>
                        </div>
                    )}
                </div>
            </div>

            <Dialog open={modalOpen} onOpenChange={(open) => { if (!open) { setModalOpen(false); resetForm(); } }}>
                <DialogContent className="max-w-2xl max-h-[90vh] flex flex-col p-0 gap-0 overflow-hidden rounded-2xl">
                    <DialogHeader className="flex flex-row items-center justify-between px-6 py-4 border-b border-border-subtle mb-0 shrink-0">
                        <DialogTitle className="text-base">{isEdit ? 'Edit Category' : 'Create Category'}</DialogTitle>
                        <DialogClose className="p-1 rounded-md text-muted-foreground hover:text-foreground hover:bg-surface-muted transition-colors">
                            <X className="w-4 h-4" />
                        </DialogClose>
                    </DialogHeader>
                    
                    <form onSubmit={handleSubmit} className="flex flex-col flex-1 min-h-0 bg-background">
                        <Tabs value={activeTab} onValueChange={setActiveTab} className="flex flex-col flex-1 min-h-0">
                            <div className="px-6 pt-5 shrink-0 z-10">
                                <TabsList className="w-full bg-surface-muted p-1.5 rounded-xl border border-border-subtle/50">
                                    <TabsTrigger value="general" className="flex-1 rounded-lg">General</TabsTrigger>
                                    <TabsTrigger value="visual" className="flex-1 rounded-lg">Visuals</TabsTrigger>
                                    <TabsTrigger value="seo" className="flex-1 rounded-lg">SEO Metadata</TabsTrigger>
                                </TabsList>
                            </div>
                            
                            <div className="flex-1 overflow-y-auto px-6 py-5">
                                <TabsContent value="general" className="space-y-4 m-0">
                                    <Input
                                        label="Name"
                                        value={name}
                                        onChange={(e) => setName(e.target.value)}
                                        error={errors.name}
                                        required
                                        placeholder="Category name (e.g., Technology)"
                                    />
                                    
                                    <Input
                                        label="Page Title (Optional)"
                                        value={title}
                                        onChange={(e) => setTitle(e.target.value)}
                                        error={errors.title}
                                        placeholder="Overwrites the H1 on the category page"
                                    />

                                    <div className="space-y-1.5">
                                        <label className="text-sm font-medium">Description</label>
                                        <textarea
                                            value={description}
                                            onChange={(e) => setDescription(e.target.value)}
                                            placeholder="Optional description shown on category page..."
                                            rows={3}
                                            className="flex w-full rounded-sm border border-border-subtle bg-background px-3 py-1.5 text-sm transition-colors placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary"
                                        />
                                        {errors.description && <p className="text-xs text-destructive">{errors.description}</p>}
                                    </div>

                                     <div className="space-y-1.5">
                                        <label className="text-sm font-medium">Parent Category</label>
                                        <Select value={parentId} onValueChange={setParentId}>
                                            <SelectTrigger className="rounded-lg">
                                                <SelectValue placeholder="None (top level)" />
                                            </SelectTrigger>
                                            <SelectContent>
                                                <SelectItem value="none">None (top level)</SelectItem>
                                                {parentCategories
                                                    .filter((p) => p.id !== editCategory?.id)
                                                    .map((p) => (
                                                        <SelectItem key={p.id} value={p.id}>{p.name}</SelectItem>
                                                    ))}
                                            </SelectContent>
                                        </Select>
                                        {errors.parent_id && <p className="text-xs text-destructive">{errors.parent_id}</p>}
                                    </div>
                                    
                                    <div className="flex items-center justify-between p-4 bg-gradient-to-r from-surface-muted/50 to-transparent rounded-xl border border-border-subtle/50 group hover:border-border-subtle transition-colors mt-2">
                                        <div>
                                            <label className="text-sm font-semibold flex items-center gap-2">
                                                Visibility
                                                {isVisible ? <span className="w-2 h-2 rounded-full bg-success shadow-[0_0_8px_rgba(34,197,94,0.5)]"></span> : <span className="w-2 h-2 rounded-full bg-muted-foreground"></span>}
                                            </label>
                                            <span className="text-xs text-muted-foreground mt-0.5 block">Show this category on the public site</span>
                                        </div>
                                        <Switch checked={isVisible} onCheckedChange={setIsVisible} />
                                    </div>
                                </TabsContent>

                                 <TabsContent value="visual" className="space-y-6 m-0">
                                    <div className="grid grid-cols-2 gap-5">
                                        <div className="space-y-2">
                                            <label className="text-sm font-medium">Category Color</label>
                                            <div className="flex items-center gap-3">
                                                <div className="relative w-10 h-10 rounded-full border-2 border-border-strong overflow-hidden shrink-0 shadow-sm" style={{ backgroundColor: color }}>
                                                    <input 
                                                        type="color" 
                                                        value={color} 
                                                        onChange={(e) => setColor(e.target.value)}
                                                        className="absolute inset-0 opacity-0 cursor-pointer w-full h-full"
                                                    />
                                                </div>
                                                <Input 
                                                    value={color} 
                                                    onChange={(e) => setColor(e.target.value)}
                                                    placeholder="#000000"
                                                    className="flex-1 font-mono text-sm uppercase"
                                                />
                                            </div>
                                            <p className="text-xs text-muted-foreground">Used for badges and accents.</p>
                                        </div>
                                        
                                        <Input
                                            label="Sort Order"
                                            type="number"
                                            value={sortOrder}
                                            onChange={(e) => setSortOrder(parseInt(e.target.value) || 0)}
                                            error={errors.sort_order}
                                        />
                                    </div>
                                    
                                     <div className="flex items-start gap-4">
                                        <div className="flex-1">
                                            <Input
                                                label="Icon (Emoji or SVG)"
                                                value={icon}
                                                onChange={(e) => setIcon(e.target.value)}
                                                error={errors.icon}
                                                placeholder="e.g. 🚀 or <svg>...</svg>"
                                            />
                                        </div>
                                        {icon && (
                                            <div className="shrink-0 w-[68px] h-[68px] mt-[22px] rounded-xl border border-border-subtle bg-surface-muted flex items-center justify-center text-3xl overflow-hidden shadow-sm" dangerouslySetInnerHTML={{ __html: icon }} />
                                        )}
                                    </div>
                                    
                                     <div className="space-y-2 pt-2">
                                        <label className="text-sm font-medium">Hero Image / Banner</label>
                                        <div className="flex items-start gap-4 p-4 rounded-xl border border-border-subtle bg-surface-muted/20">
                                            <div className="flex-1">
                                                {imagePreview ? (
                                                    <div className="relative w-full h-36 rounded-xl border border-border-subtle bg-background overflow-hidden group shadow-sm">
                                                        <img src={imagePreview} alt="Preview" className="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" />
                                                        <div className="absolute inset-0 bg-background/60 backdrop-blur-sm opacity-0 group-hover:opacity-100 flex items-center justify-center transition-all duration-300">
                                                            <button 
                                                                type="button"
                                                                onClick={() => { setImagePreview(null); setImage(null); setImageMediaId(''); setRemoveImage(true); }}
                                                                className="text-sm font-medium text-destructive bg-destructive/10 hover:bg-destructive hover:text-white px-4 py-2 rounded-lg transition-colors flex items-center gap-2"
                                                            >
                                                                <Trash2 className="w-4 h-4" />
                                                                Remove Image
                                                            </button>
                                                        </div>
                                                    </div>
                                                ) : (
                                                    <div className="w-full h-36 rounded-xl border-2 border-dashed border-border-subtle bg-background flex flex-col items-center justify-center text-muted-foreground gap-3 transition-colors hover:border-primary/50 hover:bg-primary/5">
                                                        <div className="w-10 h-10 rounded-full bg-surface-muted flex items-center justify-center">
                                                            <ImageIcon className="w-5 h-5" />
                                                        </div>
                                                        <span className="text-sm font-medium">No image uploaded</span>
                                                    </div>
                                                )}
                                                {errors.image && <p className="text-xs text-destructive mt-1">{errors.image}</p>}
                                            </div>
                                            <div className="shrink-0 flex flex-col gap-2 w-36">
                                                <button 
                                                    type="button" 
                                                    onClick={() => setIsMediaPickerOpen(true)}
                                                    className="w-full h-10 px-4 border border-border-subtle bg-background rounded-lg text-sm font-medium hover:bg-surface-muted hover:text-primary transition-colors shadow-sm flex items-center justify-center gap-2"
                                                >
                                                    <ImageIcon className="w-4 h-4" />
                                                    Library...
                                                </button>
                                                <label className="w-full h-10 px-4 border border-border-subtle bg-primary text-primary-foreground rounded-lg text-sm font-medium hover:bg-primary/90 active:bg-primary/80 transition-colors cursor-pointer flex items-center justify-center gap-2 shadow-sm">
                                                    <Plus className="w-4 h-4" />
                                                    Upload
                                                    <input 
                                                        type="file" 
                                                        className="hidden" 
                                                        accept="image/*"
                                                        onChange={(e) => {
                                                            const file = e.target.files?.[0];
                                                            if (file) {
                                                                setImage(file);
                                                                setImagePreview(URL.createObjectURL(file));
                                                                setImageMediaId('');
                                                                setRemoveImage(false);
                                                            }
                                                        }}
                                                    />
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </TabsContent>

                                <TabsContent value="seo" className="space-y-4 m-0">
                                    <Input
                                        label="SEO Meta Title"
                                        value={seoTitle}
                                        onChange={(e) => setSeoTitle(e.target.value)}
                                        error={errors.seo_title}
                                        placeholder="Optimal length 50-60 characters"
                                    />
                                    
                                    <div className="space-y-1.5">
                                        <label className="text-sm font-medium flex items-center justify-between">
                                            SEO Meta Description
                                            <span className={`text-xs ${seoDescription.length > 160 ? 'text-warning' : 'text-muted-foreground'}`}>
                                                {seoDescription.length}/160
                                            </span>
                                        </label>
                                        <textarea
                                            value={seoDescription}
                                            onChange={(e) => setSeoDescription(e.target.value)}
                                            placeholder="A brief summary for search engine results..."
                                            rows={3}
                                            className="flex w-full rounded-sm border border-border-subtle bg-background px-3 py-1.5 text-sm transition-colors placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary"
                                        />
                                        {errors.seo_description && <p className="text-xs text-destructive">{errors.seo_description}</p>}
                                    </div>

                                    <Input
                                        label="Meta Keywords"
                                        value={metaKeywords}
                                        onChange={(e) => setMetaKeywords(e.target.value)}
                                        error={errors.meta_keywords}
                                        placeholder="keyword1, keyword2, keyword3"
                                    />
                                    <p className="text-xs text-muted-foreground -mt-2">Comma separated list of keywords.</p>
                                </TabsContent>
                            </div>

                            <div className="px-6 py-4 bg-surface-muted/30 border-t border-border-subtle mt-0">
                                <div className="flex items-center justify-between w-full">
                                    <div className="flex gap-8">
                                        {isEdit && editCategory && (
                                            <>
                                                <div className="flex flex-col gap-1">
                                                    <span className="text-muted-foreground text-[10px] uppercase tracking-wider font-semibold">Created</span>
                                                    {editCategory.created_by ? (
                                                        <div className="flex flex-col">
                                                            <span className="font-medium text-sm text-foreground">{editCategory.created_by}</span>
                                                            <span className="text-xs text-muted-foreground">{formatDate(editCategory.created_at)}</span>
                                                        </div>
                                                    ) : (
                                                        <div className="flex flex-col">
                                                            <span className="font-medium text-sm text-foreground">System</span>
                                                            <span className="text-xs text-muted-foreground">{formatDate(editCategory.created_at)}</span>
                                                        </div>
                                                    )}
                                                </div>
                                                <div className="flex flex-col gap-1">
                                                    <span className="text-muted-foreground text-[10px] uppercase tracking-wider font-semibold">Updated</span>
                                                    {editCategory.updated_by && editCategory.updated_at ? (
                                                        <div className="flex flex-col">
                                                            <span className="font-medium text-sm text-foreground">{editCategory.updated_by}</span>
                                                            <span className="text-xs text-muted-foreground">{formatDate(editCategory.updated_at)}</span>
                                                        </div>
                                                    ) : (
                                                        <span className="text-sm text-muted-foreground italic mt-0.5">Never updated</span>
                                                    )}
                                                </div>
                                            </>
                                        )}
                                    </div>
                                    <div className="flex items-center gap-3 shrink-0">
                                        <DialogClose asChild>
                                            <button
                                                type="button"
                                                className="inline-flex items-center justify-center h-9 px-4 border border-border-subtle bg-background text-foreground rounded-md text-sm font-medium hover:bg-surface-muted hover:border-border-strong active:bg-surface-muted/80 transition-all shadow-sm"
                                            >
                                                Cancel
                                            </button>
                                        </DialogClose>
                                        <Btn
                                            type="submit"
                                            loading={submitting}
                                            disabled={!isDirty || submitting}
                                            className="h-9 px-4 rounded-md shadow-sm"
                                            icon={<Save className="w-4 h-4" />}
                                        >
                                            {isEdit ? 'Save Changes' : 'Create Category'}
                                        </Btn>
                                    </div>
                                </div>
                            </div>
                        </Tabs>
                    </form>
                    <MediaPicker 
                        open={isMediaPickerOpen} 
                        onOpenChange={setIsMediaPickerOpen} 
                        title="Select Category Hero Image"
                        onSelect={(selected) => {
                            if (selected instanceof File) {
                                setImage(selected);
                                setImageMediaId('');
                                setImagePreview(URL.createObjectURL(selected));
                            } else {
                                setImage(null);
                                setImageMediaId(selected.id.toString());
                                setImagePreview(selected.original_url);
                            }
                            setRemoveImage(false);
                        }} 
                    />
                </DialogContent>
            </Dialog>

            <ConfirmDialog
                open={!!deleteTarget}
                onOpenChange={(open) => { if (!open) setDeleteTarget(null); }}
                title="Delete Category"
                message={deleteTarget ? `Are you sure you want to delete "${deleteTarget.name}"? Posts in this category may become uncategorized.` : ''}
                confirmLabel="Delete"
                variant="danger"
                onConfirm={handleDelete}
            />
            </DashboardLayout>
    );
}
