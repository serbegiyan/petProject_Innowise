<?php

namespace Tests;

use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        $this->clearConfigCache();

        parent::setUp();

        $this->withoutMiddleware(ValidateCsrfToken::class);
    }

    private function clearConfigCache(): void
    {
        $cacheDir = dirname(__DIR__).'/bootstrap/cache';

        foreach (['config.php', 'routes-v7.php'] as $file) {
            $path = $cacheDir.'/'.$file;

            if (is_file($path)) {
                unlink($path);
            }
        }
    }
}
