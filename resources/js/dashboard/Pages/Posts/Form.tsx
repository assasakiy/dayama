import React, { useState, useRef, useMemo, useEffect } from 'react';
import axios from 'axios';
import { Head, router, Link } from '@inertiajs/react';
import DashboardLayout from '@dashboard/Layouts/DashboardLayout';
import TipTapEditor, { TipTapEditorRef } from '@dashboard/Components/TipTapEditor';
import MediaPicker from '@dashboard/Components/MediaPicker';
import { Card, CardHeader, CardTitle, CardContent } from '@dashboard/Components/ui/card';
import { Input } from '@dashboard/Components/ui/input';
import { Textarea } from '@dashboard/Components/ui/textarea';
import { Select, SelectTrigger, SelectValue, SelectContent, SelectItem } from '@dashboard/Components/ui/select';
import {
    Save,
    Eye,
    ArrowLeft,
    Image as ImageIcon,
    Upload,
    X,
    Globe,
    History,
    ChevronDown,
    Search
} from 'lucide-react';
import { Btn } from '@dashboard/Components/ui/btn';

interface Post {
    id: string;
    title: string;
    slug: string;
    content: string;
    excerpt: string | null;
    primary_category_id: string | null;
    categories?: { id: string; name: string }[];
    status: string;
    is_featured: boolean;
    tags: { id: string }[];
    published_at: string | null;
    scheduled_at?: string | null;
    thumbnail_url: string | null;
    seo_title?: string | null;
    seo_description?: string | null;
    meta_keywords?: string[] | null;
    canonical_url?: string | null;
    allow_comments?: boolean;
    is_pinned?: boolean;
}

interface Props {
    post: Post | null;
    categories: { id: string; name: string; parent_name: string | null }[];
    tags: { id: string; name: string }[];
}

export default function PostForm({ post, categories, tags }: Props) {
    const isEditing = !!post;
    const fileInputRef = useRef<HTMLInputElement>(null);
    const editorRef = useRef<TipTapEditorRef>(null);

    const [title, setTitle] = useState(post?.title ?? '');
    const [content, setContent] = useState(post?.content ?? '');
    const [excerpt, setExcerpt] = useState(post?.excerpt ?? '');
    const [primaryCategoryId, setPrimaryCategoryId] = useState(post?.primary_category_id ?? 'none');
    const [selectedCategories, setSelectedCategories] = useState<string[]>(post?.categories?.map((c) => c.id) ?? (post?.primary_category_id ? [post.primary_category_id] : []));
    const [selectedTags, setSelectedTags] = useState<string[]>(post?.tags?.map((t) => t.id) ?? []);
    const [status, setStatus] = useState(post?.status ?? 'draft');
    const [scheduledAt, setScheduledAt] = useState(post?.scheduled_at ? new Date(post.scheduled_at).toISOString().slice(0, 16) : '');
    const [isFeatured, setIsFeatured] = useState(post?.is_featured ?? false);
    const [isPinned, setIsPinned] = useState(post?.is_pinned ?? false);
    const [allowComments, setAllowComments] = useState(post?.allow_comments ?? true);
    const [customizeSeo, setCustomizeSeo] = useState(!!(post?.seo_title || post?.seo_description || post?.meta_keywords?.length || post?.canonical_url));
    const [seoTitle, setSeoTitle] = useState(post?.seo_title ?? '');
    const [seoDescription, setSeoDescription] = useState(post?.seo_description ?? '');
    const [metaKeywords, setMetaKeywords] = useState(post?.meta_keywords?.join(', ') ?? '');
    const [categorySearch, setCategorySearch] = useState('');
    const [tagSearch, setTagSearch] = useState('');
    const [isCategoryDropdownOpen, setIsCategoryDropdownOpen] = useState(false);
    const [isTagDropdownOpen, setIsTagDropdownOpen] = useState(false);
    const [canonicalUrl, setCanonicalUrl] = useState(post?.canonical_url ?? '');
    const [thumbnailPreview, setThumbnailPreview] = useState<string | null>(post?.thumbnail_url ?? null);
    const [thumbnailFile, setThumbnailFile] = useState<File | null>(null);
    const [thumbnailMediaId, setThumbnailMediaId] = useState<number | null>(null);
    const [isMediaPickerOpen, setIsMediaPickerOpen] = useState(false);
    const [isEditorMediaPickerOpen, setIsEditorMediaPickerOpen] = useState(false);
    const [errors, setErrors] = useState<Record<string, string>>({});
    const [submitting, setSubmitting] = useState(false);
    const [isAutosaveEnabled, setIsAutosaveEnabled] = useState(true);
    const [lastAutosavedTime, setLastAutosavedTime] = useState<Date | null>(null);

    const hasChanges = useMemo(() => {
        if (!post) return true;
        return (
            title !== post.title ||
            content !== post.content ||
            excerpt !== (post.excerpt ?? '') ||
            primaryCategoryId !== (post.primary_category_id ?? 'none') ||
            JSON.stringify(selectedCategories.sort()) !== JSON.stringify([...(post.categories?.map(c => c.id) ?? [])].sort()) ||
            status !== post.status ||
            (status === 'scheduled' ? scheduledAt !== (post.scheduled_at ? new Date(post.scheduled_at).toISOString().slice(0, 16) : '') : false) ||
            isFeatured !== post.is_featured ||
            isPinned !== (post.is_pinned ?? false) ||
            allowComments !== (post.allow_comments ?? true) ||
            customizeSeo !== !!(post?.seo_title || post?.seo_description || post?.meta_keywords?.length || post?.canonical_url) ||
            (customizeSeo ? seoTitle !== (post.seo_title ?? '') : false) ||
            (customizeSeo ? seoDescription !== (post.seo_description ?? '') : false) ||
            (customizeSeo ? metaKeywords !== (post.meta_keywords?.join(', ') ?? '') : false) ||
            (customizeSeo ? canonicalUrl !== (post.canonical_url ?? '') : false) ||
            JSON.stringify(selectedTags.sort()) !== JSON.stringify([...(post.tags?.map(t => t.id) ?? [])].sort()) ||
            !!thumbnailFile || !!thumbnailMediaId
        );
    }, [post, title, content, excerpt, primaryCategoryId, selectedCategories, status, scheduledAt, isFeatured, isPinned, allowComments, customizeSeo, seoTitle, seoDescription, metaKeywords, canonicalUrl, selectedTags, thumbnailFile, thumbnailMediaId]);

    useEffect(() => {
        if (selectedCategories.length === 1) {
            setPrimaryCategoryId(selectedCategories[0]);
        } else if (selectedCategories.length === 0) {
            setPrimaryCategoryId('none');
        } else if (primaryCategoryId !== 'none' && !selectedCategories.includes(primaryCategoryId)) {
            setPrimaryCategoryId(selectedCategories[0]);
        }
    }, [selectedCategories, primaryCategoryId]);

    const slug = post?.slug ?? title.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '');

    const generatedExcerpt = content.replace(/<[^>]*>?/gm, '').substring(0, 160) + (content.length > 160 ? '...' : '');
    const generatedTitle = title || 'Post Title';
    const generatedCanonical = `${window.location.origin}/post/${slug || 'your-post-url'}`;
    const generatedMetaKeywords = selectedTags
        .map(tagId => tags.find(t => t.id === tagId)?.name)
        .filter(Boolean)
        .join(', ');

    const handleThumbnailChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        const file = e.target.files?.[0];
        if (file) {
            setThumbnailFile(file);
            setThumbnailPreview(URL.createObjectURL(file));
        }
    };

    const removeThumbnail = () => {
        setThumbnailFile(null);
        setThumbnailMediaId(null);
        setThumbnailPreview(null);
        if (fileInputRef.current) fileInputRef.current.value = '';
    };

    useEffect(() => {
        if (!isEditing || !post || !isAutosaveEnabled) return;

        const timer = setInterval(() => {
            if (hasChanges && title && content) {
                // Background autosave request
                const formData = new FormData();
                formData.append('title', title);
                formData.append('content', content);
                formData.append('excerpt', excerpt);
                formData.append('_method', 'PATCH');
                
                axios.post(`/posts/${post.id}/autosave`, formData)
                    .then(() => setLastAutosavedTime(new Date()))
                    .catch((err: any) => console.error('Autosave failed', err));
            }
        }, 60000); // 60 seconds

        return () => clearInterval(timer);
    }, [isEditing, post, isAutosaveEnabled, hasChanges, title, content, excerpt]);

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        setSubmitting(true);

        const formData = new FormData();
        formData.append('title', title);
        formData.append('content', content);
        formData.append('excerpt', excerpt);
        if (primaryCategoryId !== 'none') {
            formData.append('primary_category_id', primaryCategoryId);
        }
        selectedCategories.forEach((catId) => formData.append('categories[]', catId));
        selectedTags.forEach((tagId) => formData.append('tags[]', tagId));
        formData.append('status', status);
        formData.append('is_featured', isFeatured ? '1' : '0');
        formData.append('is_pinned', isPinned ? '1' : '0');
        formData.append('allow_comments', allowComments ? '1' : '0');
        
        if (status === 'scheduled' && scheduledAt) {
            formData.append('scheduled_at', scheduledAt);
        } else {
            formData.append('scheduled_at', '');
        }
        
        if (customizeSeo) {
            if (seoTitle) formData.append('seo_title', seoTitle);
            if (seoDescription) formData.append('seo_description', seoDescription);
            if (metaKeywords) formData.append('meta_keywords', metaKeywords);
            if (canonicalUrl) formData.append('canonical_url', canonicalUrl);
        } else {
            formData.append('seo_title', '');
            formData.append('seo_description', '');
            formData.append('meta_keywords', '');
            formData.append('canonical_url', '');
        }

        if (thumbnailFile) formData.append('thumbnail', thumbnailFile);
        if (thumbnailMediaId) formData.append('thumbnail_media_id', thumbnailMediaId.toString());

        if (isEditing && post) {
            formData.append('_method', 'PUT');
            router.post(`/posts/${post.id}`, formData, {
                onError: (errs) => { setErrors(errs); setSubmitting(false); },
                onSuccess: () => setSubmitting(false),
                onFinish: () => setSubmitting(false),
            });
        } else {
            router.post('/posts', formData, {
                onError: (errs) => { setErrors(errs); setSubmitting(false); },
                onSuccess: () => setSubmitting(false),
                onFinish: () => setSubmitting(false),
            });
        }
    };

    const toggleTag = (tagId: string) => {
        setSelectedTags((prev) =>
            prev.includes(tagId) ? prev.filter((id) => id !== tagId) : [...prev, tagId],
        );
    };

    const toggleCategory = (catId: string) => {
        setSelectedCategories((prev) =>
            prev.includes(catId) ? prev.filter((id) => id !== catId) : [...prev, catId],
        );
    };

    return (
        <DashboardLayout>
            <Head title={isEditing ? 'Edit Post' : 'New Post'} />
            <div className="space-y-5">
                <div className="flex items-center justify-end md:justify-between w-full">
                    <div className="flex items-center gap-3">
                        <Link
                            href="/posts"
                            className="p-1.5 rounded-md text-muted-foreground hover:text-foreground hover:bg-surface-muted transition-colors"
                        >
                            <ArrowLeft className="w-4 h-4" />
                        </Link>
                        <div className="hidden md:block">
                            <h1 className="text-xl font-semibold tracking-tight">{isEditing ? 'Edit Post' : 'New Post'}</h1>
                            <p className="hidden lg:block text-sm text-muted-foreground mt-0.5">{isEditing ? 'Update your post content' : 'Create a new blog post'}</p>
                        </div>
                    </div>
                    <div className="flex items-center gap-2">
                        {isEditing && (
                            <div className="flex items-center gap-2 mr-2">
                                <label className="flex items-center gap-1.5 cursor-pointer">
                                    <input 
                                        type="checkbox" 
                                        className="rounded border-border-subtle bg-surface text-primary w-3.5 h-3.5"
                                        checked={isAutosaveEnabled}
                                        onChange={(e) => setIsAutosaveEnabled(e.target.checked)}
                                    />
                                    <span className="text-xs text-muted-foreground font-medium">Autosave</span>
                                </label>
                                {lastAutosavedTime && (
                                    <span className="text-[10px] text-muted-foreground/70">
                                        Saved {lastAutosavedTime.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}
                                    </span>
                                )}
                            </div>
                        )}
                        {post?.slug && (
                            <Link
                                href={`/post/${post.slug}`}
                                className="inline-flex items-center gap-1.5 h-8 px-3 text-xs border border-border-subtle rounded-md text-muted-foreground hover:text-foreground hover:bg-surface-muted transition-all"
                            >
                                <Eye className="w-3.5 h-3.5" />
                                Preview
                            </Link>
                        )}
                        {isEditing && post?.id && (
                            <Link
                                href={`/posts/${post.id}/revisions`}
                                className="inline-flex items-center gap-1.5 h-8 px-3 text-xs border border-border-subtle rounded-md text-muted-foreground hover:text-foreground hover:bg-surface-muted transition-all"
                            >
                                <History className="w-3.5 h-3.5" />
                                Revisions
                            </Link>
                        )}
                    </div>
                </div>

                <form onSubmit={submit}>
                    <div className="grid grid-cols-1 lg:grid-cols-3 gap-5">
                        {/* Main content */}
                        <div className="lg:col-span-2 space-y-5">
                            <Card>
                                <CardHeader>
                                    <CardTitle className="flex items-center gap-2 text-sm">
                                        <span className="w-5 h-5 rounded bg-surface-muted flex items-center justify-center">
                                            <ImageIcon className="w-3 h-3 text-muted-foreground" />
                                        </span>
                                        Content
                                    </CardTitle>
                                </CardHeader>
                                <CardContent className="space-y-4">
                                    <Input
                                        label="Title"
                                        value={title}
                                        onChange={(e) => setTitle(e.target.value)}
                                        error={errors.title}
                                        required
                                        placeholder="Enter post title"
                                    />
                                    <div>
                                        <div className="flex items-center justify-between mb-1.5">
                                            <label className="text-sm font-medium block">Excerpt (Optional)</label>
                                            {!excerpt && <span className="text-[10px] uppercase font-bold tracking-wider px-1.5 py-0.5 rounded bg-primary/10 text-primary">Auto Generated</span>}
                                        </div>
                                        <Textarea
                                            value={excerpt}
                                            onChange={(e) => setExcerpt(e.target.value)}
                                            placeholder={generatedExcerpt || "Brief summary for listings..."}
                                            rows={2}
                                        />
                                        <p className="text-xs text-muted-foreground mt-1.5">Leave empty to automatically generate from the article content.</p>
                                    </div>
                                    <div>
                                        <label className="text-sm font-medium mb-1.5 block">Body</label>
                                        <TipTapEditor 
                                            ref={editorRef}
                                            content={content} 
                                            onChange={setContent} 
                                            placeholder="Start writing your post..." 
                                            onRequestImage={() => setIsEditorMediaPickerOpen(true)}
                                        />
                                        {errors.content && <p className="text-xs text-danger mt-1.5">{errors.content}</p>}
                                    </div>
                                </CardContent>
                            </Card>
                        </div>

                        {/* Sidebar */}
                        <div className="space-y-5">
                            <Card>
                                <CardHeader>
                                    <CardTitle className="flex items-center gap-2 text-sm">
                                        <span className="w-5 h-5 rounded bg-surface-muted flex items-center justify-center">
                                            <Save className="w-3 h-3 text-muted-foreground" />
                                        </span>
                                        Publish
                                    </CardTitle>
                                </CardHeader>
                                <CardContent className="space-y-4">
                                    <div className="space-y-1.5">
                                        <label className="text-sm font-medium">Status</label>
                                        <Select value={status} onValueChange={setStatus}>
                                            <SelectTrigger><SelectValue /></SelectTrigger>
                                            <SelectContent>
                                                <SelectItem value="draft">Draft</SelectItem>
                                                <SelectItem value="published">Published</SelectItem>
                                                <SelectItem value="scheduled">Scheduled</SelectItem>
                                            </SelectContent>
                                        </Select>
                                    </div>
                                    {status === 'scheduled' && (
                                        <div className="space-y-1.5 mt-2">
                                            <label className="text-sm font-medium">Schedule Time</label>
                                            <input 
                                                type="datetime-local" 
                                                value={scheduledAt} 
                                                onChange={e => setScheduledAt(e.target.value)}
                                                className="w-full h-9 rounded-md border border-border-subtle bg-background px-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent" 
                                            />
                                        </div>
                                    )}
                                    <label className="flex items-center gap-2.5 text-sm cursor-pointer select-none">
                                        <input
                                            type="checkbox"
                                            checked={isFeatured}
                                            onChange={(e) => setIsFeatured(e.target.checked)}
                                            className="w-4 h-4 rounded border-border-subtle text-primary focus:ring-primary"
                                        />
                                        <span>Featured post</span>
                                    </label>
                                    <label className="flex items-center gap-2.5 text-sm cursor-pointer select-none">
                                        <input
                                            type="checkbox"
                                            checked={isPinned}
                                            onChange={(e) => setIsPinned(e.target.checked)}
                                            className="w-4 h-4 rounded border-border-subtle text-primary focus:ring-primary"
                                        />
                                        <span>Pinned post</span>
                                    </label>
                                    <label className="flex items-center gap-2.5 text-sm cursor-pointer select-none">
                                        <input
                                            type="checkbox"
                                            checked={allowComments}
                                            onChange={(e) => setAllowComments(e.target.checked)}
                                            className="w-4 h-4 rounded border-border-subtle text-primary focus:ring-primary"
                                        />
                                        <span>Allow comments</span>
                                    </label>
                                    {slug && (
                                        <div className="text-xs text-muted-foreground bg-surface-muted rounded-md px-3 py-2 truncate">
                                            /{slug}
                                        </div>
                                    )}
                                    {isEditing && post && (
                                        <Link
                                            href={`/posts/${post.id}/revisions`}
                                            className="inline-flex items-center gap-1.5 text-xs text-muted-foreground hover:text-primary transition-colors"
                                        >
                                            <History className="w-3.5 h-3.5" />
                                            View revision history
                                        </Link>
                                    )}
                                </CardContent>
                            </Card>

                            <Card>
                                <CardHeader>
                                    <CardTitle className="flex items-center gap-2 text-sm">
                                        <span className="w-5 h-5 rounded bg-surface-muted flex items-center justify-center">
                                            <ImageIcon className="w-3 h-3 text-muted-foreground" />
                                        </span>
                                        Featured Image
                                    </CardTitle>
                                </CardHeader>
                                <CardContent>
                                    {thumbnailPreview ? (
                                        <div className="relative rounded-lg overflow-hidden border border-border-subtle group bg-surface-muted/30">
                                            <img src={thumbnailPreview} alt="Thumbnail preview" className="w-full h-40 object-cover" />
                                            <button
                                                type="button"
                                                onClick={removeThumbnail}
                                                className="absolute top-2 right-2 p-1.5 rounded-full bg-background/80 backdrop-blur-sm text-muted-foreground hover:text-foreground opacity-0 group-hover:opacity-100 transition-opacity"
                                            >
                                                <X className="w-4 h-4" />
                                            </button>
                                        </div>
                                    ) : (
                                        <button
                                            type="button"
                                            onClick={() => setIsMediaPickerOpen(true)}
                                            className="w-full h-28 border-2 border-dashed border-border-subtle rounded-lg flex flex-col items-center justify-center gap-2 text-muted-foreground hover:text-foreground hover:border-primary hover:bg-surface-muted/30 transition-all cursor-pointer"
                                        >
                                            <Upload className="w-5 h-5" />
                                            <span className="text-sm font-medium">Select or upload image</span>
                                            <span className="text-xs">PNG, JPG, WebP up to 5MB</span>
                                        </button>
                                    )}
                                    <input
                                        ref={fileInputRef}
                                        type="file"
                                        accept="image/png,image/jpeg,image/webp"
                                        onChange={handleThumbnailChange}
                                        className="hidden"
                                    />
                                    {errors.thumbnail && <p className="text-xs text-danger mt-1.5">{errors.thumbnail}</p>}
                                </CardContent>
                            </Card>

                            <Card>
                                <CardHeader>
                                    <CardTitle className="flex items-center gap-2 text-sm">
                                        <span className="w-5 h-5 rounded bg-surface-muted flex items-center justify-center">
                                            <ImageIcon className="w-3 h-3 text-muted-foreground" />
                                        </span>
                                        Category
                                    </CardTitle>
                                </CardHeader>
                                <CardContent>
                                    <div className="space-y-4">
                                        <div className="relative">
                                            <div 
                                                className="w-full min-h-9 px-3 py-1.5 border border-border-subtle rounded-md bg-background flex items-center justify-between cursor-pointer hover:border-primary transition-colors text-sm"
                                                onClick={() => setIsCategoryDropdownOpen(!isCategoryDropdownOpen)}
                                            >
                                                <span className={selectedCategories.length === 0 ? "text-muted-foreground" : "text-foreground"}>
                                                    {selectedCategories.length === 0 ? "Select categories..." : `${selectedCategories.length} selected`}
                                                </span>
                                                <ChevronDown className="w-4 h-4 text-muted-foreground" />
                                            </div>

                                            {isCategoryDropdownOpen && (
                                                <>
                                                    <div className="fixed inset-0 z-40" onClick={() => setIsCategoryDropdownOpen(false)} />
                                                    <div className="absolute top-full left-0 right-0 mt-1 bg-background border border-border-subtle rounded-md shadow-lg z-50 overflow-hidden">
                                                        <div className="p-2 border-b border-border-subtle">
                                                            <div className="relative">
                                                                <Search className="w-4 h-4 absolute left-2 top-1/2 -translate-y-1/2 text-muted-foreground" />
                                                                <input
                                                                    type="text"
                                                                    placeholder="Search categories..."
                                                                    value={categorySearch}
                                                                    onChange={(e) => setCategorySearch(e.target.value)}
                                                                    className="w-full pl-8 pr-3 py-1.5 text-xs bg-surface-muted rounded border-transparent focus:bg-background focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-all"
                                                                    autoFocus
                                                                />
                                                            </div>
                                                        </div>
                                                        <div className="max-h-48 overflow-y-auto p-1.5 space-y-0.5">
                                                            {categories.length === 0 ? (
                                                                <p className="text-xs text-muted-foreground p-2 text-center">No categories available</p>
                                                            ) : (
                                                                categories
                                                                    .filter(c => c.name.toLowerCase().includes(categorySearch.toLowerCase()) || c.parent_name?.toLowerCase().includes(categorySearch.toLowerCase()))
                                                                    .map((c) => (
                                                                        <label key={c.id} className="flex items-center gap-2.5 text-sm cursor-pointer hover:bg-surface-muted/50 p-2 rounded transition-colors group">
                                                                            <input
                                                                                type="checkbox"
                                                                                checked={selectedCategories.includes(c.id)}
                                                                                onChange={() => toggleCategory(c.id)}
                                                                                className="w-4 h-4 rounded border-border-subtle text-primary focus:ring-primary shrink-0"
                                                                            />
                                                                            <div className="flex flex-col">
                                                                                <span className="font-medium">{c.name}</span>
                                                                                {c.parent_name && (
                                                                                    <span className="text-[10px] text-muted-foreground">{c.parent_name} &gt; {c.name}</span>
                                                                                )}
                                                                            </div>
                                                                        </label>
                                                                    ))
                                                            )}
                                                        </div>
                                                    </div>
                                                </>
                                            )}
                                        </div>

                                        {selectedCategories.length > 0 && (
                                            <div className="flex flex-wrap gap-1.5">
                                                {selectedCategories.map(catId => {
                                                    const c = categories.find(cat => cat.id === catId);
                                                    if (!c) return null;
                                                    return (
                                                        <div key={c.id} className="inline-flex items-center gap-1.5 px-2 py-1 bg-surface-muted border border-border-subtle rounded-md text-xs">
                                                            <span>{c.name}</span>
                                                            <button 
                                                                type="button" 
                                                                onClick={() => toggleCategory(c.id)}
                                                                className="text-muted-foreground hover:text-danger rounded-full p-0.5"
                                                            >
                                                                <X className="w-3 h-3" />
                                                            </button>
                                                        </div>
                                                    );
                                                })}
                                            </div>
                                        )}

                                        {selectedCategories.length > 1 && (
                                            <>
                                                <div className="h-px bg-border my-3" />
                                                <div className="space-y-2">
                                                    <p className="text-xs font-semibold text-muted-foreground">Primary Category</p>
                                                    <Select value={primaryCategoryId} onValueChange={setPrimaryCategoryId}>
                                                        <SelectTrigger className="h-9 text-sm">
                                                            <SelectValue placeholder="Select primary category" />
                                                        </SelectTrigger>
                                                        <SelectContent>
                                                            {categories
                                                                .filter(c => selectedCategories.includes(c.id))
                                                                .map(c => (
                                                                    <SelectItem key={`primary-${c.id}`} value={c.id}>
                                                                        {c.name}
                                                                    </SelectItem>
                                                                ))}
                                                        </SelectContent>
                                                    </Select>
                                                </div>
                                            </>
                                        )}
                                        {selectedCategories.length === 1 && (
                                            <>
                                                <div className="h-px bg-border my-3" />
                                                <div className="space-y-2">
                                                    <p className="text-xs font-semibold text-muted-foreground">Primary Category</p>
                                                    <p className="text-sm flex items-center gap-1.5 text-success p-1.5 bg-success/5 rounded-md border border-success/10">
                                                        <span className="w-4 h-4 rounded-full bg-success/20 flex items-center justify-center text-success text-[10px]">✓</span>
                                                        {categories.find(c => c.id === selectedCategories[0])?.name} <span className="text-xs text-muted-foreground ml-1">(Auto)</span>
                                                    </p>
                                                </div>
                                            </>
                                        )}
                                    </div>
                                </CardContent>
                            </Card>

                            <Card>
                                <CardHeader>
                                    <CardTitle className="flex items-center gap-2 text-sm">
                                        <span className="w-5 h-5 rounded bg-surface-muted flex items-center justify-center">
                                            <ImageIcon className="w-3 h-3 text-muted-foreground" />
                                        </span>
                                        Tags
                                    </CardTitle>
                                </CardHeader>
                                <CardContent>
                                    <div className="space-y-4">
                                        <div className="relative">
                                            <div 
                                                className="w-full min-h-9 px-3 py-1.5 border border-border-subtle rounded-md bg-background flex items-center justify-between cursor-pointer hover:border-primary transition-colors text-sm"
                                                onClick={() => setIsTagDropdownOpen(!isTagDropdownOpen)}
                                            >
                                                <span className="text-muted-foreground">
                                                    Search {tags.length} tags...
                                                </span>
                                                <ChevronDown className="w-4 h-4 text-muted-foreground" />
                                            </div>

                                            {isTagDropdownOpen && (
                                                <>
                                                    <div className="fixed inset-0 z-40" onClick={() => setIsTagDropdownOpen(false)} />
                                                    <div className="absolute top-full left-0 right-0 mt-1 bg-background border border-border-subtle rounded-md shadow-lg z-50 overflow-hidden">
                                                        <div className="p-2 border-b border-border-subtle">
                                                            <div className="relative">
                                                                <Search className="w-4 h-4 absolute left-2 top-1/2 -translate-y-1/2 text-muted-foreground" />
                                                                <input
                                                                    type="text"
                                                                    placeholder="Type to search..."
                                                                    value={tagSearch}
                                                                    onChange={(e) => setTagSearch(e.target.value)}
                                                                    className="w-full pl-8 pr-3 py-1.5 text-xs bg-surface-muted rounded border-transparent focus:bg-background focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-all"
                                                                    autoFocus
                                                                />
                                                            </div>
                                                        </div>
                                                        <div className="max-h-48 overflow-y-auto p-1.5 space-y-0.5">
                                                            {tags.length === 0 ? (
                                                                <p className="text-xs text-muted-foreground p-2 text-center">No tags available</p>
                                                            ) : (
                                                                tags
                                                                    .filter(t => t.name.toLowerCase().includes(tagSearch.toLowerCase()))
                                                                    .map((t) => (
                                                                        <label key={t.id} className="flex items-center gap-2.5 text-sm cursor-pointer hover:bg-surface-muted/50 p-2 rounded transition-colors group">
                                                                            <input
                                                                                type="checkbox"
                                                                                checked={selectedTags.includes(t.id)}
                                                                                onChange={() => toggleTag(t.id)}
                                                                                className="w-4 h-4 rounded border-border-subtle text-primary focus:ring-primary shrink-0"
                                                                            />
                                                                            <span className="font-medium">{t.name}</span>
                                                                        </label>
                                                                    ))
                                                            )}
                                                            {tagSearch && tags.filter(t => t.name.toLowerCase().includes(tagSearch.toLowerCase())).length === 0 && (
                                                                <p className="text-xs text-muted-foreground p-2 text-center">No matching tags found</p>
                                                            )}
                                                        </div>
                                                    </div>
                                                </>
                                            )}
                                        </div>

                                        {selectedTags.length > 0 && (
                                            <div className="flex flex-wrap gap-1.5">
                                                {selectedTags.map((tagId) => {
                                                    const tag = tags.find(t => t.id === tagId);
                                                    if (!tag) return null;
                                                    return (
                                                        <div key={tag.id} className="inline-flex items-center gap-1.5 px-2 py-1 bg-primary/10 text-primary border border-primary/20 rounded-md text-xs">
                                                            <span className="font-medium">{tag.name}</span>
                                                            <button 
                                                                type="button" 
                                                                onClick={() => toggleTag(tag.id)}
                                                                className="text-primary hover:text-primary-foreground hover:bg-primary rounded-full p-0.5 transition-colors"
                                                            >
                                                                <X className="w-3 h-3" />
                                                            </button>
                                                        </div>
                                                    );
                                                })}
                                            </div>
                                        )}
                                    </div>
                                </CardContent>
                            </Card>

                            <Card>
                                <CardHeader>
                                    <CardTitle className="flex items-center gap-2 text-sm">
                                        <span className="w-5 h-5 rounded bg-surface-muted flex items-center justify-center">
                                            <Globe className="w-3 h-3 text-muted-foreground" />
                                        </span>
                                        SEO Settings
                                    </CardTitle>
                                </CardHeader>
                                <CardContent className="space-y-4">
                                    <label className="flex items-center gap-2.5 text-sm cursor-pointer select-none">
                                        <input
                                            type="checkbox"
                                            checked={customizeSeo}
                                            onChange={(e) => setCustomizeSeo(e.target.checked)}
                                            className="w-4 h-4 rounded border-border-subtle text-primary focus:ring-primary"
                                        />
                                        <span className="font-medium">Customize SEO Metadata</span>
                                    </label>
                                    
                                    {!customizeSeo && (
                                        <p className="text-xs text-muted-foreground">
                                            Automatic SEO generation is active. We will use your title, excerpt, and current URL.
                                        </p>
                                    )}

                                    {customizeSeo && (
                                        <div className="space-y-4 pt-2 border-t border-border-subtle">
                                            <div>
                                                <div className="flex items-center justify-between mb-1.5">
                                                    <label className="text-sm font-medium block">SEO Title (Optional)</label>
                                                    {!seoTitle && <span className="text-[10px] uppercase font-bold tracking-wider px-1.5 py-0.5 rounded bg-primary/10 text-primary">Auto Generated</span>}
                                                </div>
                                                <Input
                                                    value={seoTitle}
                                                    onChange={(e) => setSeoTitle(e.target.value)}
                                                    error={errors.seo_title}
                                                    placeholder={generatedTitle}
                                                />
                                                <p className="text-xs text-muted-foreground mt-1.5">Leave empty to use the post title.</p>
                                            </div>
                                            <div>
                                                <div className="flex items-center justify-between mb-1.5">
                                                    <label className="text-sm font-medium block">SEO Description (Optional)</label>
                                                    {!seoDescription && <span className="text-[10px] uppercase font-bold tracking-wider px-1.5 py-0.5 rounded bg-primary/10 text-primary">Auto Generated</span>}
                                                </div>
                                                <Textarea
                                                    value={seoDescription}
                                                    onChange={(e) => setSeoDescription(e.target.value)}
                                                    placeholder={excerpt || generatedExcerpt || "Brief description for search results"}
                                                    rows={2}
                                                />
                                                <p className="text-xs text-muted-foreground mt-1.5">Leave empty to automatically generate from the article excerpt.</p>
                                            </div>
                                            <div>
                                                <div className="flex items-center justify-between mb-1.5">
                                                    <label className="text-sm font-medium block">Meta Keywords (Optional)</label>
                                                    {!metaKeywords && selectedTags.length > 0 && <span className="text-[10px] uppercase font-bold tracking-wider px-1.5 py-0.5 rounded bg-primary/10 text-primary">Auto Generated</span>}
                                                </div>
                                                <Input
                                                    value={metaKeywords}
                                                    onChange={(e) => setMetaKeywords(e.target.value)}
                                                    error={errors.meta_keywords}
                                                    placeholder={generatedMetaKeywords || "laravel, php, tutorial"}
                                                />
                                                <p className="text-xs text-muted-foreground mt-1.5">Optional. Search engines generally ignore this field.</p>
                                            </div>
                                            <div>
                                                <div className="flex items-center justify-between mb-1.5">
                                                    <label className="text-sm font-medium block">Canonical URL (Optional)</label>
                                                    {!canonicalUrl && <span className="text-[10px] uppercase font-bold tracking-wider px-1.5 py-0.5 rounded bg-primary/10 text-primary">Auto Generated</span>}
                                                </div>
                                                <Input
                                                    value={canonicalUrl}
                                                    onChange={(e) => setCanonicalUrl(e.target.value)}
                                                    error={errors.canonical_url}
                                                    placeholder={generatedCanonical}
                                                />
                                                <p className="text-xs text-muted-foreground mt-1.5">Leave empty to use this post URL automatically.</p>
                                            </div>
                                        </div>
                                    )}
                                </CardContent>
                            </Card>

                            <div className="flex items-center gap-2">
                                <button
                                    type="button"
                                    onClick={() => window.history.back()}
                                    className="ml-auto inline-flex items-center justify-center flex-1 h-9 px-4 border border-border-strong bg-background text-foreground rounded-md text-sm font-medium hover:bg-surface-muted transition-all shadow-sm"
                                >
                                    Cancel
                                </button>
                                <Btn
                                    type="submit"
                                    loading={submitting}
                                    disabled={submitting || (isEditing && !hasChanges)}
                                    className="flex-1 h-9 px-4"
                                    icon={<Save className="w-4 h-4" />}
                                >
                                    {isEditing ? 'Update Post' : 'Publish Post'}
                                </Btn>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <MediaPicker 
                open={isMediaPickerOpen} 
                onOpenChange={setIsMediaPickerOpen} 
                title="Select Post Thumbnail"
                onSelect={(selected) => {
                    if (selected instanceof File) {
                        setThumbnailFile(selected);
                        setThumbnailMediaId(null);
                        setThumbnailPreview(URL.createObjectURL(selected));
                    } else {
                        setThumbnailFile(null);
                        setThumbnailMediaId(selected.id);
                        setThumbnailPreview(selected.original_url);
                    }
                }} 
            />
            <MediaPicker 
                open={isEditorMediaPickerOpen} 
                onOpenChange={setIsEditorMediaPickerOpen} 
                title="Select Image for Post Content"
                onSelect={(selected) => {
                    const url = selected instanceof File ? URL.createObjectURL(selected) : selected.original_url;
                    editorRef.current?.insertImage(url);
                }} 
            />
        </DashboardLayout>
    );
}
