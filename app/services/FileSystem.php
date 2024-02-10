<?php

namespace App\services;

use App\Enums\StorageProvider;
use App\Traits\FileSystemTrait;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\File;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileSystem
{
    use FileSystemTrait;

    protected $storage;

    public StorageProvider $disk;

    public function __construct(StorageProvider $storage = StorageProvider::PUBLIC)
    {
        $this->storage = Storage::getFacadeRoot();
        $this->storage->disk($storage->value);
        $this->disk = StorageProvider::fromValue(config('filesystems.default'));
    }

    /**
     * @return mixed
     */
    public function store(string|UploadedFile|File $file, string $location = 'images', StorageProvider $provider = StorageProvider::LOCAL)
    {
        return $this->storeAs(
            $file,
            $this->rename($file),
            $location,
            $provider
        );
    }

    /**
     * @return mixed
     */
    public function storeAs(string|UploadedFile $file, string $name, string $location = 'images/', StorageProvider $provider = StorageProvider::LOCAL)
    {
        return $this->storage->disk($provider->value)
            ->putFileAs(
                $location,
                ($file instanceof UploadedFile) ? $file : new File($file),
                $name
            );
    }

    /**
     * @return mixed
     */
    public function patch(string $location, string $oldFilePath, StorageProvider $provider = StorageProvider::LOCAL)
    {
        return $this->storage
            ->disk($provider->value)
            ->move($oldFilePath, $location);
    }

    /**
     * @return mixed
     */
    public function show(string $file, StorageProvider $disk = StorageProvider::LOCAL, int $time = 60)
    {
        return ($disk === StorageProvider::S3PRIVATE) ?
            $this->signUrl($file, $disk, $time) : (
                ($disk === StorageProvider::LOCAL) ?
                    $this->showLocal($file, $disk) : (
                        ($disk === StorageProvider::YOUTUBE || $disk === StorageProvider::VIMEO) ?
                            $file :
                            $this->storage
                                ->disk($disk->value)
                                ->url($file)
                    )
            );
    }

    /**
     * @return mixed
     */
    public function showLocal(string $file, StorageProvider $provider = StorageProvider::LOCAL)
    {
        return config('app.url').$this->storage
            ->disk($provider->value)
            ->url($file);
    }

    public function download(string $file, string $contentType): Response|Application|ResponseFactory
    {

        return response(
            $this->storage
                ->disk('s3')->get($file)
        )->header([
            'Content-Type' => $contentType,
        ]);
    }

    public function disk(StorageProvider $storage)
    {
        $this->storage->disk($storage->value);

        return $this;
    }

    public function rename(string|UploadedFile $file)
    {
        $name = Str::uuid()->toString().'-'.now()->format('Y-m-d-H-i-s');

        return ($file instanceof UploadedFile) ? $name.'.'.$file->extension() :
            $file;
    }

    public function signUrl(string $path, StorageProvider $disk = StorageProvider::S3PRIVATE, int $time = 60)
    {
        return $this->storage->disk($disk->value)->temporaryUrl(
            $path,
            now()->addMinutes($time)
        );
    }
}
