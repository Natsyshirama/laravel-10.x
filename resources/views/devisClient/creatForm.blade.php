@extends('home')


@section('title', 'Nouveau Devis Client')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Nouveau Devis</h6>
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
            <form id="quotation-form" method="POST" action="{{ route('devisClient.store') }}">
                @csrf

                <!-- Section Client -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="customer_name">Client <span class="text-danger">*</span></label>
                            <select class="form-control select2" id="customer_name" name="customer_name" required>
                                <option value="">Sélectionner un client</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer['name'] }}">
                                        {{ $customer['customer_name'] }} ({{ $customer['name'] }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="transaction_date">Date</label>
                            <input type="date" class="form-control" name="transaction_date" 
                                   value="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>
                    
                </div>

                <!-- Section Articles -->
                <div class="table-responsive mb-4">
                    <table class="table table-bordered" id="items-table">
                        <thead class="thead-light">
                            <tr>
                                <th width="30%">Article</th>
                                <th width="10%">Quantité</th>
                                <th width="15%">Prix Unitaire</th>
                                <th width="5%"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="item-row">
                                <td>
                                    <select class="form-control select2 item-select" name="items[0][item_code]" required>
                                        <option value="">Sélectionner un article</option>
                                        @foreach($items as $item)
                                            <option value="{{ $item['item_code'] }}" 
                                                    data-name="{{ $item['item_name'] }}"
                                                    data-rate="{{ $item['rate'] ?? 0 }}">
                                                {{ $item['item_name'] }} ({{ $item['item_code'] }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" name="items[0][item_name]">
                                </td>
                              
                                <td>
                                    <!-- Changement de items[0][qty] à items[0][quantity] -->
                                    <input type="number" class="form-control quantity" 
                                         name="items[0][quantity]" min="1" value="1" required>
                                </td>
                                
                                <td>
                                    <input type="number" step="0.01" class="form-control rate" 
                                           name="items[0][rate]" min="0" required>
                                </td>
                                
                                <td>
                                    <button type="button" class="btn btn-danger btn-sm remove-item">
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