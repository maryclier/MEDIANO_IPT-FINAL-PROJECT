<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Medicine extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'quantity',
        'unit',
        'expiry_date',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'expiry_date' => 'date:Y-m-d',
            'quantity' => 'integer',
        ];
    }

    public function medicalRecords(): HasMany
    {
        return $this->hasMany(MedicalRecord::class);
    }
}
