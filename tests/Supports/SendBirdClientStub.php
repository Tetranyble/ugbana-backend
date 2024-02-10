<?php

namespace Tests\Supports;

use App\Interfaces\SendBird;
use App\Models\User;

class SendBirdClientStub implements SendBird
{
    use ResponseFormater;

    public function createUser(User $user, string $method = 'POST', string $uri = 'v3/users')
    {
        return $this->formatResponse([

            'user_id' => '1',
            'nickname' => 'Eino Daugherty',
            'profile_url' => 'http://localhost:8000/storage/images/116c2708-ea98-483b-a9d0-6d85cdec4b2d-2023-08-14-11-41-55.jpeg',
            'require_auth_for_profile_image' => false,
            'metadata' => [
                'font_preference' => 'times new roman',
                'font_color' => 'black',
            ],
            'access_token' => '',
            'session_tokens' => [],
            'is_online' => false,
            'last_seen_at' => -1,
            'discovery_keys' => [
                'Eino',
                'Daugherty',
            ],
            'has_ever_logged_in' => false,
            'is_active' => true,
            'is_created' => true,
            'phone_number' => '',
        ]);
    }

    public function updateUser(User $user, string $method = 'PUT', string $uri = 'v3/users')
    {
        return $this->formatResponse([
            'user_id' => '1',
            'nickname' => 'Eino Daugherty',
            'profile_url' => 'http://localhost:8000/storage/images/116c2708-ea98-483b-a9d0-6d85cdec4b2d-2023-08-14-11-41-55.jpeg',
            'require_auth_for_profile_image' => false,
            'metadata' => [
                'font_color' => 'black',
                'font_preference' => 'times new roman',
            ],
            'access_token' => '',
            'created_at' => 1700486936,
            'discovery_keys' => [
                'Eino',
                'Daugherty',
            ],
            'is_hide_me_from_friends' => false,
            'is_shadow_blocked' => false,
            'session_tokens' => [],
            'phone_number' => '',
            'is_online' => false,
            'last_seen_at' => -1,
            'is_active' => true,
            'has_ever_logged_in' => false,
            'preferred_languages' => [],
            'locale' => '',
        ]);
    }

    public function deleteUser(int $userId, string $method = 'DELETE', string $uri = 'v3/users')
    {
        return $this->formatResponse([]);
    }
}
