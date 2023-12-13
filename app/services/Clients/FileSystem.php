<?php

namespace App\services\Clients;

use App\Enums\StorageProvider;
use App\Services\Clients\FTPServiceClient;
use App\Services\ImageManipulation;
use App\Traits\FileSystemTrait;
use Illuminate\Http\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Lazzard\FtpClient\Exception\FtpClientException;
use Psr\Http\Message\StreamInterface;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FileSystem
{
    use FileSystemTrait;

    protected $storage;

    public function __construct(StorageProvider $storage = StorageProvider::LOCAL)
    {
        $this->storage = Storage::getFacadeRoot();
        $this->storage->disk($storage->value);
    }

    /**
     * @return mixed
     */
    public function store(string|UploadedFile|File $file, string $location = 'images', StorageProvider $disk = StorageProvider::LOCAL)
    {
        return $this->storeAs(
            $file,
            $this->rename($file),
            $location,
            $disk
        );
    }

    /**
     * @return mixed
     */
    public function storeAs(string|UploadedFile $file, string $name, string $location = 'images', StorageProvider $disk = StorageProvider::LOCAL)
    {
        return $this->storage->disk($disk->value)
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

    public function download(string $file, StorageProvider $disk, array $options = []): StreamedResponse
    {
        return Storage::disk($disk->value)
            ->download($file);
    }

    public function disk(StorageProvider $storage)
    {
        $this->storage->disk($storage->value);

        return $this;
    }

    public function rename(string|UploadedFile|null $file, string $extension = '.png')
    {
        $name = Str::uuid()->toString().'-'.now()->format('Y-m-d-H-i-s');

        return ($file instanceof UploadedFile) ? $name.'.'.$file->extension() :
            $name.$extension;
    }

    /**
     * @param  resource  $resource
     */
    public function writeStream($resource, StorageProvider $disk, string $path = 'resources', array $options = []): bool
    {
        return $this->storage
            ->disk($disk->value)
            ->writeStream(
                $path,
                $resource,
                $options
            );
    }

    /**
     * @return resource|null
     */
    public function readStream(string $file, StorageProvider $disk)
    {
        return $this->storage->disk($disk->value)
            ->getDriver()
            ->readStream($file);
    }

    public function makeFolder(string $path, StorageProvider $disk): bool
    {

        return $this->storage
            ->disk($disk->value)
            ->makeDirectory($path);
    }

    public function deleteFolder(string $path, StorageProvider $disk): bool
    {
        return $this->storage
            ->disk($disk->value)
            ->deleteDirectory($path);
    }

    public function fileContent(string $file, StorageProvider $disk): ?string
    {
        return $this->storage
            ->disk($disk->value)
            ->deleteDirectory($file);
    }

    /**
     * @return mixed
     */
    public function put(string $filename, mixed $data, StorageProvider $disk)
    {
        return $this->storage
            ->disk($disk->value)
            ->put(
                $filename,
                $data
            );
    }

    public function destroy(string $file, StorageProvider $disk): bool
    {
        return $this->storage
            ->disk($disk->value)
            ->delete($file);
    }

    /**
     * @return string|bool
     *
     * @throws FtpClientException
     */
    public function findAndStream(
        FTPServiceClient $client,
        string $file,
        StorageProvider $from = StorageProvider::FTP,
        StorageProvider $to = StorageProvider::S3PUBLIC,
    ): string|bool|array {

        $filename = $this->filename($file);
        $path = $client->find($filename, 'uploads') ?:
            $client->find($filename, 'images');

        if (! empty($path)) {
            $newFile = "products/images/{$this->filename($path[0]['path'])}";
            $stream = $this->readStream(
                $path[0]['path'],
                $from
            );
            $this->put(
                $newFile,
                $stream,
                $to
            );

            return $newFile;
        }

        return false;
    }

    /**
     * @return string
     */
    public function mime(string $file, StorageProvider $disk = StorageProvider::S3PUBLIC, bool $driver = false): string|false
    {
        return $driver ? (new \Illuminate\Filesystem\Filesystem())->mimeType($file) :
            $this->storage
                ->disk($disk->value)
                ->mimeType($file);
    }

    /**
     * @return string
     */
    public function size(string $file, StorageProvider $disk = StorageProvider::S3PUBLIC, bool $driver = false): int
    {
        return $driver ? (new \Illuminate\Filesystem\Filesystem())->size($file) :
            $this->storage
                ->disk($disk->value)
                ->size($file);
    }

    public function signUrl(string $path, StorageProvider $disk = StorageProvider::S3PRIVATE, int $time = 60)
    {
        return $this->storage->disk($disk->value)->temporaryUrl(
            $path,
            now()->addMinutes($time)
        );
    }

    public function resizeAndStore(
        File|UploadedFile|StreamInterface|string $filename,
        int $width,
        int $height,
        string $location = 'public',
        StorageProvider $disk = StorageProvider::LOCAL
    ): string {

        $stream = (new ImageManipulation())
            ->resize(
                $filename,
                $width,
                $height
            )->encode('png')
            ->stream();
        $file = 'photos/'.$this->rename('');

        $this->put(
            $location.'/'.$file,
            $stream,
            $disk
        );

        return $file;
    }
}
