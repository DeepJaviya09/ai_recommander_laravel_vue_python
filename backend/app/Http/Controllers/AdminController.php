<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{

    public function syncModel(Request $request)
    {
        // $pythonPath = base_path('ai/venv/bin/python'); // mac/linux
        $pythonPath = 'D:\\php\\ai_recommander\\ai\\venv\\Scripts\\python.exe';
        $scriptPath = 'D:\\php\\ai_recommander\\ai\\train_model.py';

        $output = shell_exec("$pythonPath $scriptPath 2>&1");

        return response()->json([
            'ok' => true,
            'message' => 'AI model retrained successfully.',
            'log' => $output
        ]);
    }
}
