@component('mail::message')
# Nuevo lead: {{ $lead->type === 'venue' ? 'Local' : 'Anunciante' }}

**Nombre:** {{ $lead->business_name ?: $lead->company_name }}

**Contacto:** {{ $lead->contact_name }}

**Email:** {{ $lead->email }}

**Telefono:** {{ $lead->phone ?: '-' }}

@if($lead->address || $lead->city || $lead->province)
**Ubicacion:** {{ collect([$lead->address, $lead->city, $lead->province])->filter()->join(', ') }}
@endif

@if($lead->campaign_message)
**Mensaje de campana:** {{ $lead->campaign_message }}
@endif

@if($lead->message)
**Comentarios:** {{ $lead->message }}
@endif

@if($lead->screens->isNotEmpty())
**Pantallas seleccionadas:** {{ $lead->screens->pluck('display_name')->join(', ') }}
@endif

@endcomponent
