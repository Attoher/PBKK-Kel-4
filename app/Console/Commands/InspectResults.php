<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class InspectResults extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'results:inspect {filename}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Inspect a results JSON file from storage (results/ or private/results/) and print normalized structure.';

    public function handle()
    {
        $filename = $this->argument('filename');

        if (\Illuminate\Support\Str::endsWith($filename, ['_results.json', '.json'])) {
            $resultsFilename = $filename;
        } else {
            $resultsFilename = pathinfo($filename, PATHINFO_FILENAME) . '_results.json';
        }

        $paths = [
            'results/' . $resultsFilename,
            'private/results/' . $resultsFilename
        ];

        foreach ($paths as $p) {
            if (Storage::exists($p)) {
                $this->info("Found: $p");
                $content = Storage::get($p);
                $this->line($content);
                return 0;
            }
        }

        $this->error('File not found in results/ or private/results/: ' . $resultsFilename);
        return 1;
    }
}
