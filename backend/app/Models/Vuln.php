<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vuln extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'vuln';

    protected $fillable = [
        'CVE',
        'CVSS v2.0 Base Score',
        'Risk',
        'Host',
        'Protocol',
        'Port',
        'Name',
        'Synopsis',
        'Description',
        'Solution',
        'See Also',
        'Plugin Output',
        'STIG Severity',
        'CVSS v3.0 Base Score',
        'CVSS v2.0 Temporal Score',
        'CVSS v3.0 Temporal Score',
        'VPR Score',
        'Risk_Factor',
        'BID',
        'XREF',
        'MSKB',
        'Plugin Publication Date',
        'Plugin Modification Date',
        'Metasploit',
        'Core Impact',
        'CANVAS',
        'upload_id',
        'scan',
        'file',
        'En_Description',
    ];
}
