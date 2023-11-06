<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GlbProject extends Model
{
    use HasFactory;
    protected  $table='glb_projects';
    protected $primaryKey='ID';
    // public function Audits(){
    //     return $this->hasMany(AuditPreviousAudit::class);
    //  }
}
