<?php

return [
    'attributes' => [
        'name' => 'Nombre',
        'init_date' => 'Fecha inicio',
        'end_date' => 'Fecha fin',
        'type' => 'Tipo',
        'date_from' => 'Desde',
        'date_to' => 'Hasta',
        'description' => 'Descripción general',
        'short_description' => 'Descripción breve',
        'host' => 'Anfitrión',
        'image' => 'Imagen principal',
        'mobile_image' => 'Imagen mobile',
        'gallery' => 'Galería',
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
        'media_section' => 'Imágenes',
        'short_description_hint' => 'Máximo 500 caracteres. Ideal para listados y cards.',
        'image_hint' => 'Recomendado: 1920 × 1080 px (16:9). Máx. 5 MB.',
        'mobile_image_hint' => 'Recomendado: 1080 × 1350 px (4:5). Máx. 5 MB.',
        'gallery_hint' => 'Hasta 12 fotos. Recomendado: 1600 × 1200 px. Máx. 5 MB c/u.',
        'remove_image' => 'Eliminar imagen principal',
        'remove_mobile_image' => 'Eliminar imagen mobile',
        'delete_gallery_item' => 'Eliminar',
    ],

    'messages' => [
        'created' => 'Evento creado correctamente.',
        'updated' => 'Evento actualizado correctamente.',
        'deleted' => 'Evento eliminado.',
    ],
];
