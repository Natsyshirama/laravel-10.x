@extends('home')

@section('title', 'ajout Reduction')

@section('content')
<div class="container mt-4">
   <h1>Ajouter une réduction</h1>
   <form action="{{ route('reduction.insert') }}" method="POST">
      @csrf
      <label for="mois">Mois :</label>
        <input type="date" name="mois" id="mois" required>
        <br><br>

        <label for="reduction">Réduction (%):</label>
        <input type="number" name="valeur" id="valeur" required>

        <button type="submit" class="btn btn-primary mt-3">Ajouter</button>
   </form>
</div>
@endsection
