<?php
namespace App\Contracts\Security;use App\Data\Security\FileScanResult;
interface FileScannerInterface{public function scannerKey():string;public function isAvailable():bool;public function scan(string $absolutePath):FileScanResult;}
