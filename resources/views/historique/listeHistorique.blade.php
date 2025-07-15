@extends('home')

@section('title', 'Liste des Historique')

@section('content')
<div class="container mt-4">
    <h1>Liste Historique</h1>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <strong>Erreur :</strong>
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
  
    @if(count($historiques) > 0)
        <table class="table table-bordered table-striped mt-3">
            <thead class="thead-dark">
                <tr>
                    
                    <th>Mois</th>
                    <th>employee </th>
                    <th>salaire Ancien</th>
                </tr>
            </thead>
            <tbody>
                @foreach($historiques as $index => $historique)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($historique->dateAncien)->format('F Y') }}</td>
                        <td>{{ $historique->employee  }}</td>

                        <td>
                            @if ($historique->salaire >= 0)
                                <span class="text-success">{{ $historique->salaire }}</span>
                            @else
                                <span class="text-danger">null</span>
                            @endif
                        </td>
                        


                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p>Aucune réduction trouvée.</p>
    @endif
</div>
@endsection
