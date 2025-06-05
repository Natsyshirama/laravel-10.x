@extends('home')

@section('title', 'assignement salary structure')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Nouveau salary Structure</h6>
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

            <form method="POST" action="{{route ('salaryAssg.posteAssg')}}">
                @csrf

                <div class="row mb-4">
                    
                <div class="col-md-6">
        <label>Employee</label>
        <select class="form-control select2" name="employee" required id="employee_select">
            <option value="">Sélectionner</option>
            @foreach($employee as $ep)
                <option value="{{ $ep['name'] }}" data-name="{{ $ep['first_name'] }}">
                    {{ $ep['first_name'] }}
                </option>
            @endforeach
        </select>
        <input type="hidden" name="employee_name" id="employee_name">
    </div>
                    <div class="col-md-6">
                        <label>Salary Structure</label>
                        <select class="form-control select2" name="salary_structure" required>
                            <option value="">Sélectionner</option>
                            @foreach($salaryStr as $st)
                                <option value="{{ $st['name'] }}">{{ $st['name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label>Base</label>
                        <input type="number" name="base" min="0" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label>From date</label>
                        <input type="date" name="from_date"  class="form-control" required>
                    </div>
                </div>

                <!-- GAIN -->
                

                <div class="mt-4 text-right">
                    <button type="submit" class="btn btn-success">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
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

