@extends('home')

@section('title', 'liste des salaryStructure')

@section('content')
<div class="card">
   
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
        <h2 class="card-title">New Salary structure</h2>
            <a href="{{ route('salaraStr.addForm') }}" class="btn btn-outline-primary">
                <i class="fas fa-filter"></i> New Salary Structure
            </a>
            <a href="{{ route('salaryAssg.addAssg') }}" class="btn btn-outline-primary">
                <i class="fas fa-filter"></i> Assignement
            </a>
            <h2 class="card-title">
                <i class="fas fa-file-invoice-dollar"></i> Liste des salary structure
                
            </h2>
            
        </div>
    </div>

    <div class="card-body">
        

        @if(count($sstructures) > 0)
            <div class="table-responsive">
                <table class="data-table table-hover">
                    <thead>
                        <tr>
                            
                            <th>Nom</th>
                            <th>est Activer</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sstructures as $structure)
                            <tr>
                                <td class="font-weight-bold">
                                    <a href="#" class="text-primary">
                                        {{ $structure['name'] }}
                                    </a>
                                </td>
                               
                                <td>
                                    <span>
                                        {{ $structure['is_active'] }}
                                    </span>
                                </td>
                                
                                <td>
                        <a href="{{ route('salaraStr.show', ['name' => $structure['name']]) }}" class="btn btn-primary btn-sm">Voir</a>
                    </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> Aucun Salary structure
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        $('.select2-status').select2({
            minimumResultsForSearch: Infinity
        });
    });
</script>
@endpush
@endsection