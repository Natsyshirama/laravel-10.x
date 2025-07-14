@extends('home')

@section('title', 'Modifier Réduction')

@section('content')
<div class="container mt-4">
    <h1>Modifier réduction</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('reduction.update', $reduction->id) }}">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="mois">Mois :</label>
            <input type="date" name="mois" id="mois" class="form-control" value="{{ old('mois', $reduction->mois) }}" required>
        </div>

        <div class="form-group">
            <label for="valeur">Valeur (%) :</label>
            <input type="number" step="0.01" name="valeur" id="valeur" class="form-control" value="{{ old('valeur', $reduction->valeur) }}" required>
        </div>

        <button type="submit" class="btn btn-primary mt-3">Mettre à jour</button>
        <a href="{{ route('reduction.liste') }}" class="btn btn-secondary mt-3">Annuler</a>
    </form>
</div>
@endsection
