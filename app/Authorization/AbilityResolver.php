<?php

namespace App\Authorization;

use Illuminate\Support\Str;

class AbilityResolver
{
    private array $cache = [];

    public function resolve(string $ability, mixed $target = null): AbilityResolution
    {
        $cacheKey = $this->getCacheKey($ability, $target);

        if (isset($this->cache[$cacheKey])) {
            return $this->cache[$cacheKey];
        }

        $resolution = $this->buildResolution($ability, $target);
        $this->cache[$cacheKey] = $resolution;

        return $resolution;
    }

    private function getCacheKey(string $ability, mixed $target): string
    {
        $targetName = is_object($target) ? get_class($target) : (is_string($target) ? $target : '');
        return "{$ability}:{$targetName}";
    }

    private function buildResolution(string $ability, mixed $target): AbilityResolution
    {
        // Example: 'dashboard.view' with no target
        if (!$target) {
            return new AbilityResolution(
                action: $ability,
                basePermission: $ability
            );
        }

        // Example: 'update' with App\Models\Post
        $targetClass = is_object($target) ? get_class($target) : $target;
        $resource = Str::plural(Str::snake(class_basename($targetClass))); // 'posts'

        // Map standard abilities
        $mappedAbility = match ($ability) {
            'index', 'show', 'viewAny', 'view' => 'view',
            'store', 'create' => 'create',
            'edit', 'update' => 'edit',
            'destroy', 'delete' => 'delete',
            default => $ability,
        };

        // For actions like create, there is usually no own/all distinction, just resource.create
        if ($mappedAbility === 'create') {
            return new AbilityResolution(
                action: $mappedAbility,
                resource: $resource,
                basePermission: "{$resource}.create"
            );
        }

        return new AbilityResolution(
            action: $mappedAbility,
            resource: $resource,
            ownPermission: "{$resource}.{$mappedAbility}.own",
            allPermission: "{$resource}.{$mappedAbility}.all",
            basePermission: "{$resource}.{$mappedAbility}"
        );
    }
}
