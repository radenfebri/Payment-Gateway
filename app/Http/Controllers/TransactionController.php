<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Payment\TripayController;
use App\Models\Book;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function show($reference)
    {
        $tripay = new TripayController();
        $detail = $tripay->detailTransaction($reference);

        return view('transaction.show', compact('detail'));
    }


    public function store(Request $request)
    {
        // Request Transaksi ke Tripay
        $book = Book::find($request->book_id);
        $method = $request->method;

        // dd($request->all());
        $tripay = new TripayController();
        $transaction = $tripay->requestTransaction($method, $book);


        // Request Transaksi ke Database
        Transaction::create([
            'user_id' => Auth()->user()->id,
            'book_id' => $book->id,
            'reference' => $transaction->reference,
            'merchant_ref' => $transaction->merchant_ref,
            'total_amount' => $transaction->amount,
            'status' => $transaction->status,
        ]);


        return redirect()->route('transaction.show', [
            'reference' => $transaction->reference,
        ]);

    }
}
