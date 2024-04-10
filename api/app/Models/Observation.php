<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Observation extends Model
{
    use HasFactory;

    protected $guarded = [];

    const status = [
        'PEI' => 'pending investigation',
        'BEI' => 'being investigated',
        'DOI' => 'done investigation',
    ];

    protected $with = ['observer', 'observation_type'];

    public function observer()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function observation_type()
    {
        return $this->hasOne(ObservationType::class, 'id', 'observation_type_id');
    }
}
