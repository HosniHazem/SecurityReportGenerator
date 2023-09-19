<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Project;
class Customer extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected  $table='customers';

 public function projects()
 {
     return $this->hasMany(Project::class);
 }

}