<?php

namespace Tests\Feature;

use App\Enums\ExportStatus;
use App\Jobs\ExportCatalogJob;
use App\Models\Export;
use App\Models\Product;
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

        Product::factory()->create([
            'name' => 'Test Product',
            'price' => 99.5,
            'brand' => 'TestBrand',
        ]);

        $job = new ExportCatalogJob($export->id);
        $job->handle();

        $this->assertDatabaseHas('exports', [
            'id' => $export->id,
            'status' => ExportStatus::COMPLETED->value,
        ]);

        Storage::disk('s3')->assertExists($export->fresh()->file_path);
    }
}
