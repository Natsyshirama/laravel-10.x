@extends('home')

@section('title', 'genere Salaire')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h5 class="m-0 font-weight-bold text-primary">Recherche salaire</h5>
        </div>
        <div class="card-body">

            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger">
                    <h5><i class="fas fa-exclamation-triangle"></i> Erreur :</h5>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                    </ul>
                </div>
            @endif

    <form method="POST" action="{{route('recherche.moteur')}}">
                @csrf

    <div class="row mb-4">
                    
        <div class="col-md-6">
        <label>Composent</label>
        <select class="form-control select2" name="component" >
            <option value="">Sélectionner Composant</option>
        @foreach($components as $comp)
            <option value="{{ $comp['name'] }}">
            {{ $comp['name'] }}
            </option>
        @endforeach
        </select>

        <select class="form-control select2" name="signe" required>
            <option value="">Sélectionner Opérateur</option>
            <option value=">">Supérieur à</option>
            <option value="<">Inférieur à</option>
        </select>

        <input type="number" name="montant" class="form-control" placeholder="Montant">
    </div> 
        </div>
   
    <button type="submit">rechercher </button>
</form>

</div>
@if(isset($employees) && count($employees) > 0)
    <hr>
    <h5>Résultats :</h5>
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>salary slip</th>
                    <th>Nom</th>
                    <th>Structure Salaire</th>
                    <th>Date Début</th>
                    <th>detail</th>
                </tr>
            </thead>
            <tbody>
                @foreach($employees as $emp)
                    <tr>
                        <td>{{ $emp['name']}}</td>
                        <td>{{ $emp['employee_name'] }}</td>
                        <td>{{ $emp['salary_structure'] }}</td>
                        <td>{{ $emp['start_date'] }}</td>
                        @foreach($emp['details'] as $line)
                        <td>    {{$line['salary_component']}}= {{$line['amount']}}</td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@elseif(isset($employees))
    <div class="alert alert-warning">Aucun employé trouvé.</div>
@endif

@endsection

@section('scripts')


<script>
    document.getElementById('employee_select').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        document.getElementById('employee_name').value = selectedOption.getAttribute('data-name');
    });
</script>
@endsection

