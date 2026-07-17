@component('emails.layouts.elixe', ['title' => 'Hemos recibido tu solicitud', 'locale' => $lead->locale])
    <p>Hola {{ $lead->contact_name }},</p>
    <p>Hemos recibido tu solicitud y nuestro equipo la revisará para ponerse en contacto contigo.</p>
    <p>Gracias por confiar en Elixe.</p>
@endcomponent
