<?php

namespace App\Http\Controllers;

use App\Models\Transaction;

class DashboardController extends Controller
{
    public function index()
    {
        $transactions = Transaction::with(['user', 'book'])->latest()->get();

        return view('dashboard', compact('transactions'));
    }
}
