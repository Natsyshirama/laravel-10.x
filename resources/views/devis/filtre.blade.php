
<div class="container">
    <h1>Selectionner Fournisseur</h1>

    <form method="GET" action="{{ route('devis.index') }}" class="mt-4">
        <div class="form-group">
            <label for="supplier">Fournisseur</label>
            <select name="supplier" id="supplier" class="form-control">
                <option value="">-- Tous les fournisseurs --</option>
                @foreach ($suppliers as $supplier)
                    <option value="{{ $supplier['name'] }}">
                        {{ $supplier['supplier_name'] ?? $supplier['name'] }}
                    </option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Afficher les Commandes</button>
    </form>
</div>