import React, { useState, useRef } from 'react';
import { Head, router } from '@inertiajs/react';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@dashboard/Components/ui/card';
import { Button } from '@dashboard/Components/ui/button';
import { Input } from '@dashboard/Components/ui/input';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogDescription, DialogFooter, DialogClose } from '@dashboard/Components/ui/dialog';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger, DropdownMenuSeparator } from '@dashboard/Components/ui/dropdown-menu';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@dashboard/Components/ui/select';
import { Switch } from '@dashboard/Components/ui/switch';
import { MoreVertical, Eye, Download, Link as LinkIcon, Trash2, Edit2, X, Filter, Upload, Save, Lock, Globe } from 'lucide-react';
import { toast } from '@dashboard/Components/ui/use-toast';
import { Btn } from '@dashboard/Components/ui/btn';
import DashboardLayout from '@dashboard/Layouts/DashboardLayout';
import { usePermissions } from '@dashboard/hooks/usePermissions';
import MediaViewer from '@dashboard/Components/MediaViewer';
import ConfirmDialog from '@dashboard/Components/ui/confirm-dialog';
import { BottomSheet } from '@dashboard/Components/ui/bottom-sheet';
import { usePage } from '@inertiajs/react';
import { copyToClipboard } from '@dashboard/lib/utils';
interface MediaItem {
    id: number;
    name: string;
    file_name: string;
    mime_type: string;
    size: number;
    human_readable_size: string;
    thumbnail_url: string;
    original_url: string;
    created_at: string;
    model_type: string;
    model_id: string;
    custom_properties: any;
    attached_to?: string;
    uploader?: { name: string } | null;
}

interface Props {
    media: {
        data: MediaItem[];
        current_page: number;
        last_page: number;
        per_page: number;
        total: number;
        links: { url: string | null; label: string; active: boolean }[];
    };
    filters: {
        search?: string;
        type?: string;
        role?: string;
        user?: string;
    };
    filterRoles?: { id: number; name: string; display_name: string }[];
    filterUsers?: { id: number; name: string }[];
}

export default function Index({ media, filters, filterRoles = [], filterUsers = [] }: Props) {
    const { can } = usePermissions();
    const { auth } = usePage().props as any;
    
    // Gunakan permission spesifik, jangan hardcode nama role
    const canDeleteAll = can('media.delete.all');
    const canDeleteOwn = can('media.delete.own');
    const isSuperAdminOrAdmin = canDeleteAll; // Untuk mengatur UI filter & dialog
    
    const [search, setSearch] = useState(filters.search || '');
    const [type, setType] = useState(filters.type || 'all');
    const [role, setRole] = useState(filters.role || 'all');
    const [user, setUser] = useState(filters.user || 'all');
    
    const [selectedMedia, setSelectedMedia] = useState<MediaItem | null>(null);
    const [editingMedia, setEditingMedia] = useState<MediaItem | null>(null);
    const [editTitle, setEditTitle] = useState('');
    
    const [visibilityMedia, setVisibilityMedia] = useState<MediaItem | null>(null);
    const [editIsPublic, setEditIsPublic] = useState(false);
    const [isSavingVisibility, setIsSavingVisibility] = useState(false);
    const [isSavingTitle, setIsSavingTitle] = useState(false);

    const openEdit = (item: MediaItem) => {
        setEditingMedia(item);
        setEditTitle(item.name);
    };

    const openVisibility = (item: MediaItem) => {
        setVisibilityMedia(item);
        setEditIsPublic(item.custom_properties?.is_public || false);
    };
    const [bottomSheetMedia, setBottomSheetMedia] = useState<MediaItem | null>(null);
    const [isClosingSheet, setIsClosingSheet] = useState(false);
    const [isUploadOpen, setIsUploadOpen] = useState(false);
    const [uploadFile, setUploadFile] = useState<File | null>(null);
    const [uploadError, setUploadError] = useState<string | null>(null);
    const [isUploading, setIsUploading] = useState(false);
    const [isDragging, setIsDragging] = useState(false);
    const [deleteTarget, setDeleteTarget] = useState<MediaItem | null>(null);
    const fileInputRef = useRef<HTMLInputElement>(null);

    const handleSearch = (e?: React.FormEvent) => {
        if (e) e.preventDefault();
        const query: any = {};
        if (search) query.search = search;
        if (type && type !== 'all') query.type = type;
        if (role && role !== 'all') query.role = role;
        if (user && user !== 'all') query.user = user;
        
        router.get('/media', query, { preserveState: true });
    };

    const applyFilter = (key: string, value: string) => {
        const query: any = { search, type, role, user };
        query[key] = value;
        
        Object.keys(query).forEach(k => {
            if (!query[k] || query[k] === 'all') delete query[k];
        });
        
        if (key === 'type') setType(value);
        if (key === 'role') setRole(value);
        if (key === 'user') setUser(value);
        
        router.get('/media', query, { preserveState: true });
    };

    const closeBottomSheet = () => {
        setIsClosingSheet(true);
        setTimeout(() => {
            setBottomSheetMedia(null);
            setIsClosingSheet(false);
        }, 300);
    };

    const handleUploadClick = () => {
        setIsUploadOpen(true);
    };

    const handleFileChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        if (e.target.files && e.target.files[0]) {
            setUploadFile(e.target.files[0]);
        }
    };

    const handleDragOver = (e: React.DragEvent) => {
        e.preventDefault();
        setIsDragging(true);
    };

    const handleDragLeave = (e: React.DragEvent) => {
        e.preventDefault();
        setIsDragging(false);
    };

    const handleDrop = (e: React.DragEvent) => {
        e.preventDefault();
        setIsDragging(false);
        if (e.dataTransfer.files && e.dataTransfer.files[0]) {
            setUploadFile(e.dataTransfer.files[0]);
        }
    };

    const confirmUpload = () => {
        if (!uploadFile) return;
        setIsUploading(true);
        
        const formData = new FormData();
        formData.append('file', uploadFile);

        router.post('/media', formData, {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                setIsUploadOpen(false);
                setUploadFile(null);
                setUploadError(null);
                if (fileInputRef.current) fileInputRef.current.value = '';
            },
            onError: (errors) => {
                if (errors.file) setUploadError(errors.file);
                else setUploadError('An error occurred during upload. Please check file size and type.');
                setIsUploading(false);
            },
            onFinish: () => setIsUploading(false),
        });
    };

    const handleDelete = (item: MediaItem) => {
        setDeleteTarget(item);
    };

    const confirmDelete = () => {
        if (!deleteTarget) return;
        router.delete(`/media/${deleteTarget.id}`, {
            onSuccess: () => {
                setSelectedMedia(null);
                setBottomSheetMedia(null);
                setDeleteTarget(null);
            }
        });
    };

    const canDelete = (item: MediaItem) => {
        if (canDeleteAll) return true;
        if (canDeleteOwn && item.custom_properties?.uploaded_by === auth?.user?.id) return true;
        return false;
    };

    return (
        <DashboardLayout>
            <Head title="Media Library" />

            <div className="flex items-center justify-end md:justify-between gap-4 mb-6 w-full">
                <div className="hidden md:block">
                    <h1 className="text-xl font-semibold tracking-tight">Media Library</h1>
                    <p className="hidden lg:block text-sm text-muted-foreground mt-0.5">Manage your images, documents, and assets.</p>
                </div>
                {can('media.upload') && (
                    <div className="ml-auto">
                        <Btn onClick={handleUploadClick} icon={<Upload className="w-4 h-4" />}>
                            Upload New
                        </Btn>
                    </div>
                )}
            </div>

            <div className="mb-8 flex flex-col xl:flex-row gap-4 items-start xl:items-center justify-between bg-surface border border-border-subtle p-4 rounded-xl shadow-sm">
                <form onSubmit={handleSearch} className="relative w-full xl:w-80 group shrink-0">
                    <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-muted-foreground group-focus-within:text-primary transition-colors">
                        <svg className="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <Input
                        type="search"
                        placeholder="Search media..."
                        value={search}
                        onChange={(e) => setSearch(e.target.value)}
                        className="pl-10 h-10 w-full bg-background border-border-subtle rounded-lg shadow-sm transition-all focus:ring-2 focus:ring-primary/20 focus:border-primary"
                    />
                    <button type="submit" className="hidden">Search</button>
                </form>

                <div className="flex flex-row items-center gap-2 sm:gap-3 w-full xl:flex-1 justify-end overflow-x-auto xl:overflow-visible pb-2 xl:pb-0">
                    <div className="flex items-center text-muted-foreground mr-1 shrink-0">
                        <Filter className="w-5 h-5" />
                    </div>

                    <div className="flex-1 min-w-[110px] xl:flex-none xl:w-36 shrink-0">
                        <Select value={type} onValueChange={(val) => applyFilter('type', val)}>
                            <SelectTrigger className="h-10 bg-background">
                                <SelectValue placeholder="File Type" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="all">All Types</SelectItem>
                                <SelectItem value="image">Images</SelectItem>
                                <SelectItem value="document">Documents</SelectItem>
                                <SelectItem value="video">Videos</SelectItem>
                            </SelectContent>
                        </Select>
                    </div>

                    {isSuperAdminOrAdmin && (
                        <>
                            <div className="flex-1 min-w-[120px] xl:flex-none xl:w-40 shrink-0">
                                <Select value={role} onValueChange={(val) => applyFilter('role', val)}>
                                    <SelectTrigger className="h-10 bg-background">
                                        <SelectValue placeholder="All Roles" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="all">All Roles</SelectItem>
                                        {filterRoles.map(r => (
                                            <SelectItem key={r.id} value={r.id.toString()}>{r.display_name}</SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                            </div>

                            <div className="flex-1 min-w-[120px] xl:flex-none xl:w-40 shrink-0">
                                <Select value={user} onValueChange={(val) => applyFilter('user', val)}>
                                    <SelectTrigger className="h-10 bg-background">
                                        <SelectValue placeholder="All Users" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="all">All Users</SelectItem>
                                        {filterUsers.map(u => (
                                            <SelectItem key={u.id} value={u.id.toString()}>{u.name}</SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                            </div>
                        </>
                    )}
                </div>
            </div>

            {media.data.length === 0 ? (
                <div className="text-center py-16 bg-surface border border-border-subtle rounded-xl shadow-sm">
                    <svg className="w-12 h-12 mx-auto text-muted-foreground/50 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <h3 className="text-lg font-medium text-foreground">No media found</h3>
                    <p className="hidden lg:block text-sm text-muted-foreground mt-0.5">Upload some files to see them here.</p>
                </div>
            ) : (
                <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-4 gap-4 lg:gap-6">
                    {media.data.map((item) => (
                        <Card 
                            key={item.id} 
                            className="group relative overflow-hidden cursor-pointer border-border-subtle hover:border-primary/40 transition-all duration-300 shadow-sm hover:shadow-xl hover:-translate-y-1.5 rounded-xl bg-surface"
                            onClick={() => setSelectedMedia(item)}
                        >
                            {/* File Extension Badge */}
                            <div className="absolute top-2.5 left-2.5 z-20 flex gap-1.5 items-center">
                                <span className="px-2 py-1 text-[9px] font-bold uppercase tracking-widest bg-black/50 text-white backdrop-blur-md rounded shadow-sm">
                                    {item.mime_type.split('/')[1]?.substring(0, 4) || 'FILE'}
                                </span>
                                {item.model_type === 'App\\Models\\User' ? (
                                    (item.model_id === auth.user.id || item.custom_properties?.uploaded_by === auth.user.id) ? (
                                        item.custom_properties?.is_public ? (
                                            <span className="p-1 text-[10px] bg-blue-500/80 text-white backdrop-blur-md rounded shadow-sm flex items-center gap-1" title="Shared Publicly (Personal)">
                                                <Lock className="w-3 h-3 opacity-70" />
                                                <Globe className="w-3.5 h-3.5" />
                                            </span>
                                        ) : (
                                            <span className="p-1 text-[10px] bg-amber-500/80 text-white backdrop-blur-md rounded shadow-sm" title="Private (Personal)">
                                                <Lock className="w-3.5 h-3.5" />
                                            </span>
                                        )
                                    ) : (
                                        item.custom_properties?.is_public ? (
                                            <span className="p-1 text-[10px] bg-blue-500/80 text-white backdrop-blur-md rounded shadow-sm" title="Public (Personal)">
                                                <Globe className="w-3.5 h-3.5" />
                                            </span>
                                        ) : (
                                            <span className="p-1 text-[10px] bg-amber-500/80 text-white backdrop-blur-md rounded shadow-sm" title="Private (Other User)">
                                                <Lock className="w-3.5 h-3.5" />
                                            </span>
                                        )
                                    )
                                ) : (
                                    <span className="p-1 text-[10px] bg-blue-500/80 text-white backdrop-blur-md rounded shadow-sm" title="Public">
                                        <Globe className="w-3.5 h-3.5" />
                                    </span>
                                )}
                            </div>

                            {/* Quick Action Button */}
                            <div className="absolute top-2.5 right-2.5 z-20 md:opacity-0 md:group-hover:opacity-100 transition-opacity duration-300">
                                {/* Desktop Dropdown */}
                                <div className="hidden md:block">
                                    <DropdownMenu>
                                        <DropdownMenuTrigger asChild>
                                            <div 
                                                role="button"
                                                onClick={(e) => e.stopPropagation()} 
                                                className="p-1.5 bg-white/90 dark:bg-black/60 text-foreground dark:text-white backdrop-blur-md rounded shadow-sm hover:bg-white dark:hover:bg-black/80 transition-colors"
                                            >
                                                <MoreVertical className="w-4 h-4" />
                                            </div>
                                        </DropdownMenuTrigger>
                                        <DropdownMenuContent align="end" className="w-48 z-[100]">
                                            <DropdownMenuItem onClick={(e) => { e.stopPropagation(); setSelectedMedia(item); }}>
                                                <Eye className="w-4 h-4 mr-2" /> View
                                            </DropdownMenuItem>
                                            <DropdownMenuItem onClick={(e) => { e.stopPropagation(); openEdit(item); }}>
                                                <Edit2 className="w-4 h-4 mr-2" /> Edit Title
                                            </DropdownMenuItem>
                                            {item.model_type === 'App\\Models\\User' && (
                                                <DropdownMenuItem onClick={(e) => { e.stopPropagation(); openVisibility(item); }}>
                                                    <Globe className="w-4 h-4 mr-2" /> Media Visibility
                                                </DropdownMenuItem>
                                            )}
                                            <DropdownMenuItem onClick={(e) => { 
                                                e.stopPropagation(); 
                                                const link = document.createElement('a');
                                                link.href = item.original_url;
                                                link.download = item.file_name;
                                                document.body.appendChild(link);
                                                link.click();
                                                document.body.removeChild(link);
                                            }}>
                                                <Download className="w-4 h-4 mr-2" /> Download
                                            </DropdownMenuItem>
                                            <DropdownMenuItem onClick={(e) => { 
                                                e.stopPropagation(); 
                                                copyToClipboard(new URL(item.original_url, window.location.origin).href);
                                                toast.success('Media URL copied to clipboard');
                                            }}>
                                                <LinkIcon className="w-4 h-4 mr-2" /> Copy Link
                                            </DropdownMenuItem>
                                            {canDelete(item) && (
                                                <>
                                                    <DropdownMenuSeparator />
                                                    <DropdownMenuItem className="text-destructive focus:bg-destructive/10 focus:text-destructive" onClick={(e) => { e.stopPropagation(); handleDelete(item); }}>
                                                        <Trash2 className="w-4 h-4 mr-2" /> Delete
                                                    </DropdownMenuItem>
                                                </>
                                            )}
                                        </DropdownMenuContent>
                                    </DropdownMenu>
                                </div>
                                
                                {/* Mobile/Tablet Bottom Sheet Trigger */}
                                <div 
                                    role="button"
                                    className="md:hidden p-1.5 bg-white/90 dark:bg-black/60 text-foreground dark:text-white backdrop-blur-md rounded shadow-sm hover:bg-white dark:hover:bg-black/80 transition-colors"
                                    onClick={(e) => { e.stopPropagation(); setBottomSheetMedia(item); }}
                                >
                                    <MoreVertical className="w-4 h-4" />
                                </div>
                            </div>

                            <div className="aspect-square w-full relative bg-surface-muted/50 flex items-center justify-center overflow-hidden">
                                {item.mime_type.startsWith('image/') ? (
                                    <img 
                                        src={item.thumbnail_url} 
                                        alt={item.name} 
                                        className="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" 
                                        loading="lazy"
                                    />
                                ) : (
                                    <div className="w-16 h-16 text-muted-foreground/30 transition-transform duration-700 group-hover:scale-110">
                                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="1" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                )}
                                
                                {/* Glassmorphism Bottom Overlay */}
                                <div className="absolute inset-x-0 bottom-0 p-3 pt-8 bg-gradient-to-t from-black/90 via-black/50 to-transparent transition-all duration-300">
                                    {/* Base info always visible */}
                                    <div className="transform transition-transform duration-300 group-hover:-translate-y-1">
                                        <p className="text-[11px] sm:text-xs font-semibold text-white truncate drop-shadow-sm mb-0.5">{item.name}</p>
                                        <div className="flex items-center justify-end md:justify-between">
                                            <p className="text-[9px] sm:text-[10px] text-gray-300 font-medium">{item.human_readable_size}</p>
                                            
                                            {/* Extra detail visible only on hover */}
                                            <div className="opacity-0 group-hover:opacity-100 transition-opacity duration-300 delay-75 flex items-center">
                                                <span className="text-[9px] text-emerald-400 font-semibold tracking-wider uppercase drop-shadow-sm">
                                                    View
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </Card>
                    ))}
                </div>
            )}

            {/* Pagination */}
            {media.last_page > 1 && (
                <div className="mt-8 flex justify-center">
                    <div className="flex items-center gap-1">
                        {media.links.map((link, i) => (
                            <button
                                key={i}
                                onClick={() => link.url && router.get(link.url)}
                                disabled={!link.url}
                                className={`px-3 py-1 text-sm rounded-md transition-colors ${
                                    link.active 
                                        ? 'bg-primary text-primary-foreground font-medium' 
                                        : 'bg-surface border border-border-subtle text-muted-foreground hover:bg-surface-muted hover:text-foreground disabled:opacity-50 disabled:hover:bg-surface disabled:hover:text-muted-foreground'
                                }`}
                                dangerouslySetInnerHTML={{ __html: link.label }}
                            />
                        ))}
                    </div>
                </div>
            )}

            {/* Upload Modal */}
            <Dialog open={isUploadOpen} onOpenChange={(open) => {
                setIsUploadOpen(open);
                if (!open) setUploadFile(null);
            }}>
                <DialogContent className="sm:max-w-md w-[95vw] rounded-xl">
                    <DialogHeader className="flex flex-row items-center justify-between space-y-0">
                        <DialogTitle>Upload Media</DialogTitle>
                        <DialogClose className="p-1 rounded-md text-muted-foreground hover:text-foreground hover:bg-surface-muted transition-colors">
                            <X className="w-4 h-4" />
                        </DialogClose>
                    </DialogHeader>
                    
                    <div className="px-4 sm:px-6 py-4 flex-1 overflow-y-auto">
                    {!uploadFile ? (
                        <div 
                            className={`mt-4 flex flex-col items-center justify-center border-2 border-dashed rounded-lg p-10 transition-colors cursor-pointer ${
                                isDragging ? 'border-primary bg-primary/5' : 'border-border-subtle bg-surface-muted/30 hover:bg-surface-muted/50'
                            }`}
                            onDragOver={handleDragOver}
                            onDragLeave={handleDragLeave}
                            onDrop={handleDrop}
                            onClick={() => fileInputRef.current?.click()}
                        >
                            <input 
                                type="file" 
                                className="hidden" 
                                ref={fileInputRef} 
                                onChange={handleFileChange} 
                            />
                            <div className="w-12 h-12 rounded-full bg-primary/10 flex items-center justify-center mb-4">
                                <svg className="w-6 h-6 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                </svg>
                            </div>
                            <p className="text-sm font-medium text-foreground mb-1">Click to upload or drag and drop</p>
                            <p className="text-xs text-muted-foreground text-center">Images, PDFs, or Documents (Max 10MB)</p>
                        </div>
                    ) : (
                        <div className="py-4">
                            <div className="flex items-center justify-end md:justify-between bg-surface-muted/50 border border-border-subtle rounded-lg p-3">
                                <div className="flex items-center gap-3 overflow-hidden">
                                    <div className="w-10 h-10 rounded bg-primary/10 flex items-center justify-center shrink-0">
                                        <svg className="w-5 h-5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </div>
                                    <div className="min-w-0">
                                        <p className="text-sm font-medium text-foreground truncate">{uploadFile.name}</p>
                                        <p className="text-xs text-muted-foreground">{(uploadFile.size / 1024 / 1024).toFixed(2)} MB</p>
                                    </div>
                                </div>
                                <button 
                                    onClick={() => setUploadFile(null)} 
                                    className="p-2 text-muted-foreground hover:text-danger hover:bg-danger/10 rounded-md transition-colors"
                                >
                                    <X className="w-4 h-4" />
                                </button>
                            </div>
                            
                            {uploadFile.type.startsWith('image/') && (
                                <div className="mt-4 aspect-video bg-surface-muted/30 rounded-lg overflow-hidden border border-border-subtle flex items-center justify-center p-2">
                                    <img src={URL.createObjectURL(uploadFile)} alt="Preview" className="max-w-full max-h-full object-contain rounded" />
                                </div>
                            )}
                        </div>
                    )}
                    {uploadError && (
                        <div className="mt-4 p-3 bg-destructive/10 border border-destructive/20 text-destructive text-sm rounded-md">
                            {uploadError}
                        </div>
                    )}
                    </div>
                    <DialogFooter>
                        <Btn variant="outline" onClick={() => setIsUploadOpen(false)} disabled={isUploading} icon={<X className="w-4 h-4" />}>
                            Cancel
                        </Btn>
                        <Btn onClick={confirmUpload} disabled={!uploadFile || isUploading} loading={isUploading} icon={<Upload className="w-4 h-4" />}>
                            Upload File
                        </Btn>
                    </DialogFooter>
                </DialogContent>
            </Dialog>


            {selectedMedia && (
                <MediaViewer 
                    media={selectedMedia} 
                    onClose={() => setSelectedMedia(null)}
                    onDelete={() => handleDelete(selectedMedia)}
                    canDelete={canDelete(selectedMedia)}
                    onNext={() => {
                        const currentIndex = media.data.findIndex(m => m.id === selectedMedia.id);
                        if (currentIndex < media.data.length - 1) setSelectedMedia(media.data[currentIndex + 1]);
                    }}
                    onPrev={() => {
                        const currentIndex = media.data.findIndex(m => m.id === selectedMedia.id);
                        if (currentIndex > 0) setSelectedMedia(media.data[currentIndex - 1]);
                    }}
                    hasNext={media.data.findIndex(m => m.id === selectedMedia.id) < media.data.length - 1}
                    hasPrev={media.data.findIndex(m => m.id === selectedMedia.id) > 0}
                />
            )}

            {/* Mobile/Tablet Bottom Sheet */}
            <BottomSheet 
                open={!!bottomSheetMedia} 
                onOpenChange={(open) => !open && closeBottomSheet()}
                title={bottomSheetMedia?.name}
                description={bottomSheetMedia?.human_readable_size}
            >
                {bottomSheetMedia && (
                    <div className="space-y-1">
                        <button 
                            onClick={() => { setSelectedMedia(bottomSheetMedia); closeBottomSheet(); }}
                            className="w-full flex items-center px-4 py-3 text-sm rounded-xl hover:bg-surface-muted transition-colors text-foreground"
                        >
                            <Eye className="w-5 h-5 mr-3 text-muted-foreground" /> View
                        </button>
                        <button 
                            onClick={() => { openEdit(bottomSheetMedia); closeBottomSheet(); }}
                            className="w-full flex items-center px-4 py-3 text-sm rounded-xl hover:bg-surface-muted transition-colors text-foreground"
                        >
                            <Edit2 className="w-5 h-5 mr-3 text-muted-foreground" /> Edit Title
                        </button>
                        {bottomSheetMedia.model_type === 'App\\Models\\User' && (
                            <button 
                                onClick={() => { openVisibility(bottomSheetMedia); closeBottomSheet(); }}
                                className="w-full flex items-center px-4 py-3 text-sm rounded-xl hover:bg-surface-muted transition-colors text-foreground"
                            >
                                <Globe className="w-5 h-5 mr-3 text-muted-foreground" /> Media Visibility
                            </button>
                        )}
                        <button 
                            onClick={() => { 
                                const link = document.createElement('a');
                                link.href = bottomSheetMedia.original_url;
                                link.download = bottomSheetMedia.file_name;
                                document.body.appendChild(link);
                                link.click();
                                document.body.removeChild(link);
                                closeBottomSheet();
                            }}
                            className="w-full flex items-center px-4 py-3 text-sm rounded-xl hover:bg-surface-muted transition-colors text-foreground"
                        >
                            <Download className="w-5 h-5 mr-3 text-muted-foreground" /> Download
                        </button>
                        <button 
                            onClick={() => { 
                                copyToClipboard(new URL(bottomSheetMedia.original_url, window.location.origin).href);
                                toast.success('Media URL copied to clipboard');
                                closeBottomSheet();
                            }}
                            className="w-full flex items-center px-4 py-3 text-sm rounded-xl hover:bg-surface-muted transition-colors text-foreground"
                        >
                            <LinkIcon className="w-5 h-5 mr-3 text-muted-foreground" /> Copy Link
                        </button>
                        {canDelete(bottomSheetMedia) && (
                            <button 
                                onClick={() => { handleDelete(bottomSheetMedia); closeBottomSheet(); }}
                                className="w-full flex items-center px-4 py-3 text-sm rounded-xl text-destructive hover:bg-destructive/10 transition-colors"
                            >
                                <Trash2 className="w-5 h-5 mr-3" /> Delete
                            </button>
                        )}
                    </div>
                )}
            </BottomSheet>

            {/* Edit Title Dialog */}
            <Dialog open={!!editingMedia} onOpenChange={(open) => !open && setEditingMedia(null)}>
                <DialogContent className="max-w-md w-[92vw] sm:w-full max-h-[90vh] flex flex-col p-0 gap-0">
                    <DialogHeader className="flex flex-row items-center justify-between px-6 py-4 border-b border-border-subtle mb-0 shrink-0">
                        <DialogTitle className="text-base">Edit Media Title</DialogTitle>
                        <DialogClose className="p-1 rounded-md text-muted-foreground hover:text-foreground hover:bg-surface-muted transition-colors">
                            <X className="w-4 h-4" />
                        </DialogClose>
                    </DialogHeader>
                    {editingMedia && (
                        <form onSubmit={(e) => {
                            e.preventDefault();
                            setIsSavingTitle(true);
                            router.put(`/media/${editingMedia.id}`, { 
                                name: editTitle
                            }, { 
                                preserveScroll: true,
                                onFinish: () => setIsSavingTitle(false),
                                onSuccess: () => setEditingMedia(null)
                            });
                        }} className="flex flex-col flex-1 min-h-0">
                            <div className="space-y-4 px-6 py-4 overflow-y-auto flex-1">
                                <div className="space-y-1.5">
                                    <label className="text-sm font-medium">
                                        Title
                                    </label>
                                    <Input 
                                        name="name" 
                                        value={editTitle}
                                        onChange={(e) => setEditTitle(e.target.value)}
                                        autoFocus 
                                        required 
                                        autoComplete="off"
                                    />
                                </div>

                            </div>
                            <DialogFooter className="flex items-center justify-end gap-2 px-6 py-4 border-t border-border-subtle mt-0 shrink-0">
                                <DialogClose asChild>
                                    <button 
                                        type="button"
                                        className="inline-flex items-center justify-center h-9 px-4 border border-border-subtle bg-background text-foreground rounded-md text-sm font-medium hover:bg-surface-muted active:bg-surface-muted/80 transition-all shadow-sm"
                                    >
                                        Cancel
                                    </button>
                                </DialogClose>
                                <Btn 
                                    type="submit"
                                    loading={isSavingTitle}
                                    disabled={isSavingTitle || editTitle === editingMedia.name || !editTitle}
                                    icon={<Save className="w-4 h-4" />}
                                    className="h-9 px-4 shadow-sm"
                                >
                                    Save Changes
                                </Btn>
                            </DialogFooter>
                        </form>
                    )}
                </DialogContent>
            </Dialog>
            {/* Media Visibility Dialog */}
            <Dialog open={!!visibilityMedia} onOpenChange={(open) => !open && setVisibilityMedia(null)}>
                <DialogContent className="max-w-md w-[92vw] sm:w-full max-h-[90vh] flex flex-col p-0 gap-0">
                    <DialogHeader className="flex flex-row items-center justify-between px-6 py-4 border-b border-border-subtle mb-0 shrink-0">
                        <DialogTitle className="text-base">Media Visibility</DialogTitle>
                        <DialogClose className="p-1 rounded-md text-muted-foreground hover:text-foreground hover:bg-surface-muted transition-colors">
                            <X className="w-4 h-4" />
                        </DialogClose>
                    </DialogHeader>
                    {visibilityMedia && (
                        <div className="flex flex-col flex-1 min-h-0">
                            <div className="space-y-6 px-6 py-4 overflow-y-auto flex-1">
                                <div className="flex items-center justify-between">
                                    <div className="space-y-0.5 pr-4">
                                        <label className="text-sm font-medium flex items-center gap-2">
                                            {editIsPublic ? <Globe className="w-4 h-4 text-primary" /> : <Lock className="w-4 h-4 text-muted-foreground" />}
                                            Public Access
                                        </label>
                                        <p className="text-xs text-muted-foreground">When active, anyone with the link can view this media.</p>
                                    </div>
                                    <Switch 
                                        checked={editIsPublic} 
                                        onCheckedChange={(checked) => {
                                            setEditIsPublic(checked);
                                            setIsSavingVisibility(true);
                                            router.put(`/media/${visibilityMedia.id}`, { 
                                                name: visibilityMedia.name,
                                                is_public: checked
                                            }, { 
                                                preserveScroll: true,
                                                onFinish: () => setIsSavingVisibility(false),
                                                onSuccess: () => {
                                                    // Also update local item state if we need it
                                                    const updatedMedia = {...visibilityMedia, custom_properties: {...visibilityMedia.custom_properties, is_public: checked}};
                                                    setVisibilityMedia(updatedMedia);
                                                }
                                            });
                                        }}
                                        disabled={isSavingVisibility}
                                    />
                                </div>
                                
                                {editIsPublic && (
                                    <div className="space-y-2 p-4 bg-surface-muted rounded-lg border border-border-subtle">
                                        <label className="text-xs font-semibold uppercase tracking-wider text-muted-foreground">Share Link</label>
                                        <div className="flex items-center gap-2">
                                            <Input 
                                                readOnly 
                                                value={new URL(visibilityMedia.original_url, window.location.origin).href} 
                                                className="bg-background font-mono text-xs h-9"
                                            />
                                            <Btn 
                                                variant="secondary" 
                                                className="shrink-0 h-9" 
                                                onClick={() => {
                                                    copyToClipboard(new URL(visibilityMedia.original_url, window.location.origin).href);
                                                    toast.success('Share link copied to clipboard');
                                                }}
                                                icon={<LinkIcon className="w-3.5 h-3.5" />}
                                            >
                                                Copy
                                            </Btn>
                                        </div>
                                    </div>
                                )}
                            </div>
                        </div>
                    )}
                </DialogContent>
            </Dialog>

            <ConfirmDialog
                open={!!deleteTarget}
                onOpenChange={(open) => { if (!open) setDeleteTarget(null); }}
                title="Delete Media"
                message={isSuperAdminOrAdmin 
                    ? `Are you sure you want to permanently delete "${deleteTarget?.name}" (Attached to: ${deleteTarget?.model_type === 'App\\Models\\User' ? 'Personal Library' : (deleteTarget?.model_type === 'App\\Models\\Post' ? 'Post' : 'System Asset')}) from the server?`
                    : `Remove "${deleteTarget?.name}" from your collection? (It will be transferred to Admin to prevent broken links in existing posts)`}
                confirmLabel="Delete"
                variant="danger"
                onConfirm={confirmDelete}
            />
        </DashboardLayout>
    );
}
