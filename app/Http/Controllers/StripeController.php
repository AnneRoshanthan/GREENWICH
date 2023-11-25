<?php

namespace App\Http\Controllers;

use App\Models\Add_to_cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Stripe\Checkout\Session;
use Illuminate\Support\Facades\Auth;

class StripeController extends Controller
{
    public function buyProduct(Request $request)
    {
        try {
            $request->validate([
                "id" => "required|exists:products,id",
                "quantity" => "required|integer|gt:0",
            ]);

            $data[] = $request->only([
                'id',
                'quantity',
            ]);

            $lineItems = [];
            foreach ($data as $item) {
                $product = Product::find($item['id']);

                $line_item = [
                    [
                        'price_data' => [
                            'currency' => 'USD',
                            'product_data' => [
                                'name' => $product['name'],
                            ],
                            'unit_amount' => $product['price'] * 100,
                        ],
                        'quantity' => $item['quantity'],
                    ],
                ];
                $lineItems[] = $line_item;
            }
            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => $lineItems,
                'mode' => 'payment',
                'success_url' => 'http://localhost:8000/orders',
                'cancel_url' => 'http://localhost:8000/cancel'
            ]);

            foreach ($data as $item) {
                Add_to_cart::where('product_id', $item['id'])->where('user_id',Auth::user()->id)->update(['quantity'=> $item['quantity']]);
            }
            
            return Redirect($session->url);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function buyManyProduct(Request $request)
    {
        try {
            
            $request->validate([
                "products.*.id" => "required|exists:products,id",
                "products.*.quantity" => "required|integer|gt:0",
            ]);
            
            $data = $request->only('products');
            
            $lineItems = [];
            foreach ($data['products'] as $item) {
                $product = Product::find($item['id']);
                $line_item = [
                    
                        'price_data' => [
                            'currency' => 'USD',
                            'product_data' => [
                                'name' => $product['name'],
                            ],
                            'unit_amount' => $product['price'] * 100,
                        ],
                        'quantity' => $item['quantity'],
                    
                ];
                $lineItems[] = $line_item;
            }
            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => $lineItems,
                'mode' => 'payment',
                'success_url' => 'http://localhost:8000/order' . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => 'http://localhost:8000/cancel',
                'client_reference_id' => Auth::user()->id
            ]);

            foreach ($data['products'] as $item) {
                Add_to_cart::where('product_id', $item['id'])->where('user_id',Auth::user()->id)->where('status',null)
                ->update(['quantity'=> $item['quantity'],'session'=>$session->id]);
            }
            return $session->url;
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
