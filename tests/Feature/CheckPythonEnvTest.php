<?php

namespace Tests\Feature;

use Tests\TestCase;

class CheckPythonEnvTest extends TestCase
{
    public function test_returns_503_when_python_not_runnable()
    {
        // Force an invalid python executable for this test
        putenv('PYTHON_EXECUTABLE=Z:\\this_path_does_not_exist\\python.exe');

        $response = $this->getJson(route('analyze.document', ['filename' => 'does-not-matter.pdf']));

        $response->assertStatus(503)
                 ->assertJsonFragment(['error' => 'Python environment not ready']);
    }
}
