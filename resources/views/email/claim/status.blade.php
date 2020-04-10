@component('mail::message')
# Тема: {{$claim->subject}}
# Дата посутпления: {{$published_at}}
# От кого: {{$author}}

@if ($claim->status == \App\Models\ClaimStatus::OPEN)
{{$claim->body}}
@elseif ($claim->status == \App\Models\ClaimStatus::PROCESSED)
Заявка была принята на обработку менеджером {{$manager->name}}
@else
Заявка была закрыта
@endif

@component('mail::button', ['url' => $link])
Перейти к заявке
@endcomponent

<br>
{{ config('app.name') }}
@endcomponent
