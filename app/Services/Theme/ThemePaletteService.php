<?php

namespace App\Services\Theme;

use App\Models\ThemePaletteEvent;
use App\Models\User;
use App\Models\UserThemePreference;
use App\Settings\ThemePaletteSettings;
use Illuminate\Validation\ValidationException;

class ThemePaletteService
{
    public function __construct(private ThemePaletteSettings $settings) {}

    public function normalizeHex(?string $hex): ?string
    {
        $hex = strtolower(trim((string) $hex));
        return preg_match('/^#[0-9a-f]{6}$/', $hex) ? $hex : null;
    }

    public function getDefaultHex(): string { return $this->normalizeHex($this->settings->default_hex) ?? '#ff7591'; }
    public function getUserHex(User $user): ?string { return $this->normalizeHex($user->themePreference?->hex); }
    public function getEffectiveHex(?User $user): string { return $user && $this->getUserHex($user) ? $this->getUserHex($user) : $this->getDefaultHex(); }

    public function canUsePalette(?User $user, string $surface): bool
    {
        if (! $this->settings->enabled || ! in_array($surface, ['admin', 'account', 'worker', 'public'], true)) return false;
        if (! $this->settings->{'apply_'.$surface}) return false;
        if (! $user) return $surface === 'public';
        $matches = $user->hasAnyRole($this->settings->allowed_roles);
        return $this->settings->access_mode === 'deny' ? ! $matches : $matches;
    }

    public function saveUserHex(User $user, string $hex, ?User $actor = null): UserThemePreference
    {
        $hex = $this->normalizeHex($hex) ?? throw ValidationException::withMessages(['hex' => 'HEX must use #RRGGBB format.']);
        if (! $this->settings->allow_custom_hex) throw ValidationException::withMessages(['hex' => 'Custom HEX colors are disabled.']);
        $old = $user->themePreference?->hex;
        $preference = UserThemePreference::updateOrCreate(['user_id' => $user->id], ['hex' => $hex, 'source' => 'picker', 'updated_by_id' => ($actor ?? $user)->id]);
        $this->event($user, $actor ?? $user, 'saved', $old, $hex);
        return $preference;
    }

    public function resetUserHex(User $user, ?User $actor = null): void
    {
        $old = $user->themePreference?->hex;
        UserThemePreference::where('user_id', $user->id)->delete();
        $this->event($user, $actor ?? $user, 'reset', $old, $this->getDefaultHex());
    }

    public function getConfigForUser(?User $user, string $surface): array
    {
        $allowed = $this->canUsePalette($user, $surface);
        return [
            'enabled' => $this->settings->enabled && $this->settings->{'apply_'.$surface},
            'allowed' => $allowed,
            'effectiveHex' => $allowed ? $this->getEffectiveHex($user) : $this->getDefaultHex(),
            'defaultHex' => $this->getDefaultHex(),
            'allowCustomHex' => $allowed && $this->settings->allow_custom_hex,
            'allowPresets' => $allowed && $this->settings->allow_presets,
            'surface' => $surface,
        ];
    }

    public function getRgb(string $hex): array { $h = substr($this->normalizeHex($hex) ?? $this->getDefaultHex(), 1); return [hexdec(substr($h,0,2)), hexdec(substr($h,2,2)), hexdec(substr($h,4,2))]; }
    public function getContrastColor(string $hex): string { [$r,$g,$b] = $this->getRgb($hex); return (($r*299+$g*587+$b*114)/1000) > 155 ? '#07111f' : '#ffffff'; }
    public function getAccent2(string $hex): string { [$r,$g,$b] = $this->getRgb($hex); $f = (($r+$g+$b)/3) < 120 ? 1.22 : .78; return sprintf('#%02x%02x%02x', min(255,$r*$f), min(255,$g*$f), min(255,$b*$f)); }

    private function event(User $user, User $actor, string $type, ?string $from, ?string $to): void
    {
        ThemePaletteEvent::create(['user_id'=>$user->id,'actor_id'=>$actor->id,'event_type'=>$type,'from_hex'=>$from,'to_hex'=>$to,'metadata'=>['source'=>'theme_palette'],'created_at'=>now()]);
        activity('theme_palette')->causedBy($actor)->performedOn($user)->withProperties(['event_type'=>$type,'from_hex'=>$from,'to_hex'=>$to])->log('theme_palette.'.$type);
    }
}
