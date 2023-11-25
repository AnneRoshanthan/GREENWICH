<?php

namespace App\Http\Controllers;

use App\Models\Add_to_cart;
use App\Models\Product;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Stripe\Checkout\Session;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use function Laravel\Prompts\alert;

class ProductController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware("auth:api", ["except" => ["index", "changeStatus", "getOrder", "cartCount", "orders", "getAddToCart", "deleteCart", "numberOfAddToCart", "update", "store", "indexById", "indexById2", "addToCart", "delete"]]);
    // }
    public function index(Request $request)
    {
        try {
            $category = $request->input('category');
            $search = $request->input('search');
            $query = Product::query();
            if ($category == '<10') {
                $query->where('category', '<', 10);
            } elseif ($category == '10-30') {
                $query->whereBetween('category', [10, 30]);
            } elseif ($category == '30-60') {
                $query->whereBetween('category', [30, 60]);
            } elseif ($category == '>60') {
                $query->where('category', '>', 60);
            }

            if ($search) {
                $query->where('name', 'like', "%$search%");
            }


            $product = $query->paginate(12);
            return view("welcome", ['products' => $product]);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function cartCount()
    {
        try {
            $cartCount = 0;
            if (Auth::user()) {
                $cartCount = Add_to_cart::where('user_id', Auth::user()->id)->where('status', null)->count();
            }
            return $cartCount;
            // return view("nav", ['count' => $cartCount]);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function indexById($id)
    {
        try {
            $product = Product::find($id);
            return view("product", ['product' => $product]);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function indexById2($id)
    {
        try {
            $product = Product::find($id);
            return view("update-product", ['product' => $product]);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
    public function store(Request $request)
    {
        try {
            $data = $request->all();
            $file = $request->file('image');
            Validator::make($data, [
                "name" => ["required", "string"],
                "image" => ["required", "!mimes:jpg,jpeg,png|max:2048"],
                "price" => ["required", "double"],
                "category" => ["required", "string"],
                "description" => ["required", "string"],
            ]);

            $data = $request->only(["name", "price", "category", "image", "description"]);
            $data['image'] = $this->saveImg($data['image']);
            Product::create($data);
            return redirect("/create")->with("success", "Product Created");
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function update(Request $request)
    {
        try {
            $data = $request->all();
            $file = $request->file('image');
            Validator::make($data, [
                "name" => ["required", "string"],
                "price" => ["required", "double"],
                "description" => ["required", "string"],
                "image" => ["required"],
            ]);

            $data = $request->only(["name", "price", "image", "description"]);
            if ($file) {
                $data['image'] = $this->saveImg($data['image']);
            }
            $product = Product::find($request->id);
            $product->update($data);

            return redirect("/p/$request->id")->with("success", "Product Updated");
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function delete(Request $request)
    {
        try {
            Add_to_cart::where("product_id", $request->id)->delete();
            Product::where("id", $request->id)->delete();
            return redirect("../")->with("success", "Product deleted");
        } catch (\Throwable $th) {
            throw $th;
        }
    }
    public function deleteCart(Request $request)
    {
        try {
            Add_to_cart::where("id", $request->id)->delete();
            return redirect("/cart")->with("success", "Product deleted");
        } catch (\Throwable $th) {
            throw $th;
        }
    }
    public function addToCart(Request $request)
    {
        try {
            $cart = Add_to_cart::where('product_id', $request->product_id)->where('user_id', Auth::user()->id)->where('status', null)->first();

            if ($cart) {
                return redirect("/")->with(['data' => 'Already added']);
            }

            $request->validate([
                "product_id" => "required|exists:products,id",
            ]);
            $data = $request->only([
                'product_id',
            ]);
            $data['user_id'] = Auth::user()->id;
            Add_to_cart::create($data);

            return redirect("/")->with("success", "Cart added");
        } catch (\Throwable $th) {
            return redirect("/login");
        }
    }

    public function getAddToCart()
    {
        try {

            $cartProduct = Add_to_cart::with('product')->where('user_id', Auth::user()->id)->where('status', null)->get();
            return view("product-cart", ['cartProduct' => $cartProduct]);

        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function numberOfAddToCart(Request $request)
    {
        try {
            $cartCount = Add_to_cart::where('user_id', Auth::user()->id)->where('status', null)->get()->count();
            return view("nav", ['count' => $cartCount]);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function getOrder(Request $request)
    {
        try {

            $sessionId = $request->get('session_id');

            if ($sessionId) {
                try {
                    $session = Session::retrieve($sessionId);
                } catch (\Throwable $th) {
                    throw new NotFoundHttpException;
                }
                $cart = Add_to_cart::where('session', $sessionId)->update(['status' => "Pending", 'session' => null]);

                $orders = Add_to_cart::with('product')->where('user_id', Auth::user()->id)->where('session', null)->whereNotNull('status')->orderByDesc('created_at')->get();
                return view("orders", ['orders' => $orders]);
            } else {
                $orders = Add_to_cart::with('product')->where('user_id', Auth::user()->id)->where('session', null)->whereNotNull('status')->orderByDesc('created_at')->get();
                return view("orders", ['orders' => $orders]);
            }
            
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function orders(Request $request)
    {
        try {
            $search = $request->input('search');
            $query = Product::query();
            if ($search) {
                $query->where('name', 'like', "%$search%");
            }

            $orders = $query->paginate(10);
            return view("orders", ['orders' => $orders]);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function changeStatus(Request $request)
    {
        try {
            $status = $request->all();
            Add_to_cart::where('id', $status['id'])->update(['status' => $status['status']]);

            return "Success";
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    
    public function saveImg($image)
    {
        try {
            $timehash = md5(Carbon::now());
            $filename = $timehash . '_' . $image->getClientOriginalName();
            return  $image->storeAs('/', $filename, 'product');
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
