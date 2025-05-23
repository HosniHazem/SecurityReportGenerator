<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Project;

class AuditPreviousAudit extends Model
{
    use HasFactory;
    public $timestamps = false;
    
    protected  $table='audit_previousaudits_ap';
    protected $fillable = [
        'ProjetNumero',
        'Project_name',
        'ActionNumero',
        'Action',
        'Criticite',
        'Chargee_action',
        'ChargeHJ',
        'TauxRealisation',
        'Evaluation',
        'projectID',
        'ID_Projet'
    ];
    
    
    protected $primaryKey='ID';
    
        //  public function GlbProjects(){
        //     return $this->hasMany(GlbProject::class);
        //  }
        
        public function project()
        {
            return $this->belongsTo(Project::class, 'projectID')->refresh();
        }
        

    

}
