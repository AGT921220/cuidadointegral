<?php

namespace Tests\Feature\Care;

use App\Features\Care\Mail\IncidentReportedMail;
use App\Features\Permissions\Constants\RoleTypes;
use App\Features\Permissions\PermissionHandler;
use App\Models\CareLog;
use App\Models\CareTask;
use App\Models\Patient;
use App\Models\PatientLink;
use App\Models\Shift;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CareApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_caregiver_only_sees_assigned_shifts(): void
    {
        $caregiver = $this->userFor(RoleTypes::CUIDADOR);
        $other = User::factory()->create();
        $own = Shift::factory()->create(['caregiver_id' => $caregiver->id]);
        Shift::factory()->create(['caregiver_id' => $other->id]);

        $this->actingAs($caregiver, 'sanctum')
            ->getJson('/api/shifts')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $own->id)
            ->assertJsonPath('data.0.patient.name', $own->patient->name)
            ->assertJsonPath('data.0.instructions', $own->instructions);

        $this->actingAs($caregiver, 'sanctum')
            ->getJson('/api/shifts/'.$own->id)
            ->assertOk()
            ->assertJsonPath('data.service_address', $own->service_address);

        $foreign = Shift::factory()->create();

        $this->actingAs($caregiver, 'sanctum')
            ->getJson('/api/shifts/'.$foreign->id)
            ->assertForbidden();
    }

    public function test_check_in_and_check_out_follow_the_sequence_and_store_location(): void
    {
        $caregiver = $this->userFor(RoleTypes::CUIDADOR);
        $shift = Shift::factory()->create(['caregiver_id' => $caregiver->id]);
        $location = [
            'latitude' => 19.432608,
            'longitude' => -99.133209,
            'accuracy' => 12.5,
        ];

        $this->actingAs($caregiver, 'sanctum')
            ->postJson('/api/shifts/'.$shift->id.'/check-out', $location)
            ->assertUnprocessable();

        $this->actingAs($caregiver, 'sanctum')
            ->postJson('/api/shifts/'.$shift->id.'/check-in', $location)
            ->assertOk()
            ->assertJsonPath('data.status', Shift::STATUS_IN_PROGRESS)
            ->assertJsonPath('data.check_in_location.accuracy', 12.5);

        $this->actingAs($caregiver, 'sanctum')
            ->postJson('/api/shifts/'.$shift->id.'/check-in', $location)
            ->assertUnprocessable();

        $this->actingAs($caregiver, 'sanctum')
            ->postJson('/api/shifts/'.$shift->id.'/check-out', $location)
            ->assertOk()
            ->assertJsonPath('data.status', Shift::STATUS_COMPLETED);

        $this->assertDatabaseCount('shift_events', 2);
        $this->assertDatabaseCount('shift_locations', 2);
    }

    public function test_a_cancelled_shift_cannot_be_checked_in(): void
    {
        $caregiver = $this->userFor(RoleTypes::CUIDADOR);
        $shift = Shift::factory()->create([
            'caregiver_id' => $caregiver->id,
            'status' => Shift::STATUS_CANCELLED,
        ]);

        $this->actingAs($caregiver, 'sanctum')
            ->postJson('/api/shifts/'.$shift->id.'/check-in')
            ->assertUnprocessable();
    }

    public function test_a_caregiver_cannot_read_an_unassigned_patient(): void
    {
        $caregiver = $this->userFor(RoleTypes::CUIDADOR);
        $assigned = Patient::factory()->create();
        $other = Patient::factory()->create();
        Shift::factory()->create([
            'caregiver_id' => $caregiver->id,
            'patient_id' => $assigned->id,
        ]);

        $this->actingAs($caregiver, 'sanctum')
            ->getJson('/api/patients/'.$assigned->id)
            ->assertOk()
            ->assertJsonPath('data.name', $assigned->name)
            ->assertJsonMissingPath('data.email');

        $this->actingAs($caregiver, 'sanctum')
            ->getJson('/api/patients/'.$other->id)
            ->assertForbidden();
    }

    public function test_a_family_member_only_reads_linked_patients_and_cannot_operate_a_shift(): void
    {
        $family = $this->userFor(RoleTypes::FAMILIAR);
        $linked = Patient::factory()->create();
        $hidden = Patient::factory()->create();
        $shift = Shift::factory()->create(['patient_id' => $linked->id]);
        PatientLink::query()->create([
            'patient_id' => $linked->id,
            'user_id' => $family->id,
            'link_type' => PatientLink::FAMILY,
        ]);

        $this->actingAs($family, 'sanctum')
            ->getJson('/api/patients/'.$linked->id)
            ->assertOk()
            ->assertJsonPath('data.id', $linked->id);

        $this->actingAs($family, 'sanctum')
            ->getJson('/api/patients/'.$hidden->id)
            ->assertForbidden();

        $this->actingAs($family, 'sanctum')
            ->getJson('/api/shifts/'.$shift->id)
            ->assertOk();

        $this->actingAs($family, 'sanctum')
            ->postJson('/api/shifts/'.$shift->id.'/check-in')
            ->assertForbidden();
    }

    public function test_task_records_keep_history_and_logs_stay_with_their_author(): void
    {
        $caregiver = $this->userFor(RoleTypes::CUIDADOR);
        $other = $this->userFor(RoleTypes::CUIDADOR);
        $shift = Shift::factory()->create(['caregiver_id' => $caregiver->id]);
        $task = CareTask::query()->create([
            'patient_id' => $shift->patient_id,
            'title' => 'Medicación de la mañana',
            'instructions' => 'Después del desayuno.',
        ]);

        $this->actingAs($caregiver, 'sanctum')
            ->postJson('/api/shifts/'.$shift->id.'/tasks/'.$task->id.'/records', [
                'status' => 'done',
                'observation' => 'La tomó sin problema.',
            ])->assertCreated();

        $this->actingAs($caregiver, 'sanctum')
            ->postJson('/api/shifts/'.$shift->id.'/tasks/'.$task->id.'/records', [
                'status' => 'skipped',
                'observation' => 'Se negó a la segunda toma.',
            ])->assertCreated();

        $this->actingAs($caregiver, 'sanctum')
            ->getJson('/api/shifts/'.$shift->id.'/tasks')
            ->assertOk()
            ->assertJsonPath('data.0.status', 'skipped')
            ->assertJsonCount(2, 'data.0.records');

        $this->assertDatabaseCount('care_task_records', 2);

        $created = $this->actingAs($caregiver, 'sanctum')
            ->postJson('/api/shifts/'.$shift->id.'/logs', [
                'kind' => CareLog::KIND_ACTIVITY,
                'body' => 'Caminata corta en el pasillo.',
            ])->assertCreated();

        $logId = $created->json('data.id');

        $this->actingAs($other, 'sanctum')
            ->putJson('/api/care-logs/'.$logId, ['body' => 'Texto ajeno'])
            ->assertForbidden();

        $this->actingAs($caregiver, 'sanctum')
            ->putJson('/api/care-logs/'.$logId, ['body' => 'Caminata corta con ayuda.'])
            ->assertOk()
            ->assertJsonPath('data.body', 'Caminata corta con ayuda.')
            ->assertJsonPath('data.author.id', $caregiver->id);
    }

    public function test_health_records_reject_values_outside_the_capture_range(): void
    {
        $caregiver = $this->userFor(RoleTypes::CUIDADOR);
        $shift = Shift::factory()->create(['caregiver_id' => $caregiver->id]);

        $this->actingAs($caregiver, 'sanctum')
            ->postJson('/api/shifts/'.$shift->id.'/health-records', [])
            ->assertUnprocessable();

        $this->actingAs($caregiver, 'sanctum')
            ->postJson('/api/shifts/'.$shift->id.'/health-records', [
                'temperature' => 60,
            ])->assertUnprocessable();

        $this->actingAs($caregiver, 'sanctum')
            ->postJson('/api/shifts/'.$shift->id.'/health-records', [
                'temperature' => 36.6,
                'symptoms' => 'Tos leve.',
                'progress_notes' => 'Desayunó la mitad.',
            ])->assertCreated()
            ->assertJsonPath('data.temperature', 36.6)
            ->assertJsonPath('data.author.id', $caregiver->id)
            ->assertJsonMissingPath('data.diagnosis');
    }

    public function test_an_incident_notifies_linked_family_and_keeps_status_history(): void
    {
        Mail::fake();

        $caregiver = $this->userFor(RoleTypes::CUIDADOR);
        $family = $this->userFor(RoleTypes::FAMILIAR);
        $stranger = $this->userFor(RoleTypes::FAMILIAR);
        $supervisor = $this->userFor(RoleTypes::SUPERVISOR);
        $shift = Shift::factory()->create(['caregiver_id' => $caregiver->id]);

        PatientLink::query()->create([
            'patient_id' => $shift->patient_id,
            'user_id' => $family->id,
            'link_type' => PatientLink::FAMILY,
        ]);
        PatientLink::query()->create([
            'patient_id' => $shift->patient_id,
            'user_id' => $supervisor->id,
            'link_type' => PatientLink::SUPERVISOR,
        ]);

        $response = $this->actingAs($caregiver, 'sanctum')
            ->postJson('/api/shifts/'.$shift->id.'/incidents', [
                'level' => 'high',
                'description' => 'Se cayó al levantarse de la silla.',
            ])->assertCreated()
            ->assertJsonPath('data.status', 'reported');

        Mail::assertSent(IncidentReportedMail::class, 2);
        $this->assertDatabaseCount('incident_events', 1);

        $incidentId = $response->json('data.id');

        $this->actingAs($family, 'sanctum')
            ->getJson('/api/incidents/'.$incidentId)
            ->assertOk()
            ->assertJsonPath('data.description', 'Se cayó al levantarse de la silla.');

        $this->actingAs($stranger, 'sanctum')
            ->getJson('/api/incidents/'.$incidentId)
            ->assertForbidden();

        $this->actingAs($supervisor, 'sanctum')
            ->postJson('/api/incidents/'.$incidentId.'/events', [
                'status' => 'in_attention',
                'note' => 'Voy en camino.',
            ])->assertOk()
            ->assertJsonPath('data.status', 'in_attention')
            ->assertJsonCount(2, 'data.events');
    }

    public function test_geofence_is_off_until_it_is_configured(): void
    {
        $caregiver = $this->userFor(RoleTypes::CUIDADOR);
        $shift = Shift::factory()->create([
            'caregiver_id' => $caregiver->id,
            'latitude' => 19.432608,
            'longitude' => -99.133209,
        ]);

        config(['care.geofence_meters' => null]);

        $this->actingAs($caregiver, 'sanctum')
            ->postJson('/api/shifts/'.$shift->id.'/locations', [
                'latitude' => 19.5,
                'longitude' => -99.2,
            ])->assertCreated();

        config(['care.geofence_meters' => 100]);

        $this->actingAs($caregiver, 'sanctum')
            ->postJson('/api/shifts/'.$shift->id.'/locations', [
                'latitude' => 19.5,
                'longitude' => -99.2,
            ])->assertUnprocessable();
    }

    public function test_reports_are_scoped_filtered_and_paginated(): void
    {
        $family = $this->userFor(RoleTypes::FAMILIAR);
        $patient = Patient::factory()->create();
        $other = Patient::factory()->create();
        PatientLink::query()->create([
            'patient_id' => $patient->id,
            'user_id' => $family->id,
            'link_type' => PatientLink::FAMILY,
        ]);

        Shift::factory()->create([
            'patient_id' => $patient->id,
            'starts_at' => now()->subDay(),
            'ends_at' => now()->subDay()->addHours(8),
        ]);
        $visible = Shift::factory()->create([
            'patient_id' => $patient->id,
            'starts_at' => now()->addHour(),
            'ends_at' => now()->addHours(9),
        ]);
        Shift::factory()->create(['patient_id' => $other->id]);

        $this->actingAs($family, 'sanctum')
            ->getJson('/api/reports?patient_id='.$other->id)
            ->assertForbidden();

        $this->actingAs($family, 'sanctum')
            ->getJson('/api/reports?patient_id='.$patient->id.'&limit=1&offset=0')
            ->assertOk()
            ->assertJsonPath('meta.total', 2)
            ->assertJsonCount(1, 'data')
            ->assertJsonStructure([
                'data' => [[
                    'shift',
                    'attendance',
                    'tasks',
                    'logs',
                    'health_records',
                    'incidents',
                ]],
            ]);

        $this->actingAs($family, 'sanctum')
            ->getJson('/api/reports?shift_id='.$visible->id)
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.shift.id', $visible->id);
    }

    private function userFor(string $role): User
    {
        $permissions = app(PermissionHandler::class)->permissionsForRole($role);

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        $user = User::factory()->create();
        $user->assignRole(Role::findOrCreate($role, 'web'));
        $user->givePermissionTo($permissions);

        return $user;
    }
}
