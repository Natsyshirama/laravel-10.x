@extends('home')

@section('title', 'Détails Devis Client')

@section('content')
<div class="container">
    <h1 class="mb-4">Fiche Employé : {{ $employee['employee_name'] }}</h1>
    <form method="GET" action="{{ route('fiche-paie.index') }}">
            <div class="form-group">
                <input type="hidden" name="name" value="{{ $employee['name'] }}">
            </div>

            <div class="form-footer">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-filter"></i> Fiche de paie
                </button>
            </div>
        </form>
    <div class="card mb-4">
        <div class="card-header">Informations personnelles</div>
        <div class="card-body">
            <p><strong>Nom :</strong> {{ $employee['employee_name'] }}</p>
            <p><strong>Identifiant :</strong> {{ $employee['employee'] }}</p>
            <p><strong>Sexe :</strong> {{ $employee['gender'] }}</p>
            <p><strong>Date de naissance :</strong> {{ $employee['date_of_birth'] }}</p>
            <p><strong>Email personnel :</strong> {{ $employee['personal_email'] ?? 'null'}}</p>
            <p><strong>Statut :</strong> {{ $employee['status'] }}</p>
            <p><strong>Entreprise :</strong> {{ $employee['company'] }}</p>
            <p><strong>Département :</strong> {{ $employee['department'] ?? 'null'}}</p>
            <p><strong>Poste :</strong> {{ $employee['designation'] }}</p>
            <p><strong>Date d'embauche :</strong> {{ $employee['date_of_joining'] }}</p>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">Structure salariale</div>
        
        @if(!empty($salary) && count($salary) > 0)
        @foreach($salary as $structure)
        <div class="card-body">
        <p><strong>Structure :</strong> {{ $structure['salary_structure'] }}</p>
    <p><strong>Entreprise :</strong> {{ $structure['company'] ?? 'N/A' }}</p>
    <p><strong>Département :</strong> {{ $structure['department'] ?? 'N/A' }}</p>
    <p><strong>Poste :</strong> {{ $structure['designation'] ?? 'N/A' }}</p>
    <p><strong>Devise :</strong> {{ $structure['currency'] ?? 'N/A' }}</p>
    <p><strong>Date d'effet :</strong> {{ $structure['from_date'] ?? 'N/A' }}</p>
    <P><strong>salaire Base :</strong> {{ $structure['base'] ?? 'null'}}</p>
    <p><strong>Barème fiscal :</strong> {{ $structure['income_tax_slab'] ?? 'N/A' }}</p>
    <p><strong>Compte payable :</strong> {{ $structure['payroll_payable_account'] ?? 'N/A' }}</p>


        </div>
        @endforeach

        @else
    <p>Aucune structure salariale trouvée pour cet employé.</p>
@endif
    </div>

    <a href="{{ route('employee.index') }}" class="btn btn-secondary">Retour à la liste</a>
</div>
@endsection