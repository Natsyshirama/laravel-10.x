@extends('home')

@section('title', 'liste des fiche de paie')
@section('content')
<!-- 
<form method="POST" enctype="multipart/form-data" action="{{ route('import.suppliers') }}">
    @csrf
    <label for="csv_file">Fichier CSV :</label>
    <input type="file" name="csv_file" required>
    <button type="submit">Importer Fournisseurs</button>
</form> -->

<form method="POST" enctype="multipart/form-data" action="{{ route('import.employees') }}">
    @csrf
    <label for="csv_file">Fichier CSV Employés :</label>
    <input type="file" name="csv_fileEmployee" required>
    <button type="submit">Importer</button>
</form>

<form method="POST" enctype="multipart/form-data" action="{{ route('import.salary_structure') }}">
    @csrf
    <label for="csv_file">Fichier CSV Salary Structure :</label>
    <input type="file" name="csv_fileSalaryStructure" required>
    <button type="submit">Importer</button>
</form>

@if (session('status') && isset(session('status')['message']))
    @foreach (session('status')['message'] as $line)
        <div class="alert alert-danger">{{ is_array($line) ? implode(', ', $line) : $line }}</div>
    @endforeach
@endif


@endsection