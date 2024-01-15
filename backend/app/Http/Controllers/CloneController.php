<?php

namespace App\Http\Controllers;

use Attribute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CloneController extends Controller
{
     public function getTables(){
        $tables = DB::select('SHOW TABLES FROM tactio2z_officekiller');

        $tableNames = array_map(function ($table) {
            return reset($table);
        }, $tables);

        return response()->json($tableNames);
     }
     public function getTableAttributes(Request $request)
    {
        $tableName = $request->name;

        // Check if the table exists
        if (Schema::hasTable($tableName)) {
            // Table exists, fetch all attributes with their data
            $tableData = DB::table($tableName)->get();
            $columnNames = Schema::getColumnListing($tableName);

            return response()->json(['data'=>$tableData,'attributes'=>$columnNames]);
        } else {
            // Table doesn't exist
            return response()->json(['error' => 'Table does not exist'], 404);
        }
    }

    public function Modify(Request $request){
        $tableName = $request->name;
        $attribute=$request->attribute;
        $value=$request->value;
        $id=$request->id;
        $tableAttributes=[];
        $tableAttributes=self::getAttributes($tableName);
        $primaryKey=self::getPrimaryKey($tableAttributes);


        $sqlUpdate = 'UPDATE ' . $tableName . ' SET ' . $attribute . ' = \'' . $value . '\' WHERE ' . $primaryKey . ' = \'' . $id  . '\'';
        
        
        
        try {
            DB::statement($sqlUpdate);
    
            return response()->json(['success' => 'Query executed successfully']);
        } catch (\Exception $e) {
            // Handle query execution failure
            return response()->json(['error' => $e->getMessage()]);
        }

    }



    static function getAttributes($tableName)
    {

        // Check if the table exists
        if (Schema::hasTable($tableName)) {
            // Table exists, fetch all attributes with their data
            $columnNames = Schema::getColumnListing($tableName);
            return $columnNames;
        } else {
            // Table doesn't exist
            return response()->json(['error' => 'Table does not exist'], 404);
        }
    }
    
    static function getPrimaryKey($tableAttributes){

        $primaryKey="";
        // return response()->json($tableAttributes);
        for($i=0;$i<count($tableAttributes);$i++){

            if($tableAttributes[$i]=='id'){
                $primaryKey='id';
                break;
            }
            else if($tableAttributes[$i]=='ID'){
                $primaryKey='ID';
                break;
            }

        }
        return $primaryKey;
    }


}
