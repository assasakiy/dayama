import React, { useState, useRef } from 'react';
import { Head, router, Link, usePage } from '@inertiajs/react';
import DashboardLayout from '@dashboard/Layouts/DashboardLayout';
import { Card, CardHeader, CardTitle, CardContent } from '@dashboard/Components/ui/card';
import { Input } from '@dashboard/Components/ui/input';
import { Textarea } from '@dashboard/Components/ui/textarea';
import {
    ArrowLeft,
    Save,
    Eye,
    Mail,
    Globe,
    User as UserIcon,
    Lock,
    ShieldCheck,
    Upload,
    X,
    CalendarDays,
    FileText,
    ExternalLink,
} from 'lucide-react';
import { Btn } from '@dashboard/Components/ui/btn';

interface UserData {
    id: string;
    name: string;
    email: string;
    username: string;
    avatar_url: string | null;
    biography: string | null;
    website: string | null;
    social_links: { github?: string; twitter?: string; linkedin?: string } | null;
    posts_count: number;
    roles: string[];
    is_primary_super_admin: boolean;
    is_protected: boolean;
    is_verified: boolean;
    created_at: string;
}

interface Role {
    id: string;
    name: string;
}

interface Props {
    user: UserData | null;
    roles: Role[];
}

export default function UserForm({ user, roles }: Props) {
    const { auth } = usePage().props as unknown as { auth: { user: { highest_rank: number, is_primary_super_admin: boolean } } };
    const canManageProtection = auth.user.is_primary_super_admin || auth.user.highest_rank >= 100;
    const isEditing = !!user;
    const fileInputRef = useRef<HTMLInputElement>(null);

    const [name, setName] = useState(user?.name ?? '');
    const [email, setEmail] = useState(user?.email ?? '');
    const [biography, setBiography] = useState(user?.biography ?? '');
    const [website, setWebsite] = useState(user?.website ?? '');
    const [github, setGithub] = useState(user?.social_links?.github ?? '');
    const [twitter, setTwitter] = useState(user?.social_links?.twitter ?? '');
    const [linkedin, setLinkedin] = useState(user?.social_links?.linkedin ?? '');
    const [password, setPassword] = useState('');
    const [selectedRoles, setSelectedRoles] = useState<string[]>(user?.roles ?? []);
    const [isProtected, setIsProtected] = useState(user?.is_protected ?? false);
    const [isVerified, setIsVerified] = useState(user?.is_verified ?? false);
    const [avatarPreview, setAvatarPreview] = useState<string | null>(user?.avatar_url ?? null);
    const [avatarFile, setAvatarFile] = useState<File | null>(null);
    const [errors, setErrors] = useState<Record<string, string>>({});
    const [submitting, setSubmitting] = useState(false);

    const isDirty = name !== (user?.name ?? '') ||
        email !== (user?.email ?? '') ||
        biography !== (user?.biography ?? '') ||
        website !== (user?.website ?? '') ||
        github !== (user?.social_links?.github ?? '') ||
        twitter !== (user?.social_links?.twitter ?? '') ||
        linkedin !== (user?.social_links?.linkedin ?? '') ||
        password !== '' ||
        isProtected !== (user?.is_protected ?? false) ||
        isVerified !== (user?.is_verified ?? false) ||
        avatarFile !== null ||
        JSON.stringify([...selectedRoles].sort()) !== JSON.stringify([...(user?.roles ?? [])].sort());

    const toggleRole = (roleName: string) => {
        setSelectedRoles((prev) =>
            prev.includes(roleName)
                ? prev.filter((r) => r !== roleName)
                : [...prev, roleName]
        );
    };

    const handleAvatarChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        const file = e.target.files?.[0];
        if (file) {
            setAvatarFile(file);
            setAvatarPreview(URL.createObjectURL(file));
        }
    };

    const removeAvatar = () => {
        setAvatarFile(null);
        setAvatarPreview(null);
        if (fileInputRef.current) fileInputRef.current.value = '';
    };

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        setSubmitting(true);

        const formData = new FormData();
        formData.append('name', name);
        formData.append('email', email);
        if (biography) formData.append('biography', biography);
        if (website) formData.append('website', website);
        if (github) formData.append('social_links[github]', github);
        if (twitter) formData.append('social_links[twitter]', twitter);
        if (linkedin) formData.append('social_links[linkedin]', linkedin);
        if (password) formData.append('password', password);
        formData.append('is_protected', isProtected ? '1' : '0');
        formData.append('is_verified', isVerified ? '1' : '0');
        selectedRoles.forEach((role) => formData.append('roles[]', role));
        if (avatarFile) formData.append('avatar', avatarFile);

        if (isEditing && user) {
            formData.append('_method', 'PUT');
            router.post(`/dashboard/users/${user.id}`, formData, {
                onError: (errs) => { setErrors(errs); setSubmitting(false); },
                onFinish: () => setSubmitting(false),
            });
        } else {
            router.post('/dashboard/users', formData, {
                onError: (errs) => { setErrors(errs); setSubmitting(false); },
                onFinish: () => setSubmitting(false),
            });
        }
    };

    const avatarInitial = name.charAt(0).toUpperCase();

    return (
        <DashboardLayout>
            <Head title={isEditing ? 'Edit User' : 'New User'} />
            <div className="space-y-5">
                <div className="flex items-center justify-end md:justify-between w-full">
                    <div className="flex items-center gap-3">
                        <Link
                            href="/dashboard/users"
                            className="p-1.5 rounded-md text-muted-foreground hover:text-foreground hover:bg-surface-muted transition-colors"
                        >
                            <ArrowLeft className="w-4 h-4" />
                        </Link>
                        <div className="hidden md:block">
                            <h1 className="text-xl font-semibold tracking-tight">{isEditing ? 'Edit User' : 'New User'}</h1>
                            <p className="hidden lg:block text-sm text-muted-foreground mt-0.5">
                                {isEditing ? 'Update user details and permissions' : 'Create a new user account'}
                            </p>
                        </div>
                    </div>
                    {isEditing && user && (
                        <Link
                            href={`/dashboard/users/${user.id}`}
                            className="inline-flex items-center gap-1.5 h-8 px-3 text-xs border border-border-subtle rounded-md text-muted-foreground hover:text-foreground hover:bg-surface-muted transition-all"
                        >
                            <Eye className="w-3.5 h-3.5" />
                            View Profile
                        </Link>
                    )}
                </div>

                <form onSubmit={submit}>
                    <div className="grid grid-cols-1 lg:grid-cols-3 gap-5">
                        {/* Main content */}
                        <div className="lg:col-span-2 space-y-5">
                            {/* Account details */}
                            <Card>
                                <CardHeader>
                                    <CardTitle className="flex items-center gap-2 text-sm">
                                        <span className="w-5 h-5 rounded bg-surface-muted flex items-center justify-center">
                                            <Mail className="w-3 h-3 text-muted-foreground" />
                                        </span>
                                        Account Details
                                    </CardTitle>
                                </CardHeader>
                                <CardContent className="space-y-4">
                                    <Input
                                        label="Full Name"
                                        value={name}
                                        onChange={(e) => setName(e.target.value)}
                                        error={errors.name}
                                        required
                                        placeholder="John Doe"
                                    />
                                    <Input
                                        label="Email Address"
                                        type="email"
                                        value={email}
                                        onChange={(e) => setEmail(e.target.value)}
                                        error={errors.email}
                                        required
                                        placeholder="john@example.com"
                                    />
                                </CardContent>
                            </Card>

                            {/* Profile */}
                            <Card>
                                <CardHeader>
                                    <CardTitle className="flex items-center gap-2 text-sm">
                                        <span className="w-5 h-5 rounded bg-surface-muted flex items-center justify-center">
                                            <UserIcon className="w-3 h-3 text-muted-foreground" />
                                        </span>
                                        Profile
                                    </CardTitle>
                                </CardHeader>
                                <CardContent className="space-y-4">
                                    <Textarea
                                        label="Biography"
                                        value={biography}
                                        onChange={(e) => setBiography(e.target.value)}
                                        error={errors.biography}
                                        placeholder="Brief bio..."
                                        rows={3}
                                    />
                                    <Input
                                        label="Website"
                                        value={website}
                                        onChange={(e) => setWebsite(e.target.value)}
                                        error={errors.website}
                                        placeholder="https://example.com"
                                    />
                                </CardContent>
                            </Card>

                            {/* Social links */}
                            <Card>
                                <CardHeader>
                                    <CardTitle className="flex items-center gap-2 text-sm">
                                        <span className="w-5 h-5 rounded bg-surface-muted flex items-center justify-center">
                                            <Globe className="w-3 h-3 text-muted-foreground" />
                                        </span>
                                        Social Links
                                    </CardTitle>
                                </CardHeader>
                                <CardContent className="space-y-3">
                                    {[
                                        { key: 'github', label: 'GitHub', value: github, setter: setGithub },
                                        { key: 'twitter', label: 'Twitter', value: twitter, setter: setTwitter },
                                        { key: 'linkedin', label: 'LinkedIn', value: linkedin, setter: setLinkedin },
                                    ].map((social) => (
                                        <div key={social.key} className="flex items-center gap-3">
                                            <div className="w-9 h-9 rounded-lg bg-surface-muted flex items-center justify-center shrink-0">
                                                <ExternalLink className="w-4 h-4 text-muted-foreground" />
                                            </div>
                                            <Input
                                                value={social.value}
                                                onChange={(e) => social.setter(e.target.value)}
                                                placeholder={`https://${social.key}.com/username`}
                                                error={errors[`social_links.${social.key}`]}
                                            />
                                        </div>
                                    ))}
                                </CardContent>
                            </Card>
                        </div>

                        {/* Sidebar */}
                        <div className="space-y-5">
                            {/* Avatar */}
                            <Card>
                                <CardHeader>
                                    <CardTitle className="flex items-center gap-2 text-sm">
                                        <span className="w-5 h-5 rounded bg-surface-muted flex items-center justify-center">
                                            <UserIcon className="w-3 h-3 text-muted-foreground" />
                                        </span>
                                        Avatar
                                    </CardTitle>
                                </CardHeader>
                                <CardContent>
                                    <div className="flex flex-col items-center gap-3">
                                        <div className="relative group">
                                            <div className="w-24 h-24 rounded-full bg-gradient-to-br from-primary to-primary/80 flex items-center justify-center text-primary-foreground text-3xl font-bold shadow-md">
                                                {avatarPreview ? (
                                                    <img src={avatarPreview} alt="Avatar" className="w-full h-full rounded-full object-cover" />
                                                ) : (
                                                    avatarInitial
                                                )}
                                            </div>
                                            {avatarPreview && (
                                                <button
                                                    type="button"
                                                    onClick={removeAvatar}
                                                    className="absolute -top-1 -right-1 p-1 rounded-full bg-background border border-border-subtle text-muted-foreground hover:text-foreground shadow-sm"
                                                >
                                                    <X className="w-3.5 h-3.5" />
                                                </button>
                                            )}
                                        </div>
                                        <button
                                            type="button"
                                            onClick={() => fileInputRef.current?.click()}
                                            className="inline-flex items-center gap-1.5 text-xs font-medium text-primary hover:text-primary/80 transition-colors"
                                        >
                                            <Upload className="w-3.5 h-3.5" />
                                            Upload photo
                                        </button>
                                        <input
                                            ref={fileInputRef}
                                            type="file"
                                            accept="image/png,image/jpeg,image/webp"
                                            onChange={handleAvatarChange}
                                            className="hidden"
                                        />
                                        {errors.avatar && <p className="text-xs text-destructive">{errors.avatar}</p>}
                                    </div>
                                </CardContent>
                            </Card>

                            {/* Roles */}
                            <Card>
                                <CardHeader>
                                    <CardTitle className="flex items-center gap-2 text-sm">
                                        <span className="w-5 h-5 rounded bg-surface-muted flex items-center justify-center">
                                            <ShieldCheck className="w-3 h-3 text-muted-foreground" />
                                        </span>
                                        Roles
                                    </CardTitle>
                                </CardHeader>
                                <CardContent>
                                    <div className="flex flex-wrap gap-2">
                                        {roles.length === 0 ? (
                                            <p className="text-xs text-muted-foreground">No roles available</p>
                                        ) : (
                                            roles.map((role) => {
                                                const active = selectedRoles.includes(role.name);
                                                return (
                                                    <button
                                                        key={role.id}
                                                        type="button"
                                                        onClick={() => toggleRole(role.name)}
                                                        className={`inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md text-xs font-medium transition-all border ${
                                                            active
                                                                ? 'bg-primary text-primary-foreground border-primary shadow-sm'
                                                                : 'bg-background text-muted-foreground border-border-subtle hover:border-primary/50 hover:text-foreground'
                                                        }`}
                                                    >
                                                        <ShieldCheck className="w-3 h-3" />
                                                        {role.name}
                                                    </button>
                                                );
                                            })
                                        )}
                                    </div>
                                    {errors.roles && <p className="text-xs text-destructive mt-1.5">{errors.roles}</p>}
                                </CardContent>
                            </Card>

                            {/* Security */}
                            <Card>
                                <CardHeader>
                                    <CardTitle className="flex items-center gap-2 text-sm">
                                        <span className="w-5 h-5 rounded bg-surface-muted flex items-center justify-center">
                                            <Lock className="w-3 h-3 text-muted-foreground" />
                                        </span>
                                        Security
                                    </CardTitle>
                                </CardHeader>
                                <CardContent>
                                    <Input
                                        label={isEditing ? 'New Password (optional)' : 'Password'}
                                        type="password"
                                        value={password}
                                        onChange={(e) => setPassword(e.target.value)}
                                        error={errors.password}
                                        required={!isEditing}
                                        placeholder={isEditing ? 'Leave blank to keep current' : 'Min. 8 characters'}
                                    />
                                    
                                    {canManageProtection && (
                                        <div className="pt-4 mt-4 border-t border-border-subtle space-y-4">
                                            <div className="flex items-center justify-between">
                                                <div>
                                                    <p className="text-sm font-medium">Verified Account</p>
                                                    <p className="text-xs text-muted-foreground">Grant the blue checkmark badge.</p>
                                                </div>
                                                <input
                                                    type="checkbox"
                                                    checked={isVerified}
                                                    onChange={(e) => setIsVerified(e.target.checked)}
                                                    className="w-4 h-4 rounded border-border-subtle accent-primary cursor-pointer"
                                                />
                                            </div>
                                            <div className="flex items-center justify-between">
                                                <div>
                                                    <p className="text-sm font-medium">Protected Account</p>
                                                    <p className="text-xs text-muted-foreground">Prevent account from being deleted.</p>
                                                </div>
                                                <input
                                                    type="checkbox"
                                                    checked={isProtected}
                                                    onChange={(e) => setIsProtected(e.target.checked)}
                                                    className="w-4 h-4 rounded border-border-subtle accent-primary cursor-pointer"
                                                    disabled={user?.is_primary_super_admin}
                                                />
                                            </div>
                                        </div>
                                    )}
                                </CardContent>
                            </Card>

                            {/* Meta (edit only) */}
                            {isEditing && user && (
                                <Card>
                                    <CardHeader>
                                        <CardTitle className="flex items-center gap-2 text-sm">
                                            <span className="w-5 h-5 rounded bg-surface-muted flex items-center justify-center">
                                                <CalendarDays className="w-3 h-3 text-muted-foreground" />
                                            </span>
                                            Info
                                        </CardTitle>
                                    </CardHeader>
                                    <CardContent className="space-y-2.5 text-sm">
                                        <div className="flex items-center gap-2.5 text-muted-foreground">
                                            <FileText className="w-4 h-4" />
                                            <span>{user.posts_count} posts</span>
                                        </div>
                                        <div className="flex items-center gap-2.5 text-muted-foreground">
                                            <CalendarDays className="w-4 h-4" />
                                            <span>Joined {new Date(user.created_at).toLocaleDateString()}</span>
                                        </div>
                                    </CardContent>
                                </Card>
                            )}

                            {/* Actions */}
                            <div className="flex items-center gap-2">
                                <Link
                                    href="/dashboard/users"
                                    className="ml-auto inline-flex items-center justify-center flex-1 h-9 px-4 border border-border-strong bg-background text-foreground rounded-md text-sm font-medium hover:bg-surface-muted transition-all shadow-sm"
                                >
                                    Cancel
                                </Link>
                                <Btn
                                    type="submit"
                                    loading={submitting}
                                    disabled={!isDirty || submitting}
                                    className="flex-1 h-9 px-4"
                                    icon={<Save className="w-4 h-4" />}
                                >
                                    {isEditing ? 'Update User' : 'Create User'}
                                </Btn>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </DashboardLayout>
    );
}
