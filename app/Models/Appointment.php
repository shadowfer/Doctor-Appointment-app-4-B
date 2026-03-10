<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $fillable = [
        'patient_id',
        'doctor_id',
        'date',
        'start_time',
        'end_time',
        'duration',
        'status',
        'reason',
    ];

    // Relacion uno a uno inversa con doctor
    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    // Relacion uno a uno inversa con paciente
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function consultation()
    {
        return $this->hasOne(Consultation::class);
    }
}
