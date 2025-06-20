@extends('home')

@section('title', 'Graphes des salaires')

@section('content')
<div class="card">
    <div class="card-header">
        <h4>Évolution des salaires - Année {{ $annee }}</h4>
        <form method="GET" class="form-inline mb-3">
            <label for="annee" class="mr-2">Choisir une année :</label>
            <input type="number" name="annee" value="{{ $annee }}" class="form-control mr-2">
            <button class="btn btn-primary">Filtrer</button>
        </form>
    </div>

    <div class="card-body">
        <canvas id="netPayChart" height="100"></canvas>
        <hr>
        <canvas id="componentsChart" height="100"></canvas>
    </div>
</div>
@endsection
@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const mois = @json($mois);
const netPays = @json($netPays);
const composantsData = @json($composantsData);

const datasets = [{
    label: 'Net Pay Mensuel',
    data: netPays,
    borderColor: 'green',
    fill: false,
    tension: 0.1,
    borderWidth: 2,
    pointRadius: 3
}];

Object.entries(composantsData).forEach(([name, data], idx) => {
    datasets.push({
        label: name,
        data: data,
        fill: false,
        borderColor: `hsl(${(idx * 60 + 120) % 360}, 80%, 50%)`, 
        tension: 0.1,
        pointRadius: 2
    });
});

const ctx = document.getElementById('netPayChart').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: mois,
        datasets: datasets
    },
    options: {
        responsive: true,
        plugins: {
            title: {
                display: true,
                text: 'Évolution des Salaires et Composants par Mois'
            },
            legend: {
                position: 'bottom'
            }
        }
    }
});
</script>
@endsection
