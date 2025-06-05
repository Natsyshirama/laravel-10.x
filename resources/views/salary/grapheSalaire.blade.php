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

const ctx1 = document.getElementById('netPayChart').getContext('2d');
new Chart(ctx1, {
    type: 'line',
    data: {
        labels: mois,
        datasets: [{
            label: 'Net Pay Mensuel',
            data: netPays,
            borderColor: 'green',
            fill: false,
            tension: 0.1
        }]
    },
    options: {
        responsive: true
    }
});

const datasets = [];
Object.entries(composantsData).forEach(([name, data], idx) => {
    datasets.push({
        label: name,
        data: data,
        fill: false,
        borderColor: `hsl(${idx * 60}, 70%, 50%)`,
        tension: 0.1
    });
});

const ctx2 = document.getElementById('componentsChart').getContext('2d');
new Chart(ctx2, {
    type: 'line',
    data: {
        labels: mois,
        datasets: datasets
    },
    options: {
        responsive: true
    }
});
</script>
@endsection
