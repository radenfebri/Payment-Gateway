<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="bg-gray-50">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>{{ config('app.name') }}</title>
	<link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>

<body class="antialiased font-poppins">
    <div class="w-[60%] my-12 mx-auto">

        @include('components.frontend.navbar')

        @yield('content')
    </div>
</body>