@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <ul class="nav nav-pills">
                  <li class="nav-item"><a data-toggle="pill" href="#claim" class="nav-link active">Заявка</a></li>
                  <li class="nav-item"><a data-toggle="pill" href="#responses" class="nav-link">Ответы</a></li>
                </ul>
                <div class="card-body tab-content">
                  <div id="claim" class="tab-pane active">
                    <h3>{{$item->subject}}</h3>
                    <p>{{$item->body}}</p>
                    @if (!empty($item_files))
                    <h5 class="mt-2">Прикрепленные файлы</h5>
                    <ul class="list-unstyled">
                      @foreach ($item_files as $file)
                        <li><a href="{{ route('files.download', $file['file_id']) }}">{{$file['original_name']}}</a></li>
                      @endforeach
                    </ul>
                    @endif
                  </div>
                  <div id="responses" class="tab-pane fade">
                    <h3>Ответы</h3>
                    <p>Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
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