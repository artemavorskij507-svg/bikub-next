<?php
namespace App\Settings;
use Spatie\LaravelSettings\Settings;
class SecurityFileSettings extends Settings {
 public string $security_file_scanner; public ?string $clamav_binary_path; public int $clamav_timeout_seconds; public bool $evidence_download_requires_clean_scan; public bool $evidence_download_override_enabled; public bool $evidence_retention_enabled; public bool $evidence_retention_dry_run_only; public ?int $evidence_retention_default_days;
 public static function group():string{return 'security_files';}
}
