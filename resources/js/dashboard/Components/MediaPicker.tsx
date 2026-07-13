import React, { useState, useEffect, useRef } from 'react';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogFooter, DialogClose } from './ui/dialog';
import { Btn } from './ui/btn';
import { Tabs, TabsContent, TabsList, TabsTrigger } from './ui/tabs';
import { Upload, Search, Check, Image as ImageIcon, LayoutGrid, X, RefreshCw, Lock, Globe } from 'lucide-react';
import { Input } from './ui/input';
import { usePage } from '@inertiajs/react';

interface MediaItem {
    id: number;
    name: string;
    file_name: string;
    mime_type: string;
    size: number;
    human_readable_size: string;
    thumbnail_url: string;
    original_url: string;
    custom_properties?: any;
    model_type?: string;
    model_id?: number;
}

interface MediaPickerProps {
    open: boolean;
    onOpenChange: (open: boolean) => void;
    onSelect: (media: MediaItem | File) => void;
    title?: string;
}

export default function MediaPicker({ open, onOpenChange, onSelect, title = "Select Media" }: MediaPickerProps) {
    const { auth } = usePage<any>().props;
    const [activeTab, setActiveTab] = useState('library');
    const [media, setMedia] = useState<MediaItem[]>([]);
    const [loading, setLoading] = useState(false);
    const [page, setPage] = useState(1);
    const [hasMore, setHasMore] = useState(true);
    const [selectedItem, setSelectedItem] = useState<MediaItem | null>(null);
    const [uploadFile, setUploadFile] = useState<File | null>(null);
    const [uploadPreview, setUploadPreview] = useState<string | null>(null);
    const fileInputRef = useRef<HTMLInputElement>(null);

    const [searchQuery, setSearchQuery] = useState('');

    const fetchMedia = async (pageNum: number = 1, query: string = '') => {
        setLoading(true);
        try {
            const res = await fetch(`/media/api/index?page=${pageNum}&search=${encodeURIComponent(query)}`);
            const json = await res.json();
            if (pageNum === 1) {
                setMedia(json.data);
            } else {
                setMedia(prev => [...prev, ...json.data]);
            }
            setHasMore(json.current_page < json.last_page);
        } catch (error) {
            console.error("Failed to fetch media", error);
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        if (open && activeTab === 'library') {
            setLoading(true);
            const timer = setTimeout(() => {
                setPage(1);
                fetchMedia(1, searchQuery);
            }, 400);
            return () => clearTimeout(timer);
        }
    }, [open, activeTab, searchQuery]);

    const handleFileChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        const file = e.target.files?.[0];
        if (file) {
            setUploadFile(file);
            if (file.type.startsWith('image/')) {
                setUploadPreview(URL.createObjectURL(file));
            } else {
                setUploadPreview(null);
            }
        }
    };

    const handleConfirm = () => {
        if (activeTab === 'library' && selectedItem) {
            onSelect(selectedItem);
            onOpenChange(false);
        } else if (activeTab === 'upload' && uploadFile) {
            onSelect(uploadFile);
            onOpenChange(false);
        }
    };

    const resetState = () => {
        setSelectedItem(null);
        setUploadFile(null);
        setUploadPreview(null);
        setSearchQuery('');
        setActiveTab('library');
    };

    useEffect(() => {
        if (!open) {
            setTimeout(resetState, 200);
        }
    }, [open]);

    return (
        <Dialog open={open} onOpenChange={onOpenChange}>
            <DialogContent className="max-w-4xl h-[85vh]">
                <DialogHeader className="flex flex-row items-center justify-between space-y-0">
                    <DialogTitle>{title}</DialogTitle>
                    <DialogClose className="p-1 rounded-md text-muted-foreground hover:text-foreground hover:bg-surface-muted transition-colors">
                        <X className="w-4 h-4" />
                    </DialogClose>
                </DialogHeader>
                
                <Tabs value={activeTab} onValueChange={setActiveTab} className="flex-1 flex flex-col overflow-hidden">
                    <div className="px-4 sm:px-6 pt-4 shrink-0 flex items-center justify-between gap-4">
                        <TabsList className="grid w-full max-w-md grid-cols-2">
                            <TabsTrigger value="library" className="data-[state=active]:text-primary data-[state=active]:bg-primary/10 transition-colors gap-2">
                                <LayoutGrid className="w-4 h-4" />
                                Media Library
                            </TabsTrigger>
                            <TabsTrigger value="upload" className="data-[state=active]:text-primary data-[state=active]:bg-primary/10 transition-colors gap-2">
                                <Upload className="w-4 h-4" />
                                Upload New
                            </TabsTrigger>
                        </TabsList>

                        {activeTab === 'library' && (
                            <div className="relative w-full max-w-xs">
                                <Search className="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-muted-foreground" />
                                <Input 
                                    type="text"
                                    placeholder="Search media..."
                                    value={searchQuery}
                                    onChange={(e) => setSearchQuery(e.target.value)}
                                    className="pl-9 h-9 text-sm bg-surface-muted border-transparent focus-visible:bg-background focus-visible:border-primary"
                                />
                            </div>
                        )}
                    </div>

                    <TabsContent value="library" className="flex-1 overflow-y-auto p-4 sm:p-6 m-0 outline-none">
                        {loading && page === 1 ? (
                            <div className="flex items-center justify-center h-full">
                                <div className="w-8 h-8 border-4 border-primary border-t-transparent rounded-full animate-spin"></div>
                            </div>
                        ) : media.length === 0 ? (
                            <div className="flex flex-col items-center justify-center h-full text-muted-foreground">
                                <ImageIcon className="w-12 h-12 mb-4 opacity-50" />
                                <p>No media found in your library.</p>
                                <Btn variant="outline" className="mt-4" onClick={() => setActiveTab('upload')} icon={<Upload className="w-4 h-4" />}>
                                    Upload New Media
                                </Btn>
                            </div>
                        ) : (
                            <>
                                <div className="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                                    {media.map((item) => (
                                        <div 
                                            key={item.id}
                                            onClick={() => setSelectedItem(item)}
                                            className={`relative aspect-square rounded-lg overflow-hidden border-2 cursor-pointer transition-all ${
                                                selectedItem?.id === item.id ? 'border-primary shadow-md scale-[0.98]' : 'border-transparent hover:border-border'
                                            }`}
                                        >
                                            <div className="w-full h-full bg-surface-muted/50 flex items-center justify-center">
                                                {item.mime_type.startsWith('image/') ? (
                                                    <img src={item.thumbnail_url} alt={item.name} className="w-full h-full object-cover" />
                                                ) : (
                                                    <div className="font-bold text-muted-foreground">{item.mime_type.split('/')[1]?.toUpperCase()}</div>
                                                )}
                                            </div>
                                            {selectedItem?.id === item.id && (
                                                <div className="absolute top-2 right-2 bg-primary text-primary-foreground rounded-full p-1 shadow-sm">
                                                    <Check className="w-3 h-3" />
                                                </div>
                                            )}
                                            <div className="absolute inset-x-0 bottom-0 bg-black/50 p-2 truncate text-[10px] text-white">
                                                {item.name}
                                            </div>
                                            <div className="absolute top-2 left-2 flex gap-1">
                                                {item.model_type === 'App\\Models\\User' ? (
                                                    (item.model_id === auth.user.id || item.custom_properties?.uploaded_by === auth.user.id) ? (
                                                        item.custom_properties?.is_public ? (
                                                            <span className="p-1 text-[10px] bg-blue-500/80 text-white backdrop-blur-md rounded shadow-sm flex items-center gap-1" title="Shared Publicly (Personal)">
                                                                <Lock className="w-3 h-3 opacity-70" />
                                                                <Globe className="w-3 h-3" />
                                                            </span>
                                                        ) : (
                                                            <span className="p-1 text-[10px] bg-amber-500/80 text-white backdrop-blur-md rounded shadow-sm" title="Private (Personal)">
                                                                <Lock className="w-3 h-3" />
                                                            </span>
                                                        )
                                                    ) : (
                                                        item.custom_properties?.is_public ? (
                                                            <span className="p-1 text-[10px] bg-blue-500/80 text-white backdrop-blur-md rounded shadow-sm" title="Public (Personal)">
                                                                <Globe className="w-3 h-3" />
                                                            </span>
                                                        ) : (
                                                            <span className="p-1 text-[10px] bg-amber-500/80 text-white backdrop-blur-md rounded shadow-sm" title="Private (Other User)">
                                                                <Lock className="w-3 h-3" />
                                                            </span>
                                                        )
                                                    )
                                                ) : (
                                                    <span className="p-1 text-[10px] bg-blue-500/80 text-white backdrop-blur-md rounded shadow-sm" title="Public">
                                                        <Globe className="w-3 h-3" />
                                                    </span>
                                                )}
                                            </div>
                                        </div>
                                    ))}
                                </div>
                                {hasMore && (
                                    <div className="mt-6 flex justify-center">
                                        <Btn 
                                            variant="outline" 
                                            loading={loading}
                                            disabled={loading}
                                            onClick={() => {
                                                const nextPage = page + 1;
                                                setPage(nextPage);
                                                fetchMedia(nextPage, searchQuery);
                                            }}
                                        >
                                            Load More
                                        </Btn>
                                    </div>
                                )}
                            </>
                        )}
                    </TabsContent>

                    <TabsContent value="upload" className="flex-1 overflow-y-auto p-4 sm:p-6 m-0 flex flex-col items-center justify-center outline-none">
                        {!uploadFile ? (
                            <div 
                                className="w-full max-w-lg border-2 border-dashed border-border-subtle rounded-xl p-12 flex flex-col items-center justify-center cursor-pointer hover:bg-surface-muted transition-colors"
                                onClick={() => fileInputRef.current?.click()}
                            >
                                <input type="file" ref={fileInputRef} onChange={handleFileChange} className="hidden" accept="image/*" />
                                <div className="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center mb-4">
                                    <Upload className="w-8 h-8 text-primary" />
                                </div>
                                <h3 className="text-lg font-semibold mb-1">Click to Upload</h3>
                                <p className="text-sm text-muted-foreground text-center">
                                    Select an image from your computer to attach directly.
                                </p>
                            </div>
                        ) : (
                            <div className="w-full max-w-lg space-y-4">
                                {uploadPreview && (
                                    <div className="w-full aspect-video rounded-lg overflow-hidden border border-border-subtle bg-black/5 flex items-center justify-center">
                                        <img src={uploadPreview} alt="Preview" className="max-w-full max-h-full object-contain" />
                                    </div>
                                )}
                                <div className="p-4 bg-surface-muted rounded-lg border border-border-subtle flex items-center justify-between">
                                    <div className="truncate pr-4">
                                        <p className="font-medium text-sm truncate">{uploadFile.name}</p>
                                        <p className="text-xs text-muted-foreground">{(uploadFile.size / 1024 / 1024).toFixed(2)} MB</p>
                                    </div>
                                    <Btn variant="outline" size="sm" onClick={() => { setUploadFile(null); setUploadPreview(null); }} icon={<RefreshCw className="w-3 h-3" />}>
                                        Change
                                    </Btn>
                                </div>
                            </div>
                        )}
                    </TabsContent>
                </Tabs>

                <DialogFooter>
                    <Btn variant="outline" onClick={() => onOpenChange(false)} icon={<X className="w-4 h-4" />}>
                        Cancel
                    </Btn>
                    <Btn 
                        onClick={handleConfirm} 
                        disabled={(activeTab === 'library' && !selectedItem) || (activeTab === 'upload' && !uploadFile)}
                        icon={<Check className="w-4 h-4" />}
                    >
                        Confirm Selection
                    </Btn>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    );
}
