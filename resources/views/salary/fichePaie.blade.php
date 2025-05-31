@extends('home')

@section('title', 'liste des fiche de paie')

@section('content')
<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="card-title">
                <i class="fas fa-file-invoice-dollar"></i> Liste des fiche de paie 
            </h2>
            
        </div>
    </div>

    <div class="card-body">


    @if(!empty($slips) && count($slips) > 0)

    <div class="table-responsive">
                <table class="data-table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Employee</th>
                            <th>Statut</th>
                            <th>company</th>
                            <th>date posting</th>
                            <th>salary structure</th>
                        </tr>
                    </thead>
                    @foreach($slips as $slip)

                    <tbody>
                            <tr>
                                <td class="font-weight-bold">
                                    <a href="#" class="text-primary">
                                        {{ $slip['name'] }}
                                    </a>
                                </td>
                                <td>{{ $slip['employee_name'] }} </td>
                                <td>
                                    <span class="badge 
                                        @if($slip['status'] == 'Submitted') bg-success
                                        @elseif($slip['status'] == 'Draft') bg-danger
                                        @else bg-warning
                                        @endif">
                                        {{ $slip['status'] }}
                                    </span>
                                </td>
                                <td class="font-weight-bold">
                                    <a href="#" class="text-primary">
                                        {{ $slip['company'] }}
                                    </a>
                                </td>
                                <td class="font-weight-bold">
                                    <a href="#" class="text-primary">
                                    {{ \Carbon\Carbon::parse($slip['posting_date'])->format('d/m/Y') }}
                                    </a>
                                </td>
                                <td class="font-weight-bold">
                                    <a href="#" class="text-primary">
                                        {{ $slip['salary_structure'] }}
                                    </a>
                                </td>
                                <td>
                                <a href="{{ route('fichePaie.show', ['name' => $slip['name']]) }}" class="btn btn-primary btn-sm">Voir</a>
                                </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> Aucun Fiche Trouvé
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