<?php

namespace Tests\Feature\Profile;

use App\Models\User;
use App\Services\FileSystem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfilePhotoUpdateTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_profile_photo_may_be_update(): void
    {
        $filesystem = new FileSystem();
        Storage::fake($filesystem->disk->value);

        $user = User::factory()->create();

        $file = UploadedFile::fake()->image('avatar.jpg');
        $response = $this->actingAs($user, 'api')
            ->postJson(route('v1.users.profile-photo'), [
                'photo' => $file,
            ]);

        // Assert one or more files were stored...
        Storage::disk($filesystem->disk->value)->assertExists('photo');
        $this->assertDatabaseHas('media', [
            'mediable_type' => get_class($user),
            'mediable_id' => $user->id,
        ]);
    }
}
