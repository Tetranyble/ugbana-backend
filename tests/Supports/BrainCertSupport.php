<?php

namespace Tests\Supports;

trait BrainCertSupport
{
    use ResponseFormater;

    protected string $key;

    protected string $endpoint;

    protected function prepare()
    {
        $this->key = config('services.braincert.key');
        $this->endpoint = config('services.braincert.url');

        return $this;
    }

    public function scheduleResponse()
    {
        return $this->formatResponse([
            'status' => 'ok',
            'method' => 'addclass',
            'class_id' => 1681320,
            'title' => 'Integrated Science',
        ]);
    }

    public function requestData()
    {
        $now = now()->addHours(3);

        return [
            'title' => 'Integrated Science',
            'date' => $now->format('Y-m-d'),
            'start_time' => $now->format('g:i A'),
            'end_time' => $now->addHours(1)->format('g:i A'),
            'description' => 'JSS 3 Integrated Science',
        ];
    }

    public function meetingUrl()
    {
        return $this->formatResponse([
            'status' => 'ok',
            'class_id' => '1683249',
            'method' => 'getclasslaunch',
            'launchurl' => 'https://classroom.verited.com/html5/build/whiteboard.php?token=wC54KMcnESwHLyu0ox7V--2FeMGnaj19cudm7vr0LYiRMuxR90n1u5UNAFsWGPRGOWXr81B53swO2tmk8f0mGkPFyffm8zSeH7suztpMkh0DrjoGGqsjk9v7VNmmErnqD5t4ZV5LKU1nWX1czxHQPBDYDWsM3--2FibQc5n3xAp2nlXcMeU0uH69IOBU66--2FcgyYELd20ez3aZvOb4c3zlJzOeigfaP--2BWEfGwufDoHsH9OKxxAzq0pdI3xRw5zr1RND1PnV',
            'encryptedlaunchurl' => 'https://classroom.verited.com/html5/build/whiteboard.php?token=wC54KMcnESwHLyu0ox7V--2FeMGnaj19cudm7vr0LYiRMuxR90n1u5UNAFsWGPRGOWXr81B53swO2tmk8f0mGkPFyffm8zSeH7suztpMkh0DrjoGGqsjk9v7VNmmErnqD5t4ZV5LKU1nWX1czxHQPBDYDWsM3--2FibQc5n3xAp2nlXcMeU0uH69IOBU66--2FcgyYELd20ez3aZvOb4c3zlJzOeigfaP--2BWEfGwufDoHsH9OKxxAzq0pdI3xRw5zr1RND1PnV',
        ]);
    }
}
