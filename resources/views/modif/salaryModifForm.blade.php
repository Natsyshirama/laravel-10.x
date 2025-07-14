@extends('home')

@section('title', 'genere Salaire')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h5 class="m-0 font-weight-bold text-primary">Modification salaire</h5>
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

             <form method="POST" action="{{ route('modif.salary.mass') }}">
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
    </div> 
    @csrf

        <select class="form-control select2" name="methode" required>
            <option value="">methode </option>
            <option value="moins">deduction </option>
            <option value="plus">augmentation </option>
        </select>
    <input type="number" name="pourcentage" class="form-control" placeholder="pourcentage">

   
    <button type="submit">Modifier </button>
</form>

</div>
@endsection

@section('scripts')


<script>
    document.getElementById('employee_select').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        document.getElementById('employee_name').value = selectedOption.getAttribute('data-name');
    });
</script>
@endsection

