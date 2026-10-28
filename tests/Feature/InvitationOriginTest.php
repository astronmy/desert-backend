<?php

namespace Tests\Feature;

use App\Enums\InvitationOrigin;
use App\Models\Event;
use App\Models\Invitation;
use App\Models\User;
use App\Services\Invitations\ImportEventInvitationsService;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Tests\TestCase;

class InvitationOriginTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_self_register_sets_origin_api(): void
    {
        Storage::fake('public');
        Http::fake();

        $event = Event::factory()->create(['end_date' => '2099-01-01']);

        $this->post('/api/events/'.$event->id.'/register', [
            'first_name' => 'Ana',
            'last_name' => 'Perez',
            'document_number' => '30111222',
            'id_type' => 'dni',
            'selfie' => UploadedFile::fake()->image('selfie.jpg'),
        ], [
            'Accept' => 'application/json',
        ])->assertCreated();

        $invitation = Invitation::query()->where('event_id', $event->id)->first();

        $this->assertNotNull($invitation);
        $this->assertSame(InvitationOrigin::Api, $invitation->origin);
    }

    public function test_manual_store_sets_origin_manual(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $event = Event::factory()->create();
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->post(route('admin.events.invitations.store', $event), [
                'first_name' => 'Luis',
                'last_name' => 'Gomez',
                'document_number' => '40111222',
                'id_type' => 'dni',
            ])
            ->assertRedirect(route('admin.events.invitations.index', $event));

        $invitation = Invitation::query()->where('event_id', $event->id)->first();

        $this->assertNotNull($invitation);
        $this->assertSame(InvitationOrigin::Manual, $invitation->origin);
    }

    public function test_import_sets_origin_import(): void
    {
        $event = Event::factory()->create();

        $summary = app(ImportEventInvitationsService::class)->import($event, $this->spreadsheet([
            ['Nombre', 'Apellido', 'DNI'],
            ['Maria', 'Lopez', '50111222'],
        ]));

        $this->assertSame(1, $summary['created']);

        $invitation = Invitation::query()->where('event_id', $event->id)->first();

        $this->assertNotNull($invitation);
        $this->assertSame(InvitationOrigin::Import, $invitation->origin);
    }

    public function test_admin_index_filters_by_origin(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $event = Event::factory()->create();
        $admin = User::factory()->admin()->create();

        Invitation::factory()->create([
            'event_id' => $event->id,
            'origin' => InvitationOrigin::Api,
            'code' => 'APIORIGIN',
        ]);
        Invitation::factory()->create([
            'event_id' => $event->id,
            'origin' => InvitationOrigin::Manual,
            'code' => 'MANORIGIN',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.events.invitations.index', ['event' => $event, 'origin' => 'manual']))
            ->assertOk()
            ->assertSee('MANORIGIN')
            ->assertDontSee('APIORIGIN');
    }

    /**
     * @param  list<list<string|int>>  $rows
     */
    private function spreadsheet(array $rows): UploadedFile
    {
        $spreadsheet = new Spreadsheet;
        $spreadsheet->getActiveSheet()->fromArray($rows);

        $path = tempnam(sys_get_temp_dir(), 'inv').'.xlsx';
        (new Xlsx($spreadsheet))->save($path);

        return new UploadedFile($path, 'invitaciones.xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', null, true);
    }
}
