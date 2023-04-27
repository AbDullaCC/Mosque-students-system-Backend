<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
    public function mosuqe(): BelongsTo
    {
        return $this->belongsTo(Mosuqe::class);
    }

    /**
     * Get all of the users for the Course
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
    
    public function groups(): HasMany
    {
        return $this->hasMany(Group::class);
    }
}
