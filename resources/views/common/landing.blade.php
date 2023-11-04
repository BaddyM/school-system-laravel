@extends('common.header')

@section('title')
C.H.S - Home
@endsection

@section('body')

<div class="container-fluid">
    @php
        $user_name = 'User';
    @endphp
    <h4 class="d-flex align-items-center">Welcome <div><i class="bi bi-person-fill mb-1"></i></div> <b class="text-primary fw-bold"> {{ $user_name }}</b></h4>
</div>
    
@endsection
