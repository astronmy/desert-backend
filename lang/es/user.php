<?php

return [
    'attributes' => [
        'name' => 'Nombre',
        'email' => 'Email',
        'password' => 'Contraseña',
        'password_confirmation' => 'Confirmar contraseña',
        'new_password' => 'Nueva contraseña',
    ],

    'index' => [
        'title' => 'Usuarios',
        'new' => 'Nuevo usuario',
        'search_name_placeholder' => 'Buscar por nombre',
        'search_email_placeholder' => 'Buscar por email',
        'empty' => 'No hay usuarios.',
    ],

    'form' => [
        'create_title' => 'Nuevo usuario',
        'edit_title' => 'Editar: :name',
        'password_help' => 'Mínimo 8 caracteres.',
        'new_password_help' => 'Dejar en blanco para no cambiar. Mínimo 8 caracteres si la cambiás.',
        'create_submit' => 'Crear usuario',
    ],

    'messages' => [
        'created' => 'Usuario creado correctamente.',
        'updated' => 'Usuario actualizado correctamente.',
        'deleted' => 'Usuario eliminado.',
        'cannot_delete_self' => 'No podés eliminar tu propio usuario.',
    ],
];
