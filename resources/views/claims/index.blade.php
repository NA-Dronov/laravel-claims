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
        <div class="col-md-12">
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
    </div>
</div>
@endsection