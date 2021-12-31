@extends('layouts.main')

@section('content')
    <div class="flex h-screen bg-gray-100">
        <div class="m-auto p-3 w-3/4 md:w-1/3">
            <h1 class="text-4xl text-center font-bold my-10">Login</h1>

            @include('components.login.form')
        </div>
    </div>
@endsection
