@php
    $preset = \App\Services\SettingService::get('appearance.color_preset', 'green', $context ?? 'global');
    $primaryColor = \App\Services\SettingService::get('appearance.primary_color', null, $context ?? 'global');
    $secondaryColor = \App\Services\SettingService::get('appearance.secondary_color', null, $context ?? 'global');
    $accentColor = \App\Services\SettingService::get('appearance.accent_color', null, $context ?? 'global');
    $headingColor = \App\Services\SettingService::get('appearance.heading_color', null, $context ?? 'global');
    $bodyColor = \App\Services\SettingService::get('appearance.body_color', null, $context ?? 'global');
    $mutedColor = \App\Services\SettingService::get('appearance.muted_color', null, $context ?? 'global');

    if ($preset !== 'custom') {
        $css = \App\Values\ColorPreset::getPresetCss($preset);
    } elseif ($primaryColor || $secondaryColor) {
        $css = \App\Values\ColorPreset::customCss(
            $primaryColor ?? '#15803D',
            $secondaryColor ?? '#0F766E',
            $accentColor ?? '#D4A017',
            $headingColor ?? '#0F172A',
            $bodyColor ?? '#334155',
            $mutedColor ?? '#64748B',
        );
    } else {
        $css = null;
    }
@endphp
@if($css)
<style>
{!! $css !!}
</style>
@endif
