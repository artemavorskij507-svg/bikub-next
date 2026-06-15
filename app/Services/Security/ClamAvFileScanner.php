<?php
namespace App\Services\Security;
use App\Contracts\Security\FileScannerInterface;use App\Data\Security\FileScanResult;use App\Settings\SecurityFileSettings;use Symfony\Component\Process\Process;
class ClamAvFileScanner implements FileScannerInterface {
 public function scannerKey():string{return 'clamav';}
 public function isAvailable():bool{return filled($this->binary());}
 public function scan(string $path):FileScanResult{if(!$this->isAvailable())return new FileScanResult('unavailable','clamav',message:'ClamAV binary is unavailable.');$p=new Process([$this->binary(),'--no-summary','--',$path]);$p->setTimeout(app(SecurityFileSettings::class)->clamav_timeout_seconds);try{$p->run();$out=trim($p->getOutput().$p->getErrorOutput());return match($p->getExitCode()){0=>new FileScanResult('clean','clamav',message:'ClamAV reported clean.'),1=>new FileScanResult('infected','clamav',signature:$this->signature($out),message:'ClamAV detected malware.'),default=>new FileScanResult('failed','clamav',message:'ClamAV scan failed.')};}catch(\Throwable){return new FileScanResult('failed','clamav',message:'ClamAV scan failed safely.');}}
 private function binary():?string{$configured=app(SecurityFileSettings::class)->clamav_binary_path;if($configured&&str_starts_with($configured,'/')&&is_executable($configured))return $configured;foreach(['/usr/bin/clamscan','/usr/local/bin/clamscan'] as $p)if(is_executable($p))return $p;return null;}
 private function signature(string $out):?string{return preg_match('/:\s*(.+)\s+FOUND$/m',$out,$m)?trim($m[1]):null;}
}
