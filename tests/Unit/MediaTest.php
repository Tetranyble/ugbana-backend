<?php

namespace Tests\Unit;

use App\Enums\StorageProvider;
use App\Models\Media;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class MediaTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function it_has_a_description()
    {
        $description = $this->faker->sentence;

        $media = Media::factory()->create([
            'description' => $description,
        ]);

        $this->assertEquals($description, $media->description);
    }

    /** @test */
    public function it_has_a_attribution()
    {
        $attribution = $this->faker->sentence;

        $media = Media::factory()->create([
            'attribution' => $attribution,
        ]);

        $this->assertEquals($attribution, $media->attribution);
    }

    /** @test */
    public function it_has_a_size()
    {
        $size = $this->faker->randomFloat(2);

        $media = Media::factory()->create([
            'size' => $size,
        ]);

        $this->assertEquals($size, $media->size);
    }

    /** @test */
    public function it_has_a_path()
    {
        $path = $this->faker->image;

        $media = Media::factory()->create([
            'path' => $path,
        ]);

        $this->assertEquals($path, $media->path);
    }

    /** @test */
    public function it_has_a_mime_type()
    {
        $mime_type = $this->faker->sentence;

        $media = Media::factory()->create([
            'mime_type' => $mime_type,
        ]);

        $this->assertEquals($mime_type, $media->mime_type);
    }

    /** @test */
    public function it_has_a_current()
    {
        $current = $this->faker->randomElement([true, false]);

        $media = Media::factory()->create([
            'current' => $current,
        ]);

        $this->assertEquals($current, $media->current);
    }

    /** @test */
    public function it_has_a_disk()
    {
        $disk = StorageProvider::S3PRIVATE;

        $media = Media::factory()->create([
            'disk' => $disk,
        ]);

        $this->assertEquals($disk, $media->disk);
        $this->assertInstanceOf(StorageProvider::class, $media->disk);
    }
}
