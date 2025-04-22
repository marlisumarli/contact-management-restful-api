<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddressCreateRequest;
use App\Http\Resources\AddressResource;
use App\Models\Address;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    private function getContactRefactor(User $user, int $idContact): Contact
    {
        $contact = Contact::where('user_id', $user->id)->where('id', $idContact)->first();
        if (!$contact) {
            throw new HttpResponseException(response()->json([
                'error' => [
                    'message' => [
                        'Not found'
                    ]
                ]
            ])->setStatusCode(404));
        }

        return $contact;
    }

    private function getAddressRefactor(Contact $contact, int $idAddress): Address{
        $address = Address::where('contact_id', $contact->id)->where('id', $idAddress)->first();
        if (!$address) {
            throw new HttpResponseException(response()->json([
                'error' => [
                    'message' => [
                        'Not found'
                    ]
                ]
            ])->setStatusCode(404));
        }

        return $address;
    }

    public function createAddress(int $idContact, AddressCreateRequest $request): JsonResponse
    {
        $user = Auth::user();
        $contact = $this->getContactRefactor($user, $idContact);

        $data = $request->validated();
        $address = new Address($data);
        $address->contact_id = $contact->id;
        $address->save();

        return (new AddressResource($address))->response()->setStatusCode(201);
    }

    public function getAddress(int $idContact, int $idAddress): AddressResource
    {
        $user = Auth::user();
        $contact = $this->getContactRefactor($user, $idContact);
        $address = $this->getAddressRefactor($contact, $idAddress);

        return new AddressResource($address);
    }

    public function updateAddress(int $idContact, int $idAddress, AddressCreateRequest $request): AddressResource
    {
        $user = Auth::user();
        $contact = $this->getContactRefactor($user, $idContact);
        $address = $this->getAddressRefactor($contact, $idAddress);

        $data = $request->validated();
        $address->fill($data);
        $address->save();

        return new AddressResource($address);
    }

    public function deleteAddress(int $idContact, int $idAddress): JsonResponse
    {
        $user = Auth::user();
        $contact = $this->getContactRefactor($user, $idContact);
        $address = $this->getAddressRefactor($contact, $idAddress);

        $address->delete();
        return response()->json([
            'data' => true
        ])->setStatusCode(200);
    }

    public  function listAddress(int $idContact): JsonResponse
    {
        $user = Auth::user();
        $contact = $this->getContactRefactor($user, $idContact);

        $addresses = Address::where('contact_id', $contact->id)->get();
        return (AddressResource::collection($addresses))->response()->setStatusCode(200);
    }
}
