<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    protected $fillable = [
        'speciality_id',
        'medical_license_number',
        'biography',
    ];

    //Relacion uno a uno inversa con usuario
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    //Relacion uno a uno inversa con especialidad
    public function speciality()
    {
        return $this->belongsTo(Speciality::class);
    }
}
