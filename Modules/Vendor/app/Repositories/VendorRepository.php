<?php

namespace Modules\Vendor\app\Repositories;

use App\Services\UserService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Modules\Vendor\app\Interfaces\VendorInterface;
use Modules\Vendor\app\Models\Vendor;

class VendorRepository implements VendorInterface
{

    public function __construct(
        protected UserService $userService,
    )
    {

    }
    public function getAllVendors(array $filters = [], int $perPage = 10)
    {
        $query = Vendor::with(['user', 'country', 'country.translations', 'activities', 'translations'])
        ->filter($filters);
        return ($perPage == 0) ?  $query->get() : $query->latest()->paginate($perPage);
    }

    public function getQuery(array $filters = [])
    {
        $query = Vendor::latest()->filter($filters);
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
            'documents',
            'documents.translations'
        ])->findOrFail($id);
    }

    public function createVendor(array $data)
    {
        return DB::transaction(function () use ($data) {
            // $role = rand(0, 9999);
            $userData = [
                'slug' => \Str::uuid(),
                'email' => $data['email'],
                'password' => $data['password'],
                'active' => $data['active'] ?? false,
            ];
            $user = $this->userService->createVendorAccount($userData);
            // if(isset($role)) {
            //     $user->roles()->sync([$role]);
            // }

            // Create vendor with temporary slug
            $vendor = Vendor::create([
                'user_id' => $user->id,
                'type' => $data['type'] ?? 'product',
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
                $userUpdateData = [
                    'id' => $vendor->user->id,
                ];
                // Update password if provided
                if (isset($data['password']) && !empty($data['password'])) {
                    $userUpdateData['password'] = Hash::make($data['password']);
                }
                // Update active status if provided
                if (isset($data['active'])) {
                    $userUpdateData['active'] = $data['active'];
                }
                // Update email if provided
                if (isset($data['email']) && !empty($data['email'])) {
                    $userUpdateData['email'] = $data['email'];
                }
                // Update user if there's data to update
                $this->userService->updateVendorAccount($userUpdateData);
                // $role = rand(0, 9999);
                // $vendor->user->roles()->sync([$role]);
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
                'type' => $data['type'] ?? 'product',
                'active' => $data['active'] ?? false,
            ]);

            // Sync activities (many-to-many relationship)
            if (!empty($data['activity_ids'])) {
                $vendor->activities()->sync($data['activity_ids']);
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

    public function canDeleteVendor(int $id): array
    {
        $productCount = DB::table('products')->where('vendor_id', $id)->count();

        if ($productCount > 0) {
            return [
                'can_delete' => false,
                'reason' => "Cannot delete vendor. This vendor has {$productCount} associated products. Please delete or reassign the products first.",
                'product_count' => $productCount
            ];
        }

        return ['can_delete' => true];
    }

    public function deleteVendor(int $id)
    {
        return DB::transaction(function () use ($id) {
            $vendor = Vendor::with(['user', 'attachments'])->findOrFail($id);

            // Get user before deleting vendor
            $user = $vendor->user;

            // Check if vendor can be deleted
            $canDelete = $this->canDeleteVendor($id);
            if (!$canDelete['can_delete']) {
                throw new \Exception($canDelete['reason']);
            }

            // Delete all attachments and their files
            foreach ($vendor->attachments as $attachment) {
                // Delete physical file if it exists
                if ($attachment->path && Storage::disk('public')->exists($attachment->path)) {
                    Storage::disk('public')->delete($attachment->path);
                }

                // Delete attachment translations (hard delete)
                $attachment->translations()->delete();

                // Force delete attachment record (bypass soft delete)
                $attachment->forceDelete();
            }

            // Delete vendor translations (hard delete - translations don't use soft delete)
            $vendor->translations()->delete();

            // Detach activities (many-to-many) - must be done before vendor deletion
            $vendor->activities()->detach();


            // Force delete vendor (bypass soft delete) to avoid foreign key constraint issues
            $vendor->forceDelete();

            // Delete associated user account if exists
            if ($user) {
                // Detach user roles first
                $user->roles()->detach();
                $user->forceDelete();
            }

            return true;
        });
    }

    /**
     * Store translations for vendor
     */
    protected function storeTranslations(Vendor $vendor, array $data)
    {
        // Handle translations array from form (translations[language_id][name/description])
        if (!empty($data['translations'])) {
            foreach ($data['translations'] as $languageId => $fields) {
                // Get language code from language ID
                $language = \App\Models\Language::find($languageId);
                if (!$language) {
                    continue;
                }

                // Update or create name translation
                if (!empty($fields['name'])) {
                    if($language->code == 'en') {
                        // $originalSlug = $slug;
                        if(Vendor::where('slug', Str::slug($fields['name']))->exists()) {
                            $model = Vendor::where('slug', Str::slug($fields['name']))->first();
                            $vendor->update([
                                'slug' => $model->slug . '-' . rand(1, 1000)
                            ]);

                        } else {
                            $vendor->update([
                                'slug' => Str::slug($fields['name'])
                            ]);
                        }
                    }

                    $vendor->translations()->updateOrCreate(
                        [
                            'lang_id' => $language->id,
                            'lang_key' => 'name',
                        ],
                        [
                            'lang_value' => $fields['name'],
                        ]
                    );
                }

                // Update or create description translation
                if (!empty($fields['description'])) {
                    $vendor->translations()->updateOrCreate(
                        [
                            'lang_id' => $language->id,
                            'lang_key' => 'description',
                        ],
                        [
                            'lang_value' => $fields['description'],
                        ]
                    );
                }

                // Update or create meta_title translation
                if (!empty($fields['meta_title'])) {
                    $vendor->translations()->updateOrCreate(
                        [
                            'lang_id' => $language->id,
                            'lang_key' => 'meta_title',
                        ],
                        [
                            'lang_value' => $fields['meta_title'],
                        ]
                    );
                }

                // Update or create meta_description translation
                if (!empty($fields['meta_description'])) {
                    $vendor->translations()->updateOrCreate(
                        [
                            'lang_id' => $language->id,
                            'lang_key' => 'meta_description',
                        ],
                        [
                            'lang_value' => $fields['meta_description'],
                        ]
                    );
                }

                // Update or create meta_keywords translation as JSON
                if (!empty($fields['meta_keywords'])) {
                    // Convert comma-separated string to array and then to JSON
                    $keywords = is_string($fields['meta_keywords'])
                        ? array_map('trim', explode(',', $fields['meta_keywords']))
                        : $fields['meta_keywords'];

                    // Filter out empty values
                    $keywords = array_filter($keywords, function($keyword) {
                        return !empty(trim($keyword));
                    });

                    $vendor->translations()->updateOrCreate(
                        [
                            'lang_id' => $language->id,
                            'lang_key' => 'meta_keywords',
                        ],
                        [
                            'lang_value' => json_encode(array_values($keywords)),
                        ]
                    );
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
