<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// use App\Models\Customer;

class GlbPip extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected  $table='glb_pip';
    protected $primaryKey='ID';


    public function customer()
    {
        return $this->belongsTo(Customer::class, 'Cusotmer_ID');
    }
}
