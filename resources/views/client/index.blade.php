@extends('home')

@section('title', 'Liste des clients')

@section('content')
<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="card-title">Liste des clients</h2>
            <a href="#" class="btn btn-outline-primary">
                <i class="fas fa-filter"></i> Filtrer par fournisseur
            </a>
        </div>
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="card-title">New clients</h2>
            <a href="{{route('client.createForm')}}" class="btn btn-outline-primary">
                <i class="fas fa-filter"></i> New clients
            </a>
        </div>
    </div>

    <div class="card-body">
        @if (count($clients) > 0)
            <div class="table-responsive">
                <table class="data-table table-hover">
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>group</th>
                            <th>Status</th>
                            <th>Pays</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($clients as $client)
                            <tr>
                                <td>
                                    <a href="{{ route('client.show', ['name'=> $client['name']]) }}" class="text-primary">
                                        {{ $client['name'] }}
                                    </a>
                                </td>
                                <td>{{ $client['customer_group']}}</td>
                                <td>  {{ $client['disabled'] == 0 ? 'Activer' : 'Desactiver' }}</td>
                                <td>{{ $client['territory'] }}</td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('client.show', ['name'=> $client['name']]) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> Aucun devis trouvé
            </div>
        @endif
    </div>
</div>
@endsection