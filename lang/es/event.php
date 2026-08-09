<?php

return [
    'attributes' => [
        'name' => 'Nombre',
        'init_date' => 'Fecha inicio',
        'end_date' => 'Fecha fin',
        'type' => 'Tipo',
        'date_from' => 'Desde',
        'date_to' => 'Hasta',
    ],

    'types' => [
        'wedding' => 'Casamiento',
        'birthday' => 'Cumpleaños',
        'graduation' => 'Graduación',
        'corporate' => 'Corporativo',
        'private' => 'Privado',
    ],

    'index' => [
        'title' => 'Eventos',
        'new' => 'Nuevo evento',
        'search_name_placeholder' => 'Buscar por nombre',
        'all_types' => 'Todos los tipos',
        'empty' => 'No hay eventos.',
    ],

    'form' => [
        'create_title' => 'Nuevo evento',
        'edit_title' => 'Editar: :name',
        'select_type' => 'Seleccionar tipo',
        'create_submit' => 'Crear evento',
    ],

    'messages' => [
        'created' => 'Evento creado correctamente.',
        'updated' => 'Evento actualizado correctamente.',
        'deleted' => 'Evento eliminado.',
    ],
];
