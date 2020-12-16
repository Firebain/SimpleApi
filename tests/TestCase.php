<?php

namespace Tests;

use InvalidArgumentException;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function assertAuthenticationRequired($uri, $method = 'get')
    {
        $method = strtolower($method);
        if (!in_array($method, ['get', 'post', 'put', 'patch', 'delete'])) {
            throw new InvalidArgumentException('Invalid method: '.$method);
        }

        $method = "{$method}Json";
        $this
            ->$method($uri)
            ->assertUnauthorized();
    }
}