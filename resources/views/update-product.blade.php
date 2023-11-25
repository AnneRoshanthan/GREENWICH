<style>
    .upload {
        position: relative;
        margin: auto;
    }

    .upload img {
        border: 5px solid #eaeaea;
        max-width: 450px;
        max-height: 300px;
        width: 100%;
        object-fit: cover;
    }

    .upload .round {
        text-align: center;
        overflow: hidden;
    }

    .upload .round input[type="file"] {
        position: absolute;
        left: 37.6rem;
        top: 7.4rem;
        transform: scale(4,9.3);
        opacity: 0;
    }

    input[type=file]::-webkit-file-upload-button {
        cursor: pointer;
        color: red;
    }

    .fa-cloud-arrow-up{
        font-size: 3rem;
        color: yellowgreen !important;
        z-index: 500;
    }
</style>
@include('nav')
<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('update',$product->id) }}" enctype="multipart/form-data">
        @csrf
@method('PUT')
         <!-- Image -->
         <div class="upload ">
            <img src="{{asset('product/' . $product->image)}}" class="image-fluid" id="selectedImg"/>
            <!-- <img  class="image-fluid" id="selectedImg"/> -->
            <div class="round">
                <input id="image" class="block mt-1 w-full" type="file" onchange="onFileChange(event)" name="image" required />
            </div>
            <!-- <x-input-label for="image" :value="__('image')" /> -->


            <x-input-error :messages="$errors->get('image')" class="mt-2" />
        </div>

        <!-- Name -->
        <div class="mt-4">
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" required autofocus  value="{{$product->name}}"/>
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Price -->
        <div class="mt-4">
            <x-input-label for="price" :value="__('price')" />
            <x-text-input id="price" class="block mt-1 w-full" type="number" name="price" required value="{{$product->price}}"/>

            <x-input-error :messages="$errors->get('price')" class="mt-2" />
        </div>

        <!-- Description -->
        <div class="mt-4">
            <x-input-label for="description" :value="__('description')" />
            <x-text-input id="description" class="block mt-1 w-full" type="text" name="description" required value="{{$product->description}}"/>

            <x-input-error :messages="$errors->get('description')" class="mt-2" />
        </div>

       
        <div class="text-center">
            <x-primary-button class="ml-3 mt-4 ">
                {{ __('Update') }}
            </x-primary-button>
        </div>
        </div>
    </form>
    @include('footer')
</x-guest-layout>

<script>
    function onFileChange(event) {
        const reader = new FileReader();
        if(event.target.files && event.target.files.length){
            const [file] = event.target.files;
            reader.readAsDataURL(file);
            reader.onload =()=>{
                let files = event.target.files[0];
                if(files.type !=='image/jpeg' && files.type !== 'image/png'){
                    alert("oncorrect file format")
                    return;
                }
                const selectedImg = document.getElementById('selectedImg');
                selectedImg.src = reader.result
                // console.log(reader.result);
            }
        }
    }
</script>