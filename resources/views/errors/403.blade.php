@extends('errors.layout')

@php
    $code = '403';
    $title = 'Access denied';
    $heading = 'You don’t have access';
    $message = 'This area is only for Dezato store managers. If you need help with an order, sign in to your account or browse the menu.';
@endphp

@section('actions')
    <a class="btn btn--primary" href="{{ url('/menu') }}">Browse menu</a>
    <a class="btn btn--outline" href="{{ url('/login') }}">Sign in</a>
@endsection
