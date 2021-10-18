<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TripayController extends Controller
{
    public function getPaymentChannels()
    {

        $apiKey = env('TRIPAY_API_KEY');
        // dd($apiKey);

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_FRESH_CONNECT     => true,
            CURLOPT_URL               => "https://tripay.co.id/api-sandbox/merchant/payment-channel",
            CURLOPT_RETURNTRANSFER    => true,
            CURLOPT_HEADER            => false,
            CURLOPT_HTTPHEADER        => array(
                "Authorization: Bearer ".$apiKey
            ),
            CURLOPT_FAILONERROR       => false
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        $response = json_decode($response)->data;
        // dd(json_decode($response)->data);
        return $response ? $response : $err;

    }


    public function requestTransaction($method, $book)
    {

        $apiKey = env('TRIPAY_API_KEY');
        $privateKey = env('TRIPAY_PRIVATE_KEY');
        $merchantCode = env('TRIPAY_MERCHANT_CODE');
        // dd($apiKey, $privateKey, $merchantCode);
        $merchantRef = 'RFS-'. time();
        // $amount = 1000000;
        $user = Auth()->user();

        $data = [
            'method'            => $method,
            'merchant_ref'      => $merchantRef,
            'amount'            => $book->price,
            'customer_name'     => $user->name,
            'customer_email'    => $user->email,
            // 'customer_phone'    => '081234567890',
            'order_items'       => [
                [
                    // 'sku'       => 'PRODUK1',
                    'name'      => $book->title,
                    'price'     => $book->price,
                    'quantity'  => 1
                    ]
                ],
                // 'callback_url'      => 'https://domainanda.com/callback',
                // 'return_url'        => 'https://domainanda.com/redirect',
                'expired_time'      => (time()+(24*60*60)), // 24 jam
                'signature'         => hash_hmac('sha256', $merchantCode.$merchantRef.$book->price, $privateKey)
            ];

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_FRESH_CONNECT     => true,
                CURLOPT_URL               => "https://tripay.co.id/api-sandbox/transaction/create",
                CURLOPT_RETURNTRANSFER    => true,
                CURLOPT_HEADER            => false,
                CURLOPT_HTTPHEADER        => array(
                    "Authorization: Bearer ".$apiKey
                ),
                CURLOPT_FAILONERROR       => false,
                CURLOPT_POST              => true,
                CURLOPT_POSTFIELDS        => http_build_query($data)
            ));

            $response = curl_exec($curl);
            $err = curl_error($curl);

            curl_close($curl);

            $response = json_decode($response)->data;

            // dd($response);

            return $response ? $response : $err;

        }


        public function detailTransaction($reference)
        {
            $apiKey = env('TRIPAY_API_KEY');

            $payload = [
                'reference'	=> $reference
            ];

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_FRESH_CONNECT     => true,
                CURLOPT_URL               => "https://tripay.co.id/api-sandbox/transaction/detail?".http_build_query($payload),
                CURLOPT_RETURNTRANSFER    => true,
                CURLOPT_HEADER            => false,
                CURLOPT_HTTPHEADER        => array(
                    "Authorization: Bearer ".$apiKey
                ),
                CURLOPT_FAILONERROR       => false,
            ));

            $response = curl_exec($curl);
            $err = curl_error($curl);

            curl_close($curl);

            $response = json_decode($response)->data;

            return $response ? $response : $err;

        }
    }
