<?php

namespace Tests\Feature;

use App\Models\KnowledgeRule;
use App\Models\MentalDisorder;
use App\Models\Symptom;
use App\Models\User;
use App\Services\DempsterShaferService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ExpertSystemWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_loads(): void
    {
        $this->get('/')->assertOk();
    }

    public function test_consultation_form_loads_when_route_exists(): void
    {
        $this->seed();

        $response = $this->get($this->routeUrl('consultation.create'));

        $response->assertOk();
        $response->assertDontSee('G01');
        $response->assertDontSee('G08');
    }

    public function test_public_pages_do_not_show_symptom_or_disorder_codes(): void
    {
        $this->seed();

        $this->get($this->routeUrl('info'))
            ->assertOk()
            ->assertDontSee('P01')
            ->assertDontSee('P02');

        $response = $this->followingRedirects()->post(
            $this->routeUrl('consultation.store'),
            $this->consultationPayload(),
        );

        $response->assertSuccessful();
        $response->assertDontSee('G01');
        $response->assertDontSee('G03');
        $response->assertDontSee('P01');
        $response->assertDontSee('P02');
    }

    public function test_consultation_submission_shows_a_result_when_route_exists(): void
    {
        $route = $this->routeUrl('consultation.store');

        $this->seed();

        $response = $this->followingRedirects()->post(
            $route,
            $this->consultationPayload(),
        );

        $response->assertSuccessful();
        $response->assertSessionHasNoErrors();
        $response->assertSee('SMA Uji');
        $response->assertSee('Jalan Mawar No. 7');
        $response->assertSee('08123456789');
        $response->assertSee('Konflik keluarga');
        $this->assertResponseContainsAnyText($response->getContent(), [
            'hasil',
            'result',
            'diagnosis',
            'dempster',
            'shafer',
            'kepercayaan',
            'gangguan',
        ]);
    }

    public function test_admin_dashboard_loads_when_route_exists(): void
    {
        $route = $this->routeUrl('admin.dashboard');

        $this->actingAs($this->adminUser());

        $response = $this->get($route);

        $response->assertOk();
    }

    public function test_non_admin_user_cannot_login_to_admin_panel(): void
    {
        $this->skipUnlessRoutesExist(['login.store']);

        $user = User::factory()->create([
            'email' => 'member@example.com',
            'password' => 'password',
            'is_admin' => false,
        ]);

        $response = $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_authenticated_non_admin_user_cannot_access_admin_dashboard(): void
    {
        $route = $this->routeUrl('admin.dashboard');

        $user = User::factory()->create([
            'email' => 'viewer@example.com',
            'is_admin' => false,
        ]);

        $this->actingAs($user)
            ->get($route)
            ->assertForbidden();
    }

    public function test_admin_can_manage_symptoms_when_routes_exist(): void
    {
        $this->skipUnlessRoutesExist([
            'admin.symptoms.index',
            'admin.symptoms.store',
            'admin.symptoms.edit',
            'admin.symptoms.update',
            'admin.symptoms.destroy',
        ]);

        $this->actingAs($this->adminUser());

        $this->get(route('admin.symptoms.index'))->assertOk();

        if (Route::has('admin.symptoms.create')) {
            $this->get(route('admin.symptoms.create'))->assertOk();
        }

        $createResponse = $this->post(route('admin.symptoms.store'), $this->symptomPayload('G991'));

        $createResponse->assertSessionHasNoErrors();
        $this->assertSuccessfulOrRedirect($createResponse->baseResponse->getStatusCode());

        $symptomId = $this->latestSymptomId();

        $this->get(route('admin.symptoms.edit', ['symptom' => $symptomId]))->assertOk();

        $updateResponse = $this->put(
            route('admin.symptoms.update', ['symptom' => $symptomId]),
            $this->symptomPayload('G991', [
                'name' => 'Sulit tidur setelah konflik keluarga',
                'description' => 'Gangguan tidur yang menetap setelah tekanan keluarga.',
                'weight' => 0.8,
                'belief' => 0.8,
            ]),
        );

        $updateResponse->assertSessionHasNoErrors();
        $this->assertSuccessfulOrRedirect($updateResponse->baseResponse->getStatusCode());

        $deleteResponse = $this->delete(route('admin.symptoms.destroy', ['symptom' => $symptomId]));

        $deleteResponse->assertSessionHasNoErrors();
        $this->assertSuccessfulOrRedirect($deleteResponse->baseResponse->getStatusCode());
    }

    public function test_admin_can_manage_knowledge_rules_when_routes_exist(): void
    {
        $this->skipUnlessRoutesExist([
            'admin.knowledge-rules.index',
            'admin.knowledge-rules.store',
            'admin.knowledge-rules.edit',
            'admin.knowledge-rules.update',
            'admin.knowledge-rules.destroy',
        ]);

        $this->seed();
        $this->actingAs($this->adminUser());

        $disorder = MentalDisorder::query()->orderBy('code')->firstOrFail();
        $symptom = Symptom::query()->create([
            'code' => 'G992',
            'name' => 'Sulit tenang setelah konflik keluarga',
            'description' => 'Gejala uji untuk basis pengetahuan admin.',
            'belief' => 0.45,
        ]);

        $this->get(route('admin.knowledge-rules.index'))->assertOk();

        if (Route::has('admin.knowledge-rules.create')) {
            $this->get(route('admin.knowledge-rules.create'))->assertOk();
        }

        $createResponse = $this->post(route('admin.knowledge-rules.store'), [
            'rule_code' => 'RTEST',
            'mental_disorder_id' => $disorder->id,
            'symptom_id' => $symptom->id,
            'belief' => 0.45,
        ]);

        $createResponse->assertSessionHasNoErrors();
        $this->assertSuccessfulOrRedirect($createResponse->baseResponse->getStatusCode());

        $rule = KnowledgeRule::query()
            ->where('mental_disorder_id', $disorder->id)
            ->where('symptom_id', $symptom->id)
            ->firstOrFail();

        $this->get(route('admin.knowledge-rules.edit', ['knowledge_rule' => $rule]))->assertOk();

        $updateResponse = $this->put(route('admin.knowledge-rules.update', ['knowledge_rule' => $rule]), [
            'rule_code' => 'RTEST',
            'mental_disorder_id' => $disorder->id,
            'symptom_id' => $symptom->id,
            'belief' => 0.65,
        ]);

        $updateResponse->assertSessionHasNoErrors();
        $this->assertSuccessfulOrRedirect($updateResponse->baseResponse->getStatusCode());
        $this->assertDatabaseHas('knowledge_rules', [
            'id' => $rule->id,
            'belief' => 0.65,
        ]);

        $deleteResponse = $this->delete(route('admin.knowledge-rules.destroy', ['knowledge_rule' => $rule]));

        $deleteResponse->assertSessionHasNoErrors();
        $this->assertSuccessfulOrRedirect($deleteResponse->baseResponse->getStatusCode());
        $this->assertDatabaseMissing('knowledge_rules', [
            'id' => $rule->id,
        ]);
    }

    public function test_dempster_shafer_service_uses_database_knowledge_rules(): void
    {
        $this->seed();

        $symptom = Symptom::query()->where('code', 'G01')->firstOrFail();

        $symptom->update(['belief' => 0.92]);
        KnowledgeRule::query()
            ->where('symptom_id', $symptom->id)
            ->update(['belief' => 0.92]);

        $result = app(DempsterShaferService::class)->diagnose(['G01']);

        $this->assertSame('P01', $result['result']['code']);
        $this->assertEqualsWithDelta(0.92, $result['belief'], 0.000001);
        $this->assertSame('92%', $result['percentage_text']);
    }

    public function test_database_seed_matches_bab_three_document_example(): void
    {
        $this->seed();

        $result = app(DempsterShaferService::class)->diagnose(['G01', 'G03', 'G08', 'G11']);

        $this->assertSame(2, MentalDisorder::query()->count());
        $this->assertSame('P02', $result['result']['code']);
        $this->assertEqualsWithDelta(0.976, $result['belief'], 0.000001);
        $this->assertSame('97.6%', $result['percentage_text']);
        $this->assertSame(['P02' => 0.976, 'Theta' => 0.024], $result['masses']);
    }

    public function test_database_seed_matches_bab_three_symptom_table(): void
    {
        $this->seed();

        $expectedSymptoms = [
            'G01' => ['Tindakan ingin bunuh diri', 0.8],
            'G02' => ['Malas berkomunikasi', 0.3],
            'G03' => ['Menarik diri dari keluarga', 0.6],
            'G04' => ['Mudah marah', 0.5],
            'G05' => ['Sulit berpikir jernih', 0.5],
            'G06' => ['Tidak memiliki nafsu makan', 0.3],
            'G07' => ['Aktivitas terganggu', 0.3],
            'G08' => ['Overtinhking', 0.5],
            'G09' => ['Merasa cemas berlebih', 0.4],
            'G10' => ['Tidak memiliki rasa percaya diri', 0.3],
            'G11' => ['Sulit tidur', 0.4],
            'G12' => ['Mudah takut', 0.2],
            'G13' => ['Sering menderita sakit kepala', 0.3],
            'G14' => ['Sedih berkepanjangan', 0.4],
            'G15' => ['Merasa tidak bahagia', 0.5],
        ];

        foreach ($expectedSymptoms as $code => [$name, $belief]) {
            $this->assertDatabaseHas('symptoms', [
                'code' => $code,
                'name' => $name,
                'belief' => $belief,
            ]);
        }
    }

    private function routeUrl(string $name, array $parameters = []): string
    {
        if (! Route::has($name)) {
            $this->markTestSkipped("Route [{$name}] belum tersedia.");
        }

        return route($name, $parameters);
    }

    /**
     * @param  array<int, string>  $names
     */
    private function skipUnlessRoutesExist(array $names): void
    {
        foreach ($names as $name) {
            if (! Route::has($name)) {
                $this->markTestSkipped("Route [{$name}] belum tersedia.");
            }
        }
    }

    private function adminUser(): User
    {
        $user = User::factory()->create([
            'name' => 'Admin Sistem Pakar',
            'email' => 'admin@example.com',
        ]);

        $attributes = [];

        if (Schema::hasColumn('users', 'is_admin')) {
            $attributes['is_admin'] = true;
        }

        if (Schema::hasColumn('users', 'role')) {
            $attributes['role'] = 'admin';
        }

        if (Schema::hasColumn('users', 'email_verified_at')) {
            $attributes['email_verified_at'] = now();
        }

        if ($attributes !== []) {
            $user->forceFill($attributes)->save();
        }

        return $user->refresh();
    }

    /**
     * @return array<string, mixed>
     */
    private function consultationPayload(): array
    {
        return [
            'name' => 'Remaja Uji',
            'age' => 16,
            'gender' => 'female',
            'address' => 'Jalan Mawar No. 7',
            'phone' => '08123456789',
            'school' => 'SMA Uji',
            'parent_guardian' => 'Wali Uji',
            'family_stressor' => 'konflik',
            'notes' => 'Catatan konteks keluarga untuk pengujian.',
            'symptoms' => $this->exampleSymptomIds(),
        ];
    }

    /**
     * @return array<int, int|string>
     */
    private function exampleSymptomIds(): array
    {
        if (Schema::hasTable('symptoms')) {
            $ids = DB::table('symptoms')->orderBy('id')->limit(3)->pluck('id')->all();

            $this->assertNotEmpty($ids, 'Seeder harus menyediakan minimal satu data symptoms untuk konsultasi.');

            return $ids;
        }

        return [
            'sleep_disturbance',
            'irritability',
            'family_conflict_stress',
        ];
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function symptomPayload(string $code, array $overrides = []): array
    {
        return array_merge([
            'code' => $code,
            'name' => 'Sulit tidur karena konflik keluarga',
            'question' => 'Apakah kamu sering sulit tidur setelah konflik keluarga?',
            'description' => 'Gejala gangguan tidur yang muncul setelah tekanan dari lingkungan keluarga.',
            'weight' => 0.7,
            'belief' => 0.7,
            'plausibility' => 0.3,
            'is_active' => true,
        ], $overrides);
    }

    private function latestSymptomId(): int
    {
        $this->assertTrue(Schema::hasTable('symptoms'), 'Tabel [symptoms] harus tersedia untuk CRUD gejala.');

        $id = DB::table('symptoms')->orderByDesc('id')->value('id');

        $this->assertNotNull($id, 'Store route harus membuat satu data symptom.');

        return (int) $id;
    }

    /**
     * @param  array<int, string>  $needles
     */
    private function assertResponseContainsAnyText(string $content, array $needles): void
    {
        $content = mb_strtolower(strip_tags($content));

        foreach ($needles as $needle) {
            if (str_contains($content, mb_strtolower($needle))) {
                $this->assertTrue(true);

                return;
            }
        }

        $this->fail('Halaman hasil konsultasi harus menampilkan hasil diagnosis atau istilah Dempster-Shafer.');
    }

    private function assertSuccessfulOrRedirect(int $status): void
    {
        $this->assertTrue(
            $status >= 200 && $status < 400,
            "Expected a successful or redirect response, received [{$status}].",
        );
    }
}
