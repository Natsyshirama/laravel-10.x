@extends('home')

@section('content')
<div class="card">
    <div class="card-header">
        <h2 class="card-title"><i class="fas fa-file-invoice-dollar"></i> Détail de la Salary</h2>
    </div>
    

    <div class="card-body">
        @if(!empty($detail))
            <div class="row mb-3">
                <div class="col-md-6">
                    <p><strong>Nom Fiche :</strong> {{ $detail['name'] }}</p>
                    <p><strong>Company :</strong> {{ $detail['company'] }} </p>
                    <p><strong>Is Active :</strong> {{ $detail['is_active'] ?? 'N/A' }}</p>
                    <p><strong>Devise :</strong> {{ $detail['currency'] ?? 'N/A' }}</p>
                
                </div>
            </div>

           

            <h4>Gains</h4>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="thead-light">
                        <tr>
                            <th>Type</th>
                            <th>Montant</th>
                            <th>formule</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($detail['earnings'] as $earning)
                            <tr>
                                <td>{{ $earning['salary_component'] ?? 'N/A' }}</td>
                                <td>{{ $earning['amount'] ?? 0 }}</td>
                                <td>{{ $earning['formula'] ?? '' }}</td>
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
                            <th>formule</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($detail['deductions'] as $deduction)
                            <tr>
                                <td>{{ $deduction['salary_component'] ?? 'N/A' }}</td>
                                <td>{{ $deduction['amount'] ?? 0 }}</td>
                                <td>{{ $deduction['formula'] ?? '' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-center">Aucune déduction</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <hr>

            
        @else
            <div class="alert alert-warning">
                <i class="fas fa-info-circle"></i> Aucune Salary Structure trouvée.
            </div>
        @endif
    </div>
</div>
@endsection
