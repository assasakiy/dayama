import React from 'react';
import { Head, Link } from '@inertiajs/react';
import DashboardLayout from '@dashboard/Layouts/DashboardLayout';
import {
    ArrowLeft,
    Pencil,
    Mail,
    Globe,
    CalendarDays,
    FileText,
    ShieldCheck,
    ExternalLink,
    BadgeCheck,
    User as UserIcon,
} from 'lucide-react';
import { Card, CardHeader, CardTitle, CardContent } from '@dashboard/Components/ui/card';
import { Button } from '@dashboard/Components/ui/button';

interface ShowUserData {
    id: string;
    name: string;
    email: string;
    username: string;
    avatar_url: string | null;
    biography: string | null;
    website: string | null;
    social_links: { github?: string; twitter?: string; linkedin?: string } | null;
    posts_count: number;
    created_at: string;
    roles: { id: string; name: string; color: string }[];
    email_verified_at: string | null;
    is_verified: boolean;
    updated_at: string;
}

interface Props {
    user: ShowUserData;
}

export default function UserShow({ user }: Props) {
    const avatarInitial = user.name.charAt(0).toUpperCase();
    const joinedDate = new Date(user.created_at).toLocaleDateString('en-US', {
        year: 'numeric', month: 'long', day: 'numeric',
    });

    const socialLinks = [
        { key: 'github', href: user.social_links?.github, label: 'GitHub' },
        { key: 'twitter', href: user.social_links?.twitter, label: 'Twitter' },
        { key: 'linkedin', href: user.social_links?.linkedin, label: 'LinkedIn' },
    ];

    return (
        <DashboardLayout>
            <Head title={user.name} />
            <div className="space-y-5">
                {/* Header */}
                <div className="flex items-center justify-end md:justify-between w-full">
                    <div className="flex items-center gap-3">
                        <Link
                            href="/dashboard/users"
                            className="p-1.5 rounded-md text-muted-foreground hover:text-foreground hover:bg-surface-muted transition-colors"
                        >
                            <ArrowLeft className="w-4 h-4" />
                        </Link>
                        <div className="hidden md:block">
                            <h1 className="text-xl font-semibold tracking-tight">{user.name}</h1>
                            <p className="hidden lg:block text-sm text-muted-foreground mt-0.5">@{user.username}</p>
                        </div>
                    </div>
                    <Link
                        href={`/dashboard/users/${user.id}/edit`}
                        className="inline-flex items-center gap-2 h-9 px-4 bg-primary text-primary-foreground rounded-md text-sm font-medium hover:bg-primary/90 transition-all shadow-sm"
                    >
                        <Pencil className="w-4 h-4" />
                        Edit User
                    </Link>
                </div>

                {/* Profile grid */}
                <div className="grid grid-cols-1 lg:grid-cols-3 gap-5">
                    {/* Left - Profile Card */}
                    <div>
                        <Card>
                            <CardContent className="pt-6">
                                <div className="flex flex-col items-center text-center">
                                    <div className="w-20 h-20 rounded-full bg-gradient-to-br from-primary to-primary/80 flex items-center justify-center text-primary-foreground text-2xl font-bold shadow-md mb-4">
                                        {user.avatar_url ? (
                                            <img src={user.avatar_url} alt={user.name} className="w-full h-full rounded-full object-cover" />
                                        ) : (
                                            avatarInitial
                                        )}
                                    </div>
                                    <h2 className="text-lg font-semibold flex items-center justify-center gap-1.5">
                                        {user.name}
                                        {user.is_verified && <BadgeCheck className="w-5 h-5 text-primary fill-primary/10" />}
                                    </h2>
                                    <p className="text-sm text-muted-foreground">@{user.username}</p>
                                    <div className="flex flex-wrap justify-center gap-1.5 mt-3">
                                        {user.roles.map((role) => (
                                            <span
                                                key={role.id}
                                                className="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary-muted text-primary"
                                                style={role.color ? { backgroundColor: `${role.color}15`, color: role.color } : {}}
                                            >
                                                <ShieldCheck className="w-3 h-3" />
                                                {role.name}
                                            </span>
                                        ))}
                                    </div>
                                    {user.biography && (
                                        <p className="text-sm text-muted-foreground mt-4 max-w-xs leading-relaxed">{user.biography}</p>
                                    )}
                                </div>
                            </CardContent>
                        </Card>

                        {/* Quick stats */}
                        <div className="grid grid-cols-2 gap-3 mt-5">
                            <Card>
                                <CardContent className="pt-4 pb-3 text-center">
                                    <FileText className="w-5 h-5 text-primary mx-auto mb-1.5" />
                                    <p className="text-lg font-semibold">{user.posts_count}</p>
                                    <p className="text-xs text-muted-foreground">Posts</p>
                                </CardContent>
                            </Card>
                            <Card>
                                <CardContent className="pt-4 pb-3 text-center">
                                    <BadgeCheck className={`w-5 h-5 mx-auto mb-1.5 ${user.is_verified ? 'text-primary' : 'text-muted-foreground/40'}`} />
                                    <p className="text-lg font-semibold">{user.is_verified ? 'Yes' : 'No'}</p>
                                    <p className="text-xs text-muted-foreground">Verified</p>
                                </CardContent>
                            </Card>
                        </div>
                    </div>

                    {/* Right - Info cards */}
                    <div className="lg:col-span-2 space-y-5">
                        {/* Contact info */}
                        <Card>
                            <CardHeader>
                                <CardTitle className="flex items-center gap-2 text-sm">
                                    <span className="w-5 h-5 rounded bg-surface-muted flex items-center justify-center">
                                        <Mail className="w-3 h-3 text-muted-foreground" />
                                    </span>
                                    Contact Information
                                </CardTitle>
                            </CardHeader>
                            <CardContent className="space-y-3">
                                <div className="flex items-center gap-3 text-sm">
                                    <div className="w-8 h-8 rounded-lg bg-surface-muted flex items-center justify-center shrink-0">
                                        <Mail className="w-4 h-4 text-muted-foreground" />
                                    </div>
                                    <div>
                                        <p className="text-xs text-muted-foreground">Email</p>
                                        <a href={`mailto:${user.email}`} className="font-medium hover:text-primary transition-colors">{user.email}</a>
                                    </div>
                                </div>
                                {user.website && (
                                    <div className="flex items-center gap-3 text-sm">
                                        <div className="w-8 h-8 rounded-lg bg-surface-muted flex items-center justify-center shrink-0">
                                            <Globe className="w-4 h-4 text-muted-foreground" />
                                        </div>
                                        <div>
                                            <p className="text-xs text-muted-foreground">Website</p>
                                            <a href={user.website} target="_blank" rel="noopener noreferrer" className="font-medium hover:text-primary transition-colors">
                                                {new URL(user.website).hostname}
                                            </a>
                                        </div>
                                    </div>
                                )}
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
                            <CardContent>
                            {socialLinks.some((s) => s.href) ? (
                                <div className="space-y-3">
                                    {socialLinks.map((social) =>
                                        social.href ? (
                                            <div key={social.key} className="flex items-center gap-3 text-sm">
                                                <div className="w-8 h-8 rounded-lg bg-surface-muted flex items-center justify-center shrink-0">
                                                    <ExternalLink className="w-4 h-4 text-muted-foreground" />
                                                </div>
                                                <div>
                                                    <p className="text-xs text-muted-foreground">{social.label}</p>
                                                    <a href={social.href} target="_blank" rel="noopener noreferrer" className="font-medium hover:text-primary transition-colors">
                                                        {social.href.replace(/^https?:\/\//, '')}
                                                    </a>
                                                </div>
                                            </div>
                                        ) : null
                                    )}
                                </div>
                            ) : (
                                <p className="text-sm text-muted-foreground">No social links configured.</p>
                            )}
                            </CardContent>
                        </Card>

                        {/* Account meta */}
                        <Card>
                            <CardHeader>
                                <CardTitle className="flex items-center gap-2 text-sm">
                                    <span className="w-5 h-5 rounded bg-surface-muted flex items-center justify-center">
                                        <CalendarDays className="w-3 h-3 text-muted-foreground" />
                                    </span>
                                    Account
                                </CardTitle>
                            </CardHeader>
                            <CardContent className="space-y-3">
                                <div className="flex items-center gap-3 text-sm">
                                    <div className="w-8 h-8 rounded-lg bg-surface-muted flex items-center justify-center shrink-0">
                                        <CalendarDays className="w-4 h-4 text-muted-foreground" />
                                    </div>
                                    <div>
                                        <p className="text-xs text-muted-foreground">Member since</p>
                                        <p className="font-medium">{joinedDate}</p>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>
                    </div>
                </div>
            </div>
        </DashboardLayout>
    );
}
