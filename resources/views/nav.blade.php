<style>
    .count{
        position: relative;
        right: 7px;
    }
</style>
<nav class="navbar navbar-expand-lg bg-body-tertiary">
    <div class="container-fluid">
        <a class="navbar-brand" href="/">GREENWICH</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <!-- <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="/">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Link</a>
                </li> -->


            </ul>
            <ul class="navbar-nav d-flex">
                @if (Route::has('login'))
                @auth
                @if ( Auth::user()->role == 'admin')
                <i class="bi bi-0-circle"></i>
                <li class="nav-item">
                <a href="{{ url('/create') }}" class="nav-link">Create Product</a>
                </li>
                <li class="nav-item">
                <a href="{{ url('/dashboard') }}" class="nav-link">Dashboard</a>
                </li>
                @endif   
                <li class="nav-item"><a class="nav-link" href="{{ route('cart') }}"><i class="fa-solid fa-cart-shopping"></i></a></li>
               <span class="count text-success" id="count"></span>
               <li class="nav-item"><a class="nav-link" href="{{ route('order') }}">Orders</a></li>
               <li class="nav-item"><a class="nav-link" href="/profile">Profile</a></li>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <li class="nav-item"><button type="submit" class="nav-link" >{{ __('Log Out') }}</button></li>
                </form>
                <!-- <a href="{{ url('/') }}" class="font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">Dashboard</a> -->
                 
                @else
                <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Login</a></li>
                @if (Route::has('register'))
                <li class="nav-item"><a class="nav-link" href="{{ route('register') }}">Register</a></li>
                @endif
               
                @endauth

                
                @endif
            </ul>
            <!-- <form class="d-flex" role="search">
        <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
        <button class="btn btn-outline-success" type="submit">Search</button>
      </form> -->
        </div>
    </div>
</nav>

<script>
    window.onload = function () {
        fetch('/count').then(response=>{
            return response.text();
        }).then(res=>{
            let count = document.getElementById('count')
            count.innerText= res
        })
    }
</script>