<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class AppSetting extends Model
{
    protected $table = 'app_settings';

    protected $fillable = ['key', 'value'];

    // ── Helpers ───────────────────────────────────────────────────────────────

    public static function get(string $key, mixed $default = null): mixed
    {
        $all = static::allCached();

        return $all[$key] ?? $default;
    }

    public static function set(string $key, mixed $value): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => $value === true ? '1' : ($value === false ? '0' : $value)]
        );

        Cache::forget('app_settings_all');
    }

    /** Guarda un array completo de clave → valor. */
    public static function setMany(array $data): void
    {
        foreach ($data as $key => $value) {
            static::set($key, $value);
        }
    }

    /** Devuelve todos los ajustes como array cacheado. */
    public static function allCached(): array
    {
        return Cache::remember('app_settings_all', 3600, function () {
            return static::pluck('value', 'key')->toArray();
        });
    }

    /** Fuerza recarga del caché y reconfigura Laravel. */
    public static function reloadConfig(): void
    {
        Cache::forget('app_settings_all');
        $settings = static::allCached();

        // ── Mail ──────────────────────────────────────────────────────────
        if (! empty($settings['mail_host'])) {
            config([
                'mail.default'                       => 'smtp',
                'mail.mailers.smtp.host'             => $settings['mail_host'],
                'mail.mailers.smtp.port'             => $settings['mail_port'] ?? 587,
                'mail.mailers.smtp.username'         => $settings['mail_username'] ?? null,
                'mail.mailers.smtp.password'         => $settings['mail_password'] ?? null,
                'mail.mailers.smtp.encryption'       => $settings['mail_encryption'] ?? 'tls',
                'mail.from.address'                  => $settings['mail_from_address'] ?? null,
                'mail.from.name'                     => $settings['mail_from_name'] ?? config('app.name'),
            ]);
        }

        // ── Twilio / WhatsApp ─────────────────────────────────────────────
        if (! empty($settings['twilio_sid'])) {
            config([
                'services.twilio.sid'            => $settings['twilio_sid'],
                'services.twilio.token'          => $settings['twilio_token'] ?? null,
                'services.twilio.whatsapp_from'  => $settings['twilio_whatsapp_from'] ?? null,
            ]);
        }
    }
}
