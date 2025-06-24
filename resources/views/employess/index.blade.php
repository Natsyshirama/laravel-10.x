@extends('home')

@section('title', 'Liste des employés')

@section('content')
<div class="container mt-4">
    <h3>Liste des employés</h3>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Matricule</th>
                <th>Nom</th>
                <th>Société</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($employees as $emp)
            <tr>
                <td>{{ $emp->name }}</td>
                <td>{{ $emp->employee_name }}</td>
                <td>{{ $emp->company }}</td>
                <td>
                    <a href="{{ route('employees.show', $emp->name) }}" class="btn btn-sm btn-info">Détail</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
