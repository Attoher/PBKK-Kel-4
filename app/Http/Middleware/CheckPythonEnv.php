<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CheckPythonEnv
{
    /**
     * Ensure the configured Python executable is available and runnable.
     * If the check fails, return a 503 JSON response for API requests or
     * redirect back to the upload form for web requests with an explanatory message.
     */
    public function handle(Request $request, Closure $next)
    {
        $isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';

        // Prefer explicit env override
        $pythonExec = env('PYTHON_EXECUTABLE');

        // Fallback to common Windows Python path if not provided
        if (empty($pythonExec) && $isWindows) {
            $default = 'C:\\Users\\lenovo\\AppData\\Local\\Programs\\Python\\Python37\\python.exe';
            if (file_exists($default)) {
                $pythonExec = $default;
            }
        }

        if (empty($pythonExec)) {
            $pythonExec = 'python';
        }

        // Run a minimal python command to verify the interpreter runs.
        // Use exec to capture exit code.
        $cmd = escapeshellcmd($pythonExec) . ' -c ' . escapeshellarg("import sys; print('ok')");

        $output = [];
        $returnVar = 1;
        @exec($cmd . ' 2>&1', $output, $returnVar);

        if ($returnVar !== 0) {
            Log::warning('CheckPythonEnv: python check failed', ['cmd' => $cmd, 'output' => $output]);

            $message = 'Python environment not configured or not runnable. Set PYTHON_EXECUTABLE in .env or ensure Python is installed and available in PATH.';

            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Python environment not ready',
                    'message' => $message,
                ], 503);
            }

            // For web requests, redirect to upload form with suggestion
            return redirect()->route('upload.form')
                ->with('error', $message)
                ->with('suggestion', "Example: set PYTHON_EXECUTABLE=C:\\\\\\Users\\\\lenovo\\\\AppData\\\\Local\\\\Programs\\\\Python\\\\Python37\\\\python.exe");
        }

        return $next($request);
    }

    /**
     * Public helper so other parts of the application can check Python availability
     * without going through HTTP middleware flow.
     */
    public static function isPythonRunnable(): bool
    {
        $isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
        $pythonExec = env('PYTHON_EXECUTABLE');
        if (empty($pythonExec) && $isWindows) {
            $default = 'C:\\Users\\lenovo\\AppData\\Local\\Programs\\Python\\Python37\\python.exe';
            if (file_exists($default)) {
                $pythonExec = $default;
            }
        }
        if (empty($pythonExec)) {
            $pythonExec = 'python';
        }

        $cmd = escapeshellcmd($pythonExec) . ' -c ' . escapeshellarg("import sys; print('ok')");
        $output = [];
        $returnVar = 1;
        @exec($cmd . ' 2>&1', $output, $returnVar);

        return $returnVar === 0;
    }

    /**
     * Optional terminate method if post-response work is needed.
     */
    public function terminate($request, $response)
    {
        // No-op for now.
    }
}
