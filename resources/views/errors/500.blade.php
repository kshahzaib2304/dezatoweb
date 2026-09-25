@extends('errors.layout')

@php
    $code = '500';
    $title = 'Something went wrong';
    $heading = 'Something went wrong in the kitchen';
    $message = 'We’re fixing it. Please try again in a moment. Your cart is saved on this device.';
@endphp

@section('actions')
    <a class="btn btn--primary" href="{{ url('/') }}">Back home</a>
    <a class="btn btn--outline" href="{{ url('/menu') }}">Browse menu</a>
@endsection
