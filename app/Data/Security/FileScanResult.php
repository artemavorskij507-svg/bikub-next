<?php
namespace App\Data\Security;
class FileScanResult{public function __construct(public readonly string $status,public readonly string $engine,public readonly ?string $signature=null,public readonly ?string $message=null,public readonly ?string $rawOutput=null){}}
