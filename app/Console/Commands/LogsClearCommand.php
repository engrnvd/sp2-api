<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Traits\HasLogs;
use Illuminate\Support\Facades\File;

class LogsClearCommand extends Command
{
    use HasLogs;
    private $logDir = 'logs-clear';
    private $threshold;
    private $removeEmptyDir;
    protected $signature = 'logs:clear';
    protected $description = 'The command used to remove old log files';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->threshold = env('LOGS_CLEAR_THRESHOLD', 20);
        $this->removeEmptyDir = env('LOGS_REMOVE_EMPTY_DIR', true);
        $this->clearDir(storage_path("/logs"));
    }

    private function clearDir($dir)
    {
        foreach (new \DirectoryIterator($dir) as $fileInfo) {
            if ($fileInfo->isDot() || $fileInfo->getFilename() == '.gitignore') continue;
            $path = $fileInfo->getRealPath();
            if ($fileInfo->isFile()) {
                if ($fileInfo->getMTime() < (time() - $this->threshold * 24 * 3600)) {
                    $this->log("Removing file: {$path}");
                    @unlink($path);
                }
            } else {
                $this->clearDir($path);
                if ($this->removeEmptyDir && count(File::allFiles($path)) === 0) {
                    $this->log("Removing directory: {$path}");
                    rmdir($path);
                }
            }
        }
    }
}
