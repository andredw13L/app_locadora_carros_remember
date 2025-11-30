<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected $user;
    
    protected function setUp(): void
    {
        parent::setUp(); 

        $this->user = User::factory()->create(); 
    }

    protected function authenticate(User $user)
    {
        $token = $user->createToken('test_token')->plainTextToken;

        return $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ]);
    }
}
