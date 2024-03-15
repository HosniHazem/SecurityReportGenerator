<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Project;
use App\Models\GlbPip;

class Customer extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected  $table='customers';
     protected $fillable = ['SN', 'LN', 'Logo', 'Organigramme', 'Description', 'SecteurActivité', 'Categorie', 'Site_Web', 'Addresse_mail'];
 public function projects()
 {
     return $this->hasMany(Project::class);
 }


}