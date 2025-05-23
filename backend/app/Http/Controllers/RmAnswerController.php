<?php

namespace App\Http\Controllers;

use App\Models\RmAnswer;
use App\Models\RmQuestion;
use App\Models\RmIteration;
use Illuminate\Support\Facades\Validator;


use Illuminate\Http\Request;

class RmAnswerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    public function CreateIteration(Request $request)
{
    // Validate the request data
    $validator = Validator::make($request->all(), [
        'CustomerID' => 'required|integer',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => 422,
            'validate_err' => $validator->getMessageBag(),
        ]);
    }

    // Check if an rm_iteration with the same CustomerID already exists
    $existingIteration = RmIteration::where('CustomerID', $request->input('CustomerID'))->first();

    if ($existingIteration) {
        // If an iteration already exists, return a response
        return response()->json([
            'status' => 200,
            'message' => 'An rm_iteration with the same CustomerID already exists.',
            'rm_iteration_id' => $existingIteration->id,
        ]);
    }
    $currentDate = date('Y-m-d');
    

    // If no iteration exists, create a new rm_iteration
    $newIteration = RmIteration::create([
        'MehariVersion' => 2,
        'CustomerID' => $request->input('CustomerID'),
        'Date création' => $currentDate, 
    ]);

    return response()->json([
        'status' => 201,
        'message' => 'New rm_iteration created successfully.',
        'rm_iteration' => $newIteration,
    ]);
}
public function getAllQuestions (){

    $results = RmQuestion::select('Dépendance', 'Question')
    ->groupBy('Dépendance')
    ->get();
    return response()->json($results);

}

public function associateResponseWithQuestion(Request $request)
{
    // Validate the request data
    $validator = Validator::make($request->all(), [
        'ID_Question' => 'required|integer',
        'Answer' => 'required|in:0,1',
        'Commentaire' => 'nullable|string',
        'ID_ITERATION' => 'required|integer',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => 422,
            'validate_err' => $validator->getMessageBag(),
        ]);
    }

    // Find the question by ID

    // Create a new answer for the question
    $answer = new RmAnswer([
        'ID_Question' => $request->input('ID_Question'),
        'Answer' => $request->input('Answer'),
        'Commentaire' => $request->input('Commentaire'),
        'ID_ITERATION' => $request->input('ID_ITERATION'),
    ]);

    // Save the answer for the question

    return response()->json([
        'status' => 200,
        'message' => 'Response associated with the question successfully.',
        $answer
    ]);
}


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\RmAnswer  $rmAnswer
     * @return \Illuminate\Http\Response
     */
    public function show(RmAnswer $rmAnswer)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\RmAnswer  $rmAnswer
     * @return \Illuminate\Http\Response
     */
    public function edit(RmAnswer $rmAnswer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\RmAnswer  $rmAnswer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, RmAnswer $rmAnswer)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\RmAnswer  $rmAnswer
     * @return \Illuminate\Http\Response
     */
    public function destroy(RmAnswer $rmAnswer)
    {
        //
    }
}
