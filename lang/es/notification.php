<?php

return [
    'index' => [
        'title' => 'Notificaciones',
        'new' => 'Nueva notificación',
        'empty' => 'No hay notificaciones.',
        'all_events' => 'Todos los eventos',
    ],

    'form' => [
        'create_title' => 'Nueva notificación',
        'edit_title' => 'Editar notificación',
        'create_submit' => 'Crear y encolar',
        'select_event' => 'Elegí un evento',
        'send_at_help' => 'Se enviará en esta fecha (timezone de la app). Requiere el worker de cola.',
        'specific_help' => 'Solo se listan invitados que ya tienen uuid de notificación (confirmaron o se registraron desde la app).',
        'no_notifiable' => 'Este evento no tiene invitados con uuid de notificación.',
        'loading_invitations' => 'Cargando invitados…',
        'load_error' => 'No se pudieron cargar los invitados.',
    ],

    'show' => [
        'title' => 'Detalle de notificación',
        'recipients' => 'Destinatarios',
        'no_recipients' => 'Alcance general: se enviará a todos los invitados del evento con uuid de notificación al momento del envío.',
    ],

    'attributes' => [
        'event' => 'Evento',
        'title' => 'Título',
        'message' => 'Mensaje',
        'type' => 'Tipo',
        'scope' => 'Alcance',
        'status' => 'Estado',
        'send_at' => 'Enviar el',
        'sent_at' => 'Enviada el',
        'external_id' => 'ID OneSignal',
        'invitation_ids' => 'Invitados',
    ],

    'types' => [
        'instant' => 'Inmediata',
        'scheduled' => 'Programada',
    ],

    'scopes' => [
        'general' => 'Todo el evento',
        'specific' => 'Invitados puntuales',
    ],

    'statuses' => [
        'pending' => 'Pendiente',
        'sent' => 'Enviada',
        'failed' => 'Fallida',
        'cancelled' => 'Cancelada',
    ],

    'messages' => [
        'created' => 'Notificación creada. El envío se procesa en cola.',
        'updated' => 'Notificación actualizada.',
        'deleted' => 'Notificación cancelada y eliminada.',
        'only_pending' => 'Solo se puede editar una notificación pendiente.',
    ],
];
