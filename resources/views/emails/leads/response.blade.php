@component('emails.layouts.elixe', ['title' => $title, 'locale' => $lead->locale])
    <p>{!! $bodyHtml !!}</p>
@endcomponent
