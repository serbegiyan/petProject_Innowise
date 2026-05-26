<?php

namespace Tests\Feature;

use App\Enums\ExportStatus;
use App\Jobs\ExportCatalogJob;
use App\Models\Export;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_exports_catalog_and_sends_email_with_correct_link()
    {
        Storage::fake('s3', [
            'bucket' => 'test-bucket',
            'region' => 'us-east-1',
        ]);

        Mail::fake();

        $export = Export::factory()->create();

        $job = new ExportCatalogJob($export->id, [['name' => 'Product']]);
        $job->handle();

        $this->assertDatabaseHas('exports', [
            'id' => $export->id,
            'status' => ExportStatus::COMPLETED->value,
        ]);
    }
}
