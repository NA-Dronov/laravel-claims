@extends('layouts.app')

@push('styles')
<link href="{{ asset('css/claims.show.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <ul class="nav nav-pills">
                  <li class="nav-item"><a data-toggle="pill" href="#claim" class="nav-link @if((session()->has('active') && session()->get('active') == 'claim') || !session()->has('active')) active @endif">Заявка</a></li>
                  <li class="nav-item"><a data-toggle="pill" href="#responses" class="nav-link @if((session()->has('active') && session()->get('active') == 'responses')) active @endif">Ответы</a></li>
                </ul>
                <div class="card-body tab-content">
                  <div id="claim" class="tab-pane @if((session()->has('active') && session()->get('active') == 'claim') || !session()->has('active')) active @else fade @endif">
                    <h3>{{$item->subject}}</h3>
                    <p>{{$item->body}}</p>
                    @if ($item->files->isNotEmpty())
                    <ul class="list-unstyled">
                      @foreach ($item->files as $file)
                        <li><a href="{{ route('files.download', $file->file_id) }}"><i class="fas fa-file-download"></i>&nbsp;{{$file->original_name}}</a></li>
                      @endforeach
                    </ul>
                    @endif
                  </div>
                  <div id="responses" class="tab-pane @if(session()->has('active') && session()->get('active') == 'responses') active @else fade @endif">
                    @if ($item->claim_status->code != \App\Models\ClaimStatus::CLOSED)
                    @include('claims.includes.create', ['route' => route('claims.response', [$item->claim_id]), 'item' => $new_reponse])
                    @endif
                    <div class="mt-2 card border-0 container-fluid">
                      <div class="row">
                        @if ($item->responses->isNotEmpty())
                        @foreach ($item->responses as $response)                          
                          <div class="col-md-8 card my-1 @if($response->author->roles->contains('name', 'manager')) response-manager ml-auto @else response-owner mr-auto @endif text-white">
                            <h5 class="card-title clearfix py-1 px-0 m-0"><span class="float-left">{{$response->subject}}</span><span class="float-right">{{$response->author->name}} {{\Carbon\Carbon::parse($response->created_at)->format('d-m-Y H:i')}}</span></h5>
                            <div class="card-body py-1 px-0">
                              <p class="card-text">{{$response->body}}</p>
                              @if ($response->files->isNotEmpty())
                              <ul class="list-unstyled">
                                @foreach ($response->files as $file)
                                  <li><a class="text-white" href="{{ route('files.download', $file->file_id) }}"><i class="fas fa-file-download"></i>&nbsp;{{$file->original_name}}</a></li>
                                @endforeach
                              </ul>
                              @endif
                            </div>
                          </div>
                        @endforeach
                        @else
                        <div class="col-md-12 jumbotron text-center">
                          <p>Ничего не найдено</p>
                        </div>
                        @endif
                      </div>
                    </div>
                  </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    @can('assign_claim')
                    @if (Auth::user()->user_id != $item->manager_id)
                    <form action="{{route('claims.assign', [$item->claim_id, Auth::user()->user_id])}}" method="post" class="form-group row">
                        @csrf
                        <button class="btn btn-primary btn-block" type="submit">Назначить мне</button>
                    </form>
                    @endif
                    @endcan
                    <div class="form-group row">
                        <label for="manager" class="col-sm-5 col-form-label">Ответственный:</label>
                        <div class="col-sm-7">
                          <input type="text" readonly class="form-control-plaintext" id="manager" value="{{ optional($item->manager)->name ?? "--" }}">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="created_at" class="col-sm-5 col-form-label">Дата создания:</label>
                        <div class="col-sm-7">
                          <input type="text" readonly class="form-control-plaintext" id="created_at" value="{{ $item->created_at }}">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="user" class="col-sm-5 col-form-label">Заявитель:</label>
                        <div class="col-sm-7">
                          <input type="text" readonly class="form-control-plaintext" id="user" value="{{ $item->user->name }}">
                        </div>
                    </div>
                    @if ($item->status != \App\Models\ClaimStatus::CLOSED)
                    <form action="{{route('claims.close', [$item->claim_id])}}" method="post" class="form-group row">
                      @csrf
                      <button class="btn btn-danger btn-block" type="submit">Закрыть заявление</button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection