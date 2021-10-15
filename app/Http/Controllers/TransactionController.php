<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function show()
    {
         return view('transaction.show');
    }
}
