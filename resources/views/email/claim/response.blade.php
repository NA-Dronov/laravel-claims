@component('mail::message')
# Дата посутпления: {{$published_at}}
# От кого: {{$author}}

{{$response->body}}

@component('mail::button', ['url' => ''])
Button Text
@endcomponent
<br>
{{ config('app.name') }}
@endcomponent
