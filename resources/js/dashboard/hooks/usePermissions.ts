import { usePage } from '@inertiajs/react';

export function usePermissions() {
    const { auth } = usePage().props as any;
    const permissions: string[] = auth?.permissions || [];
    const roles: string[] = auth?.roles || [];
    const user = auth?.user || null;

    const hasPermission = (permission: string) => {
        if (!user) return false;
        
        // Handle wildcards (e.g., 'posts.*') or exact matches
        return permissions.some((p: string) => {
            if (p === permission) return true;
            
            // If user has a scoped permission (like posts.view.all), allow access when checking base permission (posts.view)
            if (p.startsWith(`${permission}.`)) return true;

            // If the permission string itself ends with .* (though unlikely in our new strict DB)
            if (p.endsWith('.*')) {
                const prefix = p.replace('.*', '');
                return permission.startsWith(prefix);
            }
            return false;
        });
    };

    const hasRole = (role: string) => {
        if (!user) return false;
        return roles.includes(role);
    };

    return {
        can: hasPermission,
        hasRole,
        permissions,
        roles,
    };
}
