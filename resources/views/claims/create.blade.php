@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            @include('claims.includes.create', ['route' => route('claims.store'), 'item' => $item])
        </div>
    </div>
</div>
@endsection