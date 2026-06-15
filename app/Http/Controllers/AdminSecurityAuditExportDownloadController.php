<?php
namespace App\Http\Controllers;
use App\Models\SecurityAuditExport;use App\Services\Security\SecurityAuditExportService;
class AdminSecurityAuditExportDownloadController extends Controller{public function __invoke(SecurityAuditExport $export,SecurityAuditExportService $service){return $service->downloadExport($export,auth()->user());}}
