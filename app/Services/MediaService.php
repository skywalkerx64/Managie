<?php

namespace App\Services;

use App\Models\AppConfiguration;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaService
{
    public function associateMedia(array $filesToAdd, array $existingMedia, string $collectionName, Model $model): array
    {
        // Step 1: Add new files to the app media collection
        $appConfiguration = AppConfiguration::where('code', 'app.mediatheque')->first();

        foreach ($filesToAdd as $file) {
            $media = $appConfiguration->addMedia($file)->toMediaCollection($collectionName);

            array_push($existingMedia, $media->id);

        }
        
        // Step 2: Associate existing media models with the model
        $new_added = [];

        $mediaModels = Media::whereIn('id', $existingMedia)->get();
        foreach ($mediaModels as $mediaModel) {

            // Check if the media is already associated with the model
            if (!$model->medias->contains($mediaModel->id)) {

                $model->medias()->attach($mediaModel->id, [
                    'collection_name' => $collectionName,
                    'model_type' => get_class($model),

                ]);
                array_push($new_added, $mediaModel);
            }
        }
        
        // Step 3: Optimize and process media
        // $mediaCollection = $model->getMedia($collectionName);
        // $mediaCollection->each(function (Media $media) {
        //     $media->manipulations = [];
        //     $media->save();
        //     $media->manipulate();
        //     $media->optimize();
        //     $media->generateResponsiveImages();
        // });

        return $new_added;
    }

    public function updateMedias(array $filesToAdd = [], array $filesToRemove = [], string $collectionName = null, Model $model) : void
    {
        //Add new files
        foreach ($filesToAdd as $file) {
            $media = $model->addMedia($file)->toMediaCollection($collectionName);
        }

        //Remove given files
        $medias_to_delete = Media::whereIn('id', $filesToRemove)->get();

        foreach($medias_to_delete as $media)
        {
            if($media != null)
            {
                $media->delete();
            }
        }

    }
}
