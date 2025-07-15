@extends('home')

@section('title', 'Prepa')
@section('content')







@endsection
@section('scripts')
<script>
    function confirmReset() {
        return confirm("⚠️ Êtes-vous sûr de vouloir réinitialiser toutes les données ? Cette action est irréversible.");
    }
</script>


@endsection