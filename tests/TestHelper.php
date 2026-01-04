<?php

namespace Tests;

use Illuminate\Support\Facades\Artisan;

class TestHelper
{
    public static function runAllTests()
    {
        echo "\n🔥 EXECUTANDO TODOS OS TESTES PHPUNIT... 🔥\n\n";

        Artisan::call('test', [
            '--testsuite' => 'Unit',
        ]);

        Artisan::call('test', [
            '--testsuite' => 'Feature',
        ]);

        Artisan::call('test', [
            '--testsuite' => 'Security',
        ]);
    }
}
