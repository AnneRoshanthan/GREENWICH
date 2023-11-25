<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Styles -->
</head>

<body >
@include('nav')
<div class="card mb-3 mt-3" >
  <div class="row g-0">
    <div class="col-md-4">
      <img src="{{asset('product/' . $product->image)}}" class="img-fluid rounded-start" alt="...">
    </div>
    <div class="col-md-8">
      <div class="card-body">
        <h5 class="card-title text-center">{{$product->name}}</h5>
        <div style="height: 180px; overflow:auto;">
            <p class="card-text">{{$product->description}}</p>
        </div>
        <form method="POST" action="{{ route('buy') }}" id="buyForm">
        @csrf
        <div class="d-flex justify-content-between">
          <div>
            <p class="card-text">Price: ${{$product->price}}</p>
            <x-text-input id="id" class="block mt-1 w-full" type="text" name="id" required value="{{$product->id}}" hidden/>
          </div>

            <div >
            <p class="card-text">Qty : 
                <button type="button" class="btn btn-outline-secondary" onclick="document.getElementById('quantity').stepDown()">-</button>
                <x-text-input id="quantity" style="width: 70px;" class="d-inline mt-1 w-full form-control" type="number" name="quantity" min="1" value="1"/>
<button type="button" class="btn btn-outline-secondary" onclick="document.getElementById('quantity').stepUp()">+</button>
</p>
</div>
            
          </div>
        </div>
        </form>
        <form method="POST" action="{{ route('atc') }}" id="addtoCartForm">
        @csrf
        <x-text-input id="product_id" class="block mt-1 w-full" type="text" name="product_id" required value="{{$product->id}}" hidden/>
        <x-text-input id="quantity" class="block mt-1 w-full" type="number" name="quantity" required value="{{$product->id}}" hidden/>
      </form>
        <div class="text-center">
          <button class="btn btn-primary " onclick="addToCart()">Cart</button>
          <button onclick="buy()" class="btn btn-primary">Buy</button>
        </div>
      
    </div>
  </div>
</div>


<script>
  function buy() {
    @auth
    document.getElementById('buyForm').submit();
    @else
    alert("Please login to buy");
    window.location.href= '/login';
    @endauth
  }

  function addToCart() {
    @auth
    document.getElementById('addtoCartForm').submit();
    @else
    window.location.href= '/login';
    @endauth
  }
  // function decrement() {
  //   document.getElementById
  // }
</script>

</body>

</html>