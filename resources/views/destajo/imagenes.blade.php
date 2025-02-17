@extends('layouts.app')

@section('content')
<div class="container" style="max-width: 1200px; margin: 20px auto; padding: 20px; background: #ffffff; border-radius: 10px; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);">
    <div class="section-header" style="text-align: center;">
        <h1 style="font-size: 28px; font-weight: bold; color: #2c3e50; margin-bottom: 5px;">
            Imágenes de Destajo: {{ $detalle->frente }}
        </h1>
    </div>

    <div class="image-gallery" style="margin-top: 20px;">
        <h3>Imágenes Subidas:</h3>
        <div class="row">
            @if(isset($imagenes) && count($imagenes) > 0)
                @foreach($imagenes as $imagen)
                    <div class="col-md-3">
                        <div class="thumbnail">
                            <img src="{{ asset('storage/' . $imagen->path) }}" alt="Imagen" style="width:100%">
                            <div class="caption">
                                <p>{{ $imagen->created_at }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <p>No hay imágenes subidas para este destajo.</p>
            @endif
        </div>
    </div>
</div>

<style>
    .image-gallery .thumbnail {
        border: 1px solid #ddd;
        border-radius: 4px;
        padding: 5px;
        margin-bottom: 20px;
    }

    .image-gallery .thumbnail img {
        border-radius: 4px;
    }
</style>
@endsection
