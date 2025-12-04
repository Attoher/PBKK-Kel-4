<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Support\Facades\Process;

class ProcessTest extends TestCase
{
    public function test_process_fake_sequence_exit_code()
    {
        Process::fake([
            '*' => Process::sequence()
                ->push($m1 = Process::result(output: 'output1', exitCode: 0))
                ->push($m2 = Process::result(output: 'output2', exitCode: 1)),
        ]);
        dump($m1);
        dump($m2);

        $r1 = Process::run('cmd1');
        $this->assertEquals(0, $r1->exitCode());
        $this->assertEquals('output1', $r1->output());

        $r2 = Process::run('cmd2');
        dump($r2);
        dump($r2->exitCode());
        $this->assertEquals(1, $r2->exitCode());
        $this->assertEquals('output2', $r2->output());
    }
}
