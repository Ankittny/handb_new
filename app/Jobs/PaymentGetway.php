<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use GuzzleHttp\Client;
use App\Models\ShippingAddress;
use App\Models\Shop;
use App\Utils\Helpers;
use Illuminate\Support\Facades\Log;
use DB;

class PaymentGetway implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $order = Order::where(['order_status'=>'confirmed','delhivery_status'=>1])->get();
        foreach ($order as $key => $value) {
            $order_details = OrderDetail::where('order_id', $value->id)->get();
            foreach($order_details as $key1 => $value1){
                $product = Product::where(['id' => $value1->product_id])->with('digitalVariation')->first()->toArray();
                $total_cod_price = $value1->price*$value1->qty;
                $totaltex = $value1->tax*$value1->qty;
                $totaldiscount = $value1->discount*$value1->qty;
                $totalshipping = $value1->shipping_cost*$value1->qty;
                $total_cod =  Helpers::delevery_currency_converter(($total_cod_price + $totaltex + $totalshipping-$totaldiscount));
                $client = new Client();
                $url = 'https://track.delhivery.com/api/cmu/create.json';
                $token = '298946431eb6b00835b0cf6aaaad8c9a4242c111';
                $data1 = [
                    'shipments' => [
                        [
                            'name' => ShippingAddress::find($value->shipping_address)->contact_person_name,
                            'add' => ShippingAddress::find($value->shipping_address)->address,
                            'pin' => ShippingAddress::find($value->shipping_address)->zip,
                            'city' => ShippingAddress::find($value->shipping_address)->city,
                            'state' => ShippingAddress::find($value->shipping_address)->state,
                            'country' => ShippingAddress::find($value->shipping_address)->country,
                            'phone' => ShippingAddress::find($value->shipping_address)->phone,
                            'order' => $value1['order_id'],
                            'payment_mode' => 'prepaid',
                            'return_pin' => '',
                            'return_city' => '',
                            'return_phone' => '',
                            'return_add' => '',
                            'return_state' => '',
                            'return_country' => '',
                            'products_desc' => $product['name'],
                            'hsn_code' => $product['code'],
                            'cod_amount' => $total_cod,
                            'order_date' => now(),
                            'total_amount' => $total_cod,
                            'seller_add' => Shop::where(['seller_id' => $value1['seller_id']])->first()->address,
                            'seller_name' => Shop::where(['seller_id' => $value1['seller_id']])->first()->name,
                            'seller_inv' => '',
                            'quantity' => $value1['qty'],
                            'waybill' => '',
                            'shipment_width' => $product['weight_grams'],
                            'shipment_height' => $product['weight_grams'],
                            'weight' => $product['weight_grams'],
                            'seller_gst_tin' => '',
                            'shipping_mode' => 'Surface',
                            'address_type' => Shop::where(['seller_id' => $value1['seller_id']])->first()->address_type,
                        ]
                    ],
                    'pickup_location' => [
                        'name' => Shop::where(['seller_id' => $value1['seller_id']])->first()->name,
                        'add' => Shop::where(['seller_id' => $value1['seller_id']])->first()->address,
                        'city' => Shop::where(['seller_id' => $value1['seller_id']])->first()->city,
                        'pin_code' => Shop::where(['seller_id' => $value1['seller_id']])->first()->pin_code,
                        'country' => Shop::where(['seller_id' => $value1['seller_id']])->first()->country,
                        'phone' => Shop::where(['seller_id' => $value1['seller_id']])->first()->contact
                    ]
                ];
                try {
                    $response = $client->post($url, [
                        'headers' => [
                            'Authorization' => "Token $token",
                            'Accept'        => 'application/json',
                        ],
                        'form_params' => [
                            'format' => 'json',
                            'data'   => json_encode($data1)
                        ],
                        'verify' => false,
                    ]);
                    $responseBody = $response->getBody()->getContents();
                    Log::info('onedelhivery order generate for awb ' . $responseBody);
                    $returnData =  json_decode($responseBody);
                    Log::info('create response================> ' .json_encode($data1));
                    Log::info('onedelhivery order generate for order id ' . $value1['order_id']);

                    
                    DB::table('orders')->where('id', $value1['order_id'])
                        ->update([
                            'delivery_service_name' => "onedelhivery",
                            'third_party_delivery_tracking_id' => $returnData->packages[0]->waybill
                    ]);
                  	DB::table('order_details')->where('id', $value1['id'])
                    ->update([
                        'delivery_service_name' => "onedelhivery",
                        'third_party_delivery_tracking_id' => $returnData->packages[0]->waybill
                    ]);
                    DB::table('order_details')->where('id', $value1['id'])
                    ->update([
                        'delivery_service_name' => "onedelhivery",
                        'third_party_delivery_tracking_id' => $returnData->packages[0]->waybill
                    ]);
                    Log::info('onedelhivery order generate for awb ' . $responseBody);
                    // return response()->json(json_decode($responseBody));
                } catch (\Exception $e) {
                    Log::info('onedelhivery order generate for order id ' . $e);
                }
            }
            $value->order_status = 'confirmed';
            $value->delhivery_status = 0;
            $value->save();
        }
    }
}