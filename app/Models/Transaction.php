<?php

namespace App\Models;

use App\Models\Scopes\UserScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Transaction extends Model
{
    use HasFactory;
    use UserScope;

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $casts = [
        'transaction_date' => 'date',
        'amount' => 'decimal:2',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'location_metadata' => 'array',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'transaction_tags')
            ->withTimestamps();
    }

    // Accessors
    public function getFormattedAmountAttribute()
    {
        return number_format($this->amount, 2);
    }

    public function getTagNamesAttribute()
    {
        return $this->tags->pluck('name')->join(', ');
    }

    public function getHasLocationAttribute()
    {
        return ! is_null($this->latitude) && ! is_null($this->longitude);
    }

    public function getLocationUrlAttribute()
    {
        if ($this->has_location) {
            return "https://www.google.com/maps?q={$this->latitude},{$this->longitude}";
        }

        return null;
    }

    // Methods
    public function attachTags(array $tagIds)
    {
        $this->tags()->sync($tagIds);
    }

    public function detachTags(?array $tagIds = null)
    {
        if ($tagIds) {
            $this->tags()->detach($tagIds);
        } else {
            $this->tags()->detach();
        }
    }

    public function setLocation($latitude, $longitude, $locationName = null, $metadata = null)
    {
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->location_name = $locationName;
        $this->location_metadata = $metadata;
        $this->save();
    }

    public function clearLocation()
    {
        $this->latitude = null;
        $this->longitude = null;
        $this->location_name = null;
        $this->location_metadata = null;
        $this->save();
    }
}
