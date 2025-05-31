@extends('home')

@section('title', 'liste des employees')

@section('content')
<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="card-title">
                <i class="fas fa-file-invoice-dollar"></i> Liste des Employees
            </h2>
            
        </div>
    </div>

    <div class="card-body">
        <form method="GET" action="{{ route('employee.index') }}" class="mb-4">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label for="type" class="form-label">Departement</label>
                        
                    <select name="department" id="department" class="form-control select2-status">
                        <option value="">-- Tous les départements --</option>
                        @foreach($departments as $department)

                        <option value="{{ $department['name'] }}" {{ $selectDepartment == $department['name'] ? 'selected' : '' }}>
                            {{ $department['name'] }}
                        </option>
                        @endforeach
                    </select>
                   
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter"></i> Filtrer
                    </button>
                </div>
            </div>
        </form>

        @if(count($employees) > 0)
            <div class="table-responsive">
                <table class="data-table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nom</th>
                            <th>Statut</th>
                            <th>designation</th>
                            <th>Department</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($employees as $employee)
                            <tr>
                                <td class="font-weight-bold">
                                    <a href="#" class="text-primary">
                                        {{ $employee['name'] }}
                                    </a>
                                </td>
                                <td>{{ $employee['first_name'] }} </td>
                                <td>
                                    <span class="badge 
                                        @if($employee['status'] == 'Active') bg-success
                                        @elseif($employee['status'] == 'Inactive') bg-danger
                                        @else bg-warning
                                        @endif">
                                        {{ $employee['status'] }}
                                    </span>
                                </td>
                                <td class="font-weight-bold">
                                    <a href="#" class="text-primary">
                                        {{ $employee['designation'] }}
                                    </a>
                                </td>
                                <td class="font-weight-bold">
                                    <a href="#" class="text-primary">
                                        {{ $employee['department'] }}
                                    </a>
                                </td>
                                <td>
                        <a href="{{ route('employee.show', $employee['name']) }}" class="btn btn-primary btn-sm">Voir</a>
                    </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> Aucun Employee Trouvé
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