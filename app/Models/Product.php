<?php

namespace App\Models;

use App\Models\Scopes\OwnerScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    protected $fillable = [
        'name',
        'description',
        'image',
        'barcode',
        'price',
        'tax',
        'quantity',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean'
    ];

    protected $appends = ['image_url'];

    public function getImageUrlAttribute()
    {
        return $this->getImageUrl();
    }

    public function getImageUrl()
    {
        if ($this->image) {
            /** @phpstan-ignore-next-line */
            // return Storage::disk('public_uploads')->url($this->image);
            return '';
        }
        return asset('img/img-placeholder.jpg');
    }

    protected static function booted()
    {
        static::addGlobalScope(new OwnerScope);
    }
}
