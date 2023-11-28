<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RmIteration extends Model
{
    use HasFactory;
    protected $table = 'rm_iteration';
    protected $primaryKey='ID';
    public $timestamps = false; // Disable automatic timestamps

    protected $fillable = [
        'MehariVersion',
        'CustomerID',
        'Date création',
        // Add other fields as needed
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'CusotmerID');
    }
}
