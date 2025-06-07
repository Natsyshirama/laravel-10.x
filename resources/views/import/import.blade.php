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


<form method="POST" enctype="multipart/form-data" action="{{ route('import.all') }}">
    @csrf
    
    <div class="form-group">
        <label for="csv_employees">Fichier CSV Employés :</label>
        <input type="file" class="form-control-file" name="csv_employees" required>
    </div>
    
    <div class="form-group">
        <label for="csv_salary_structure">Fichier CSV Structure Salariale :</label>
        <input type="file" class="form-control-file" name="csv_salary_structure" required>
    </div>
    
    <div class="form-group">
        <label for="csv_salary_slip">Fichier CSV Bulletins de Paie :</label>
        <input type="file" class="form-control-file" name="csv_salary_slip" required>
    </div>
    
    <button type="submit" class="btn btn-primary">Importer </button>
</form>
@if (session('status') && isset(session('status')['message']))
    @foreach (session('status')['message'] as $line)
        <div class="alert alert-danger">{{ is_array($line) ? implode(', ', $line) : $line }}</div>
    @endforeach
@endif


@endsection