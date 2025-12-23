<?php

namespace App\Repositories;

use App\Actions\UserAction;
use App\Interfaces\UserInterface;
use App\Models\User;
use App\Models\UserType;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserRepository implements UserInterface {

    public function __construct(public UserAction $userAction)
    {

    }

    public function login($request) {
        return $this->userAction->login($request);
    }

    public function forgetPassword($request) {
        return $this->userAction->forgetPassword($request);
    }

    public function resetPassword($request, $user) {
        return $this->userAction->resetPassword($request, $user);
    }

    public function logout() {
        return $this->userAction->logout();
    }
    

    public function createVendorAccount($data) {
        // Create user account
        $user = User::create([
            'uuid' => Str::uuid(),
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'user_type_id' => UserType::VENDOR_TYPE, // Vendor type,
            'active' => $data['active'],
            'image' => $data['image'] ?? null,
        ]);
        
        // Store name translations if provided
        if (!empty($data['translations'])) {
            foreach ($data['translations'] as $languageId => $fields) {
                $language = \App\Models\Language::find($languageId);
                if (!$language || empty($fields['name'])) {
                    continue;
                }
                
                $user->translations()->updateOrCreate(
                    [
                        'lang_id' => $language->id,
                        'lang_key' => 'name',
                    ],
                    [
                        'lang_value' => $fields['name'],
                    ]
                );
            }
        }
        
        return $user;
    }
    public function updateVendorAccount($data) {
        $user = User::find($data['id']);
        
        $updateData = [];
        
        // Only update fields that are present in the data array
        if (isset($data['email'])) {
            $updateData['email'] = $data['email'];
        }
        
        if (isset($data['password'])) {
            $updateData['password'] = $data['password']; // Already hashed in VendorRepository
        }
        
        if (isset($data['active'])) {
            $updateData['active'] = $data['active'];
        }
        
        if (isset($data['image'])) {
            $updateData['image'] = $data['image'];
        }
        
        if (!empty($updateData)) {
            $user->update($updateData);
        }
        
        // Update name translations if provided
        if (!empty($data['translations'])) {
            foreach ($data['translations'] as $languageId => $fields) {
                $language = \App\Models\Language::find($languageId);
                if (!$language || empty($fields['name'])) {
                    continue;
                }
                
                $user->translations()->updateOrCreate(
                    [
                        'lang_id' => $language->id,
                        'lang_key' => 'name',
                    ],
                    [
                        'lang_value' => $fields['name'],
                    ]
                );
            }
        }
        
        return $user;
    }


}
