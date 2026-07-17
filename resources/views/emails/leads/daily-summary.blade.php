@component('emails.layouts.elixe', ['title' => 'Resumen diario de leads'])
    <p>Se recibieron <strong>{{ $leads->count() }}</strong> solicitudes durante el día anterior.</p>
    @foreach($leads as $lead)
        <p style="padding:12px;background:#f8fafc;border-radius:8px">
            <strong>{{ $lead->business_name ?: $lead->company_name ?: 'Consulta' }}</strong><br>
            {{ $lead->contact_name }} · {{ $lead->email }} · {{ $lead->phone ?: 'sin teléfono' }}
        </p>
    @endforeach
@endcomponent
