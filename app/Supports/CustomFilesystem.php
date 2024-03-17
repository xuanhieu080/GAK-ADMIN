<?php

namespace App\Supports;

use Illuminate\Contracts\Filesystem\Factory;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\MediaCollections\Filesystem as BaseFilesystem;
use Spatie\MediaLibrary\Support\PathGenerator\PathGeneratorFactory;

class CustomFilesystem extends BaseFilesystem
{
    public function __construct(
        protected Factory $filesystem
    ) {
    }
    public function removeFiles(Media $media)
    {
        $mediaDirectory = $this->getMediaDirectory($media);

        $this->filesystem->deleteDirectory($mediaDirectory);

        if (count($this->filesystem->files($mediaDirectory)) === 0) {
            $this->filesystem->deleteDirectory($this->getMediaDirectory($media));
        }
    }


    public function removeAllFiles(Media $media): void
    {
        $mediaDirectory = $this->getMediaDirectory($media);

        $this->filesystem->disk($media->disk)->delete($mediaDirectory . "/$media->file_name");

        if (count($this->filesystem->disk($media->disk)->files($mediaDirectory)) === 0) {
            $this->filesystem->disk($media->disk)->deleteDirectory($mediaDirectory);
        }
    }

    public function getMediaDirectory(Media $media, ?string $type = null): string
    {
        $directory = null;
        $pathGenerator = PathGeneratorFactory::create($media);

        if (! $type) {
            $directory = $pathGenerator->getPath($media);
        }

        if ($type === 'conversions') {
            $directory = $pathGenerator->getPathForConversions($media);
        }

        if ($type === 'responsiveImages') {
            $directory = $pathGenerator->getPathForResponsiveImages($media);
        }

        return $directory;
    }

}