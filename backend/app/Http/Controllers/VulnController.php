<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vuln;
use Illuminate\Support\Facades\DB;


class VulnController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $vulns = Vuln::all();
        return response()->json($vulns);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

     public function store(Request $request)
{
    try {
        // Get all attributes and their values from the request
        $attributes = $request->all();

        // Extract the table name from the request (assuming it's 'vuln')
        $tableName = 'vuln';

        // Escape column names with backticks
        $escapedColumns = array_map(function($column) {
            return "`$column`";
        }, array_keys($attributes));

        // Build the SQL query
        $columns = implode(', ', $escapedColumns);
        $values = implode(', ', array_fill(0, count($attributes), '?'));

        // Print the SQL query
        $sqlQuery = "INSERT INTO $tableName ($columns) VALUES ($values)";

        // Execute the SQL query
        DB::insert($sqlQuery, array_values($attributes));

        return response()->json(['message' => 'Vuln created successfully', 'success' => true]);
    } catch (\Exception $e) {
        return response()->json(['message' => $e->getMessage(), 'success' => false]);
    }
}

     
    

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $vuln = Vuln::findOrFail($id);
        return response()->json($vuln);
    }
    public function showByProjectID($id)
    {
        $item = Vuln::where('ID_Projet', $id)->get();
        return response()->json($item);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $attribute = $request->attribute;
        $value = $request->value;
        $tableName = 'vuln';
        $primaryKey = 'id';
        
        // Check if the provided attribute is allowed to be updated
        if (in_array($attribute, array_column($this->observeColumns(), 'name'))) {
            // Check if the value matches the data type of the attribute
            $columnInfo = array_column($this->observeColumns(), 'type', 'name');
            if (!isset($columnInfo[$attribute])) {
                return response()->json(['error' => 'Attribute not found']);
            }
    
            $expectedType = $columnInfo[$attribute];
            if (!$this->validateValueType($value, $expectedType)) {
                return response()->json(['message' => 'Value type mismatch for attribute ' . $attribute ,'success'=>false]);
            }
    
            // Construct the SQL update statement
            $sqlUpdate = 'UPDATE `' . $tableName . '` SET `' . $attribute . '` = ? WHERE `' . $primaryKey . '` = ?';
            
            try {
                // Execute the SQL update statement
                DB::update($sqlUpdate, [$value, $id]);
                
                // Return success message
                return response()->json(['message' => 'Record updated successfully', 'success' => true]);
            } catch (\Exception $e) {
                // Handle query execution failure
                return response()->json(['message' => $e->getMessage(),'success'=>false]);
            }
        } else {
            // If the provided attribute is not allowed, return an error message
            return response()->json(['message' => 'Attribute not allowed','success'=>false]);
        }
    }
    
    private function validateValueType($value, $expectedType)
    {
        switch ($expectedType) {
            case 'string':
                return is_string($value);
            case 'integer':
                return is_int($value);
            case 'float':
                return is_float($value);
            case 'date':
                // Implement date validation logic if necessary
                return true;
            default:
                return true; // Allow unknown types by default
        }
    }
    

public function getColumnNamesVuln()
{
    $typeMapping = [
        'varchar' => 'string',
        'text' => 'string',
        'int' => 'integer',
        'bigint' => 'integer',
        'decimal' => 'float',
        'date' => 'date',
        'float'=>'float'
        // Add more mappings as needed
    ];

    $sqlAttribute = "SELECT COLUMN_NAME, DATA_TYPE
                     FROM INFORMATION_SCHEMA.COLUMNS
                     WHERE TABLE_NAME = 'vuln'";

    // Execute the SQL query
    $attributesArray = DB::select($sqlAttribute);

    // Extract column names and mapped data types from the result
    $columnInfo = array_map(function ($attribute) use ($typeMapping) {
        // Check if the database data type exists in the mapping array
        if (isset($typeMapping[$attribute->DATA_TYPE])) {
            // If it exists, use the mapped PHP data type
            $dataType = $typeMapping[$attribute->DATA_TYPE];
        } else {
            // If not, default to 'unknown'
            $dataType = 'unknown';
        }

        return [
            'name' => $attribute->COLUMN_NAME,
            'type' => $dataType
        ];
    }, $attributesArray);

    // Return column names and mapped data types as JSON response
    return response()->json($columnInfo);
}





    private function observeColumns(){
        
       
    $typeMapping = [
        'varchar' => 'string',
        'text' => 'string',
        'int' => 'integer',
        'bigint' => 'integer',
        'decimal' => 'float',
        'date' => 'date',
        'float'=>'float'
        // Add more mappings as needed
    ];

    $sqlAttribute = "SELECT COLUMN_NAME, DATA_TYPE
                     FROM INFORMATION_SCHEMA.COLUMNS
                     WHERE TABLE_NAME = 'vuln'";

    // Execute the SQL query
    $attributesArray = DB::select($sqlAttribute);

    // Extract column names and mapped data types from the result
    $columnInfo = array_map(function ($attribute) use ($typeMapping) {
        // Check if the database data type exists in the mapping array
        if (isset($typeMapping[$attribute->DATA_TYPE])) {
            // If it exists, use the mapped PHP data type
            $dataType = $typeMapping[$attribute->DATA_TYPE];
        } else {
            // If not, default to 'unknown'
            $dataType = 'unknown';
        }

        return [
            'name' => $attribute->COLUMN_NAME,
            'type' => $dataType
        ];
    }, $attributesArray);
    return $columnInfo;
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $vuln = Vuln::findOrFail($id);
            $vuln->delete();

            return response()->json(['message'=>" deleted succeffully","success"=>true]);

        } catch (\Exception $e) {

            return response()->json(['message' => $e->getMessage(),'success'=>false]);
        }
    }
}
