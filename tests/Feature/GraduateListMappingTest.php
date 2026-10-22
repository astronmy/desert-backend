<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Tests\TestCase;

class GraduateListMappingTest extends TestCase
{
    use RefreshDatabase;

    public function test_an_admin_can_open_the_tools_and_map_a_graduate_list(): void
    {
        $this->seed(RolePermissionSeeder::class);
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('admin.tools.index'))
            ->assertOk()
            ->assertSee('Herramientas')
            ->assertSee('Mapeo de Listas');

        $this->actingAs($admin)
            ->get(route('admin.tools.list-mapping'))
            ->assertOk()
            ->assertSee('Nombre Egresado')
            ->assertSee('Observaciones');

        $response = $this->actingAs($admin)
            ->post(route('admin.tools.list-mapping.store'), [
                'file' => $this->spreadsheet([
                    ['Egresado', 'Invitado', 'Tipo', 'Menú', 'Mesa', 'Total invitados'],
                    ['Allub Tomás', 'Vanesa Zicato Allub', 'Adulto', 'Regular', 3, 4],
                    ['Allub Tomás', 'Sofia Allub Allub', 'Adulto', 'Regular', 3, 4],
                    ['Allub Tomás', 'Luciana Belen Allub Zicato', 'Adulto', 'Regular', 36, 4],
                    ['Allub Tomás', 'Claudio Allub Allub', 'Adulto', 'Regular', 3, 4],
                    ['Ballarini Thiago', 'Adulto Uno', 'Adulto', 'Regular', 31, 3],
                    ['Ballarini Thiago', 'Adulto Dos', 'Adulto', 'Regular', 31, 3],
                    ['Ballarini Thiago', 'Santino Ballarini', 'Menor', 'Regular', 31, 3],
                ]),
            ]);

        $response->assertOk();
        $response->assertDownload('mapeo-listas.xlsx');

        $rows = Excel::toCollection(null, $response->baseResponse->getFile()->getPathname())->first();

        $this->assertSame(['Nombre Egresado', 'Cantidad', 'Mesa', 'Observaciones'], $rows[0]->take(4)->all());
        $this->assertSame(['Allub Tomás', 3, 3, null], $this->row($rows[1]));
        $this->assertSame(['Allub Tomás', 1, 36, null], $this->row($rows[2]));
        $this->assertSame(['Ballarini Thiago', 3, 31, '1 menor'], $this->row($rows[3]));
    }

    public function test_a_client_cannot_open_the_tools(): void
    {
        $this->seed(RolePermissionSeeder::class);
        $client = User::factory()->client()->create();

        $this->actingAs($client)
            ->get(route('admin.tools.index'))
            ->assertForbidden();
    }

    /**
     * @param  list<list<string|int>>  $rows
     */
    private function spreadsheet(array $rows): UploadedFile
    {
        $spreadsheet = new Spreadsheet;
        $spreadsheet->getActiveSheet()->fromArray($rows);

        $path = tempnam(sys_get_temp_dir(), 'lista').'.xlsx';
        (new Xlsx($spreadsheet))->save($path);

        return new UploadedFile($path, 'lista.xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', null, true);
    }

    /**
     * @return list<mixed>
     */
    private function row(mixed $row): array
    {
        $values = collect($row)->take(4)->values()->all();
        $values[3] = $values[3] === '' ? null : $values[3];

        return $values;
    }
}
