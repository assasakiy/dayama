<?php

declare(strict_types=1);

namespace App\Values;

/**
 * Color system engine — defines 3 presets (green, orange, blue) + custom.
 *
 * Each preset provides a full set of CSS custom properties for light & dark mode,
 * covering brand colors, text colors, and their derived variants.
 */
final class ColorPreset
{
    public const GREEN = 'green';
    public const ORANGE = 'orange';
    public const BLUE = 'blue';
    public const CUSTOM = 'custom';

    public const ALL = [self::GREEN, self::ORANGE, self::BLUE, self::CUSTOM];

    private const PRESETS = [
        self::GREEN => [
            'label' => 'Hijau Alami',
            'light' => [
                'primary'              => '#15803D',
                'primary-foreground'   => '#FFFFFF',
                'primary-muted'        => '#F0FDF4',
                'primary-border'       => '#86EFAC',
                'primary-hover'        => '#166534',
                'primary-active'       => '#14532D',
                'secondary'            => '#0F766E',
                'secondary-foreground' => '#FFFFFF',
                'secondary-hover'      => '#115E59',
                'secondary-active'     => '#134E4A',
                'accent'               => '#D4A017',
                'accent-foreground'    => '#422006',
                'accent-hover'         => '#B79318',
                'foreground'           => '#0F172A',
                'muted-foreground'     => '#334155',
                'subtle-foreground'    => '#64748B',
            ],
            'dark' => [
                'primary'              => '#4ADE80',
                'primary-foreground'   => '#052E16',
                'primary-muted'        => '#052E16',
                'primary-border'       => '#166534',
                'primary-hover'        => '#22C55E',
                'primary-active'       => '#16A34A',
                'secondary'            => '#2DD4BF',
                'secondary-foreground' => '#042F2E',
                'secondary-hover'      => '#14B8A6',
                'secondary-active'     => '#0D9488',
                'accent'               => '#FCD34D',
                'accent-foreground'    => '#451A03',
                'accent-hover'         => '#FBBF24',
                'foreground'           => '#F8FAFC',
                'muted-foreground'     => '#CBD5E1',
                'subtle-foreground'    => '#94A3B8',
            ],
        ],
        self::ORANGE => [
            'label' => 'Orange Hangat',
            'light' => [
                'primary'              => '#EA580C',
                'primary-foreground'   => '#FFFFFF',
                'primary-muted'        => '#FFF7ED',
                'primary-border'       => '#FDBA74',
                'primary-hover'        => '#C2410C',
                'primary-active'       => '#9A3412',
                'secondary'            => '#D97706',
                'secondary-foreground' => '#FFFFFF',
                'secondary-hover'      => '#B45309',
                'secondary-active'     => '#92400E',
                'accent'               => '#0891B2',
                'accent-foreground'    => '#164E63',
                'accent-hover'         => '#0E7490',
                'foreground'           => '#1C1917',
                'muted-foreground'     => '#44403C',
                'subtle-foreground'    => '#78716C',
            ],
            'dark' => [
                'primary'              => '#FB923C',
                'primary-foreground'   => '#1A0A00',
                'primary-muted'        => '#1A0A00',
                'primary-border'       => '#9A3412',
                'primary-hover'        => '#F97316',
                'primary-active'       => '#EA580C',
                'secondary'            => '#FBBF24',
                'secondary-foreground' => '#1C0F00',
                'secondary-hover'      => '#F59E0B',
                'secondary-active'     => '#D97706',
                'accent'               => '#22D3EE',
                'accent-foreground'    => '#083344',
                'accent-hover'         => '#06B6D4',
                'foreground'           => '#FAFAF9',
                'muted-foreground'     => '#D6D3D1',
                'subtle-foreground'    => '#A8A29E',
            ],
        ],
        self::BLUE => [
            'label' => 'Biru Profesional',
            'light' => [
                'primary'              => '#2563EB',
                'primary-foreground'   => '#FFFFFF',
                'primary-muted'        => '#EFF6FF',
                'primary-border'       => '#93C5FD',
                'primary-hover'        => '#1D4ED8',
                'primary-active'       => '#1E40AF',
                'secondary'            => '#7C3AED',
                'secondary-foreground' => '#FFFFFF',
                'secondary-hover'      => '#6D28D9',
                'secondary-active'     => '#5B21B6',
                'accent'               => '#059669',
                'accent-foreground'    => '#022C22',
                'accent-hover'         => '#047857',
                'foreground'           => '#0F172A',
                'muted-foreground'     => '#334155',
                'subtle-foreground'    => '#64748B',
            ],
            'dark' => [
                'primary'              => '#60A5FA',
                'primary-foreground'   => '#0C1F3F',
                'primary-muted'        => '#0C1F3F',
                'primary-border'       => '#1E40AF',
                'primary-hover'        => '#3B82F6',
                'primary-active'       => '#2563EB',
                'secondary'            => '#A78BFA',
                'secondary-foreground' => '#1E0A3C',
                'secondary-hover'      => '#8B5CF6',
                'secondary-active'     => '#7C3AED',
                'accent'               => '#34D399',
                'accent-foreground'    => '#022C22',
                'accent-hover'         => '#10B981',
                'foreground'           => '#F8FAFC',
                'muted-foreground'     => '#CBD5E1',
                'subtle-foreground'    => '#94A3B8',
            ],
        ],
    ];

    /**
     * Generate a complete <style> block with all CSS custom properties
     * for the given preset. Returns null for 'custom' mode.
     */
    public static function getPresetCss(string $preset): ?string
    {
        if ($preset === self::CUSTOM) {
            return null;
        }

        $data = self::PRESETS[$preset] ?? self::PRESETS[self::GREEN];

        $css = ":root {\n";
        foreach ($data['light'] as $var => $hex) {
            $css .= "  --color-{$var}: {$hex};\n";
        }
        $css .= "}\n\n";

        $css .= ".dark {\n";
        foreach ($data['dark'] as $var => $hex) {
            $css .= "  --color-{$var}: {$hex};\n";
        }
        $css .= "}\n";

        return $css;
    }

    /**
     * Generate CSS for custom mode using individual hex colors.
     * Uses color-mix() to derive hover/active/muted/border variants.
     */
    public static function customCss(
        string $primary,
        string $secondary,
        string $accent,
        string $heading,
        string $body,
        string $muted,
    ): string {
        return <<<CSS
:root {
  --color-primary: {$primary};
  --color-primary-foreground: #FFFFFF;
  --color-primary-muted: color-mix(in srgb, {$primary}, white 88%);
  --color-primary-border: color-mix(in srgb, {$primary}, white 60%);
  --color-primary-hover: color-mix(in srgb, {$primary}, black 15%);
  --color-primary-active: color-mix(in srgb, {$primary}, black 25%);
  --color-secondary: {$secondary};
  --color-secondary-foreground: #FFFFFF;
  --color-secondary-hover: color-mix(in srgb, {$secondary}, black 15%);
  --color-secondary-active: color-mix(in srgb, {$secondary}, black 25%);
  --color-accent: {$accent};
  --color-accent-foreground: color-mix(in srgb, {$accent}, black 60%);
  --color-accent-hover: color-mix(in srgb, {$accent}, black 15%);
  --color-foreground: {$heading};
  --color-muted-foreground: {$body};
  --color-subtle-foreground: {$muted};
}
.dark {
  --color-primary: color-mix(in srgb, {$primary}, white 40%);
  --color-primary-foreground: color-mix(in srgb, {$primary}, black 80%);
  --color-primary-muted: color-mix(in srgb, {$primary}, black 80%);
  --color-primary-border: color-mix(in srgb, {$primary}, black 50%);
  --color-primary-hover: color-mix(in srgb, {$primary}, white 25%);
  --color-primary-active: color-mix(in srgb, {$primary}, white 10%);
  --color-secondary: color-mix(in srgb, {$secondary}, white 40%);
  --color-secondary-foreground: color-mix(in srgb, {$secondary}, black 80%);
  --color-secondary-hover: color-mix(in srgb, {$secondary}, white 25%);
  --color-secondary-active: color-mix(in srgb, {$secondary}, white 10%);
  --color-accent: color-mix(in srgb, {$accent}, white 40%);
  --color-accent-foreground: color-mix(in srgb, {$accent}, black 60%);
  --color-accent-hover: color-mix(in srgb, {$accent}, white 25%);
  --color-foreground: color-mix(in srgb, {$heading}, white 85%);
  --color-muted-foreground: color-mix(in srgb, {$body}, white 60%);
  --color-subtle-foreground: {$muted};
}
CSS;
    }

    /**
     * Get preset colors as [light => [...], dark => [...]].
     */
    public static function getPresetColors(string $preset): ?array
    {
        return self::PRESETS[$preset] ?? null;
    }

    /**
     * Get the base hex colors for a preset (for UI auto-fill).
     * @return array{primary: string, secondary: string, accent: string, heading: string, body: string, muted: string}
     */
    public static function getBaseColors(string $preset): array
    {
        $data = self::PRESETS[$preset] ?? self::PRESETS[self::GREEN];
        return [
            'primary'   => $data['light']['primary'],
            'secondary' => $data['light']['secondary'],
            'accent'    => $data['light']['accent'],
            'heading'   => $data['light']['foreground'],
            'body'      => $data['light']['muted-foreground'],
            'muted'     => $data['light']['subtle-foreground'],
        ];
    }

    /**
     * List all presets for UI selectors.
     * @return array<int, array{value: string, label: string}>
     */
    public static function options(): array
    {
        $opts = [];
        foreach (self::PRESETS as $value => $data) {
            $opts[] = ['value' => $value, 'label' => $data['label']];
        }
        $opts[] = ['value' => self::CUSTOM, 'label' => 'Kustom'];
        return $opts;
    }
}
