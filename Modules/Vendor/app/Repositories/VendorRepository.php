<?php

namespace Modules\Vendor\app\Repositories;

use App\Models\Attachment;
use App\Models\User;
use App\Models\Translation;
use App\Models\UserType;
use App\Services\UserService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Modules\Vendor\app\Interfaces\VendorInterface;
use Modules\Vendor\app\Models\Vendor;

class VendorRepository implements VendorInterface
{

    public function __construct(protected UserService $userService)
    {
        
    }
    public function getAllVendors(array $filters = [], int $perPage = 10)
    {
        $query = Vendor::with(['user', 'country', 'country.translations', 'activities', 'translations', 'commission']);

        // Search in translations or user email
        if (!empty($filters['search'])) {
            $searchTerm = $filters['search'];
            $query->where(function($q) use ($searchTerm) {
                $q->whereHas('translations', function($query) use ($searchTerm) {
                    $query->where('lang_key', 'name')
                          ->where('lang_value', 'like', '%' . $searchTerm . '%');
                })
                ->orWhereHas('user', function($query) use ($searchTerm) {
                    $query->where('email', 'like', '%' . $searchTerm . '%');
                });
            });
        }

        // Filter by active status
        if (isset($filters['active']) && $filters['active'] !== '') {
            $query->where('active', $filters['active']);
        }

        // Filter by country
        if (!empty($filters['country_id'])) {
            $query->where('country_id', $filters['country_id']);
        }

        // Filter by date range
        if (!empty($filters['created_date_from'])) {
            $query->whereDate('created_at', '>=', $filters['created_date_from']);
        }
        
        if (!empty($filters['created_date_to'])) {
            $query->whereDate('created_at', '<=', $filters['created_date_to']);
        }

        return $query->latest()->paginate($perPage);
    }

    public function getQuery(array $filters = [])
    {
        $query = Vendor::latest();

        // Search in translations or user email
        if (!empty($filters['search'])) {
            $searchTerm = $filters['search'];
            $query->where(function($q) use ($searchTerm) {
                $q->whereHas('translations', function($query) use ($searchTerm) {
                    $query->where('lang_key', 'name')
                          ->where('lang_value', 'like', '%' . $searchTerm . '%');
                })
                ->orWhereHas('user', function($query) use ($searchTerm) {
                    $query->where('email', 'like', '%' . $searchTerm . '%');
                });
            });
        }

        // Filter by active status
        if (isset($filters['active']) && $filters['active'] !== '') {
            $query->where('active', $filters['active']);
        }

        // Filter by country
        if (!empty($filters['country_id'])) {
            $query->where('country_id', $filters['country_id']);
        }

        // Filter by date range
        if (!empty($filters['created_date_from'])) {
            $query->whereDate('created_at', '>=', $filters['created_date_from']);
        }
        
        if (!empty($filters['created_date_to'])) {
            $query->whereDate('created_at', '<=', $filters['created_date_to']);
        }
        return $query;
    }

    public function getVendorById(int $id)
    {
        return Vendor::with([
            'user', 
            'country', 
            'country.translations', 
            'activities', 
            'activities.translations',
            'translations',
            'attachments',
            'attachments.translations',
            'logo',
            'banner',
            'commission'
        ])->findOrFail($id);
    }

    public function createVendor(array $data)
    {
        return DB::transaction(function () use ($data) {
            $userData = [
                'email' => $data['email'],
                'password' => $data['password'],
            ];
            $user = $this->userService->createVendorAccount($userData);

            // Create vendor
            $vendor = Vendor::create([
                'slug' => Str::uuid(),
                'user_id' => $user->id,
                'country_id' => $data['country_id'],
                'active' => $data['active'] ?? false,
            ]);
            // Handle logo upload
            if (isset($data['logo'])) {
                $logoPath = $data['logo']->store("vendors/$vendor->id/logo", 'public');
                $vendor->attachments()->create([
                    'path' => $logoPath,
                    'type' => 'logo',
                ]);
            }

            // Handle banner upload
            if (isset($data['banner'])) {
                $bannerPath = $data['banner']->store("vendors/$vendor->id/banner", 'public');
                $vendor->attachments()->create([
                    'path' => $bannerPath,
                    'type' => 'banner',
                ]);
            }


            // Sync activities (many-to-many relationship)
            if (!empty($data['activity_ids'])) {
                $vendor->activities()->sync($data['activity_ids']);
            }

            // Store commission
            if (isset($data['commission'])) {
                $vendor->commission()->create([
                    'commission' => $data['commission'],
                ]);
            }

            // Store translations
            $this->storeTranslations($vendor, $data);

            // Handle documents
            if (!empty($data['documents'])) {
                $this->storeDocuments($vendor, $data['documents']);
            }

            return $vendor;
        });
    }

    public function updateVendor(int $id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $vendor = Vendor::findOrFail($id);

            // Update user account (email and password)
            if ($vendor->user) {
                $userUpdateData = [];
                
                // Update email if provided
                if (!empty($data['email'])) {
                    $userUpdateData['email'] = $data['email'];
                }
                
                // Update password if provided
                if (!empty($data['password'])) {
                    $userUpdateData['password'] = Hash::make($data['password']);
                }
                
                // Update user if there's data to update
                if (!empty($userUpdateData)) {
                    $vendor->user->update($userUpdateData);
                }
            }

            // Handle logo upload
            if (isset($data['logo'])) {
                if ($vendor->logo) {
                    Storage::disk('public')->delete($vendor->logo->path);
                    $vendor->logo()->delete();
                }
                $logoPath = $data['logo']->store("vendors/$vendor->id/logo", 'public');
                $vendor->attachments()->create([
                    'path' => $logoPath,
                    'type' => 'logo',
                ]);
            }

            // Handle banner upload
            if (isset($data['banner'])) {
                if ($vendor->banner) {
                    Storage::disk('public')->delete($vendor->banner->path);
                    $vendor->banner()->delete();
                }
                $bannerPath = $data['banner']->store("vendors/$vendor->id/banner", 'public');
                $vendor->attachments()->create([
                    'path' => $bannerPath,
                    'type' => 'banner',
                ]);
            }

            // Update vendor
            $vendor->update([
                'country_id' => $data['country_id'],
                'active' => $data['active'] ?? false,
            ]);

            // Sync activities (many-to-many relationship)
            if (!empty($data['activity_ids'])) {
                $vendor->activities()->sync($data['activity_ids']);
            }

            // Update commission
            if (isset($data['commission'])) {
                $vendor->commission()->delete();
                $vendor->commission()->create([
                    'commission' => $data['commission'],
                ]);
            }

            // Update translations
            $this->storeTranslations($vendor, $data);

            // Handle documents
            if (!empty($data['documents'])) {
                $this->storeDocuments($vendor, $data['documents']);
            }

            return $vendor;
        });
    }

    public function deleteVendor(int $id)
    {
        return DB::transaction(function () use ($id) {
            $vendor = Vendor::with(['user', 'attachments'])->findOrFail($id);
            
            // Delete all attachments from storage
            foreach ($vendor->attachments as $attachment) {
                // Delete attachment translations
                $attachment->translations()->delete();
                
                // Delete attachment record
                $attachment->delete();
            }
            
            // Delete vendor translations
            $vendor->translations()->delete();
            
            // Detach activities (many-to-many)
            $vendor->activities()->detach();
            
            // Get user before deleting vendor
            $user = $vendor->user;
            
            // Delete vendor
            $vendor->delete();
            
            // Delete associated user account if exists
            if ($user) {
                $user->delete();
            }
            
            return true;
        });
    }

    /**
     * Store translations for vendor
     */
    protected function storeTranslations(Vendor $vendor, array $data)
    {
        // Delete existing translations
        $vendor->translations()->delete();

        // Handle translations array from form (translations[language_id][name/description])
        if (!empty($data['translations'])) {
            foreach ($data['translations'] as $languageId => $fields) {
                // Get language code from language ID
                $language = \App\Models\Language::find($languageId);
                if (!$language) {
                    continue;
                }

                // Store name translation
                if (!empty($fields['name'])) {
                    $vendor->translations()->create([
                        'lang_id' => $language->id,
                        'lang_key' => 'name',
                        'lang_value' => $fields['name'],
                    ]);
                }

                // Store description translation
                if (!empty($fields['description'])) {
                    $vendor->translations()->create([
                        'lang_id' => $language->id,
                        'lang_key' => 'description',
                        'lang_value' => $fields['description'],
                    ]);
                }

                // Store meta_title translation
                if (!empty($fields['meta_title'])) {
                    $vendor->translations()->create([
                        'lang_id' => $language->id,
                        'lang_key' => 'meta_title',
                        'lang_value' => $fields['meta_title'],
                    ]);
                }

                // Store meta_description translation
                if (!empty($fields['meta_description'])) {
                    $vendor->translations()->create([
                        'lang_id' => $language->id,
                        'lang_key' => 'meta_description',
                        'lang_value' => $fields['meta_description'],
                    ]);
                }

                // Store meta_keywords translation
                if (!empty($fields['meta_keywords'])) {
                    $vendor->translations()->create([
                        'lang_id' => $language->id,
                        'lang_key' => 'meta_keywords',
                        'lang_value' => $fields['meta_keywords'],
                    ]);
                }
            }
        }
    }

    /**
     * Store documents for vendor using Attachment model
     */
    protected function storeDocuments(Vendor $vendor, array $documents)
    {
        foreach ($documents as $documentData) {
            // Check if file exists
            if (empty($documentData['file'])) {
                continue;
            }
            
            $file = $documentData['file'];
            $filePath = $file->store("vendors/{$vendor->id}/documents", 'public');
            
            // Create the attachment
            $attachment = $vendor->attachments()->create([
                'type' => 'docs',
                'path' => $filePath,
            ]);
            
            // Store document name translations if they exist
            if (!empty($documentData['translations'])) {
                foreach ($documentData['translations'] as $languageId => $fields) {
                    // Get language code from language ID
                    $language = \App\Models\Language::find($languageId);
                    if (!$language || empty($fields['name'])) {
                        continue;
                    }
                    
                    // Store document name translation
                    $attachment->translations()->create([
                        'lang_id' => $language->id,
                        'lang_key' => 'name',
                        'lang_value' => $fields['name'],
                    ]);
                }
            }
        }
    }
}
