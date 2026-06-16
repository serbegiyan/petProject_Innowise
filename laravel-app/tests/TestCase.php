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
        $cachedConfig = dirname(__DIR__).'/bootstrap/cache/config.php';

        if (is_file($cachedConfig)) {
            unlink($cachedConfig);
        }
    }
}
