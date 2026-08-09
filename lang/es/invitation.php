<?php

return [
    'attributes' => [
        'code' => 'Código',
        'status' => 'Estado',
        'confirmed_at' => 'Confirmada',
        'selfie' => 'Selfie',
    ],

    'statuses' => [
        'pending' => 'Pendiente',
        'confirmed' => 'Confirmada',
        'cancelled' => 'Cancelada',
    ],

    'index' => [
        'title' => 'Invitaciones',
        'subtitle' => 'Evento: :name',
        'new' => 'Nueva invitación',
        'import' => 'Importar Excel',
        'search_name_placeholder' => 'Buscar por nombre',
        'search_document_placeholder' => 'Buscar por documento',
        'search_code_placeholder' => 'Buscar por código',
        'all_statuses' => 'Todos los estados',
        'empty' => 'No hay invitaciones para este evento.',
    ],

    'form' => [
        'create_title' => 'Nueva invitación',
        'edit_title' => 'Editar invitación',
        'create_submit' => 'Crear invitación',
        'select_status' => 'Seleccionar estado',
        'select_id_type' => 'Seleccionar tipo',
    ],

    'import' => [
        'title' => 'Importar invitaciones',
        'help' => 'Subí un archivo .xlsx o .csv con las columnas requeridas.',
        'columns_title' => 'Columnas requeridas',
        'drop_title' => 'Arrastrá el archivo acá',
        'drop_subtitle' => 'o hacé clic para seleccionarlo',
        'formats' => 'Formatos: .xlsx, .xls, .csv',
        'selected' => 'Archivo seleccionado',
        'change_file' => 'Cambiar archivo',
        'download_template' => 'Descargar plantilla CSV',
        'file' => 'Archivo',
        'submit' => 'Importar invitaciones',
        'summary' => 'Importación finalizada: :created creadas, :reused guests reutilizados, :skipped omitidas, :errors errores.',
        'notes_title' => 'Cómo funciona',
        'note_create' => 'Si el DNI no existe, se crea el invitado y su invitación.',
        'note_reuse' => 'Si el DNI ya existe, se reutiliza el invitado y se crea la invitación si falta.',
        'note_skip' => 'Si ya tiene invitación en este evento, la fila se omite.',
    ],

    'messages' => [
        'created' => 'Invitación creada correctamente.',
        'updated' => 'Invitación actualizada correctamente.',
        'deleted' => 'Invitación eliminada.',
        'cancelled' => 'Invitación cancelada.',
        'already_exists' => 'Este invitado ya tiene una invitación para este evento.',
    ],
];
