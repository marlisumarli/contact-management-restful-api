<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Contact;
use Database\Seeders\AddressSeeder;
use Database\Seeders\ContactSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AddressTest extends TestCase
{
    public function testCreateSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->post("/api/contacts/$contact->id/addresses",
            [
                'street' => 'Jalan',
                'city' => 'Jakarta',
                'province' => 'DKI Jakarta',
                'country' => 'Indonesia',
                'postal_code' => '2312',
            ],
            [
                'Authorization' => 'test'
            ]
        )->assertStatus(201)
            ->assertJson([
                'data' => [
                    'street' => 'Jalan',
                    'city' => 'Jakarta',
                    'province' => 'DKI Jakarta',
                    'country' => 'Indonesia',
                    'postal_code' => '2312',
                ]
            ]);
    }

    public function testCreateFailed()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->post("/api/contacts/$contact->id/addresses",
            [
                'street' => '',
                'city' => '',
                'province' => '',
                'country' => '',
                'postal_code' => '',
            ],
            [
                'Authorization' => 'test'
            ]
        )->assertStatus(400)
            ->assertJson([
                'error' => [
                    'country' => [
                        'The country field is required.'
                    ],
                ]
            ]);
    }

    public function testCreateNotFound()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();


        $this->post('/api/contacts/' . $contact->id + 1 . '/addresses',
            [
                'street' => 'Jalan',
                'city' => 'Jakarta',
                'province' => 'DKI Jakarta',
                'country' => 'Indonesia',
                'postal_code' => '2312',
            ],
            [
                'Authorization' => 'test'
            ]
        )->assertStatus(404)
            ->assertJson([
                'error' => [
                    'message' => [
                        'Not found'
                    ]
                ]
            ]);
    }

    public function testGetSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);;
        $address = Address::query()->limit(1)->first();

        $this->get("/api/contacts/$address->contact_id/addresses/$address->id", [
            'Authorization' => 'test'
        ])->assertStatus(200)
            ->assertJson([
                'data' => [
                    'street' => 'Jalan',
                    'city' => 'Jakarta',
                    'province' => 'DKI Jakarta',
                    'country' => 'Indonesia',
                    'postal_code' => '2312',
                ]
            ]);
    }

    public function testGetNF()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);;
        $address = Address::query()->limit(1)->first();

        $this->get("/api/contacts/$address->contact_id/addresses/" . ($address->id + 1), [
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

    public function testUpdateSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);;
        $address = Address::query()->limit(1)->first();
        $this->put("/api/contacts/$address->contact_id/addresses/$address->id",
            [
                'street' => 'Jalan',
                'city' => 'Bekasi',
                'province' => 'Jawa Barat',
                'country' => 'Indonesia',
                'postal_code' => '0000',
            ],
            [
                'Authorization' => 'test'
            ]
        )->assertStatus(200)
            ->assertJson([
                'data' => [
                    'street' => 'Jalan',
                    'city' => 'Bekasi',
                    'province' => 'Jawa Barat',
                    'country' => 'Indonesia',
                    'postal_code' => '0000',
                ]
            ]);
    }

    public function testUpdateFailed()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);;
        $address = Address::query()->limit(1)->first();
        $this->put("/api/contacts/$address->contact_id/addresses/$address->id",
            [
                'street' => 'Jalan',
                'city' => 'Bekasi',
                'province' => 'Jawa Barat',
                'country' => '',
                'postal_code' => '0000',
            ],
            [
                'Authorization' => 'test'
            ]
        )->assertStatus(400)
            ->assertJson([
                'error' => [
                    'country' => [
                        'The country field is required.'
                    ]
                ]
            ]);
    }

    public function testUpdateNF()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);;
        $address = Address::query()->limit(1)->first();
        $this->put("/api/contacts/$address->contact_id/addresses/" . ($address->id + 1),
            [
                'street' => 'Jalan',
                'city' => 'Bekasi',
                'province' => 'Jawa Barat',
                'country' => 'Indonesia',
                'postal_code' => '0000',
            ],
            [
                'Authorization' => 'test'
            ]
        )->assertStatus(404)
            ->assertJson([
                'error' => [
                    'message' => [
                        'Not found'
                    ]
                ]
            ]);
    }

    public function testDeleteSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);;
        $address = Address::query()->limit(1)->first();
        $this->delete("/api/contacts/$address->contact_id/addresses/$address->id", [],
            [
                'Authorization' => 'test'
            ]
        )->assertStatus(200)
            ->assertJson([
                'data' => true
            ]);
    }

    public function testDeleteNF()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);;
        $address = Address::query()->limit(1)->first();
        $this->delete("/api/contacts/$address->contact_id/addresses/" . ($address->id + 1), [],
            [
                'Authorization' => 'test'
            ]
        )->assertStatus(404)
            ->assertJson([
                'error' => [
                    'message' => [
                        'Not found'
                    ]
                ]
            ]);
    }

    public function testListSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);;
        $contact = Contact::query()->limit(1)->first();
        $this->get("/api/contacts/$contact->id/addresses", [
            'Authorization' => 'test'
        ])->assertStatus(200)
            ->assertJson([
                'data' => [
                    [
                        'street' => 'Jalan',
                        'city' => 'Jakarta',
                        'province' => 'DKI Jakarta',
                        'country' => 'Indonesia',
                        'postal_code' => '2312',
                    ]
                ]
            ]);
    }

    public function testListNF()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);;
        $contact = Contact::query()->limit(1)->first();
        $this->get("/api/contacts/" . ($contact->id + 1) . "/addresses", [
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
}
