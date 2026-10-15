<?php

namespace App\Imports\Concerns;

use App\Jobs\UploadImageToCloudinaryJob;
use App\Models\Business;
use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
 * What both importers do with a row's venture: find the person's business or work profile (by name, else their
 * first one) and update or create it, and queue images that are not on Cloudinary yet for upload.
 */
trait UpsertsVentures
{
    private function upsertBusiness(User $user, string $name, array $data): Business
    {
        $business = Business::where('user_id', $user->id)->where('name', $name)->first()
            ?? Business::where('user_id', $user->id)->where('type', 'entrepreneur')->first();

        if ($business) {
            $business->update($data);

            return $business;
        }

        return Business::create(array_merge($data, ['user_id' => $user->id, 'name' => $name]));
    }

    private function upsertCompany(User $user, string $name, array $data): Company
    {
        $company = Company::where('user_id', $user->id)->where('name', $name)->first()
            ?? Company::where('user_id', $user->id)->first();

        if ($company) {
            $company->update(array_merge($data, ['name' => $name]));

            return $company;
        }

        return Company::create(array_merge($data, ['user_id' => $user->id, 'name' => $name]));
    }

    private function queueImageUpload(Model $model, string $column, ?string $url, string $folder, ?string $identifier): void
    {
        if ($url && ! str_contains($url, 'cloudinary.com')) {
            UploadImageToCloudinaryJob::dispatch($model, $column, $url, $folder, $identifier);
        }
    }
}
