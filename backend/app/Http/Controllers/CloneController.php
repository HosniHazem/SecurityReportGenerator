<?php

namespace App\Http\Controllers;

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
}
