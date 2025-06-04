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
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <h5><i class="fas fa-exclamation-triangle"></i> Erreur :</h5>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="card-body">
            <form id="quotation-form" method="POST" action="#">
                @csrf

                <!-- Section Client -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="customer_name">Company <span class="text-danger">*</span></label>
                            <select class="form-control select2" id="customer_name" name="customer_name" required>
                                <option value="">Sélectionner un Company</option>
                                @foreach($company as $cp)
                                    <option value="{{ $cp['name'] }}">
                                        {{ $cp['name'] }} 
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                    </div>
                    
                    
                </div>

                <!-- Section Gains -->
                <div class="table-responsive mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h2 class="mb-0">Gains</h2>
                        <button type="button" class="btn btn-primary btn-sm" id="add-gain-row">
                            <i class="fas fa-plus"></i> Ajouter une ligne
                        </button>
                    </div>
                    <table class="table table-bordered" id="gain-table">
                        <thead class="thead-light">
                            <tr>
                                <th width="30%">Component</th>
                                <th width="10%">Formule</th>
                                <th width="5%">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="gain-row">
                                <td>
                                    <select class="form-control select2 item-select" name="gain[0][component]" required>
                                        <option value="">Sélectionner un gain</option>
                                        @foreach($gainComponent as $gain)
                                            <option value="{{ $gain['name'] }}">
                                                {{ $gain['name'] }} 
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                              
                                <td>
                                    <input type="text" class="form-control" name="gain[0][formula]" value="1">
                                </td>
                                
                                <td>
                                    <button type="button" class="btn btn-danger btn-sm remove-row" disabled>
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Section Déductions -->
                <div class="table-responsive mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h2 class="mb-0">Deduction</h2>
                        <button type="button" class="btn btn-primary btn-sm" id="add-deduction-row">
                            <i class="fas fa-plus"></i> Ajouter une ligne
                        </button>
                    </div>
                    <table class="table table-bordered" id="deduction-table">
                        <thead class="thead-light">
                            <tr>
                                <th width="30%">Component</th>
                                <th width="10%">Formule</th>
                                <th width="5%">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="deduction-row">
                                <td>
                                    <select class="form-control select2 item-select" name="deduction[0][component]" required>
                                        <option value="">Sélectionner une déduction</option>
                                        @foreach($deductionComponent as $deduction)
                                            <option value="{{ $deduction['name'] }}">
                                                {{ $deduction['name'] }} 
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                              
                                <td>
                                    <input type="text" class="form-control" name="deduction[0][formula]" value="1">
                                </td>
                                
                                <td>
                                    <button type="button" class="btn btn-danger btn-sm remove-row" disabled>
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <div class="form-group text-right">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Enregistrer le devis
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Initialiser Select2
    $('.select2').select2();

    // Compteurs pour les index
    let gainCounter = 1;
    let deductionCounter = 1;

    // Ajouter une ligne de gain
    $('#add-gain-row').click(function() {
        let newRow = `
            <tr class="gain-row">
                <td>
                    <select class="form-control select2 item-select" name="gain[${gainCounter}][component]" required>
                        <option value="">Sélectionner un gain</option>
                        @foreach($gainComponent as $gain)
                            <option value="{{ $gain['name'] }}">
                                {{ $gain['name'] }} 
                            </option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <input type="text" class="form-control" name="gain[${gainCounter}][formula]" value="1">
                </td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm remove-row">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
        $('#gain-table tbody').append(newRow);
        // Réinitialiser Select2 pour la nouvelle ligne
        $('#gain-table tbody tr:last .select2').select2();
        gainCounter++;
    });

    // Ajouter une ligne de déduction
    $('#add-deduction-row').click(function() {
        let newRow = `
            <tr class="deduction-row">
                <td>
                    <select class="form-control select2 item-select" name="deduction[${deductionCounter}][component]" required>
                        <option value="">Sélectionner une déduction</option>
                        @foreach($deductionComponent as $deduction)
                            <option value="{{ $deduction['name'] }}">
                                {{ $deduction['name'] }} 
                            </option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <input type="text" class="form-control" name="deduction[${deductionCounter}][formula]" value="1">
                </td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm remove-row">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
        $('#deduction-table tbody').append(newRow);
        // Réinitialiser Select2 pour la nouvelle ligne
        $('#deduction-table tbody tr:last .select2').select2();
        deductionCounter++;
    });

    // Supprimer une ligne
    $(document).on('click', '.remove-row', function() {
        if ($(this).closest('tbody').find('tr').length > 1) {
            $(this).closest('tr').remove();
        }
    });
});
</script>
@endsection