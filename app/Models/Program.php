<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Program extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function scholars(): HasMany
    {
        return $this->hasMany(Scholar::class);
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    public function quickApplications(): HasMany
    {
        return $this->hasMany(QuickApplication::class);
    }
}
