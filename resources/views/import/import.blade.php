@extends('home')

@section('title', 'liste des fiche de paie')
@section('content')

<form method="POST" enctype="multipart/form-data" action="{{ route('import.suppliers') }}">
    @csrf
    <label for="csv_file">Fichier CSV :</label>
    <input type="file" name="csv_file" required>
    <button type="submit">Importer Fournisseurs</button>
</form>
@if (session('status'))
    @foreach (session('status.message') as $line)
        <div>{{ $line }}</div>
    @endforeach
@endif

@endsection