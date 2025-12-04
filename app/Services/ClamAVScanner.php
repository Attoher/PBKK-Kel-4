<?php

namespace App\Services;

use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Log;

class ClamAVScanner
{
    protected $enabled;
    protected $clamscanPath;

    public function __construct()
    {
        $this->enabled = config('clamav.enabled', false); // Default to false for local dev
        $this->clamscanPath = config('clamav.path', 'clamscan');
    }

    /**
     * Scan a file for viruses.
     *
     * @param string $filePath
     * @return bool True if clean, False if infected or error
     * @throws \Exception
     */
    public function scan(string $filePath): bool
    {
        // Bypass if disabled (e.g. for local dev without ClamAV)
        if (!$this->enabled) {
            Log::info("ClamAV scan skipped for: {$filePath} (Disabled in config)");
            return true;
        }

        if (!file_exists($filePath)) {
            throw new \Exception("File not found for scanning: {$filePath}");
        }

        // Check if clamscan is available
        $versionResult = Process::run("{$this->clamscanPath} --version");

        if ($versionResult->failed()) {
            Log::warning("ClamAV binary not found or not working. Skipping scan.", [
                'error' => $versionResult->errorOutput()
            ]);
            return true;
        }

        // Run the scan
        // --no-summary to keep output clean
        // --infected to only print infected files
        $command = "{$this->clamscanPath} --no-summary {$filePath}";
        $result = Process::run($command);

        // Exit code 0: No virus found
        // Exit code 1: Virus found
        // Exit code 2: Error

        if ($result->exitCode() === 0) {
            Log::info("ClamAV scan clean: {$filePath}");
            return true;
        } elseif ($result->exitCode() === 1) {
            Log::warning("ClamAV virus detected in: {$filePath}", ['output' => $result->output()]);
            return false;
        } else {
            Log::error("ClamAV scan error: {$filePath}", ['error' => $result->errorOutput()]);
            throw new \Exception("ClamAV scan failed to execute correctly.");
        }
    }
}
