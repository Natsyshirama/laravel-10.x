@extends('home')

@section('title', 'Résumé annuel des salaires')
@section('content')
<div class="card">
    <div class="card-header">
        <h4>Résumé des salaires - Année {{ $annee }}</h4>
        <form method="GET" action="{{ route('fichePaie.resumeParAnnee') }}" class="form-inline">
            <select name="annee" class="form-control mr-2">
                @foreach(range(date('Y'), 2020) as $y)
                    <option value="{{ $y }}" {{ $y == $annee ? 'selected' : '' }}>{{ $y }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-primary">Filtrer</button>
        </form>
    </div>

    <div class="card-body">
        @if(count($moisData) > 0)
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Mois</th>
                        <th>Net payé (€)</th>
                        @foreach($components as $comp)
                            <th>{{ $comp }} (€)</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($moisData as $mois => $data)
                        <tr>
                            <td>{{ \Carbon\Carbon::createFromFormat('F', $mois)->translatedFormat('F') }}</td>
                            <td> <a href="{{ route('fichePaie.filtreParMois', ['mois' => date('Y', strtotime($annee . '-' . $mois . '-01')) . '-' . str_pad(date('m', strtotime($annee . '-' . $mois . '-01')), 2, '0', STR_PAD_LEFT)]) }}">
                                {{ number_format($data['net_pay'], 2, ',', ' ') }}
                                </a></td>
                            @foreach($components as $comp)
                                <td>{{ number_format($data['components'][$comp] ?? 0, 2, ',', ' ') }}</td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="alert alert-info">Aucune fiche de paie trouvée pour l'année sélectionnée.</div>
        @endif
    </div>
</div>
@endsection
