<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;

class TripayCallbackController extends Controller
{
    protected $privateKey = 'DEV-IzBdMi5RSOuz5BmIsREtEhuiX3JOg6yhdwbtfeEQ';

    public function handle(Request $request)
    {
        // ambil callback signature
        $callbackSignature = $request->server('HTTP_X_CALLBACK_SIGNATURE') ?? '';

        // ambil data JSON
        $json = $request->getContent();

        // generate signature untuk dicocokkan dengan X-Callback-Signature
        $signature = hash_hmac('sha256', $json, $this->privateKey);

        // validasi signature
        // if( $callbackSignature !== $signature ) {
        //     return "Invalid Signature"; // signature tidak valid, hentikan proses
        // }

        $data = json_decode($json);
        $event = $request->server('HTTP_X_CALLBACK_EVENT');

        if( $event == 'payment_status' )
        {
            $reference = $data->reference;

            // pembayaran sukses, lanjutkan proses sesuai sistem Anda, contoh:
            $transaction = Transaction::where('reference', $reference)
                ->where('status', 'UNPAID')
                ->first();

            if( !$transaction ) {
                return "Order not found or current status is not UNPAID";
            }

            // Lakukan validasi nominal
            if( intval($data->total_amount) !== intval($transaction->total_amount) ) {
                return "Invalid amount";
            }

            if( $data->status == 'PAID' ) // handle status PAID
            {
                $transaction->update([
                    'status'	=> 'PAID'
                ]);

                return response()->json([
                    'success' => true
                    ]);
            }
            elseif( $data->status == 'EXPIRED' ) // handle status EXPIRED
            {
                $transaction->update([
                    'status'	=> 'UNPAID'
                ]);

                return response()->json([
                    'success' => true
                    ]);
            }
            elseif( $data->status == 'FAILED' ) // handle status FAILED
            {
                $transaction->update([
                    'status'	=> 'UNPAID'
                ]);

                return response()->json([
                    'success' => true
                    ]);
            }
        }

        return "No action was taken";
    }
}

