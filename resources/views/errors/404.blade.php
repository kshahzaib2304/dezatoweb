@extends('errors.layout')

@php
    $code = '404';
    $title = 'Page not found';
    $heading = 'We can’t find that page';
    $message = 'The link may be outdated, or the treat you’re looking for has moved. Head back to the menu and keep browsing.';
@endphp

@section('actions')
    <a class="btn btn--primary" href="{{ url('/menu') }}">Browse menu</a>
    <a class="btn btn--outline" href="{{ url('/') }}">Back home</a>
@endsection
