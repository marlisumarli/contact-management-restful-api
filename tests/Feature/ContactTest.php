<?php

namespace Tests\Feature;

use App\Models\Contact;
use Database\Seeders\ContactSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ContactTest extends TestCase
{
    public function testCreateSuccess()
    {
        $this->seed([UserSeeder::class]);

        $this->post('/api/contacts',
            [
                'first_name' => 'Marli',
                'last_name' => 'Sumarli',
                'email' => 'gg.marlisumarli@gmail.com',
                'phone' => '6283872453682'
            ],
            [
                'Authorization' => 'test'
            ]
        )->assertStatus(201)
            ->assertJson([
                'data' => [
                    'first_name' => 'Marli',
                    'last_name' => 'Sumarli',
                    'email' => 'gg.marlisumarli@gmail.com',
                    'phone' => '6283872453682'
                ]
            ]);
    }

    public function testCreateFailed()
    {
        $this->seed([UserSeeder::class]);

        $this->post('/api/contacts',
            [
                'first_name' => '',
                'last_name' => 'Sumarli',
                'email' => '123haha',
                'phone' => ''
            ],
            [
                'Authorization' => 'test'
            ]
        )->assertStatus(400)
            ->assertJson([
                'error' => [
                    'first_name' => [
                        'The first name field is required.'
                    ],
                    'email' => [
                        'The email field must be a valid email address.'
                    ]
                ]
            ]);
    }

    public function testCreateUnauthorized()
    {
        $this->seed([UserSeeder::class]);

        $this->post('/api/contacts',
            [
                'first_name' => '',
                'last_name' => 'Sumarli',
                'email' => '123haha',
                'phone' => ''
            ],
            [
                'Authorization' => 'salah'
            ]
        )->assertStatus(401)
            ->assertJson([
                'error' => [
                    'message' => [
                        'Unauthorized'
                    ]
                ]
            ]);
    }

    public function testContactDetailSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);

        $contact = Contact::query()->limit(1)->first();

        $this->get("/api/contacts/$contact->id", [
            'Authorization' => 'test'
        ])->assertStatus(200)
            ->assertJson([
                'data' => [
                    'first_name' => 'test',
                    'last_name' => 'test',
                    'email' => 'test@gmail.com',
                    'phone' => '1234567890',
                ]
            ]);
    }

    public function testContactDetailNotFound()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);

        $contact = Contact::query()->limit(1)->first();

        $this->get('/api/contacts/' . ($contact->id + 1), [
            'Authorization' => 'test'
        ])->assertStatus(404)
            ->assertJson([
                'error' => [
                    'message' => [
                        'Not found'
                    ]
                ]
            ]);
    }

    public function testContactUnauthorized()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);

        $contact = Contact::query()->limit(1)->first();

        $this->get('/api/contacts/' . ($contact->id), [
            'Authorization' => 'attacker'
        ])->assertStatus(404)
            ->assertJson([
                'error' => [
                    'message' => [
                        'Not found'
                    ]
                ]
            ]);
    }
}
