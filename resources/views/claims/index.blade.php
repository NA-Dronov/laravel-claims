@extends('layouts.app')

@push('styles')
<link href="{{ asset('css/claims.index.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10">
            @if($paginator->total() > $paginator->count())
            <div class="float-left">
                {{ $paginator->links() }}
            </div>
            @endif

            <a href="{{ route('claims.create') }}" class="btn btn-primary float-right">Написать заявление</a>
        </div>
    </div>
    <div class="row">
        <div class="col-md-10 index-block">
            <table class="table table-hover">
                <thead>
                    <tr>
                        @include('claims.table.header', ['field_name' => 'claim_id', 'field_desc' => '#', 'sorting' => $sorting])
                        @include('claims.table.header', ['field_name' => 'subject', 'field_desc' => 'Заявка', 'sorting' => $sorting])
                        @include('claims.table.header', ['field_name' => 'status', 'field_desc' => 'Статус', 'sorting' => $sorting])
                        @include('claims.table.header', ['field_name' => 'user', 'field_desc' => 'Отправитель', 'sorting' => $sorting, 'classes' => 'd-none d-sm-table-cell'])
                        @include('claims.table.header', ['field_name' => 'manager', 'field_desc' => 'Ответственный', 'sorting' => $sorting, 'classes' => 'd-none d-md-table-cell'])
                        @include('claims.table.header', ['field_name' => 'created_at', 'field_desc' => 'Дата', 'sorting' => $sorting])
                    </tr>
                </thead>
                <tbody>
                    @foreach ($paginator as $claim)
                        @php
                            /** @var \App\Models\Claim $claim */
                        @endphp
                        <tr>
                            <td>{{ $claim->claim_id }}</td>
                            <td>
                                <a href="{{ route('claims.show', $claim->claim_id) }}" class="table-link">{{ $claim->subject }}</a>
                            </td>
                            <td>{{ $claim->claim_status->status }}</td>
                            <td class="d-none d-sm-table-cell">{{ $claim->user->name }}</td>
                            <td class="d-none d-md-table-cell">Менеджер Василий</td>
                            <td>{{ $claim->created_at ? \Carbon\Carbon::parse($claim->created_at)->format('d M H:i') : '' }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot></tfoot>
            </table>
        </div>
        <div class="col-md-2 search-block">
        <form action="{{route('claims.index')}}" method="get">
            @if (isset($sorting['sort_by']) && isset($sorting['sort_order']))
            <input type="hidden" name="sort_by" value="{{$sorting['sort_by']}}">
            <input type="hidden" name="sort_order" value="{{$sorting['sort_order']}}">
            @endif
            <div class="form-group">
                <label for="status">Статус</label>
                <select name="status" 
                    id="status"
                    class="form-control"
                >
                <option value="">----</option>
                @foreach ($claimsStatuses as $statusKey => $statusDesc)
                    <option value="{{ $statusKey }}" @if (isset($search['status']) && $search['status'] == $statusKey) selected @endif>
                        {{ $statusDesc }}
                    </option>
                @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="viewed">Просмторенные</label>
                <select name="viewed" 
                    id="viewed"
                    class="form-control"
                >
                <option value="">----</option>
                <option value="1" @if (isset($search['viewed']) && $search['viewed'] == 1) selected @endif>Да</option>
                <option value="0" @if (isset($search['viewed']) && $search['viewed'] == 0) selected @endif>Нет</option>
                </select>
            </div>
            <div class="form-group">
                <label for="has_answer">Есть ответ</label>
                <select name="has_answer" 
                    id="has_answer"
                    class="form-control"
                >
                <option value="">----</option>
                <option value="1" @if (isset($search['has_answer']) && $search['has_answer'] == 1) selected @endif>Да</option>
                <option value="0" @if (isset($search['has_answer']) && $search['has_answer'] == 0) selected @endif>Нет</option>
                </select>
            </div>
            <button class="btn btn-primary btn-block" type="submit">Поиск</button>
        </form>
        </div>
    </div>
</div>
@endsection