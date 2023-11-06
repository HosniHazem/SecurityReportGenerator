<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\GlbProject;

class AuditPreviousAudit extends Model
{
    use HasFactory;
    public $timestamps = false;
    
    protected  $table='audit_previousaudits_ap';
    protected $fillable = [
        'ID_Projet',
        'ProjetNumero',
        'Project_name',
        'ActionNumero',
        'Action',
        'Criticite',
        'Chargee_action',
        'ChargeHJ',
        'TauxRealisation',
        'Evaluation',
    ];
    
    protected $primaryKey='ID';
    
        //  public function GlbProjects(){
        //     return $this->hasMany(GlbProject::class);
        //  }
}
