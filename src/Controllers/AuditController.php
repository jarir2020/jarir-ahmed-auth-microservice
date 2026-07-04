<?php

namespace JarirAhmed\AuthMicroservice\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;

class AuditController extends Controller
{
    public function loginHistory(Request $request)
    {
        return response()->json(
            $request->user()->loginHistories()->latest()->paginate(20)
        );
    }

    public function auditLogs(Request $request)
    {
        return response()->json(
            $request->user()->auditLogs()->latest()->paginate(20)
        );
    }
}
