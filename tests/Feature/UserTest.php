<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    public function testRegisterSuccess()
    {
        $this->post('/api/users', [
            'username' => 'marleess',
            'password' => 'rahasia',
            'name' => 'Marli',
        ])->assertStatus(201)
            ->assertJson([
                'data' => [
                    'username' => 'marleess',
                    'name' => 'Marli',
                ]
            ]);
    }

    public function testRegisterFailed()
    {
        $this->post('/api/users', [
            'username' => '',
            'password' => '',
            'name' => '',
        ])->assertStatus(400)
            ->assertJson([
                'error' => [
                    'username' => [
                        'The username field is required.'
                    ],
                    'password' => [
                        'The password field is required.'
                    ],
                    'name' => [
                        'The name field is required.'
                    ],
                ]
            ]);
    }

    public function testUsernameExist()
    {
        $this->testRegisterSuccess();
        $this->post('/api/users', [
            'username' => 'marleess',
            'password' => 'rahasia',
            'name' => 'Marli',
        ])->assertStatus(400)
            ->assertJson([
                'error' => [
                    'username' => [
                        'username already registered'
                    ],
                ]
            ]);
    }

    public function testLoginSuccess()
    {
        $this->seed([UserSeeder::class]);
        $this->post('/api/users/login', [
            'username' => 'test',
            'password' => 'test',
            'name' => 'test',
        ])->assertStatus(200)
            ->assertJson([
                'data' => [
                    'username' => 'test',
                    'name' => 'test',
                ]
            ]);

        $user = User::query()->where('username', 'test')->first();
        self::assertNotNull($user->token);
    }

    public function testLoginUsernameNotFound()
    {
        $this->post('/api/users/login', [
            'username' => 'test',
            'password' => 'test',
            'name' => 'test',
        ])->assertStatus(401)
            ->assertJson([
                'error' => [
                    'message' => [
                        'username or password wrong'
                    ]
                ]
            ]);
    }

    public function testLoginPasswordWrong()
    {
        $this->seed([UserSeeder::class]);
        $this->post('/api/users/login', [
            'username' => 'test',
            'password' => 'salah',
            'name' => 'test',
        ])->assertStatus(401)
            ->assertJson([
                'error' => [
                    'message' => [
                        'username or password wrong'
                    ]
                ]
            ]);
    }
}
