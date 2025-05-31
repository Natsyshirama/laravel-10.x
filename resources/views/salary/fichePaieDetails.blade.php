@extends('home')

@section('content')
<div class="card">
    <div class="card-header">
        <h2 class="card-title"><i class="fas fa-file-invoice-dollar"></i> Détail de la fiche de paie</h2>
    </div>

    <div class="card-body">
        @if(!empty($fichePaie))
            <div class="row mb-3">
                <div class="col-md-6">
                    <p><strong>Nom Fiche :</strong> {{ $fichePaie['name'] }}</p>
                    <p><strong>Employé :</strong> {{ $fichePaie['employee'] }} - {{ $fichePaie['employee_name'] }}</p>
                    <p><strong>Département :</strong> {{ $fichePaie['department'] }}</p>
                    <p><strong>Poste :</strong> {{ $fichePaie['designation'] ?? 'N/A' }}</p>
                    <p><strong>Structure salariale :</strong> {{ $fichePaie['salary_structure'] }}</p>
                    <p><strong>Devise :</strong> {{ $fichePaie['currency'] }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Statut :</strong> 
                        <span class="badge 
                            @if($fichePaie['status'] == 'Submitted') bg-success
                            @elseif($fichePaie['status'] == 'Draft') bg-danger
                            @else bg-warning
                            @endif">
                            {{ $fichePaie['status'] }}
                        </span>
                    </p>
                    <p><strong>Date de publication :</strong> {{ \Carbon\Carbon::parse($fichePaie['posting_date'])->format('d/m/Y') }}</p>
                    <p><strong>Période :</strong> {{ $fichePaie['start_date'] }} au {{ $fichePaie['end_date'] }}</p>
                    <p><strong>Fréquence :</strong> {{ $fichePaie['payroll_frequency'] }}</p>
                    <p><strong>Entrée paie :</strong> {{ $fichePaie['payroll_entry'] ?? '' }}</p>
                </div>
            </div>

            <hr>

            <div class="row mb-3">
                <div class="col-md-4">
                    <p><strong>Jours ouvrables :</strong> {{ $fichePaie['total_working_days'] }}</p>
                    <p><strong>Absences :</strong> {{ $fichePaie['absent_days'] }}</p>
                </div>
                <div class="col-md-4">
                    <p><strong>Heures travaillées :</strong> {{ $fichePaie['total_working_hours'] }}</p>
                    <p><strong>Taux horaire :</strong> {{ $fichePaie['hour_rate'] }}</p>
                </div>
            </div>

            <hr>

            <h4>Gains</h4>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="thead-light">
                        <tr>
                            <th>Type</th>
                            <th>Montant</th>
                            <th>Détails</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($fichePaie['earnings'] as $earning)
                            <tr>
                                <td>{{ $earning['salary_component'] ?? 'N/A' }}</td>
                                <td>{{ $earning['amount'] ?? 0 }}</td>
                                <td>{{ $earning['description'] ?? '' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-center">Aucun gain</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <h4>Déductions</h4>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="thead-light">
                        <tr>
                            <th>Type</th>
                            <th>Montant</th>
                            <th>Détails</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($fichePaie['deductions'] as $deduction)
                            <tr>
                                <td>{{ $deduction['salary_component'] ?? 'N/A' }}</td>
                                <td>{{ $deduction['amount'] ?? 0 }}</td>
                                <td>{{ $deduction['description'] ?? '' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-center">Aucune déduction</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <hr>

            <div class="row">
                <div class="col-md-4">
                    <p><strong>Salaire Brut :</strong> {{ $fichePaie['base_gross_pay'] }}</p>
                    <p><strong>Total Déductions :</strong> {{ $fichePaie['base_total_deduction'] }}</p>
                    <p><strong>Net à payer :</strong> {{ $fichePaie['base_net_pay'] }}</p>
                    <p><strong>En lettres :</strong> {{ $fichePaie['base_total_in_words'] }}</p>
                </div>
            </div>
        @else
            <div class="alert alert-warning">
                <i class="fas fa-info-circle"></i> Aucune fiche de paie trouvée.
            </div>
        @endif
    </div>
</div>
@endsection
