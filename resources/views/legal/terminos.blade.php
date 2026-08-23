@extends('layouts.legal')

@section('title', 'Términos y Condiciones')
@section('meta_description', 'Términos y Condiciones de uso de la aplicación móvil Desert Eventos.')

@section('content')
    <h1>Términos y Condiciones de uso</h1>

    <p class="meta"><strong>Desert Eventos</strong></p>
    <p class="meta">Luján de Cuyo, Mendoza, República Argentina</p>
    <p class="meta">Correo: <a href="mailto:info@deserteventos.com.ar">info@deserteventos.com.ar</a></p>

    <p class="vigencia">Vigencia: 22 de agosto de 2026.</p>

    <p class="lead">
        Estos Términos regulan el uso de la aplicación móvil <strong>Desert Eventos</strong> (la “App”),
        disponible en Google Play y en App Store. Al descargar, instalar o usar la App, aceptás estos Términos.
        Si no estás de acuerdo, no la uses.
    </p>

    <hr>

    <h2>1. Quiénes somos</h2>
    <p>
        La App es operada por <strong>Desert Eventos</strong>, predio de eventos ubicado en Luján de Cuyo, Mendoza
        (el “Organizador”, “nosotros”).
    </p>
    <p>
        Contacto: <a href="mailto:info@deserteventos.com.ar">info@deserteventos.com.ar</a><br>
        WhatsApp: +54 9 261 708-3142
    </p>

    <h2>2. Qué es la App</h2>
    <p>La App es un servicio institucional y de invitaciones digitales. Permite, entre otras cosas:</p>
    <ul>
        <li>Consultar información del predio (ubicación, galería, contacto);</li>
        <li>Ver la agenda pública de eventos;</li>
        <li>Confirmar o registrarse en una invitación con código o enlace;</li>
        <li>Guardar en el teléfono el código QR de acceso al evento;</li>
    </ul>
    <p>
        No es una red social, no ofrece pagos dentro de la App y no vende entradas al público general desde la App.
    </p>

    <h2>3. Elegibilidad</h2>
    <p>
        La App está dirigida a personas de <strong>18 años o más</strong>. No está pensada para menores ni se dirige a niños.
    </p>
    <p>
        Si confirmás una invitación o te registrás a un evento, declarás que los datos (nombre, documento y foto)
        son tuyos o que tenés autorización de la persona titular.
    </p>

    <h2>4. Cómo se accede</h2>
    <p>No hay cuenta de usuario con usuario y contraseña.</p>
    <p>El acceso a una invitación se hace:</p>
    <ul>
        <li>con un <strong>código</strong> que te envió el organizador; o</li>
        <li>
            con un <strong>enlace de activación</strong>
            (<code>https://desert.rxstudio.dev/activar</code> o el esquema <code>deserteventos://activar</code>).
            Ese enlace puede ser de un solo uso y está ligado al dispositivo.
        </li>
    </ul>
    <p>
        El uso del enlace o del código no te convierte en titular de un derecho patrimonial sobre el evento:
        es un medio técnico para identificarte en la entrada.
    </p>

    <h2>5. Obligaciones del usuario</h2>
    <p>Te comprometés a:</p>
    <ul>
        <li>proporcionar datos <strong>veraces</strong> (nombre, apellido, tipo y número de documento, selfie);</li>
        <li>no usar un documento o una foto de otra persona;</li>
        <li>no compartir, reenviar, vender ni ceder tu QR ni el código de invitación;</li>
        <li>no intentar eludir el control de acceso ni alterar la App;</li>
        <li>usar la App solo para los fines previstos (asistir al evento o consultar información del predio).</li>
    </ul>
    <p>
        Podemos rechazar, cancelar o marcar como usada una invitación si hay indicios de fraude, datos falsos,
        uso de un enlace ya canjeado en otro dispositivo, o incumplimiento de estos Términos.
    </p>

    <h2>6. Invitación y código QR</h2>
    <p>El QR que se guarda en el teléfono es un <strong>comprobante de acceso</strong> al evento indicado, sujeto a:</p>
    <ul>
        <li>que la invitación esté <strong>confirmada / aprobada</strong> por el organizador;</li>
        <li>
            las condiciones del evento (fecha, horario, cupo, vestimenta, menores, etc.) que comunique Desert Eventos
            por fuera de la App;
        </li>
        <li>el control en puerta, que puede incluir cotejo de documento y de la selfie.</li>
    </ul>
    <p>
        El QR <strong>no es</strong> un título de propiedad, un contrato de locación ni una garantía de que el evento
        se realizará en las condiciones publicitadas. El organizador puede modificar, reprogramar o cancelar un evento
        según su propia comunicación a los invitados.
    </p>
    <p>
        Si desinstalás la App, borrás los datos del teléfono o cambiás de dispositivo, es posible que pierdas el QR local.
        En ese caso contactanos a
        <a href="mailto:info@deserteventos.com.ar">info@deserteventos.com.ar</a>.
    </p>

    <h2>7. Agenda de eventos</h2>
    <p>
        El listado de eventos se obtiene de nuestros servidores. Puede no estar completo, puede atrasarse o no estar
        disponible. La publicación de un evento en la App no implica por sí sola una invitación personal.
    </p>

    <h2>8. Permisos del dispositivo</h2>
    <p>La App puede pedir:</p>
    <ul>
        <li>
            <strong>Cámara</strong>, para la selfie de la invitación y, en el flujo de staff, para leer el QR en puerta;
        </li>
        <li>
            <strong>Internet</strong>, para validar códigos, enviar el registro, listar eventos y canjear enlaces.
        </li>
    </ul>
    <p>
        No usamos el GPS. La pantalla “Ubicación” muestra cómo llegar al predio; no rastrea tu posición.
    </p>
    <p>
        En iOS y Android el permiso de cámara se pide en el momento de usarla. Podés denegarlo: en ese caso no podrás
        completar el registro que exige foto ni escanear QR con la cámara.
    </p>

    <h2>9. Disponibilidad y cambios</h2>
    <p>
        La App se ofrece “tal cual”. Puede haber cortes, errores, versiones incompletas o funciones que todavía no
        están publicadas (por ejemplo, un listado de eventos si el servidor no responde).
    </p>
    <p>
        Podemos actualizar la App, estos Términos o discontinuar funciones. Si el cambio es sustancial, actualizaremos
        la fecha de vigencia y, cuando sea razonable, lo comunicaremos en la ficha de la tienda o en la App.
    </p>

    <h2>10. Propiedad intelectual</h2>
    <p>
        La marca Desert Eventos, el diseño, los textos institucionales, las fotos del predio y el software de la App
        son de Desert Eventos o de sus licenciantes. No se concede ninguna licencia más allá de la necesaria para usar
        la App de forma personal y no comercial.
    </p>

    <h2>11. Limitación de responsabilidad</h2>
    <p>En la máxima medida permitida por la ley argentina:</p>
    <ul>
        <li>
            no respondemos por daños indirectos, lucro cesante o pérdida de chance derivados del uso o la imposibilidad
            de uso de la App (falta de red, QR no visible, tienda que rechaza la instalación, etc.);
        </li>
        <li>
            la relación contractual del evento (catering, horario, seguridad, terceros proveedores) se rige por lo
            acordado con Desert Eventos <strong>fuera</strong> de estos Términos;
        </li>
        <li>
            Apple y Google no son parte de estos Términos y no tienen obligación de prestar soporte sobre el contenido
            del evento.
        </li>
    </ul>
    <p>
        Nada de lo anterior limita derechos de consumidores que no puedan renunciarse
        (Ley 24.240 y normas complementarias).
    </p>

    <h2>12. App Store y Google Play</h2>
    <p>
        Si descargás la App desde <strong>App Store</strong>, además aplican las condiciones de Apple. Apple no es
        responsable del contenido ni del soporte de la App, salvo lo que impongan sus propias reglas.
    </p>
    <p>
        Si descargás desde <strong>Google Play</strong>, aplican las condiciones de Google. El uso de la App también
        está sujeto a las políticas vigentes de cada tienda.
    </p>

    <h2>13. Privacidad</h2>
    <p>
        El tratamiento de datos personales se describe en la
        <a href="{{ route('legal.privacidad') }}">Política de Privacidad</a>.
        Al usar la App también aceptás esa política.
    </p>

    <h2>14. Ley aplicable y jurisdicción</h2>
    <p>
        Estos Términos se rigen por las leyes de la <strong>República Argentina</strong>. Para cualquier controversia,
        serán competentes los tribunales ordinarios de <strong>Mendoza</strong>, sin perjuicio de los fueros que
        correspondan al consumidor.
    </p>

    <h2>15. Contacto</h2>
    <p>
        Consultas sobre estos Términos:
        <a href="mailto:info@deserteventos.com.ar">info@deserteventos.com.ar</a>.
    </p>
@endsection
