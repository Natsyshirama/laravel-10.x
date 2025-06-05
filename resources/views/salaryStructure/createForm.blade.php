@extends('home')

@section('title', 'Nouveau Salary Structure')

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

            <form method="POST" action="{{ route('salaraStr.store') }}">
                @csrf

                <div class="row mb-4">
                    <div class="col-md-6">
                        <label>Nom du Salary Structure</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label>Company</label>
                        <select class="form-control select2" name="company" required>
                            <option value="">Sélectionner</option>
                            @foreach($company as $cp)
                                <option value="{{ $cp['name'] }}">{{ $cp['name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- GAIN -->
                <h5>Gains</h5>
                <table class="table" id="gain-table">
                    <thead><tr><th>Component</th><th>Formule</th><th>Action</th></tr></thead>
                    <tbody>
                        <tr>
                            <td>
                                <select name="earnings[0][salary_component]" class="form-control select2" required>
                                    <option value="">-- Choisir --</option>
                                    @foreach($gain as $g)
                                        <option value="{{ $g['name'] }}">{{ $g['name'] }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td><input name="earnings[0][formula]" class="form-control" /></td>
                            <td><button type="button" class="btn btn-danger remove-row" disabled>X</button></td>
                        </tr>
                    </tbody>
                </table>
                <button type="button" id="add-gain-row" class="btn btn-sm btn-primary">Ajouter gain</button>

                <!-- DEDUCTION -->
                <h5 class="mt-4">Déductions</h5>
                <table class="table" id="deduction-table">
                    <thead><tr><th>Component</th><th>Formule</th><th>Action</th></tr></thead>
                    <tbody>
                        <tr>
                            <td>
                                <select name="deductions[0][salary_component]" class="form-control select2" required>
                                    <option value="">-- Choisir --</option>
                                    @foreach($deduction as $d)
                                        <option value="{{ $d['name'] }}">{{ $d['name'] }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td><input name="deductions[0][formula]" class="form-control" /></td>
                            <td><button type="button" class="btn btn-danger remove-row" disabled>X</button></td>
                        </tr>
                    </tbody>
                </table>
                <button type="button" id="add-deduction-row" class="btn btn-sm btn-primary">Ajouter déduction</button>

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
$(document).ready(function() {
    $('.select2').select2();
    let gainIndex = 1, deductionIndex = 1;

    $('#add-gain-row').click(function() {
        $('#gain-table tbody').append(`
            <tr>
                <td>
                    <select name="earnings[${gainIndex}][salary_component]" class="form-control select2" required>
                        <option value="">-- Choisir --</option>
                        @foreach($gain as $g)
                            <option value="{{ $g['name'] }}">{{ $g['name'] }}</option>
                        @endforeach
                    </select>
                </td>
                <td><input name="earnings[${gainIndex}][formula]" class="form-control" /></td>
                <td><button type="button" class="btn btn-danger remove-row">X</button></td>
            </tr>
        `);
        $('.select2').select2();
        gainIndex++;
    });

    $('#add-deduction-row').click(function() {
        $('#deduction-table tbody').append(`
            <tr>
                <td>
                    <select name="deductions[${deductionIndex}][salary_component]" class="form-control select2" required>
                        <option value="">-- Choisir --</option>
                        @foreach($deduction as $d)
                            <option value="{{ $d['name'] }}">{{ $d['name'] }}</option>
                        @endforeach
                    </select>
                </td>
                <td><input name="deductions[${deductionIndex}][formula]" class="form-control" /></td>
                <td><button type="button" class="btn btn-danger remove-row">X</button></td>
            </tr>
        `);
        $('.select2').select2();
        deductionIndex++;
    });

    $(document).on('click', '.remove-row', function() {
        $(this).closest('tr').remove();
    });
});
</script>
@endsection
