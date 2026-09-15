@extends('layouts.patient', ['title' => 'Tableau de bord'])

@section('content')
<div class="w-[96%] mx-auto min-h-screen pb-12 space-y-6">

    <!-- Bannière de Bienvenue Médicale -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-teal-700 via-emerald-700 to-cyan-800 p-6 md:p-8 text-white shadow-2xl">
        <div className="absolute -top-24 -right-24 w-96 h-96 bg-white/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-2">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/15 backdrop-blur-md text-xs font-semibold text-teal-100">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse"></span>
                    Espace Patient Sécurisé
                </div>
                <h1 class="text-2xl md:text-4xl font-extrabold tracking-tight">
                    Bonjour, <span class="text-teal-200">{{ $patient->user->prenom }} {{ $patient->user->name }}</span> 👋
                </h1>
                <p class="text-teal-100/90 text-sm max-w-xl">
                    Consultez votre passeport santé, l'historique de vos rendez-vous et vos informations personnelles.
                </p>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('patient.setting') }}" class="px-5 py-3 bg-white text-teal-800 hover:bg-teal-50 font-bold rounded-2xl shadow-lg transition-all text-xs md:text-sm flex items-center gap-2">
                    <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    <span>Modifier mon profil</span>
                </a>
            </div>
        </div>
    </div>



    <!-- Grille des Activités & Consultations -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Dernières consultations -->
        <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-xl shadow-slate-200/40">
            <div class="flex items-center space-x-3 mb-4 pb-3 border-b border-slate-100">
                <div class="w-10 h-10 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <h2 class="text-base font-bold text-slate-900">Dernières consultations</h2>
            </div>
            
            <ul class="space-y-3">
                @forelse ($consultations as $item)
                    <li class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-between">
                        <div>
                            <p class="text-sm font-bold text-teal-700">{{ optional($item->admission)->type_admission ?? 'Consultation Générale' }}</p>
                            <p class="text-xs text-slate-500 mt-0.5">{{ dateCompletFr($item->created_at) }}</p>
                        </div>
                        <span class="px-3 py-1 rounded-full text-xs font-bold bg-teal-100 text-teal-800">
                            Effectuée
                        </span>
                    </li>
                @empty
                    <li class="py-6 text-center text-slate-400 text-sm">
                        Aucune consultation enregistrée pour le moment.
                    </li>
                @endforelse
            </ul>
        </div>

        <!-- Déclarations -->
        <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-xl shadow-slate-200/40">
            <div class="flex items-center space-x-3 mb-4 pb-3 border-b border-slate-100">
                <div class="w-10 h-10 rounded-2xl bg-cyan-50 text-cyan-600 flex items-center justify-center font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <h2 class="text-base font-bold text-slate-900">Déclarations</h2>
            </div>

            <ul class="space-y-3">
                @forelse ($declarations as $item)
                    <li class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-between">
                        <div>
                            <p class="text-sm font-bold text-cyan-700">
                                {{ $item->type == 'death' ? 'Déclaration de décès' : 'Déclaration de naissance' }}
                            </p>
                            <p class="text-xs text-slate-500 mt-0.5">{{ dateCompletFr($item->created_at) }}</p>
                        </div>
                        <span class="px-3 py-1 rounded-full text-xs font-bold bg-cyan-100 text-cyan-800">
                            Enregistrée
                        </span>
                    </li>
                @empty
                    <li class="py-6 text-center text-slate-400 text-sm">
                        Aucune déclaration enregistrée.
                    </li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
@endsection