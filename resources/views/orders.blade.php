<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

@include('nav')
<div style="overflow: hidden;">
    <table class="table table-primary table-striped table-hover table-responsive">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Name</th>
                <th scope="col">Price</th>
                <th scope="col">Quantity</th>
                <th scope="col">total</th>
                <th scope="col">Status</th>
                <th scope="col">Date</th>
            </tr>
        </thead>

        <tbody>
            @foreach($orders as $product)
            <tr data-product-id="{{$product->id}}">
                <th scope="row">
                    <a href="p/{{$product->id}}">
                        <img src="{{asset('product/' .$product->product->image)}}" class="" style="width:50px;height: 50px; object-fit: cover; object-position: center;" alt="" srcset="">
                </th>
                </a>
                <td>{{$product->product->name}}</td>
                <td class="product-price">${{$product->product->price}}</td>
                <td>
                    {{$product->quantity}}
                </td>
                <td class="product-total">
                    ${{$product->product->price * $product->quantity}}
                </td>
                <td class="">
                @if (Route::has('login'))
                {{$product->status}}
                @auth
                @if ( Auth::user()->role == 'admin')
                    <!-- {{$product->status}} -->
                    <form action="{{ route('status') }}" method="post" id="statusForm_{{$product->id}}">
                        @csrf
                        @method("PATCH")
                        <!-- {{$product}} -->
                        <input type="text" name="id" value="{{$product->id}}" hidden>
                        <select id="statusSelect" name="status" selected="{{$product->status}}" class="form-control" style="width: 100px;" onchange="onChange(this)">
                            <option class="bg-warning" value="pending" @if($product->status == 'pending') selected @endif>Pending</option>
                            <option class="bg-info" value="shipped" @if($product->status == 'shipped') selected @endif >Shipped</option>
                            <option class="bg-success" value="delivered" @if($product->status == 'delivered') selected @endif>Delivered</option>
                            <option class="bg-danger" value="cancel" @if($product->status == 'cancel') selected @endif>Cancel</option>
                        </select>
                    </form>
@endif
@endauth
@endif

                </td>
                <td class="">
                    {{$product->created_at->format('Y-m-d')}}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@include('footer')

<script>
    function onChange(element) {
        const form = element.closest('form');
        const formData = new FormData(form);

        fetch(form.action, {
            method: 'POST',
            body: formData
        }).then((response) => {
            // console.log(response);
            // if (response =='Success') {
            //     alert("Updated successful")
            //     window.location.href = '/order'
            // }
        })
    }




    //     $(document).ready(function() {
    //  $('#statusSelect').select();
    //  $('#statusSelect').on('change',function(){
    //     const selectedValue = $(this).val();
    //     $(this).find('option').removeClass().addClass('bg-default');
    //     $(this).find('option[value-"' + selectedValue +'"]').addClass('bg'+selectedValue);
    //  })      
    //     })
</script>