@extends('home')

@section('title', 'Tableau salaire')
@section('content')
<div class="card">
    <div class="card-header">
        <h4>Filtrer les fiches de paie par mois</h4>
        <form method="GET" action="{{ route('fichePaie.filtreParMois') }}" class="form-inline">
            <input type="month" name="mois" class="form-control mr-2" value="{{ $mois }}">
            <button type="submit" class="btn btn-primary">Filtrer</button>
        </form>
    </div>

    @if($mois && count($resultats) > 0)
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Employé</th>
                        <th>Gains (€)</th>
                        <th>Détails des gains</th>
                        <th>Déductions (€)</th>
                        <th>Détails des déductions</th>
                        <th>Net Payé (€)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($resultats as $ligne)
                        <tr>
                            <td>{{ $ligne['employee_name'] }}</td>
                            <td>{{ number_format($ligne['gains'], 2, ',', ' ') }}</td>
                            <td>{!! $ligne['gainDetails'] !!}</td>
                            <td>{{ number_format($ligne['deductions'], 2, ',', ' ') }}</td>
                            <td>{!! $ligne['deductionDetails'] !!}</td>
                            <td>{{ number_format($ligne['net_pay'], 2, ',', ' ') }}</td>
                        </tr>
                    @endforeach
                    <tr class="bg-light font-weight-bold">
                        <td>Total</td>
                        <td>{{ number_format($total_gains, 2, ',', ' ') }}</td>
                        <td></td>
                        <td>{{ number_format($total_deductions, 2, ',', ' ') }}</td>
                        <td></td>
                        <td>{{ number_format($total_net, 2, ',', ' ') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    @elseif($mois)
        <div class="card-body">
            <div class="alert alert-info">Aucune fiche de paie trouvée pour ce mois.</div>
        </div>
    @endif
</div>
@endsection