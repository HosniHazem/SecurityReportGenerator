<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerSites extends Model
{
    use HasFactory;
    protected  $table='customer_sites';
    protected $primaryKey='ID';
    public $timestamps = false;

    // protected $fillable = ['Numero_site', 'Structure', 'Lieu', 'Customer_ID'];
 

}
