<?php

namespace Tests\Supports;

use App\Enums\StorageProvider;

trait WebServiceSupport
{
    public function token(StorageProvider $service = StorageProvider::GOOGLE)
    {
        if ($service === StorageProvider::GOOGLE) {
            return $this->google();
        }
    }

    private function google()
    {
        return [
            'access_token' => 'ya29.a0AWY7CklY2uT07yUSzdqXrl0wh6BFEh-LlHgW9Q1I3_MObbOdfMI64wTJS8XUMqHd2jafwqdtSfFhUhhdbCBsOJEHUIZNqrZICk5fiKaZg8RMtYQkWWtvubztIaNFTEZEkmknwmctLwQNOIASqlPBW7uNOaXIaCgYKAVcSARASFQG1tDrph_SNUSSSoBhC2o1vPl-h9A0163',
            'expires_in' => 3599,
            'refresh_token' => '1//03a4KwSNYtO7DCgYIARAAGAMSNwF-L9IryET-otKZ2EKVyLO0doZq1dmHSPzHC89FHoE1Rt5YVRt3aHBoGGuHHTg6cLzZqhnzwMo',
            'scope' => 'https://www.googleapis.com/auth/drive https://www.googleapis.com/auth/drive.file',
            'token_type' => 'Bearer',
            'created' => 1685705133,
        ];
    }
}
