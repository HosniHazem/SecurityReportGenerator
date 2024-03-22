<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RmProcessusDomains extends Model
{
    use HasFactory;
    public $timestamps = false; // Corrected property name

    protected $table="rm_processus_domains";
    protected $primaryKey='ID';
    protected $fillable = [
        'ID_ITERATION',
        'Processus_domaine',
        'Description'
    ];
}
