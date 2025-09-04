<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;

trait HasProfilePicUpload
{
    public function uploadProfilePic($model, $file)
    {
        $entityFolder = $folder ?? strtolower(class_basename($model)) . 's';

        $filename = $entityFolder . '_' . $model->id . '_' . now()->format('Ymd_His') . '.' . $file->getClientOriginalExtension();
        $path = "{$entityFolder}/{$filename}";

        // Delete old file if exists
        if ($model->profile_pic && Storage::disk('public')->exists($model->profile_pic)) {
            Storage::disk('public')->delete($model->profile_pic);
        }

        // Store new file
        Storage::disk('public')->put($path, file_get_contents($file));

        // Save path in DB
        $model->profile_pic = $path;
        $model->save();

        return $model;
    }
}
