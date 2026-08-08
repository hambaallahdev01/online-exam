<?php

namespace Tests\Unit;

use App\Services\MediaUploadService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;
use Tests\TestCase;

class MediaUploadServiceTest extends TestCase
{
    public function test_image_is_resized_proportionally_within_1024x1024()
    {
        Storage::fake('s3');
        config(['filesystems.default' => 's3']);

        // Create a fake large image 2000x1000
        $file = UploadedFile::fake()->image('large_photo.jpg', 2000, 1000);

        $url = MediaUploadService::upload($file, 'questions');

        $this->assertNotEmpty($url);

        // Verify file was saved in s3 fake storage
        $files = Storage::disk('s3')->allFiles('questions');
        $this->assertCount(1, $files);

        // Verify image dimensions after resize
        $path = Storage::disk('s3')->path($files[0]);
        list($w, $h) = getimagesize($path);

        $this->assertLessThanOrEqual(1024, $w);
        $this->assertLessThanOrEqual(1024, $h);
        $this->assertEquals(1024, $w);
        $this->assertEquals(512, $h);
    }

    public function test_video_uploads_are_prohibited()
    {
        $videoFile = UploadedFile::fake()->create('sample_video.mp4', 1000, 'video/mp4');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Direct video file uploads are disabled');

        MediaUploadService::upload($videoFile, 'questions');
    }

    public function test_pdf_exceeding_5mb_is_rejected()
    {
        // 6MB PDF file (6144 KB)
        $largePdf = UploadedFile::fake()->create('large_doc.pdf', 6144, 'application/pdf');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('PDF document size exceeds the maximum limit of 5MB');

        MediaUploadService::upload($largePdf, 'questions');
    }

    public function test_valid_pdf_under_5mb_is_allowed()
    {
        Storage::fake('s3');
        config(['filesystems.default' => 's3']);

        // 2MB PDF file (2048 KB)
        $validPdf = UploadedFile::fake()->create('valid_doc.pdf', 2048, 'application/pdf');

        $url = MediaUploadService::upload($validPdf, 'questions');

        $this->assertNotEmpty($url);
        $this->assertCount(1, Storage::disk('s3')->allFiles('questions'));
    }
}
