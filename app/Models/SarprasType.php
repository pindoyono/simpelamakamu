<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SarprasType extends Model
{
    use HasFactory;

    protected $fillable = [
        'sarpras_category_id',
        'name',
        'description',
        'unit',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get the category for this sarpras type.
     */
    public function sarprasCategory(): BelongsTo
    {
        return $this->belongsTo(SarprasCategory::class);
    }

    /**
     * Alias for sarprasCategory relationship.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(SarprasCategory::class, 'sarpras_category_id');
    }

    /**
     * Get the sekolah sarpras for this type.
     */
    public function sekolahSarpras(): HasMany
    {
        return $this->hasMany(SekolahSarpras::class);
    }
}
