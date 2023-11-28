<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerSites extends Model
{
    use HasFactory;
    protected  $table='customer_sites';
    protected $primaryKey='ID';
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'Cusotmer_ID');
    }
}
