<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolePermissionSeeder extends Seeder
{
    /**
     * @var list<array{module: string, action: string, label: string}>
     */
    private array $catalog = [
        ['module' => 'dashboard', 'action' => 'ver', 'label' => 'Ver dashboard'],
        ['module' => 'usuarios', 'action' => 'ver', 'label' => 'Ver usuarios'],
        ['module' => 'usuarios', 'action' => 'crear', 'label' => 'Crear usuarios'],
        ['module' => 'usuarios', 'action' => 'editar', 'label' => 'Editar usuarios'],
        ['module' => 'usuarios', 'action' => 'eliminar', 'label' => 'Eliminar usuarios'],
        ['module' => 'roles', 'action' => 'ver', 'label' => 'Ver roles'],
        ['module' => 'roles', 'action' => 'crear', 'label' => 'Crear roles'],
        ['module' => 'roles', 'action' => 'editar', 'label' => 'Editar roles'],
        ['module' => 'roles', 'action' => 'eliminar', 'label' => 'Eliminar roles'],
        ['module' => 'eventos', 'action' => 'ver', 'label' => 'Ver eventos'],
        ['module' => 'eventos', 'action' => 'crear', 'label' => 'Crear eventos'],
        ['module' => 'eventos', 'action' => 'editar', 'label' => 'Editar eventos'],
        ['module' => 'eventos', 'action' => 'eliminar', 'label' => 'Eliminar eventos'],
        ['module' => 'invitaciones', 'action' => 'ver', 'label' => 'Ver invitaciones'],
        ['module' => 'invitaciones', 'action' => 'crear', 'label' => 'Crear invitaciones'],
        ['module' => 'invitaciones', 'action' => 'editar', 'label' => 'Editar invitaciones'],
        ['module' => 'invitaciones', 'action' => 'eliminar', 'label' => 'Eliminar invitaciones'],
        ['module' => 'invitaciones', 'action' => 'exportar', 'label' => 'Exportar invitaciones'],
        ['module' => 'invitaciones', 'action' => 'importar', 'label' => 'Importar invitaciones'],
        ['module' => 'invitaciones', 'action' => 'moderar', 'label' => 'Moderar invitaciones'],
        ['module' => 'accesos', 'action' => 'ver', 'label' => 'Ver accesos'],
        ['module' => 'deeplink', 'action' => 'ver', 'label' => 'Ver link de registro / métricas'],
        ['module' => 'deeplink', 'action' => 'generar', 'label' => 'Generar link de registro'],
    ];

    public function run(): void
    {
        DB::transaction(function () {
            $permissionIds = [];

            foreach ($this->catalog as $item) {
                $slug = $item['module'].'.'.$item['action'];
                $permission = Permission::query()->updateOrCreate(
                    ['slug' => $slug],
                    [
                        'module' => $item['module'],
                        'action' => $item['action'],
                        'label' => $item['label'],
                    ]
                );
                $permissionIds[$slug] = $permission->id;
            }

            $admin = Role::query()->updateOrCreate(
                ['slug' => Role::SLUG_ADMIN],
                [
                    'name' => 'Administrador',
                    'requires_event' => false,
                    'is_system' => true,
                    'is_active' => true,
                ]
            );

            $client = Role::query()->updateOrCreate(
                ['slug' => Role::SLUG_CLIENT],
                [
                    'name' => 'Cliente',
                    'requires_event' => true,
                    'is_system' => true,
                    'is_active' => true,
                ]
            );

            $admin->permissions()->sync(array_values($permissionIds));

            $clientSlugs = [
                'dashboard.ver',
                'invitaciones.ver',
                'invitaciones.crear',
                'invitaciones.exportar',
                'invitaciones.moderar',
                'deeplink.generar',
            ];
            $client->permissions()->sync(
                collect($clientSlugs)->map(fn (string $slug) => $permissionIds[$slug])->all()
            );

            User::query()
                ->whereNull('role_id')
                ->update(['role_id' => $admin->id, 'event_id' => null]);
        });
    }
}
