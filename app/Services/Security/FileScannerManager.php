<?php
namespace App\Services\Security;use App\Contracts\Security\FileScannerInterface;
class FileScannerManager{public function resolve():FileScannerInterface{$selected=config('security.file_scanner','disabled');return $selected==='clamav'?app(ClamAvFileScanner::class):app(DisabledFileScanner::class);}public function status():array{$s=$this->resolve();return ['key'=>$s->scannerKey(),'available'=>$s->isAvailable(),'reason'=>$s->isAvailable()?'Scanner available.':'Malware scanning is not configured or unavailable.'];}}
