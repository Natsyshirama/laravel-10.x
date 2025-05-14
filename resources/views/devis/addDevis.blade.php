@extends('home')

@section('title', 'Créer un Devis Fournisseur')

@section('content')
<div class="card">
    <div class="card-header bg-primary text-white">
        <h2 class="card-title">Nouveau Devis Fournisseur</h2>
    </div>

    <div class="card-body">
        <form action="{{ route('devis.store') }}" method="POST">
            @csrf

            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Fournisseur *</label>
                        <select name="supplier" class="form-control" required>
                            <option value="">Sélectionner un fournisseur</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier['name'] }}" @selected(old('supplier') == $supplier['name'])>
                                    {{ $supplier['supplier_name'] ?? $supplier['name'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label>Date *</label>
                        <input type="date" name="transaction_date" class="form-control" 
                               value="{{ old('transaction_date', now()->format('Y-m-d')) }}" required>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label>Validité jusqu'au *</label>
                        <input type="date" name="valid_till" class="form-control" 
                               value="{{ old('valid_till', now()->addDays(30)->format('Y-m-d')) }}" required>
                    </div>
                </div>
            </div>

            <div class="table-responsive mb-4">
                <table class="table" id="items-table">
                    <thead class="bg-light">
                        <tr>
                            <th>Article *</th>
                            <th>Quantité *</th>
                            <th>Prix Unitaire *</th>
                            <th>Entrepôt *</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach(old('items', [['item_code' => '']]) as $index => $item)
                        <tr class="item-row">
                            <td>
                                <select name="items[{{ $index }}][item_code]" class="form-control" required>
                                    <option value="">Sélectionner un article</option>
                                    @foreach($items as $it)
                                        <option value="{{ $it['name'] }}" @selected($item['item_code'] == $it['name'])>
                                            {{ $it['item_name'] ?? $it['name'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <input type="number" name="items[{{ $index }}][qty]" 
                                       value="{{ $item['qty'] ?? 1 }}" 
                                       class="form-control" step="0.01" min="0.01" required>
                            </td>
                            <td>
                                <input type="number" name="items[{{ $index }}][rate]" 
                                       value="{{ $item['rate'] ?? 0 }}" 
                                       class="form-control" step="0.01" min="0" required>
                            </td>
                             <!--  -->
                            </td> 
                            <td>
                                @if($index > 0)
                                <button type="button" class="btn btn-danger remove-row">
                                    <i class="fas fa-trash"></i>
                                </button>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="form-group text-center">
                <button type="button" id="add-item" class="btn btn-secondary mr-2">
                    <i class="fas fa-plus"></i> Ajouter un article
                </button>
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i> Enregistrer le devis
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Ajouter une ligne d'article
    document.getElementById('add-item').addEventListener('click', function() {
        const table = document.getElementById('items-table').getElementsByTagName('tbody')[0];
        const newRow = table.rows[table.rows.length - 1].cloneNode(true);
        const newIndex = table.rows.length;
        
        // Mise à jour des noms des champs
        Array.from(newRow.querySelectorAll('[name]')).forEach(input => {
            input.name = input.name.replace(/\[\d+\]/, `[${newIndex}]`);
            input.value = '';
        });
        
        // Ajout du bouton supprimer
        const tdAction = newRow.cells[newRow.cells.length - 1];
        tdAction.innerHTML = '<button type="button" class="btn btn-danger remove-row"><i class="fas fa-trash"></i></button>';
        
        table.appendChild(newRow);
    });

    // Supprimer une ligne
    document.getElementById('items-table').addEventListener('click', function(e) {
        if(e.target.closest('.remove-row')) {
            const row = e.target.closest('tr');
            if(document.querySelectorAll('#items-table tbody tr').length > 1) {
                row.remove();
            }
        }
    });
});
</script>
@endpush
@endsection