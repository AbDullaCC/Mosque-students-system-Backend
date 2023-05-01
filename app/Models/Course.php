<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'mosque_id',
        'name',
        'description',
        'image',
        'systemType'
    ];

    /**
     * Get the mosuqe that owns the Course
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function mosque(): BelongsTo
    {
        return $this->belongsTo(Mosque::class);
    }

    /**
     * The adminstrators that belong to the Course
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function adminstrators(): BelongsToMany
    {
        return $this->belongsToMany(Adminstrator::class);
    }

    /**
     * Get all of the users for the Course
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    
    public function groups(): HasMany
    {
        return $this->hasMany(Group::class);
    }

}
