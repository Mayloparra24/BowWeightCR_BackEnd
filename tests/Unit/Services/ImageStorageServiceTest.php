<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Services\ImageStorageService;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ImageStorageServiceTest extends TestCase
{
    #[Test]
    public function it_uploads_an_image_to_the_configured_disk(): void
    {
        Storage::fake('local');

        $service = new ImageStorageService('local');
        $file = UploadedFile::fake()->image('bovino.jpg');

        $path = $service->upload($file, 'fotografias');

        $this->assertStringStartsWith('fotografias/', $path);
        $this->assertStringEndsWith('.jpg', $path);
        Storage::disk('local')->assertExists($path);
    }

    #[Test]
    public function it_returns_the_public_url_of_a_stored_image(): void
    {
        Storage::fake('local');

        $service = new ImageStorageService('local');
        $file = UploadedFile::fake()->image('bovino.jpg');
        $path = $service->upload($file);

        $url = $service->url($path);

        $this->assertStringContainsString($path, $url);
    }

    #[Test]
    public function it_deletes_an_existing_image(): void
    {
        Storage::fake('local');

        $service = new ImageStorageService('local');
        $file = UploadedFile::fake()->image('bovino.jpg');
        $path = $service->upload($file);

        $deleted = $service->delete($path);

        $this->assertTrue($deleted);
        Storage::disk('local')->assertMissing($path);
    }

    #[Test]
    public function it_returns_false_when_deleting_a_missing_image(): void
    {
        Storage::fake('local');

        $service = new ImageStorageService('local');

        $deleted = $service->delete('fotografias/missing.jpg');

        $this->assertFalse($deleted);
    }
}
