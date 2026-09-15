<?php

return [
    'attributes' => [
        'invitation_code' => 'Código',
        'guest' => 'Invitado',
        'document_number' => 'Documento',
        'accessed_at' => 'Primer acceso',
        'entrada_at' => 'Entrada',
        'salon_at' => 'Salón',
        'is_complete' => 'Completo',
        'completion' => 'Estado',
        'date_from' => 'Desde',
        'date_to' => 'Hasta',
    ],

    'checkpoints' => [
        'entrada' => 'Entrada',
        'salon' => 'Salón',
    ],

    'completion' => [
        'partial' => 'Parcial',
        'complete' => 'Completo',
        'all' => 'Todos',
    ],

    'push' => [
        'welcome_title' => 'Bienvenido',
        'welcome_message' => 'Bienvenido a :event',
    ],

    'index' => [
        'title' => 'Accesos',
        'subtitle' => 'Evento: :name',
        'search_name_placeholder' => 'Buscar por nombre',
        'search_document_placeholder' => 'Buscar por documento',
        'search_code_placeholder' => 'Buscar por código',
        'empty' => 'Todavía no hay accesos registrados para este evento.',
        'yes' => 'Sí',
        'no' => 'No',
        'pending' => '—',
    ],
];
