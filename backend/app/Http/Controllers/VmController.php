<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vm;
use Illuminate\Support\Facades\Validator;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Message;

class VmController extends Controller
{
    public function index()
    {

        $item =Vm::all();



        return response()->json(['Vm'=>$item,'status' => 200]);
    }
}
