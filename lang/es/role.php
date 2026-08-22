<?php

return [
    'attributes' => [
        'name' => 'Nombre',
        'slug' => 'Slug',
        'requires_event' => 'Requiere evento',
        'is_system' => 'Rol de sistema',
        'is_active' => 'Activo',
        'permissions' => 'Permisos',
        'users_count' => 'Usuarios',
        'permissions_count' => 'Permisos',
    ],

    'index' => [
        'title' => 'Roles',
        'new' => 'Nuevo rol',
        'search_name_placeholder' => 'Buscar por nombre',
        'empty' => 'No hay roles.',
        'system' => 'Sistema',
        'inactive' => 'Inactivo',
        'requires_event_yes' => 'Sí',
        'requires_event_no' => 'No',
    ],

    'form' => [
        'create_title' => 'Nuevo rol',
        'edit_title' => 'Editar: :name',
        'permissions_help' => 'Seleccioná los permisos por módulo y acción.',
        'requires_event_help' => 'Si está activo, los usuarios con este rol deben tener un evento asociado.',
        'create_submit' => 'Crear rol',
        'system_locked' => 'Este es un rol de sistema: el slug no se puede cambiar.',
    ],

    'modules' => [
        'dashboard' => 'Dashboard',
        'usuarios' => 'Usuarios',
        'roles' => 'Roles',
        'eventos' => 'Eventos',
        'invitaciones' => 'Invitaciones',
        'accesos' => 'Accesos',
        'deeplink' => 'Link de registro',
    ],

    'messages' => [
        'created' => 'Rol creado correctamente.',
        'updated' => 'Rol actualizado correctamente.',
        'deleted' => 'Rol eliminado.',
        'deactivated_system' => 'Los roles de sistema no se eliminan; el rol quedó inactivo.',
        'deactivated_has_users' => 'El rol tiene usuarios asignados; quedó inactivo en lugar de eliminarse.',
        'forbidden' => 'No tenés permiso para realizar esta acción.',
        'inactive_role' => 'Tu rol está inactivo. Contactá al administrador.',
        'client_needs_event' => 'Tu usuario Cliente no tiene un evento asociado. Contactá al administrador.',
    ],
];
