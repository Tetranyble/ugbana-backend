<?php

namespace Tests\Supports;

trait ResponseFormater
{
    /**
     * Formats the response
     *
     * @return mixed
     */
    public function formatResponse(array $response)
    {
        return json_decode(json_encode($response));
    }
}
