@extends('layouts.app')

@section('content')
<div class="container">
    @include('claims.result_messages')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('claims.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label for="subject">Заголовок</label>
                            <input class="form-control" type="text" id="subject" name="subject" placeholder="Введите заголовок" value="{{ old('subject', $item->subject) }}" required>
                        </div>
                        <div class="form-group">
                            <label for="message">Текст заявления</label>
                            <textarea class="form-control" id="message" name="body" rows="3" required>{{ old('body', $item->body) }}</textarea>
                        </div>
                        <div class="form-group">
                            <label for="attachments">File input</label>
                            <input type="file" id="attachments" name="attachments[]" multiple class="form-control-file">
                            <small class="form-text text-muted" id="attachmentsHelp">Max 3mb size</small>
                        </div>
                        <div class="form-group mt-4">
                            <button class="btn btn-primary btn-block" type="submit">Отправить</button>
                        </div>
                    </form>
                </div>
            </div>
    </div>
</div>
@endsection