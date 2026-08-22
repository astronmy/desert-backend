<?php

return [
    'attributes' => [
        'name' => 'Nombre',
        'email' => 'Email',
        'password' => 'Contraseña',
        'password_confirmation' => 'Confirmar contraseña',
        'new_password' => 'Nueva contraseña',
        'role_id' => 'Rol',
        'event_id' => 'Evento asociado',
    ],

    'index' => [
        'title' => 'Usuarios',
        'new' => 'Nuevo usuario',
        'search_name_placeholder' => 'Buscar por nombre',
        'search_email_placeholder' => 'Buscar por email',
        'empty' => 'No hay usuarios.',
        'role' => 'Rol',
        'event' => 'Evento',
    ],

    'form' => [
        'create_title' => 'Nuevo usuario',
        'edit_title' => 'Editar: :name',
        'password_help' => 'Mínimo 8 caracteres.',
        'new_password_help' => 'Dejar en blanco para no cambiar. Mínimo 8 caracteres si la cambiás.',
        'create_submit' => 'Crear usuario',
        'select_role' => 'Elegí un rol',
        'select_event' => 'Elegí un evento',
        'role_help' => 'El rol define qué puede ver y hacer el usuario en el panel.',
        'event_help' => 'Obligatorio para roles que requieren evento (p. ej. Cliente).',
        'requires_event_badge' => 'Requiere evento',
        'full_access_hint' => 'Acceso completo al panel',
        'search_event_placeholder' => 'Buscar evento por nombre…',
        'no_events_found' => 'No hay eventos que coincidan.',
        'no_events' => 'No hay eventos cargados.',
        'clear_event' => 'Quitar selección',
    ],

    'validation' => [
        'event_required' => 'El rol seleccionado requiere un evento asociado.',
        'role_inactive' => 'El rol seleccionado está inactivo.',
    ],

    'messages' => [
        'created' => 'Usuario creado correctamente.',
        'updated' => 'Usuario actualizado correctamente.',
        'deleted' => 'Usuario eliminado.',
        'cannot_delete_self' => 'No podés eliminar tu propio usuario.',
    ],
];
