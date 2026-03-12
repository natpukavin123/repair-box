<?php

namespace App\Http\Controllers;

use App\Services\ReportService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(ReportService $reportService)
    {
        if (request()->ajax()) {
            return response()->json($reportService->getDashboardStats());
        }
        return view('dashboard');
    }
}
