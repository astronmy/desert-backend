<?php

return [
    'title' => 'Herramientas',
    'subtitle' => 'Utilidades para preparar la información de los eventos.',

    'list_mapping' => [
        'title' => 'Mapeo de Listas',
        'card' => 'Agrupá el listado de egresados por mesa y marcá cuántos menores hay en cada una.',
        'help' => 'Subí el Excel con las columnas Egresado, Invitado, Tipo, Menú, Mesa y Total invitados. El archivo nuevo agrupa a cada egresado por mesa.',
        'columns_in' => 'El archivo que subís',
        'columns_out' => 'El archivo que se descarga',
        'input_columns' => ['Egresado', 'Invitado', 'Tipo', 'Menú', 'Mesa', 'Total invitados'],
        'output_columns' => ['Nombre Egresado', 'Cantidad', 'Mesa', 'Observaciones'],
        'note_group' => 'Cantidad es la cantidad de invitados de ese egresado en esa mesa.',
        'note_minors' => 'Observaciones indica cuántos de esos invitados son menores. Si no hay, queda vacía.',
        'drop_title' => 'Arrastrá el Excel o hacé clic para elegirlo',
        'drop_subtitle' => 'Se descarga el listado agrupado.',
        'formats' => 'Formatos: .xlsx, .xls',
        'selected' => 'Archivo seleccionado',
        'change_file' => 'Cambiar archivo',
        'submit' => 'Generar Excel',
        'empty' => 'El archivo no tiene egresados para agrupar.',
    ],
];
