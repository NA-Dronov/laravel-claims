@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            @if($paginator->total() > $paginator->count())
            {{ $paginator->links() }}
            @endif
        </div>
    </div>
    <div class="row">
        <div class="col-md-8">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Заявка</th>
                        <th>Статус</th>
                        <th class="d-none d-sm-table-cell">Отправитель</th>
                        <th class="d-none d-md-table-cell">Ответственный</th>
                        <th>Дата создания</th>
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
                                <a href="{{ route('claims.show', $claim->claim_id) }}">{{ $claim->subject }}</a>
                            </td>
                            <td>{{ $claim->status }}</td>
                            <td class="d-none d-sm-table-cell">{{ $claim->user->name }}</td>
                            <td class="d-none d-md-table-cell">Менеджер Василий</td>
                            <td>{{ $claim->created_at ? \Carbon\Carbon::parse($claim->created_at)->format('d M H:i') : '' }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot></tfoot>
            </table>
        </div>
        <div class="col-md-4">
        <form action="{{route('claims.index')}}" method="get">
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