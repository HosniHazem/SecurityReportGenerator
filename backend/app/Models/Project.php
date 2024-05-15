<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Customer;

class Project extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected  $table='projects';
// In your Project model
    protected $fillable = ['Nom', 'URL', 'Description', 'QualityChecked', 'QualityCheckedDateTime', 'QualityCheckedMessage', 'Preuve', 'iterationKey'];

  
public function customer()
{
    return $this->belongsTo(Customer::class);
}
}