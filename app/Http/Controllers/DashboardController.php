<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Marketing\orderConfirmation;
use App\Models\Marketing\salesOrder;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:PPIC']);
    }
    public function index()
    {
        // Hitung jumlah berdasarkan status
        $total = orderConfirmation::count();
        $posted = orderConfirmation::where('status', 'Posted')->count();
        $unposted = orderConfirmation::whereIn('status', ['Un Posted', 'Request'])->count();
        $closed = orderConfirmation::where('status', 'Closed')->count();

        $totalSO = salesOrder::count();
        $postedSO = salesOrder::where('status', 'Posted')->count();
        $unpostedSO = salesOrder::whereIn('status', ['Un Posted', 'Request'])->count();
        $closedSO = salesOrder::where('status', 'Closed')->count();

        // Filter data berdasarkan tanggal hari ini
        $today = Carbon::today()->toDateString();

        $totalToday = orderConfirmation::whereDate('date', $today)->count();
        $postedToday = orderConfirmation::whereDate('date', $today)->where('status', 'Posted')->count();
        $unpostedToday = orderConfirmation::whereDate('date', $today)->whereIn('status', ['Un Posted', 'Request'])->count();
        $closedToday = orderConfirmation::whereDate('date', $today)->where('status', 'Closed')->count();

        $totalTodaySO = salesOrder::whereDate('date', $today)->count();
        $postedTodaySO = salesOrder::whereDate('date', $today)->where('status', 'Posted')->count();
        $unpostedTodaySO = salesOrder::whereDate('date', $today)->whereIn('status', ['Un Posted', 'Request'])->count();
        $closedTodaySO = salesOrder::whereDate('date', $today)->where('status', 'Closed')->count();

        return view('dashboard.index', compact('total', 'posted', 'unposted', 'closed', 'totalToday', 'postedToday', 'unpostedToday', 'closedToday', 'totalSO', 'postedSO', 'unpostedSO', 'closedSO', 'totalTodaySO', 'postedTodaySO', 'unpostedTodaySO', 'closedTodaySO'));
    }
}
