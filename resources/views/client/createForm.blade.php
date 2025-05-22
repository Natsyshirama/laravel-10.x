@extends('home')

@section('title', 'Nouveau Client')
@section('content')
<div class="card">
    <div class="card-header bg-primary text-white">
        <h2 class="card-title">Nouveau CLient</h2>
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
    <form method="POST" action="{{route('client.store')}}">
        @csrf
        
        <div class="form-group">
            <label>Nom du client*</label>
            <input type="text" name="customer_name" class="form-control" required>
        </div>

        <div class="row mb-4">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Type de client</label>
                    <select name="customer_type" class="form-control">
                        <option value="Individual">Particulier</option>
                        <option value="Company">Entreprise</option>
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Groupe de clients</label>
                    <select name="customer_group" class="form-control">
                        @foreach($options['customer_groups'] as $group)
                            <option value="{{ $group['name'] }}">{{ $group['name'] }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Téléphone</label>
                    <input type="text" name="mobile_no" class="form-control">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email_id" class="form-control">
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Enregistrer</button>
    </form>
</div>
@endsection