@extends('home')

@section('title', 'Liste des réductions')

@section('content')
<div class="container mt-4">
    <h1>Liste des réductions mensuelles</h1>

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
    <form method="GET" class="form-inline mb-3">
    <label for="mois" class="mr-2">Filtrer par mois :</label>
    <input type="month" name="mois" id="mois" class="form-control mr-2" value="{{ $mois ?? '' }}">
    <button type="submit" class="btn btn-primary">Filtrer</button>
    <a href="{{ route('reduction.create') }}" class="nav-item">
        <i class="fas fa-add"></i>Ajout Reduction
    </a>
</form>

    @if(count($reductions) > 0)
        <table class="table table-bordered table-striped mt-3">
            <thead class="thead-dark">
                <tr>
                    <th>#</th>
                    <th>Mois</th>
                    <th>Valeur (%)</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reductions as $index => $reduction)
                    <tr>
                        <td>{{ $reduction->id  }}</td>
                        <td>{{ \Carbon\Carbon::parse($reduction->mois)->format('F Y') }}</td>
                        <td>
                            @if ($reduction->valeur >= 0)
                                <span class="text-success">+{{ $reduction->valeur }}%</span>
                            @else
                                <span class="text-danger">{{ $reduction->valeur }}%</span>
                            @endif
                        </td>
                        <td>
                         <form action="{{ route('reduction.delete', $reduction->id) }}" method="POST" onsubmit="return confirm('Confirmer la suppression ?')">
                             @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">
                                <i class="fas fa-trash"></i>
                            </button>
                            </form>
                        </td>
                        <td>
    <a href="{{ route('reduction.edit', $reduction->id) }}" class="btn btn-sm btn-warning">
        <i class="fas fa-edit"></i>
    </a>
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
