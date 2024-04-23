<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Sow extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected  $table='sow';
    protected $primaryKey='ID';

    protected $fillable = [
        'Nom', 'IP_Host', 'field3', 'field4', 'field5', 'dev_by', 'URL', 'Number_users'
    ];


}
