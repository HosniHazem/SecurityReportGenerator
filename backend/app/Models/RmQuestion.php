<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RmQuestion extends Model
{
    use HasFactory;
    protected $table = 'rm_questions';
    protected $primaryKey='QuestionID ';

}
