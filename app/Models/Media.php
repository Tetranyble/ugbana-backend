<?php

namespace App\Models;

use App\Enums\StorageProvider;
use App\Services\FileSystem;
use App\Traits\FileSystemTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(schema="Media")
 * {
 *
 *   @OA\Property(
 *    property="path",
 *    type="string",
 *    description="The resource link."
 *   ),
 *   @OA\Property(
 *    property="description",
 *    type="string",
 *    description="The resource description."
 *   ),
 *   @OA\Property(
 *    property="attribution",
 *    type="string",
 *    description="The resource attribution."
 *   ),
 *   @OA\Property(
 *    property="mime_type",
 *    type="string",
 *    description="The resource mime type. RFC 6838."
 *   ),
 *   @OA\Property(
 *    property="size",
 *    type="string",
 *    description="The resource file size."
 *   ),
 *   @OA\Property(
 *    property="current",
 *    type="boolean",
 *    description="The resource is default."
 *   ),
 * }
 */
class Media extends Model
{
    use FileSystemTrait, HasFactory;

    protected $fillable = [
        'description',
        'attribution',
        'size',
        'path',
        'mime_type',
        'current',
        'disk',
    ];

    public function media()
    {
        return $this->morphTo('mediable');
    }

    protected $casts = [
        'current' => 'boolean',
        'disk' => StorageProvider::class,
    ];

    public function setIsCurrentAttribute($value)
    {
        $this->attributes['current'] = (int) $value;
    }

    public function getIsCurrentAttribute($value): bool
    {
        return (bool) $value;
    }

    public function getUrlAttribute(): string
    {
        return (new FileSystem())->show(
            $this->path,
            $this->disk
        );
    }
}
