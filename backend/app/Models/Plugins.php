<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Project;
class Plugins extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected  $table='plugins';



}
