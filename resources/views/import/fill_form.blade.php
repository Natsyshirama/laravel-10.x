@extends('home')

@section('title', 'Résumé avant import')
@section('content')

<h3>Choisissez combien de lignes importer</h3>

<form method="POST" action="{{ route('import.confirm') }}">
    @csrf

    <ul class="list-group mb-4">
        @foreach ($datasets as $key => $dataset)
            <li class="list-group-item">
                <strong>{{ ucfirst(str_replace('csv_', '', $key)) }}</strong>
                <div class="form-group mt-2">
                    <label>nombre de ligne  :</label>
                    <input type="number" name="lines[{{ $key }}]" class="form-control" min="1" max="{{ $dataset['count'] }}" required>
                    <small class="form-text text-muted">Ligne disponible : {{ $dataset['count'] }} ligne(s)</small>
                </div>
            </li>
        @endforeach
    </ul>

    <button type="submit" class="btn btn-success"> Confirme importation</button>
    <a href="{{ route('import.index') }}" class="btn btn-secondary"> Annuler</a>
</form>

@endsection
