<?php

use App\Http\Controllers\Admin\ConsultationController as AdminConsultationController;
use App\Http\Controllers\Admin\DisorderController;
use App\Http\Controllers\Admin\KnowledgeRuleController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\SymptomController;
use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\ConsultationController;
use App\Models\Consultation;
use App\Models\ConsultationResult;
use App\Models\KnowledgeRule;
use App\Models\MentalDisorder;
use App\Models\Symptom;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

Route::get('/', function () {
    $hasExpertTables = Schema::hasTable('symptoms') && Schema::hasTable('knowledge_rules') && Schema::hasTable('consultations');

    return view('home', [
        'stats' => [
            ['label' => 'Gejala terpetakan', 'value' => $hasExpertTables ? Symptom::count() : 0, 'note' => 'indikator perilaku dan emosi'],
            ['label' => 'Basis aturan', 'value' => $hasExpertTables ? KnowledgeRule::count() : 0, 'note' => 'relasi evidence dan gangguan'],
            ['label' => 'Konsultasi tercatat', 'value' => $hasExpertTables ? Consultation::count() : 0, 'note' => 'riwayat deteksi awal'],
        ],
    ]);
})->name('home');

Route::view('/welcome', 'welcome')->name('welcome');
Route::get('/info-penyakit', fn () => view('info', [
    'disorders' => Schema::hasTable('mental_disorders') ? MentalDisorder::query()->orderBy('code')->get() : collect(),
]))->name('info');
Route::view('/profil-pakar', 'expert-profile')->name('expert-profile');

Route::get('/konsultasi', [ConsultationController::class, 'create'])->name('consultation.create');
Route::post('/konsultasi', [ConsultationController::class, 'store'])->name('consultation.store');
Route::get('/konsultasi/{consultation}', [ConsultationController::class, 'show'])->name('consultation.show');
Route::get('/konsultasi/{consultation}/cetak', [ConsultationController::class, 'printResult'])->name('consultation.print');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AdminLoginController::class, 'create'])->name('login');
    Route::post('/login', [AdminLoginController::class, 'store'])->middleware('throttle:5,1')->name('login.store');
});

Route::post('/logout', [AdminLoginController::class, 'destroy'])->middleware('auth')->name('logout');

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', function () {
        $hasExpertTables = Schema::hasTable('symptoms')
            && Schema::hasTable('mental_disorders')
            && Schema::hasTable('knowledge_rules')
            && Schema::hasTable('consultations')
            && Schema::hasTable('consultation_results');

        if (! $hasExpertTables) {
            return view('admin.dashboard', [
                'summary' => [
                    'symptoms_count' => 0,
                    'disorders_count' => 0,
                    'rules_count' => 0,
                    'consultations_count' => 0,
                ],
                'recentConsultations' => collect(),
                'topDisorders' => collect(),
            ]);
        }

        $recentConsultations = Consultation::query()
            ->with(['results.mentalDisorder', 'detectedMentalDisorder'])
            ->latest()
            ->limit(5)
            ->get();

        $totalResults = max(1, ConsultationResult::query()->where('is_selected', true)->count());
        $topDisorders = MentalDisorder::query()
            ->withCount(['consultationResults as count' => fn ($query) => $query->where('is_selected', true)])
            ->orderByDesc('count')
            ->get()
            ->map(fn (MentalDisorder $disorder): array => [
                'name' => $disorder->name,
                'count' => $disorder->count,
                'percentage' => round(($disorder->count / $totalResults) * 100, 1),
            ]);

        return view('admin.dashboard', [
            'summary' => [
                'symptoms_count' => Symptom::count(),
                'disorders_count' => MentalDisorder::count(),
                'rules_count' => KnowledgeRule::count(),
                'consultations_count' => Consultation::count(),
            ],
            'recentConsultations' => $recentConsultations,
            'topDisorders' => $topDisorders,
        ]);
    })->name('dashboard');

    Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::resource('symptoms', SymptomController::class);
    Route::resource('disorders', DisorderController::class);
    Route::resource('knowledge-rules', KnowledgeRuleController::class);
    Route::resource('consultations', AdminConsultationController::class)->only(['index', 'show', 'destroy']);
});
