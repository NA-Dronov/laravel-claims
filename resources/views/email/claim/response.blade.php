@component('mail::message')
# Дата посутпления: {{$published_at}}
# От кого: {{$author}}

{{$response->body}}

@component('mail::button', ['url' => $link])
Перейти к заявке
@endcomponent
<br>
{{ config('app.name') }}
@endcomponent
