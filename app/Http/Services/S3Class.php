<?php


namespace App\Http\Services;


use App\Helpers\Image;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class S3Class
{
    const SIZE = 700;

    public static function addPhotos(string $path, array|null $documents): void
    {
        if ($documents) {
            $files = Storage::disk('s3')->files($path);
            Storage::disk('s3')->delete($files);

            foreach ($documents as $type => $document) {
                //If photo from regula (base64 format)
                if(gettype($document) === 'string') {
                    $document = Image::resizeEncodeBase64($document);
                    if (preg_match('/^data:image\/(\w+);base64,/', $document, $matches)) {
                        $originalName = $matches[1];
                    }
                }else{
                    $originalName = $document->getClientOriginalName().'.'.$document->extension();
                }

                $manager = new ImageManager(new Driver());
                $resizedImage = $manager->read($document);
                $resizedImage->scaleDown(width: self::SIZE);

                $tempPath = storage_path("app/temp/{$originalName}");

                if (!file_exists(dirname($tempPath))) {
                    mkdir(dirname($tempPath), 0755, true);
                }

                $resizedImage->save($tempPath);

                $uploadedFile = new UploadedFile(
                    $tempPath,
                    $originalName,
                    mime_content_type($tempPath),
                    null,
                    true
                );

                $uploadedFile->storeAs($path, '___' . $type . '___' . $originalName, 's3');
                unlink($tempPath);
            }
        }
    }

    public static function getPhotos(string $path): array|null|string
    {
        $images = [];
        $documents = Storage::disk('s3')->files($path);
        foreach ($documents as $document) {
            $type = explode('___',$document)[1];
            $images[$type] = Storage::disk('s3')->temporaryUrl($document, now()->addMinutes(15));
        }
        return $images;
    }

}
