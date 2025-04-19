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
                        'Username already registered'
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
                        'Username or password wrong'
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
                        'Username or password wrong'
                    ]
                ]
            ]);
    }

    public function testGetCurrentUserSuccess()
    {
        $this->seed(UserSeeder::class);

        $this->get('/api/users/current', [
            'Authorization' => 'test'
        ])->assertStatus(200)
            ->assertJson([
                'data' => [
                    'username' => 'test',
                    'token' => 'test'
                ]
            ]);
    }

    public function testGetCurrentUserUnauthorized()
    {
        $this->seed(UserSeeder::class);

        $this->get('/api/users/current')
            ->assertStatus(401)
            ->assertJson([
                'error' => [
                    'message' => [
                        'Unauthorized'
                    ]
                ]
            ]);
    }

    public function testGetCurrentUserInvalidToken()
    {
        $this->seed(UserSeeder::class);

        $this->get('/api/users/current', [
            'Authorization' => 'salah'
        ])->assertStatus(401)
            ->assertJson([
                'error' => [
                    'message' => [
                        'Unauthorized'
                    ]
                ]
            ]);
    }

    public function testUpdatePasswordSuccess()
    {
        $this->seed(UserSeeder::class);
        $oldUser = User::where('username', 'test')->first();

        $this->patch('/api/users/current',
            [
                'password' => 'newPassword1',
            ],
            [
                'Authorization' => 'test'
            ]
        )->assertStatus(200)
            ->assertJson([
                'data' => [
                    'username' => 'test',
                    'token' => 'test'
                ]
            ]);

        $newUser = User::where('username', 'test')->first();
        self::assertNotEquals($oldUser->password, $newUser->password);
    }

    public function testUpdateNameSuccess()
    {
        $this->seed(UserSeeder::class);
        $oldUser = User::where('username', 'test')->first();

        $this->patch('/api/users/current',
            [
                'name' => 'Laravel',
            ],
            [
                'Authorization' => 'test'
            ]
        )->assertStatus(200)
            ->assertJson([
                'data' => [
                    'username' => 'test',
                    'token' => 'test'
                ]
            ]);

        $newUser = User::where('username', 'test')->first();
        self::assertNotEquals($oldUser->name, $newUser->name);
    }

    public function testUpdateFailed()
    {
        $this->seed(UserSeeder::class);

        $this->patch('/api/users/current',
            [
                'name' => "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.",
            ],
            [
                'Authorization' => 'test'
            ]
        )->assertStatus(400)
            ->assertJson([
                'error' => [
                    "name" => [
                        "The name field must not be greater than 100 characters."
                    ]
                ]
            ]);
    }

    public function testLogoutSuccess()
    {
        $this->seed([UserSeeder::class]);

        $this->delete(uri: '/api/users/logout', headers: [
            'Authorization' => 'test'
        ])->assertStatus(200)
            ->assertJson([
                'data' => true
            ]);

        $user = User::where('username', 'test')->first();
        self::assertNull($user->token);
    }

    public function testLogoutFailed()
    {
        $this->seed([UserSeeder::class]);

        $this->delete('/api/users/logout', [
            'Authorization' => 'salah'
        ])->assertStatus(401)
            ->assertJson([
                'error' => [
                    'message' => [
                        'Unauthorized'
                    ]
                ]
            ]);
    }
}
