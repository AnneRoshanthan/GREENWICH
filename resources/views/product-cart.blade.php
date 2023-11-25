<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">


<style>
    input[type="number"] {
        -webkit-appearance: textfield;
        -moz-appearance: textfield;
        appearance: textfield;
    }

    input[type="number"]::-webkit-inner-spin-button,
    input[type="number"]::-webkit-outer-spin-button {
        -webkit-appearance: none;
    }

    .number-input {
        /* border: 2px solid #ddd; */
        display: inline-flex;
    }

    .number-input,
    .number-input * {
        box-sizing: border-box;
    }

    .number-input button {
        outline: none;
        content: '+';
        -webkit-appearance: none;
        background-color: transparent;
        border: none;
        align-items: center;
        justify-content: center;
        /* width: 3rem;
        height: 3rem; */
        cursor: pointer;
        margin: 0;
        position: relative;
    }

    .number-input button:after {
        display: inline-block;
        position: absolute;
        font-family: 'Franklin Gothic Medium', 'Arial Narrow', Arial, sans-serif;
        font-weight: 900;

    }

    .number-input button.minus:after {
        padding-right: 8px;
        content: '-';
        transform: translate(-50%, -50%) rotate(0deg);
    }

    .number-input button.plus:after {
        content: '+';
        padding-left: 8px;
        transform: translate(-50%, -50%) rotate(0deg);
    }

    .number-input input[type=number] {
        max-width: 56px;
        border: solid #ddd;
        font-size: 1.3rem;
        height: 40px;
        text-align: center;
        margin-top: 4px;
        background-color: darkgray;
    }
</style>

@php
$totalPrice = 0;
@endphp

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
            <th scope="col">Handle</th>
        </tr>
    </thead>
    
    <tbody>
        <tr>
            <th colspan="6" class="text-center">
            @if(count($cartProduct)==0)
            <h2>No products</h2>
            @endif
            </th>
        </tr>
        @foreach($cartProduct as $product)
            <tr data-product-id="{{$product->product->id}}">
                <th scope="row"><img src="{{asset('product/' .$product->product->image)}}" class="" style="width:50px;height: 50px; object-fit: cover; object-position: center;" alt="" srcset=""></th>
                <td>{{$product->product->name}}</td>
                <td class="product-price">${{$product->product->price}}</td>
                <td>
                    <div class="number-input">
                        <button class="minus" onclick="stepDown(this)"></button>
                        <input class="quantity" type="number" name="quantity" required value="{{$product->quantity}}" min="1" oninput="onChangeQuantity(this)" />
                        <button class="plus" onclick="stepUp(this)"></button>

                    </div>
                </td>
                <td class="product-total">
                    ${{$product->product->price * $product->quantity}}
                    @php
                    $totalPrice += $product->product->price * $product->quantity;
                    @endphp
                </td>
                <!-- <td colspan="4">$0.00</td> -->

                <form style="display: inline;" method="POST" action="{{ route('deca',$product->id) }}" id="deleteForm">
                    @csrf
                    @method('DELETE')
                    <td>
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Do you want to delete?')"><i class="fa-solid fa-trash"></i></button>
                    </td>
                </form>

            </tr>
            @endforeach
            <!-- <tr>
                <td colspan="4" class="text-center">Total</td>
                <td colspan="2" id="totalPrice">${{$totalPrice}}</td>
            </tr> -->
        </tbody>
    </table>
    @if(count($cartProduct)!==0)
    <div class="text-center">
        <button class="btn btn-primary " id="buyButton">Buy</button>
    </div>
    @endif
</div>

@if(session('data'))
<script>
    alert("{{session('data')}}")
</script>
@endif

@include('footer')

<script>
    $(document).ready(function() {
        $('#buyButton').on('click', function() {
            var products = [];

            $('tbody tr').each(function() {
                var productId = $(this).data('product-id');
                var quantity = $(this).find('.quantity').val();

                products.push({
                    id: productId,
                    quantity: quantity
                });
            });
            $.ajax({
                type: 'POST',
                url: '{{route("buym")}}',
                data: {
                    _token: '{{ csrf_token() }}',
                    products: products
                },
                success: function(response) {
                    window.location.href = response;
                    console.log('response',response);
                },
                error: function(error) {
                    console.error(error);
                }
            })
        })
    })
</script>

<script>
    function onChangeQuantity(input) {
        var quantity = input.value;
        var productId = input.closest('tr').getAttribute('data-product-id');
        var productPrice = parseFloat(document.querySelector(`[data-product-id="${productId}"] .product-price`).innerText.replace('$', ''));
        console.log();

        let total = 0
        total = quantity * productPrice
        input.closest('tr').querySelector('.product-total').innerText = '$' + total

        let raw = input.closest('tr');
        let ele = raw.querySelector('.product-total').innerText;
        let sp = ele.split('$')[1]
        //  let tp = document.getElementById('#totalPrice');
        //  tp.innerText = sp
        console.log(sp);
        // $('tbody tr').each(function() {
        //         var productId = $(this).find('product-');
        //         var quantity = $(this).find('.quantity').val();
        //         console.log(quantity);
        // })
    }

    function stepUp(button) {
        var input = button.closest('tr').querySelector('.quantity');
        input.value = parseInt(input.value) + 1;
        onChangeQuantity(input)
    }

    function stepDown(button, inputId) {
        var input = button.closest('tr').querySelector('.quantity');
        input.value = parseInt(input.value) - 1;
        onChangeQuantity(input)
    }
</script>