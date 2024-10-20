@extends('layouts.default')

@section('content')

<section class="bg-gray-50">
    <div class="mx-auto max-w-screen-xl px-4 py-32 lg:flex lg:h-screen lg:items-center">
        <div class="mx-auto max-w-xl text-center">
            <h1 class="text-3xl font-extrabold sm:text-5xl">
                <strong class="font-extrabold text-red-700 sm:block">  Welcome to Stock Price Monitoring application! </strong>
            </h1>

            <p class="mt-4 sm:text-xl/relaxed">
                In order to see the recent price changes the database should be synced with the market provider. Please make sure that you have registered with Stock Market Data API service and updated the API key in the environment file. Then click on the button bellow to manually fetch the data for the first time.
            </p>

            <p class="mt-4 text-red-400 sm:text-sm/relaxed">
                This may take a while. Please be patient.
            </p>

            <div class="mt-8 flex flex-wrap justify-center gap-4">
                <a
                class="block w-full rounded bg-red-600 px-12 py-3 text-sm font-medium text-white shadow hover:bg-red-700 focus:outline-none focus:ring active:bg-red-500 sm:w-auto"
                href="{{ route('seed.run') }}"
                >
                Fetch Data
            </a>
        </a>
    </div>
</div>
</div>
</section>

@endsection
