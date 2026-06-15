<?php
namespace App\Services\Security;use App\Contracts\Security\FileScannerInterface;use App\Data\Security\FileScanResult;
class DisabledFileScanner implements FileScannerInterface{public function scannerKey():string{return 'disabled';}public function isAvailable():bool{return false;}public function scan(string $absolutePath):FileScanResult{return new FileScanResult('unavailable','disabled',message:'Malware scanning is not configured.');}}
