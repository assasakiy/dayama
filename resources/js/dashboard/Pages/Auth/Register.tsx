import React, { useState } from 'react';
import { router, Link } from '@inertiajs/react';
import { Mail, Lock, Eye, EyeOff, UserPlus, User } from 'lucide-react';
import { Btn } from '@dashboard/Components/ui/btn';

export default function Register() {
    const [name, setName] = useState('');
    const [email, setEmail] = useState('');
    const [password, setPassword] = useState('');
    const [password_confirmation, setPasswordConfirmation] = useState('');
    const [showPassword, setShowPassword] = useState(false);
    const [errors, setErrors] = useState<Record<string, string>>({});
    const [submitting, setSubmitting] = useState(false);

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        setSubmitting(true);
        router.post('/register', { name, email, password, password_confirmation }, {
            onError: (errs) => { setErrors(errs); setSubmitting(false); },
            onSuccess: () => { window.location.href = '/dashboard'; },
            onFinish: () => setSubmitting(false),
        });
    };

    return (
        <div className="min-h-screen flex items-center justify-center bg-gradient-to-br from-surface via-background to-surface p-4">
            <div className="absolute inset-0 bg-[radial-gradient(ellipse_at_top_right,_var(--color-primary-muted)_0%,_transparent_60%)] opacity-40" />
            <div className="absolute inset-0 bg-[radial-gradient(ellipse_at_bottom_left,_var(--color-primary-muted)_0%,_transparent_60%)] opacity-30" />

            <div className="w-full max-w-sm relative">
                <div className="bg-background border border-border-subtle rounded-lg shadow-elevated overflow-hidden">
                    <div className="h-1.5 bg-gradient-to-r from-primary/60 via-primary to-primary/60" />

                    <div className="p-8">
                        <div className="text-center mb-8">
                            <div className="w-12 h-12 rounded-xl bg-gradient-to-br from-primary to-primary/80 flex items-center justify-center text-primary-foreground font-bold text-xl mx-auto mb-4 shadow-sm">
                                M
                            </div>
                            <h1 className="text-xl font-semibold tracking-tight">Create an account</h1>
                            <p className="text-sm text-muted-foreground mt-1.5">Sign up for a new account</p>
                        </div>

                        <form onSubmit={submit} className="space-y-5">
                            <div>
                                <label htmlFor="name" className="block text-sm font-medium mb-1.5 text-foreground">Name</label>
                                <div className="relative">
                                    <User className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground pointer-events-none" />
                                    <input
                                        id="name"
                                        type="text"
                                        value={name}
                                        onChange={(e) => setName(e.target.value)}
                                        className="w-full h-10 pl-9 pr-3 text-sm bg-surface border border-border-subtle rounded-md outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-all placeholder:text-muted-foreground/50"
                                        placeholder="John Doe"
                                        required
                                        autoFocus
                                        autoComplete="name"
                                    />
                                </div>
                                {errors.name && <p className="text-xs text-danger mt-1.5 flex items-center gap-1">{errors.name}</p>}
                            </div>

                            <div>
                                <label htmlFor="email" className="block text-sm font-medium mb-1.5 text-foreground">Email</label>
                                <div className="relative">
                                    <Mail className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground pointer-events-none" />
                                    <input
                                        id="email"
                                        type="email"
                                        value={email}
                                        onChange={(e) => setEmail(e.target.value)}
                                        className="w-full h-10 pl-9 pr-3 text-sm bg-surface border border-border-subtle rounded-md outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-all placeholder:text-muted-foreground/50"
                                        placeholder="john@example.com"
                                        required
                                        autoComplete="email"
                                    />
                                </div>
                                {errors.email && <p className="text-xs text-danger mt-1.5 flex items-center gap-1">{errors.email}</p>}
                            </div>

                            <div>
                                <label htmlFor="password" className="block text-sm font-medium mb-1.5 text-foreground">Password</label>
                                <div className="relative">
                                    <Lock className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground pointer-events-none" />
                                    <input
                                        id="password"
                                        type={showPassword ? 'text' : 'password'}
                                        value={password}
                                        onChange={(e) => setPassword(e.target.value)}
                                        className="w-full h-10 pl-9 pr-10 text-sm bg-surface border border-border-subtle rounded-md outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-all placeholder:text-muted-foreground/50"
                                        placeholder="Create a password"
                                        required
                                        autoComplete="new-password"
                                    />
                                    <button
                                        type="button"
                                        onClick={() => setShowPassword(!showPassword)}
                                        className="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground transition-colors"
                                        tabIndex={-1}
                                    >
                                        {showPassword ? <EyeOff className="w-4 h-4" /> : <Eye className="w-4 h-4" />}
                                    </button>
                                </div>
                                {errors.password && <p className="text-xs text-danger mt-1.5 flex items-center gap-1">{errors.password}</p>}
                            </div>

                            <div>
                                <label htmlFor="password_confirmation" className="block text-sm font-medium mb-1.5 text-foreground">Confirm Password</label>
                                <div className="relative">
                                    <Lock className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground pointer-events-none" />
                                    <input
                                        id="password_confirmation"
                                        type={showPassword ? 'text' : 'password'}
                                        value={password_confirmation}
                                        onChange={(e) => setPasswordConfirmation(e.target.value)}
                                        className="w-full h-10 pl-9 pr-10 text-sm bg-surface border border-border-subtle rounded-md outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-all placeholder:text-muted-foreground/50"
                                        placeholder="Confirm your password"
                                        required
                                        autoComplete="new-password"
                                    />
                                </div>
                            </div>

                            <Btn
                                type="submit"
                                loading={submitting}
                                disabled={submitting}
                                className="w-full h-10"
                                icon={<UserPlus className="w-4 h-4" />}
                            >
                                {submitting ? 'Signing up...' : 'Sign up'}
                            </Btn>
                        </form>

                        <div className="mt-6 text-center text-sm text-muted-foreground">
                            Already have an account?{' '}
                            <Link href="/login" className="text-primary hover:underline transition-colors font-medium">
                                Sign in
                            </Link>
                        </div>
                    </div>
                </div>

                <p className="text-center text-xs text-muted-foreground mt-6">
                    &copy; {new Date().getFullYear()} ModernBlog. All rights reserved.
                </p>
            </div>
        </div>
    );
}
