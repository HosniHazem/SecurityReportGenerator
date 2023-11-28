<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RmAnswer extends Model
{
    use HasFactory;
    protected $table = 'rm_answers';
    protected $primaryKey='ID';
    protected $fillable = [
        'ID_Question',
        'Answer',
        'Commentaire',
        'ID_ITERATION',
    ];
    public function iteration()
    {
        return $this->belongsTo(RmIteration::class, 'ID_ITERATION');
    }
    public function question()
    {
        return $this->belongsTo(RmQuestion::class, 'ID_Question');
    }
}
