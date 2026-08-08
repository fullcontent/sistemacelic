@php
    $apiKey = config('services.google_maps.api_key');
    $addressParts = array_filter([
        $unidade->endereco ?? '',
        !empty($unidade->numero) ? "{$unidade->numero}" : '',
        $unidade->bairro ?? '',
        $unidade->cidade ?? '',
        $unidade->uf ?? '',
        'Brasil'
    ]);
    $locationQuery = urlencode(implode(', ', $addressParts));
    $staticImageUrl = "https://maps.googleapis.com/maps/api/streetview?size=600x400&location={$locationQuery}&key={$apiKey}";
@endphp

<div class="box box-primary" style="border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.02); border: 1px solid #ebf0f5; overflow: hidden; background: #fff; margin-bottom: 20px;">
    <div class="box-header with-border" style="background: #fff; border-bottom: 1px solid #ebf0f5; padding: 12px 15px;">
        <h3 class="box-title" style="font-weight: 700; color: #2c3e50; font-size: 15px; margin: 0;">
            <i class="fa fa-image text-primary"></i> Fachada da Unidade (Street View)
        </h3>
    </div>
    <div class="box-body no-padding text-center" style="background: #f8fafc; min-height: 220px; display: flex; align-items: center; justify-content: center;">
        <a href="https://www.google.com/maps/search/?api=1&query={{ $locationQuery }}" target="_blank" title="Clique para abrir no Google Maps" style="display: block; width: 100%;">
            <img src="{{ $staticImageUrl }}" class="img-responsive center-block" alt="Fachada da Unidade" style="cursor: pointer; width: 100%; height: 230px; object-fit: cover;">
        </a>
    </div>
</div>
