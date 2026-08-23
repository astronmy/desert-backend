@extends('layouts.legal')

@section('title', 'Política de Privacidad')
@section('meta_description', 'Política de Privacidad de la aplicación móvil Desert Eventos.')

@section('content')
    <h1>Política de Privacidad</h1>

    <p class="meta"><strong>Responsable:</strong> Desert Eventos</p>
    <p class="meta">Luján de Cuyo, Mendoza, República Argentina</p>
    <p class="meta">Correo: <a href="mailto:info@deserteventos.com.ar">info@deserteventos.com.ar</a></p>

    <p class="vigencia">Vigencia: 22 de agosto de 2026.</p>

    <p class="lead">
        Esta política describe qué datos personales trata la aplicación móvil <strong>Desert Eventos</strong>
        (la “App”), para qué, con quién se comparten y cómo ejercer tus derechos. Rige en conjunto con los
        <a href="{{ route('legal.terminos') }}">Términos y Condiciones</a>.
    </p>
    <p class="lead">
        El tratamiento se sujeta a la <strong>Ley 25.326</strong> de Protección de Datos Personales (Argentina) y a las
        exigencias de privacidad de <strong>Apple App Store</strong> y <strong>Google Play</strong>.
    </p>

    <hr>

    <h2>1. Resumen (lo que piden las tiendas)</h2>
    <div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th>Dato</th>
                <th>¿Se recolecta?</th>
                <th>¿Se vincula a tu identidad?</th>
                <th>¿Se usa para tracking publicitario?</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Nombre y apellido</td>
                <td>Sí (si confirmás una invitación)</td>
                <td>Sí</td>
                <td>No</td>
            </tr>
            <tr>
                <td>Documento (DNI o pasaporte)</td>
                <td>Sí</td>
                <td>Sí</td>
                <td>No</td>
            </tr>
            <tr>
                <td>Foto / selfie</td>
                <td>Sí</td>
                <td>Sí (identificación en puerta)</td>
                <td>No</td>
            </tr>
            <tr>
                <td>Identificador de dispositivo (<code>device_id</code>)</td>
                <td>Sí (al canjear un enlace de activación)</td>
                <td>Se asocia al canje del enlace</td>
                <td>No</td>
            </tr>
            <tr>
                <td>Código de invitación / QR</td>
                <td>Sí</td>
                <td>Sí</td>
                <td>No</td>
            </tr>
            <tr>
                <td>Ubicación GPS</td>
                <td>No</td>
                <td>—</td>
                <td>—</td>
            </tr>
            <tr>
                <td>Contactos, micrófono, fotos de la galería</td>
                <td>No (la selfie se toma con la cámara en el momento)</td>
                <td>—</td>
                <td>—</td>
            </tr>
            <tr>
                <td>Analítica de publicidad / SDK de ads</td>
                <td>No</td>
                <td>—</td>
                <td>—</td>
            </tr>
        </tbody>
    </table>
    </div>
    <p>No vendemos datos personales.</p>

    <h2>2. Responsable</h2>
    <p><strong>Desert Eventos</strong>, Luján de Cuyo, Mendoza.</p>
    <p>
        Para ejercer derechos o pedir la supresión de tus datos en servidor:
        <a href="mailto:info@deserteventos.com.ar">info@deserteventos.com.ar</a>.
    </p>

    <h2>3. Datos que tratamos y finalidad</h2>

    <h3>3.1 Si solo navegás la App (inicio, agenda, predio)</h3>
    <ul>
        <li>No pedimos nombre ni documento.</li>
        <li>El listado de eventos se descarga de forma pública.</li>
        <li>
            El dispositivo necesita <strong>internet</strong>. El servidor puede registrar logs técnicos habituales
            (IP, fecha, ruta) por seguridad y operación, no para perfil comercial.
        </li>
    </ul>

    <h3>3.2 Si confirmás una invitación o te registrás por enlace</h3>
    <p>Enviamos a nuestros servidores, y en parte guardamos en el teléfono:</p>
    <div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th>Dato</th>
                <th>Finalidad</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Nombre y apellido</td>
                <td>Identificarte en la lista de invitados y en el QR</td>
            </tr>
            <tr>
                <td>Tipo y número de documento</td>
                <td>Verificar que coincidís con la invitación y en la entrada</td>
            </tr>
            <tr>
                <td>Selfie (foto del rostro)</td>
                <td>Control de ingreso: el staff coteja la foto con la persona que se presenta</td>
            </tr>
            <tr>
                <td>Código de invitación</td>
                <td>Vincular el registro al evento</td>
            </tr>
            <tr>
                <td>Estado de la invitación (pendiente, confirmada, etc.)</td>
                <td>Saber si el QR es válido</td>
            </tr>
            <tr>
                <td><code>device_id</code> (identificador generado en el teléfono)</td>
                <td>Evitar que un enlace de activación se use en otro dispositivo (un solo uso)</td>
            </tr>
        </tbody>
    </table>
    </div>
    <p>
        <strong>Base del tratamiento:</strong> prestar el servicio de invitación digital que pediste (relación con el
        organizador del evento) y <strong>seguridad en puerta</strong> (evitar que otra persona use tu invitación).
    </p>
    <p>
        La selfie se usa <strong>solo</strong> para identificación en el evento. No la publicamos en redes, no la
        usamos para publicidad ni para entrenar sistemas de reconocimiento masivo.
    </p>

    <h3>3.3 En el dispositivo</h3>
    <ul>
        <li>El <strong>contenido del QR</strong> se guarda en almacenamiento seguro del teléfono.</li>
        <li>Nombre y documento pueden guardarse en preferencias locales para mostrar tu perfil.</li>
        <li>Invitaciones ya confirmadas pueden verse <strong>sin internet</strong> (el QR queda en el teléfono).</li>
    </ul>

    <h3>3.4 Staff (control de acceso)</h3>
    <p>
        Quienes operan la puerta pueden escanear un QR y ver nombre, documento, selfie y si esa invitación ya ingresó.
        Ese uso es interno de Desert Eventos para el evento.
    </p>

    <h2>4. Selfie y datos sensibles</h2>
    <p>
        Una foto del rostro destinada a identificarte puede considerarse <strong>dato sensible / biométrico</strong>
        según cómo se interprete la normativa.
    </p>
    <p>
        La tomamos porque el ingreso al evento lo requiere (cotejo visual). No es opcional si querés completar ese flujo:
        sin selfie no se confirma la invitación ni el auto-registro.
    </p>
    <p>
        Si no querés que tratemos tu foto, no uses la confirmación por App y coordiná el acceso con el organizador por
        otro medio (mail o WhatsApp).
    </p>

    <h2>5. Con quién se comparte</h2>
    <p>No vendemos ni alquilamos tus datos.</p>
    <p>Se comunican únicamente a:</p>
    <ul>
        <li><strong>Desert Eventos</strong> (organización y personal de puerta);</li>
        <li>
            <strong>proveedores técnicos</strong> que alojan o transmiten la API (<code>desert.rxstudio.dev</code>),
            en calidad de encargados, solo para operar la App;
        </li>
        <li><strong>autoridades</strong> si una norma o una orden judicial lo exige.</li>
    </ul>
    <p>
        Apple y Google, como tiendas, tienen sus propias políticas; no les enviamos el DNI ni la selfie para publicar
        la App.
    </p>
    <p>
        Enlaces de contacto (WhatsApp, correo, mapas de cómo llegar) abren <strong>otras apps o sitios</strong>.
        Ahí rige la privacidad de esos terceros, no esta política.
    </p>

    <h2>6. Conservación</h2>
    <ul>
        <li>
            <strong>En el teléfono:</strong> hasta que desinstales la App o borres sus datos en Ajustes del sistema.
        </li>
        <li>
            <strong>En servidor:</strong> mientras sea necesario para el evento (lista de invitados, control de ingreso
            y un plazo razonable posterior de auditoría o reclamos, en general no más de lo que el organizador necesite
            para esa edición). Podés pedir la supresión antes, según el punto 7.
        </li>
    </ul>
    <p>Los logs técnicos se conservan el tiempo mínimo de operación y seguridad.</p>

    <h2>7. Tus derechos (Ley 25.326)</h2>
    <p>
        Podés pedir <strong>acceso, rectificación, actualización o supresión</strong> de tus datos, y la información
        prevista en el art. 6 de la Ley 25.326, escribiendo a
        <a href="mailto:info@deserteventos.com.ar">info@deserteventos.com.ar</a>
        desde un correo que permita identificarte (y, si aplica, el código de invitación o el evento).
    </p>
    <p>
        La Agencia de Acceso a la Información Pública (AAIP) es el órgano de control en Argentina.
    </p>

    <h3>Cómo borrar datos (requisito de las tiendas, aunque no haya “cuenta”)</h3>
    <p>La App <strong>no tiene login</strong>. Aun así:</p>
    <ol>
        <li>
            <strong>En el teléfono:</strong> desinstalá la App o borrá el almacenamiento de Desert Eventos en Ajustes.
            Eso elimina el QR y el perfil locales.
        </li>
        <li>
            <strong>En nuestros servidores:</strong> pedí la baja por mail a
            <a href="mailto:info@deserteventos.com.ar">info@deserteventos.com.ar</a>,
            indicando nombre, documento y evento. Procesaremos la eliminación o anonimización de tu registro de
            invitación y selfie, salvo que debamos conservar algo por una obligación legal o un reclamo abierto.
        </li>
    </ol>
    <p>
        Si Google Play o App Store exigen un formulario web de eliminación, publicaremos esa URL junto a esta política.
    </p>

    <h2>8. Menores</h2>
    <p>
        La App está dirigida a personas de <strong>13 años o más</strong>. No está hecha para niños menores de 13
        ni se publica en las categorías Kids / “hecha para niños” de las tiendas. No recolectamos datos de niños
        menores de 13 de forma intencional.
    </p>
    <p>
        Quienes tienen entre 13 y 17 años, al enviar el formulario de invitación, declaran que un padre, madre o tutor
        autorizó ese uso para el ingreso al evento (ver
        <a href="{{ route('legal.terminos') }}">Términos — Elegibilidad</a>).
    </p>
    <p>
        Si un menor de 13 se registró, el responsable o tutor puede pedir la eliminación a
        <a href="mailto:info@deserteventos.com.ar">info@deserteventos.com.ar</a>.
    </p>

    <h2>9. Permisos del sistema</h2>
    <div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th>Permiso</th>
                <th>Para qué</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Cámara</td>
                <td>Selfie de la invitación; lectura de QR en el flujo de staff</td>
            </tr>
            <tr>
                <td>Internet</td>
                <td>API de invitaciones, eventos y canje de enlaces</td>
            </tr>
        </tbody>
    </table>
    </div>
    <p>
        <strong>No pedimos</strong> ubicación, contactos, micrófono ni acceso amplio a la galería para el registro
        (la foto se captura en el momento).
    </p>
    <p>
        En iOS, el sistema mostrará un texto del estilo: <em>“Para tomar la selfie de tu invitación”</em>.
    </p>

    <h2>10. Cookies, SDK y publicidad</h2>
    <p>
        La App no incluye redes de anuncios ni herramientas de tracking publicitario de terceros.
    </p>
    <p>
        Fuera de la App, el sitio institucional o las fuentes de Google Fonts / mapas que se abran en el navegador
        pueden usar cookies propias de esos servicios.
    </p>

    <h2>11. Seguridad</h2>
    <p>
        Usamos HTTPS hacia la API. El payload del QR se guarda en el almacén seguro del dispositivo cuando el sistema
        lo permite. Ningún envío por internet es infalible: si detectás un uso indebido de tu invitación, escribinos
        de inmediato.
    </p>

    <h2>12. Transferencias</h2>
    <p>
        Los datos se tratan para un servicio prestado en Argentina. El hosting de
        <code>desert.rxstudio.dev</code> puede estar en infraestructura fuera del país. En ese caso, el encargado solo
        procesa lo necesario para la App, con medidas de seguridad equivalentes.
    </p>

    <h2>13. Cambios</h2>
    <p>
        Si cambia el tratamiento de forma relevante, actualizaremos esta política y la fecha de vigencia. El uso
        continuado de la App después de esa fecha implica conocimiento de la versión publicada en la URL pública.
    </p>

    <h2>14. Contacto</h2>
    <p>
        <strong>Desert Eventos</strong><br>
        <a href="mailto:info@deserteventos.com.ar">info@deserteventos.com.ar</a><br>
        WhatsApp: +54 9 261 708-3142<br>
        Luján de Cuyo, Mendoza, Argentina
    </p>
@endsection
