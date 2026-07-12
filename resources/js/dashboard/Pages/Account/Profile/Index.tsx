import React, { useState, useRef } from 'react';
import { useForm, usePage, router } from '@inertiajs/react';
import AccountSettingsLayout from '../../../Layouts/AccountSettingsLayout';
import { Card, CardHeader, CardTitle, CardContent } from '../../../Components/ui/card';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '../../../Components/ui/dropdown-menu';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogFooter } from '../../../Components/ui/dialog';
import ConfirmDialog from '../../../Components/ui/confirm-dialog';
import { Mail, Globe, ShieldCheck, Pencil, X, Check, ExternalLink, FileText, BadgeCheck, ChevronDown, User as UserIcon, Settings, Camera, Upload, Trash, Save, Eye } from 'lucide-react';
import { Btn } from '../../../Components/ui/btn';
import { BottomSheet } from '../../../Components/ui/bottom-sheet';
import MediaViewer from '../../../Components/MediaViewer';

export default function ProfileIndex() {
    const { auth } = usePage().props as any;
    const user = auth.user;
    
    const [editingSection, setEditingSection] = useState<'personal' | 'social' | null>(null);
    const [isAvatarMenuOpen, setIsAvatarMenuOpen] = useState(false);
    const [isBannerMenuOpen, setIsBannerMenuOpen] = useState(false);
    const [isDeleteAvatarConfirmOpen, setIsDeleteAvatarConfirmOpen] = useState(false);
    const [isDeleteBannerConfirmOpen, setIsDeleteBannerConfirmOpen] = useState(false);
    
    const [viewingMedia, setViewingMedia] = useState<any>(null);

    const avatarInputRef = useRef<HTMLInputElement>(null);
    const bannerInputRef = useRef<HTMLInputElement>(null);

    const { data, setData, post, processing, errors, recentlySuccessful, reset, transform, isDirty } = useForm({
        name: user.name || '',
        username: user.username || '',
        biography: user.biography || '',
        website: user.website || '',
        social_links: user.social_links || { github: '', twitter: '', linkedin: '' },
        avatar: null as File | null,
        banner: null as File | null,
        _method: 'put',
    });

    const [avatarPreview, setAvatarPreview] = useState<string | null>(null);
    const [bannerPreview, setBannerPreview] = useState<string | null>(null);

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        // Since we are sending files, Inertia will convert to FormData.
        // Nested objects like social_links won't be serialized correctly unless we stringify them or handle them.
        // We will transform the data just before sending:
        transform((data) => ({
            ...data,
            // Only send avatar/banner if they exist to avoid overwriting or validation issues
            avatar: data.avatar || '',
            banner: data.banner || '',
            // Stringify the social links so backend can decode if necessary, 
            // OR send them individually
            'social_links[github]': data.social_links.github,
            'social_links[twitter]': data.social_links.twitter,
            'social_links[linkedin]': data.social_links.linkedin,
        }));
        
        post('/dashboard/account/profile', { 
            preserveScroll: true,
            onSuccess: () => {
                setEditingSection(null);
            }
        });
    };

    const handleAvatarChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        if (e.target.files && e.target.files[0]) {
            const file = e.target.files[0];
            router.post('/dashboard/account/profile', {
                ...data,
                avatar: file,
                _method: 'put'
            }, { preserveScroll: true });
            setIsAvatarMenuOpen(false);
        }
    };

    const handleBannerChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        if (e.target.files && e.target.files[0]) {
            const file = e.target.files[0];
            router.post('/dashboard/account/profile', {
                ...data,
                banner: file,
                _method: 'put'
            }, { preserveScroll: true });
            setIsBannerMenuOpen(false);
        }
    };

    const cancelEdit = () => {
        reset();
        setEditingSection(null);
    };

    const confirmDeleteAvatar = () => {
        router.post('/dashboard/account/profile', {
            ...data,
            delete_avatar: true,
            avatar: null,
            _method: 'put'
        }, {
            preserveScroll: true,
            onSuccess: () => {
                setIsDeleteAvatarConfirmOpen(false);
            },
        });
    };

    const confirmDeleteBanner = () => {
        router.post('/dashboard/account/profile', {
            ...data,
            delete_banner: true,
            banner: null,
            _method: 'put'
        }, {
            preserveScroll: true,
            onSuccess: () => {
                setIsDeleteBannerConfirmOpen(false);
            },
        });
    };

    const avatarInitial = user.name?.charAt(0).toUpperCase() || 'U';
    const joinedDate = new Date(user.created_at).toLocaleDateString('en-US', {
        year: 'numeric', month: 'long', day: 'numeric',
    });

    const socialLinks = [
        { key: 'github', href: user.social_links?.github, label: 'GitHub' },
        { key: 'twitter', href: user.social_links?.twitter, label: 'Twitter' },
        { key: 'linkedin', href: user.social_links?.linkedin, label: 'LinkedIn' },
    ];

    return (
        <AccountSettingsLayout 
            title="Profile" 
            description="Manage your public profile and how you appear to others."
        >
            {/* Facebook-style Cover & Avatar Header */}
            <div className="bg-background border border-border-subtle rounded-xl overflow-hidden mb-6 shadow-sm">
                {/* Cover Photo Area */}
                <DropdownMenu>
                    <DropdownMenuTrigger asChild>
                        <div 
                            className="hidden md:block h-48 w-full bg-gradient-to-r from-primary/80 via-primary/60 to-primary/40 relative cursor-pointer group bg-cover bg-center"
                            style={bannerPreview ? { backgroundImage: `url(${bannerPreview})` } : user.banner_url ? { backgroundImage: `url(${user.banner_url})` } : {}}
                        >
                            <div className="absolute inset-0 bg-black/20 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                <Camera className="w-8 h-8 text-white drop-shadow-md" />
                            </div>
                        </div>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent align="end">
                        <DropdownMenuItem onSelect={(e) => { e.preventDefault(); user.banner_media ? setViewingMedia(user.banner_media) : null; }}>
                            <Eye className="w-4 h-4 mr-2" /> View Image
                        </DropdownMenuItem>
                        <DropdownMenuItem onSelect={(e) => { e.preventDefault(); bannerInputRef.current?.click(); }}>
                            <Upload className="w-4 h-4 mr-2" /> Upload Image
                        </DropdownMenuItem>
                        {user.banner_url && (
                            <DropdownMenuItem className="text-destructive focus:bg-destructive/10 focus:text-destructive" onClick={() => setIsDeleteBannerConfirmOpen(true)}>
                                <Trash className="w-4 h-4 mr-2" /> Remove Image
                            </DropdownMenuItem>
                        )}
                    </DropdownMenuContent>
                </DropdownMenu>

                {/* Mobile Cover Trigger */}
                <div 
                    className="md:hidden h-48 w-full bg-gradient-to-r from-primary/80 via-primary/60 to-primary/40 relative cursor-pointer group bg-cover bg-center"
                    style={bannerPreview ? { backgroundImage: `url(${bannerPreview})` } : user.banner_url ? { backgroundImage: `url(${user.banner_url})` } : {}}
                    onClick={() => setIsBannerMenuOpen(true)}
                >
                    <div className="absolute inset-0 bg-black/20 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                        <Camera className="w-8 h-8 text-white drop-shadow-md" />
                    </div>
                </div>
                
                {/* Avatar and Info */}
                <div className="px-6 pb-8">
                    <div className="flex flex-col items-center text-center -mt-16 sm:-mt-20 mb-2 gap-3 z-10 relative">
                        <div className="mx-auto z-10 relative">
                            {/* Desktop Avatar Trigger */}
                            <DropdownMenu>
                                <DropdownMenuTrigger asChild>
                                    <div className="hidden md:block w-32 h-32 rounded-full bg-background p-1.5 shrink-0 cursor-pointer group">
                                        <div className="w-full h-full rounded-full bg-gradient-to-br from-primary to-primary/80 flex items-center justify-center text-primary-foreground text-4xl font-bold overflow-hidden shadow-inner relative bg-cover bg-center"
                                            style={avatarPreview ? { backgroundImage: `url(${avatarPreview})` } : user.avatar_url ? { backgroundImage: `url(${user.avatar_url})` } : {}}
                                        >
                                            {!avatarPreview && !user.avatar_url && avatarInitial}
                                            <div className="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                                <Camera className="w-8 h-8 text-white drop-shadow-md" />
                                            </div>
                                        </div>
                                    </div>
                                </DropdownMenuTrigger>
                                <DropdownMenuContent align="center">
                                    <DropdownMenuItem onSelect={(e) => { e.preventDefault(); user.avatar_media ? setViewingMedia(user.avatar_media) : null; }}>
                                        <Eye className="w-4 h-4 mr-2" /> View Image
                                    </DropdownMenuItem>
                                    <DropdownMenuItem onSelect={(e) => { e.preventDefault(); avatarInputRef.current?.click(); }}>
                                        <Upload className="w-4 h-4 mr-2" /> Upload Image
                                    </DropdownMenuItem>
                                    {user.avatar_url && (
                                        <DropdownMenuItem className="text-destructive focus:bg-destructive/10 focus:text-destructive" onClick={() => setIsDeleteAvatarConfirmOpen(true)}>
                                            <Trash className="w-4 h-4 mr-2" /> Remove Image
                                        </DropdownMenuItem>
                                    )}
                                </DropdownMenuContent>
                            </DropdownMenu>

                            {/* Mobile Avatar Trigger */}
                            <div 
                                className="md:hidden block w-32 h-32 rounded-full bg-background p-1.5 shrink-0 cursor-pointer group"
                                onClick={() => setIsAvatarMenuOpen(true)}
                            >
                                <div className="w-full h-full rounded-full bg-gradient-to-br from-primary to-primary/80 flex items-center justify-center text-primary-foreground text-4xl font-bold overflow-hidden shadow-inner relative bg-cover bg-center"
                                    style={avatarPreview ? { backgroundImage: `url(${avatarPreview})` } : user.avatar_url ? { backgroundImage: `url(${user.avatar_url})` } : {}}
                                >
                                    {!avatarPreview && !user.avatar_url && avatarInitial}
                                    <div className="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                        <Camera className="w-8 h-8 text-white drop-shadow-md" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div className="mt-1 space-y-1">
                            <h1 className="text-2xl font-bold tracking-tight flex items-center justify-center gap-2">
                                {user.name}
                                {user.is_verified && <BadgeCheck className="w-6 h-6 text-primary fill-primary/10" />}
                            </h1>
                            <p className="text-muted-foreground font-medium">{user.email}</p>
                            <p className="text-muted-foreground/80 text-sm">@{user.username || 'username'}</p>
                        </div>
                    </div>

                    <div className="flex flex-wrap items-center justify-center gap-2 mt-4">
                        {user.roles?.map((role: any) => {
                            const roleName = typeof role === 'string' ? role : role.name;
                            const roleKey = typeof role === 'string' ? role : role.id;
                            return (
                                <span
                                    key={roleKey}
                                    className="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-primary/10 text-primary border border-primary/20"
                                >
                                    <ShieldCheck className="w-3.5 h-3.5" />
                                    {roleName}
                                </span>
                            );
                        })}
                    </div>
                </div>
            </div>

            {/* Content Area */}
            <div className="flex flex-col space-y-5 w-full">
                
                {/* About Section */}
                {(!editingSection || editingSection === 'personal') && (
                    editingSection === 'personal' ? (
                        <Card id="edit-basic-info" className="border-primary/20 shadow-sm scroll-mt-24">
                            <CardHeader className="pb-3 border-b border-border-subtle bg-surface-muted/30">
                                <CardTitle className="text-sm font-semibold text-primary">Edit Basic Information</CardTitle>
                            </CardHeader>
                            <CardContent className="pt-6 space-y-5">
                                <div className="grid grid-cols-1 md:grid-cols-2 gap-5">
                                    <div className="space-y-2.5">
                                        <label className="text-sm font-medium">Full Name</label>
                                        <input
                                            type="text"
                                            value={data.name}
                                            onChange={e => setData('name', e.target.value)}
                                            className="w-full px-3 py-2.5 border border-border rounded-lg bg-background focus:ring-2 focus:ring-primary/20 focus:border-primary text-sm transition-all"
                                        />
                                        {errors.name && <p className="text-sm text-destructive">{errors.name}</p>}
                                    </div>
                                    <div className="space-y-2.5">
                                        <label className="text-sm font-medium">Username</label>
                                        <input
                                            type="text"
                                            value={data.username}
                                            onChange={e => setData('username', e.target.value)}
                                            className="w-full px-3 py-2.5 border border-border rounded-lg bg-background focus:ring-2 focus:ring-primary/20 focus:border-primary text-sm transition-all"
                                        />
                                        {errors.username && <p className="text-sm text-destructive">{errors.username}</p>}
                                    </div>
                                </div>
                                <div className="space-y-2.5">
                                    <label className="text-sm font-medium">Biography</label>
                                    <textarea
                                        value={data.biography}
                                        onChange={e => setData('biography', e.target.value)}
                                        rows={4}
                                        placeholder="Write a few sentences about yourself."
                                        className="w-full px-3 py-2.5 border border-border rounded-lg bg-background focus:ring-2 focus:ring-primary/20 focus:border-primary text-sm resize-none transition-all"
                                    />
                                    {errors.biography && <p className="text-sm text-destructive">{errors.biography}</p>}
                                </div>
                            </CardContent>
                            <div className="px-6 py-4 border-t border-border-subtle flex justify-end gap-3 bg-surface-muted/10 rounded-b-lg">
                                <button 
                                    type="button"
                                    onClick={cancelEdit}
                                    className="inline-flex items-center gap-2 px-4 py-2 bg-surface-muted text-foreground border border-border rounded-md text-sm font-medium hover:bg-border/50 transition-colors"
                                >
                                    <X className="w-4 h-4" />
                                    Cancel
                                </button>
                                <Btn 
                                    type="button"
                                    onClick={submit}
                                    loading={processing}
                                    disabled={!isDirty || processing}
                                    icon={<Save className="w-4 h-4" />}
                                >
                                    Save Changes
                                </Btn>
                            </div>
                        </Card>
                    ) : (
                        <Card>
                            <CardHeader className="pb-3 border-b border-border-subtle flex flex-row items-center justify-between space-y-0">
                                <CardTitle className="text-sm font-semibold">About</CardTitle>
                                <button 
                                    onClick={() => setEditingSection('personal')}
                                    className="w-8 h-8 rounded-full bg-surface-muted flex items-center justify-center text-muted-foreground hover:bg-primary hover:text-primary-foreground transition-colors"
                                    title="Edit Personal Info"
                                >
                                    <Pencil className="w-3.5 h-3.5" />
                                </button>
                            </CardHeader>
                            <CardContent className="pt-4 space-y-4">
                                <div className="space-y-3 pb-4 border-b border-border-subtle">
                                    <div className="grid grid-cols-[100px_1fr] sm:grid-cols-[120px_1fr] gap-2 items-start">
                                        <span className="text-sm text-muted-foreground font-medium">Full Name</span>
                                        <span className="text-sm font-medium text-foreground">{user.name}</span>
                                    </div>
                                    <div className="grid grid-cols-[100px_1fr] sm:grid-cols-[120px_1fr] gap-2 items-start">
                                        <span className="text-sm text-muted-foreground font-medium">Username</span>
                                        <span className="text-sm font-medium text-foreground">@{user.username || 'username'}</span>
                                    </div>
                                    <div className="grid grid-cols-[100px_1fr] sm:grid-cols-[120px_1fr] gap-2 items-start">
                                        <span className="text-sm text-muted-foreground font-medium">Email</span>
                                        <span className="text-sm font-medium text-foreground">{user.email}</span>
                                    </div>
                                    <div className="grid grid-cols-[100px_1fr] sm:grid-cols-[120px_1fr] gap-2 items-start">
                                        <span className="text-sm text-muted-foreground font-medium">Role</span>
                                        <div className="flex flex-wrap gap-1.5">
                                            {user.roles?.map((role: any) => {
                                                const roleName = typeof role === 'string' ? role : role.name;
                                                const roleKey = typeof role === 'string' ? role : role.id;
                                                return (
                                                    <span key={roleKey} className="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] sm:text-xs font-semibold bg-primary/10 text-primary border border-primary/20">
                                                        {roleName}
                                                    </span>
                                                )
                                            })}
                                        </div>
                                    </div>
                                </div>
                                
                                <div>
                                    <h4 className="text-xs font-semibold text-muted-foreground uppercase tracking-wider mb-2">Biography</h4>
                                    {user.biography ? (
                                        <p className="text-sm leading-relaxed text-foreground">{user.biography}</p>
                                    ) : (
                                        <p className="text-sm text-muted-foreground italic">No biography provided.</p>
                                    )}
                                </div>
                                <div className="pt-4 border-t border-border-subtle grid grid-cols-2 gap-3">
                                    <div className="text-center">
                                        <FileText className="w-5 h-5 text-primary mx-auto mb-1.5" />
                                        <p className="text-lg font-semibold">{user.posts_count || 0}</p>
                                        <p className="text-xs text-muted-foreground">Posts</p>
                                    </div>
                                    <div className="text-center">
                                        <BadgeCheck className={`w-5 h-5 mx-auto mb-1.5 ${user.email_verified_at ? 'text-primary' : 'text-muted-foreground/40'}`} />
                                        <p className="text-lg font-semibold">{user.email_verified_at ? 'Yes' : 'No'}</p>
                                        <p className="text-xs text-muted-foreground">Verified</p>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>
                    )
                )}

                {/* Online Presence Section */}
                {(!editingSection || editingSection === 'social') && (
                    editingSection === 'social' ? (
                        <Card id="edit-online-presence" className="border-primary/20 shadow-sm scroll-mt-24">
                            <CardHeader className="pb-3 border-b border-border-subtle bg-surface-muted/30">
                                <CardTitle className="flex items-center gap-2 text-sm font-semibold text-primary">
                                    <Globe className="w-4 h-4" />
                                    Edit Online Presence
                                </CardTitle>
                            </CardHeader>
                            <CardContent className="pt-6 space-y-5">
                                <div className="space-y-2.5">
                                    <label className="text-sm font-medium">Personal Website</label>
                                    <input
                                        type="url"
                                        value={data.website}
                                        onChange={e => setData('website', e.target.value)}
                                        placeholder="https://example.com"
                                        className="w-full px-3 py-2.5 border border-border rounded-lg bg-background focus:ring-2 focus:ring-primary/20 focus:border-primary text-sm transition-all"
                                    />
                                    {errors.website && <p className="text-sm text-destructive">{errors.website}</p>}
                                </div>
                                <div className="space-y-2.5">
                                    <label className="text-sm font-medium">GitHub URL</label>
                                    <input
                                        type="url"
                                        value={data.social_links.github}
                                        onChange={e => setData('social_links', { ...data.social_links, github: e.target.value })}
                                        placeholder="https://github.com/username"
                                        className="w-full px-3 py-2.5 border border-border rounded-lg bg-background focus:ring-2 focus:ring-primary/20 focus:border-primary text-sm transition-all"
                                    />
                                </div>
                                <div className="space-y-2.5">
                                    <label className="text-sm font-medium">X (Twitter) URL</label>
                                    <input
                                        type="url"
                                        value={data.social_links.twitter}
                                        onChange={e => setData('social_links', { ...data.social_links, twitter: e.target.value })}
                                        placeholder="https://twitter.com/username"
                                        className="w-full px-3 py-2.5 border border-border rounded-lg bg-background focus:ring-2 focus:ring-primary/20 focus:border-primary text-sm transition-all"
                                    />
                                </div>
                                <div className="space-y-2.5">
                                    <label className="text-sm font-medium">LinkedIn URL</label>
                                    <input
                                        type="url"
                                        value={data.social_links.linkedin}
                                        onChange={e => setData('social_links', { ...data.social_links, linkedin: e.target.value })}
                                        placeholder="https://linkedin.com/in/username"
                                        className="w-full px-3 py-2.5 border border-border rounded-lg bg-background focus:ring-2 focus:ring-primary/20 focus:border-primary text-sm transition-all"
                                    />
                                </div>
                            </CardContent>
                            <div className="px-6 py-4 border-t border-border-subtle flex justify-end gap-3 bg-surface-muted/10 rounded-b-lg">
                                <button 
                                    type="button"
                                    onClick={cancelEdit}
                                    className="inline-flex items-center gap-2 px-4 py-2 bg-surface-muted text-foreground border border-border rounded-md text-sm font-medium hover:bg-border/50 transition-colors"
                                >
                                    <X className="w-4 h-4" />
                                    Cancel
                                </button>
                                <Btn 
                                    type="button"
                                    onClick={submit}
                                    loading={processing}
                                    disabled={!isDirty || processing}
                                    icon={<Save className="w-4 h-4" />}
                                >
                                    Save Changes
                                </Btn>
                            </div>
                        </Card>
                    ) : (
                        <Card>
                            <CardHeader className="pb-3 border-b border-border-subtle flex flex-row items-center justify-between space-y-0">
                                <CardTitle className="flex items-center gap-2 text-sm">
                                    <span className="w-6 h-6 rounded bg-surface-muted flex items-center justify-center">
                                        <Globe className="w-3.5 h-3.5 text-muted-foreground" />
                                    </span>
                                    Online Presence
                                </CardTitle>
                                <button 
                                    onClick={() => setEditingSection('social')}
                                    className="w-8 h-8 rounded-full bg-surface-muted flex items-center justify-center text-muted-foreground hover:bg-primary hover:text-primary-foreground transition-colors"
                                    title="Edit Social Links"
                                >
                                    <Pencil className="w-3.5 h-3.5" />
                                </button>
                            </CardHeader>
                            <CardContent className="pt-4 space-y-4">
                                {user.website && (
                                    <div className="flex items-center gap-3 text-sm">
                                        <div className="w-9 h-9 rounded-lg bg-surface-muted flex items-center justify-center shrink-0">
                                            <Globe className="w-4.5 h-4.5 text-muted-foreground" />
                                        </div>
                                        <div>
                                            <p className="text-xs text-muted-foreground font-medium mb-0.5">Website</p>
                                            <a href={user.website} target="_blank" rel="noopener noreferrer" className="font-medium hover:text-primary transition-colors">
                                                {new URL(user.website).hostname}
                                            </a>
                                        </div>
                                    </div>
                                )}
                                {socialLinks.some(s => s.href) ? (
                                    socialLinks.map((social) => social.href ? (
                                        <div key={social.key} className="flex items-center gap-3 text-sm">
                                            <div className="w-9 h-9 rounded-lg bg-surface-muted flex items-center justify-center shrink-0">
                                                <ExternalLink className="w-4.5 h-4.5 text-muted-foreground" />
                                            </div>
                                            <div>
                                                <p className="text-xs text-muted-foreground font-medium mb-0.5">{social.label}</p>
                                                <a href={social.href} target="_blank" rel="noopener noreferrer" className="font-medium hover:text-primary transition-colors">
                                                    {social.href.replace(/^https?:\/\//, '')}
                                                </a>
                                            </div>
                                        </div>
                                    ) : null)
                                ) : !user.website && (
                                    <p className="text-sm text-muted-foreground">No online presence configured.</p>
                                )}
                            </CardContent>
                        </Card>
                    )
                )}
            </div>

            <input type="file" ref={avatarInputRef} className="hidden" accept="image/*" onChange={handleAvatarChange} />
            <input type="file" ref={bannerInputRef} className="hidden" accept="image/*" onChange={handleBannerChange} />

            <ConfirmDialog
                open={isDeleteAvatarConfirmOpen}
                onOpenChange={setIsDeleteAvatarConfirmOpen}
                title="Delete Profile Picture"
                message="Are you sure you want to delete your profile picture?"
                confirmLabel="Delete"
                variant="danger"
                onConfirm={confirmDeleteAvatar}
            />

            <ConfirmDialog
                open={isDeleteBannerConfirmOpen}
                onOpenChange={setIsDeleteBannerConfirmOpen}
                title="Delete Cover Photo"
                message="Are you sure you want to delete your cover photo?"
                confirmLabel="Delete"
                variant="danger"
                onConfirm={confirmDeleteBanner}
            />

            {/* Mobile Bottom Sheets */}
            <BottomSheet 
                open={isAvatarMenuOpen} 
                onOpenChange={setIsAvatarMenuOpen}
                title="Profile Picture"
            >
                <div className="space-y-1">
                    <button onClick={() => { setIsAvatarMenuOpen(false); user.avatar_media ? setViewingMedia(user.avatar_media) : null; }} className="w-full flex items-center px-4 py-3 text-sm rounded-xl hover:bg-surface-muted transition-colors text-foreground">
                        <Eye className="w-5 h-5 mr-3 text-muted-foreground" /> View Image
                    </button>
                    <button onClick={() => { setIsAvatarMenuOpen(false); avatarInputRef.current?.click(); }} className="w-full flex items-center px-4 py-3 text-sm rounded-xl hover:bg-surface-muted transition-colors text-foreground">
                        <Upload className="w-5 h-5 mr-3 text-muted-foreground" /> Upload Image
                    </button>
                    {user.avatar_url && (
                        <button onClick={() => { setIsAvatarMenuOpen(false); setIsDeleteAvatarConfirmOpen(true); }} className="w-full flex items-center px-4 py-3 text-sm rounded-xl text-destructive hover:bg-destructive/10 transition-colors">
                            <Trash className="w-5 h-5 mr-3" /> Remove Image
                        </button>
                    )}
                </div>
            </BottomSheet>

            <BottomSheet 
                open={isBannerMenuOpen} 
                onOpenChange={setIsBannerMenuOpen}
                title="Cover Photo"
            >
                <div className="space-y-1">
                    <button onClick={() => { setIsBannerMenuOpen(false); user.banner_media ? setViewingMedia(user.banner_media) : null; }} className="w-full flex items-center px-4 py-3 text-sm rounded-xl hover:bg-surface-muted transition-colors text-foreground">
                        <Eye className="w-5 h-5 mr-3 text-muted-foreground" /> View Image
                    </button>
                    <button onClick={() => { setIsBannerMenuOpen(false); bannerInputRef.current?.click(); }} className="w-full flex items-center px-4 py-3 text-sm rounded-xl hover:bg-surface-muted transition-colors text-foreground">
                        <Upload className="w-5 h-5 mr-3 text-muted-foreground" /> Upload Image
                    </button>
                    {user.banner_url && (
                        <button onClick={() => { setIsBannerMenuOpen(false); setIsDeleteBannerConfirmOpen(true); }} className="w-full flex items-center px-4 py-3 text-sm rounded-xl text-destructive hover:bg-destructive/10 transition-colors">
                            <Trash className="w-5 h-5 mr-3" /> Remove Image
                        </button>
                    )}
                </div>
            </BottomSheet>

            {/* Media Viewer for Avatar/Banner */}
            {viewingMedia && (
                <MediaViewer 
                    media={viewingMedia} 
                    onClose={() => setViewingMedia(null)}
                    onDelete={() => {
                        setViewingMedia(null);
                        if (viewingMedia.name === 'avatars') setIsDeleteAvatarConfirmOpen(true);
                        else setIsDeleteBannerConfirmOpen(true);
                    }}
                    canDelete={true}
                    hasNext={false}
                    hasPrev={false}
                />
            )}

            {/* Hidden inputs for dropdown uploads */}
            <input type="file" ref={avatarInputRef} onChange={handleAvatarChange} className="hidden" accept="image/*" />
            <input type="file" ref={bannerInputRef} onChange={handleBannerChange} className="hidden" accept="image/*" />
        </AccountSettingsLayout>
    );
}
