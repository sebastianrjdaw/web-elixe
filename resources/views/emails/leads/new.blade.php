@component('emails.layouts.elixe', ['title' => 'Nueva solicitud comercial'])
    <p><strong>Tipo:</strong> {{ $lead->type }}</p>
    <p><strong>Negocio:</strong> {{ $lead->business_name ?: $lead->company_name ?: 'Consulta' }}</p>
    <p><strong>Contacto:</strong> {{ $lead->contact_name }} · {{ $lead->email }} · {{ $lead->phone ?: '-' }}</p>
    @if($lead->municipality || $lead->province)
        <p><strong>Ubicación:</strong> {{ collect([$lead->municipality, $lead->province])->filter()->join(', ') }}</p>
    @endif
    @if($lead->message)
        <p><strong>Mensaje:</strong><br>{{ $lead->message }}</p>
    @endif
    @if($lead->screens->isNotEmpty())
        <p><strong>Pantallas:</strong> {{ $lead->screens->pluck('display_name')->join(', ') }}</p>
    @endif
    <p><a href="{{ url('/admin/leads/'.$lead->id) }}" style="display:inline-block;background:#0891b2;color:#fff;text-decoration:none;padding:11px 18px;border-radius:8px">Abrir en el CRM</a></p>
@endcomponent
