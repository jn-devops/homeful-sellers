<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ImportExportController extends Controller
{
    //
    public function export(Request $request){
        $table = $request->tableName;
        $tableColumn[] = $request->columns;

        return $tableColumn;
    }
}
