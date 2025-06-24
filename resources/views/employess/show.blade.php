@extends('home')

@section('title', 'Détail employé')

@section('content')
<div class="container mt-4">
    <h3>Détail de l'employé</h3>
    <table class="table table-bordered">
        <tr>
            <th>Nom complet</th>
            <td>{{ $employee->employee_name }}</td>
        </tr>
        <tr>
            <th>Matricule</th>
            <td>{{ $employee->name }}</td>
        </tr>
        <tr>
            <th>Date de naissance</th>
            <td>{{ $employee->date_of_birth }}</td>
        </tr>
        <tr>
            <th>Genre</th>
            <td>{{ $employee->gender }}</td>
        </tr>
        <tr>
            <th>Société</th>
            <td>{{ $employee->company }}</td>
        </tr>
        <tr>
            <th>Date d'entrée</th>
            <td>{{ $employee->date_of_joining }}</td>
        </tr>
    </table>
    <a href="{{ route('employees.index') }}" class="btn btn-secondary">Retour</a>
</div>
@endsection
