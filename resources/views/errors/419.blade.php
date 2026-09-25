@extends('errors.layout')

@php
    $code = '419';
    $title = 'Session expired';
    $heading = 'Your session expired';
    $message = 'For your security, the page timed out. Refresh and try again — nothing was charged.';
@endphp

@section('actions')
    <a class="btn btn--primary" href="{{ url()->previous() !== url()->current() ? url()->previous() : url('/') }}">Try again</a>
    <a class="btn btn--outline" href="{{ url('/') }}">Back home</a>
@endsection
