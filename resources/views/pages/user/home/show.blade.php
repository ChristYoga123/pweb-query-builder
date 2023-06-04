@extends('layouts.user.app')
@section('content')
    <div class="w-full h-screen flex justify-center">
        <div class="mt-36 flex flex-col items-center">
            <h1 class="text-center text-4xl font-semibold text-[#152C5B]">
                Village Angga
            </h1>
            <p class="text-center mt-2 text-lg font-light text-[#B0B0B0]">Bogor, Indonesia</p>

            <div class="hero-image my-5">
                <img src="{{ asset("assets/images/hero.png") }}" alt="">
            </div>
        </div>
    </div>

    <div class="px-24 flex gap-12 my-10">
        <div class="description flex flex-col gap-5">
            <h1 class="text-lg font-semibold">About the Place</h1>
            <p class="text-justify text-[#B0B0B0]">
                Lorem ipsum dolor sit amet consectetur adipisicing elit. Aperiam quia expedita iure impedit quo alias. Amet magnam exercitationem, quae porro repudiandae consequuntur ducimus! Obcaecati, iste ducimus tempora, saepe quo nostrum sapiente eius blanditiis nesciunt corrupti nulla aspernatur est minima similique! Cum ex a quisquam fugiat aut voluptates quas aliquam unde laboriosam. Facere at quis quibusdam debitis ratione voluptatum, cumque cum esse suscipit modi perspiciatis aspernatur iure? Vitae sequi, dolor sed deleniti est sint pariatur minus numquam quibusdam ipsam similique asperiores alias molestiae, modi temporibus ut quidem laborum velit eum perferendis placeat at mollitia! Rem cupiditate, quam aliquam quis itaque nulla.
            </p>
        </div>

        <div class="pricing">
            <div class="w-[400px] shadow-md py-12 px-16 flex flex-col gap-5 rounded-lg">
                <h1 class="text-lg font-semibold">Start Booking</h1>
                <h1><span class="text-2xl font-semibold text-green-500">$280 </span> <span class="text-2xl font-light">per night</span></h1>
                <div class="q1 flex flex-col gap-3">
                    <h6 class="text-sm">How long will you stay?</h6>
                    <input type="number" class="input w-full border border-gray-400" placeholder="Masukkan jumlah malam">
                </div>
                <div class="q2 flex flex-col gap-3">
                    <h6 class="text-sm">Pick a date</h6>
                    <input type="date" class="input w-full border border-gray-400">
                    <h6 class="text-sm mx-auto">To</h6>
                    <input type="date" class="input w-full border border-gray-400">
                    <h6 class="text-sm text-[#B0B0B0]">You will pay <span id="price" class="text-[#152C5B] font-semibold">$ 420</span> per <span id="night" class="text-[#152C5B] font-semibold">2 nights</span></h6>
                </div>
                <button class="btn bg-[#3252DF] text-white">Continue to Book</button>
            </div>
        </div>
    </div>

    <div class="mt-10 w-full lg:px-24">
        <div class="house flex flex-col gap-5">
            <p class="text-3xl font-semibold">Galeri</p>
            <div class="flex gap-5">
                <div class="flex flex-wrap flex-col">
                    <img src="{{ asset("assets/images/placeholders/200x200.jpg") }}" width="263px" height="180px">
                </div>

                <div class="flex flex-wrap flex-col">
                    <img src="{{ asset("assets/images/placeholders/200x200.jpg") }}" width="263px" height="180px">
                </div>

                <div class="flex flex-wrap flex-col">
                    <img src="{{ asset("assets/images/placeholders/200x200.jpg") }}" width="263px" height="180px">
                </div>

                <div class="flex flex-wrap flex-col">
                    <img src="{{ asset("assets/images/placeholders/200x200.jpg") }}" width="263px" height="180px">
                </div>

                <div class="flex flex-wrap flex-col">
                    <img src="{{ asset("assets/images/placeholders/200x200.jpg") }}" width="263px" height="180px">
                </div>

                <div class="flex flex-wrap flex-col">
                    <img src="{{ asset("assets/images/placeholders/200x200.jpg") }}" width="263px" height="180px">
                </div>
            </div>
        </div>
    </div>

@push('script')

@endpush
@endsection