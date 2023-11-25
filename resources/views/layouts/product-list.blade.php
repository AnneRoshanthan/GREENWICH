@extends('product-list')

@section('content')
<div class="row ">
    @foreach($products as $product)
    <div class="d-flex justify-content-center col col-12 col-sm-6 col-md-4 col-lg-3">
        <div class="card mt-3" style="width: 18rem;">
            <img src="{{asset('product/' . $product->image)}}" class="card-img-top" style="height: 191px;" alt="...">
            <div class="card-body">
                <h5 class="card-title">{{$product->name}}</h5>
                <div class="text-center">
                    <a href="p/{{$product->id}}" class="btn btn-primary">View</a>
                    <a href="#" class="btn btn-primary">Cart</a>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endsection