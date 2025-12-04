<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\ClamAVScanner;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Config;

class ClamAVScannerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        file_put_contents('dummy_file.pdf', 'dummy content');
    }

    protected function tearDown(): void
    {
        if (file_exists('dummy_file.pdf')) {
            unlink('dummy_file.pdf');
        }
        parent::tearDown();
    }

    public function test_scan_returns_true_when_clean()
    {
        Config::set('clamav.enabled', true);
        Config::set('clamav.path', 'clamscan');

        Process::fake([
            'clamscan --version' => Process::result('ClamAV 0.103.8'),
            'clamscan --no-summary *' => Process::result('', 0),
        ]);

        $scanner = new ClamAVScanner();
        $result = $scanner->scan('dummy_file.pdf');

        $this->assertTrue($result);
    }

    public function test_scan_returns_false_when_infected()
    {
        Config::set('clamav.enabled', true);
        Config::set('clamav.path', 'clamscan');

        Process::fake([
            '*' => Process::sequence()
                ->push(Process::result('ClamAV 0.103.8'))
                ->push(Process::result('dummy_file.pdf: Win.Test.EICAR_HDB-1 FOUND', 1)),
        ]);

        $scanner = new ClamAVScanner();
        $result = $scanner->scan('dummy_file.pdf');

        $this->assertFalse($result);
    }

    public function test_scan_bypassed_when_disabled()
    {
        Config::set('clamav.enabled', false);

        $scanner = new ClamAVScanner();
        $result = $scanner->scan('dummy_file.pdf');

        $this->assertTrue($result);
    }
}
