@extends('home')

@section('title', 'Profil Utilisateur')

@section('content')
<div class="card">
    <div class="card-header">
        <h2 class="card-title">
            <i class="fas fa-user-circle"></i> Profil Utilisateur
        </h2>
    </div>

    <div class="card-body">
        <div class="row">
            <!-- Colonne de gauche - Photo de profil -->
            <div class="col-md-3 text-center">
                <div class="profile-avatar mb-4">
                    <div class="avatar-circle">
                        <span class="initials">
                            {{ substr($profile['first_name'] ?? '', 0, 1) }}{{ substr($profile['last_name'] ?? '', 0, 1) }}
                        </span>
                    </div>
                    
                </div>
                
               
            </div>

            <!-- Colonne de droite - Détails du profil -->
            <div class="col-md-9">
                <div class="profile-details">
                    <h4 class="section-title mb-4">
                        <i class="fas fa-id-card"></i> Informations personnelles
                    </h4>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-item">
                                <span class="info-label">Nom complet :</span>
                                <span class="info-value">
                                    {{ $profile['first_name'] ?? '' }} {{ $profile['last_name'] ?? '' }}
                                </span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <span class="info-label">Email :</span>
                                <span class="info-value">{{ $profile['email'] }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-item">
                                <span class="info-label">Genre :</span>
                                <span class="info-value">
                                    {{ $profile['gender'] ?? 'Non spécifié' }}
                                </span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <span class="info-label">Date de création :</span>
                                <span class="info-value">
                                    {{ \Carbon\Carbon::parse($profile['creation'])->format('d/m/Y H:i') }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                   

                    <div class="text-right mt-4">
                        <a href="{{ route('logout') }}" class="btn btn-danger">
                            <i class="fas fa-sign-out-alt"></i> Se déconnecter
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .avatar-circle {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        background-color: #4361ee;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
    }
    
    .initials {
        color: white;
        font-size: 2.5rem;
        font-weight: bold;
    }
    
    .info-item {
        display: flex;
        margin-bottom: 1.5rem;
    }
    
    .info-label {
        font-weight: 600;
        color: #64748b;
        min-width: 150px;
    }
    
    .info-value {
        flex: 1;
    }
    
    .section-title {
        color: #4361ee;
        font-weight: 600;
    }
    
    .security-item {
        display: flex;
        align-items: flex-start;
        padding: 1rem;
        background: #f8fafc;
        border-radius: 8px;
        margin-bottom: 1rem;
    }
    
    .security-icon {
        font-size: 1.5rem;
        margin-right: 1rem;
        margin-top: 0.25rem;
    }
    
    .security-content h5 {
        margin-bottom: 0.25rem;
    }
    
    .security-content p {
        color: #64748b;
        margin-bottom: 0.5rem;
    }
    
    .list-group-item.active {
        background-color: #4361ee;
        border-color: #4361ee;
    }
</style>
@endsection