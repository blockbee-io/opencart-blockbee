<?php

// Heading
$_['heading_title'] = 'BlockBee';

$_['title'] = 'Título';

$_['blockchain_fees'] = 'Añadir la tarifa de blockchain al pedido';
$_['fees'] = 'Tarifa de servicio a cobrar al cliente';

$_['never'] = 'Nunca';

// Text
$_['text_extension'] = 'Extensiones';
$_['text_success'] = '¡Éxito: Has modificado los detalles de BlockBee!';
$_['text_edit'] = 'Editar BlockBee';
$_['text_blockbee'] = '<a target="_BLANK" href="https://blockbee.io/"><img src="' . HTTP_CATALOG . '/extension/blockbee/admin/view/image/payment/blockbee.png" alt="blockbee" title="blockbee" style="border: 1px solid #EEEEEE; height:37px" /></a>';
$_['text_connect_blockbee'] = 'Este módulo te permite aceptar pagos BlockBee de forma segura.';
$_['text_blockbee_image'] = '<a target="_BLANK" href="https://blockbee.io/"><img src="' . HTTP_CATALOG . '/extension/blockbee/admin/view/image/payment/blockbee.png" alt="blockbee" title="blockbee" style="height:50px" class="img-fluid" /></a>';
$_['text_blockbee_suppport'] = 'Si necesitas ayuda o tienes alguna sugerencia, contáctanos a través del chat en vivo en nuestro <a target="_blank" href="https://blockbee.io">sitio web</a>';
$_['text_blockchain_fees'] = 'Esto añadirá una estimación de la tarifa de blockchain al valor del pedido';
$_['text_fees'] = 'Establece la tarifa de servicio BlockBee que quieres cobrar al cliente. Nota: Tarifa que quieres cobrar a tus clientes (para cubrir total o parcialmente las tarifas de BlockBee)';
$_['text_qrcode'] = 'Selecciona cómo quieres mostrar el código QR al usuario. Puedes elegir un predeterminado para mostrar primero u ocultar uno de ellos.';
$_['text_btc'] = 'Bitcoin';
$_['text_refresh_values'] = 'El sistema actualizará automáticamente el valor de conversión de las facturas (con datos en tiempo real), cada X minutos. Esta función es útil cuando un cliente tarda mucho en pagar una factura generada y la cripto elegida es una moneda/token volátil (no stablecoin). Aviso: Establecer esto en "Nunca" puede causar problemas de conversión; recomendamos mantenerlo en 5 minutos.';
$_['text_order_cancelation_timeout'] = 'Selecciona el tiempo que el usuario tiene para pagar el pedido. Cuando este tiempo termine, el pedido se marcará como "Cancelado" y cualquier valor pagado será ignorado. Aviso: Si el usuario aún envía dinero a la dirección generada, el valor se redirigirá hacia ti. Aviso: No recomendamos más de 1 hora.';

$_['text_tab_general'] = 'General';
$_['text_tab_crypto'] = 'Criptomonedas';
$_['text_tab_advanced'] = 'Avanzado';

// Entry
$_['entry_cryptocurrencies'] = 'Criptomonedas aceptadas';
$_['entry_btc_address'] = $_['text_btc'] . ' Dirección';

$_['entry_order_status'] = 'Estado del pedido';
$_['entry_paid_order_statuses'] = 'Estados de pedido pagados';
$_['text_paid_order_statuses'] = 'Selecciona qué estados de pedido cuentan como "pagado". Los pedidos en estos estados no serán reprocesados por callbacks ni consultados para pagos adicionales. Mantén Ctrl/Cmd para seleccionar múltiples.';
$_['entry_status'] = 'Estado';

$_['branding'] = 'Mostrar el logo BlockBee y créditos debajo del código QR';

$_['qrcode_default'] = 'Mostrar código QR';
$_['qrcode'] = 'Código QR a mostrar';
$_['qrcode_size'] = 'Tamaño del código QR';
$_['qrcode_without_ammount'] = 'Predeterminado sin importe';
$_['qrcode_ammount'] = 'Predeterminado con importe';
$_['qrcode_hide_ammount'] = 'Ocultar con importe';
$_['qrcode_hide_without_ammount'] = 'Ocultar sin importe';

$_['color_scheme'] = 'Esquema de color';
$_['scheme_light'] = 'Claro';
$_['scheme_dark'] = 'Oscuro';
$_['scheme_auto'] = 'Automático';

$_['refresh_values'] = 'Actualizar valor convertido';
$_['five_minutes'] = 'Cada 5 minutos';
$_['ten_minutes'] = 'Cada 10 minutos';
$_['fifteen_minutes'] = 'Cada 15 minutos';
$_['thirty_minutes'] = 'Cada 30 minutos';
$_['forty_five_minutes'] = 'Cada 45 minutos';
$_['sixty_minutes'] = 'Cada 60 minutos';

$_['order_cancelation_timeout'] = 'Tiempo límite de cancelación del pedido';
$_['fifteen_minutes_cancellation'] = '15 minutos';
$_['thirty_minutes_cancellation'] = '30 minutos';
$_['forty_five_minutes_cancellation'] = '45 minutos';
$_['one_hour'] = '1 hora';
$_['six_hours'] = '6 horas';
$_['twelve_hours'] = '12 horas';
$_['eighteen_hours'] = '18 horas';
$_['twenty_four_hours'] = '24 horas';

$_['entry_geo_zone'] = 'Zona geográfica';
$_['entry_sort_order'] = 'Orden de clasificación';

// Error
$_['error_permission'] = 'Aviso: No tienes permiso para modificar el módulo de pago BlockBee';
$_['warning_currency_unsupported'] = 'La moneda de tu tienda (%s) no está en la lista de monedas compatibles con BlockBee. Las estimaciones de tarifas de blockchain usarán USD. Consulta https://blockbee.io/fees/ para la lista actual.';

// Help hints
$_['help_cryptocurrencies'] = 'Si estás usando BlockBee puedes elegir si establecer las direcciones de recepción aquí o en tu página de configuración BlockBee.<br/>Para establecer las direcciones en la configuración del plugin, selecciona "Address Override" al crear la API Key.<br/>Para establecer las direcciones en la configuración BlockBee, NO selecciones "Address Override" al crear la API Key.';
$_['help_cryptocurrency'] = 'Haz clic en la casilla para activar la criptomoneda';


// Order page - payment tab
$_['text_payment_info'] = 'Información de pago';

$_['disable_conversion'] = 'Desactivar conversión';
$_['disable_conversion_warn_bold'] = 'Atención: Esta opción desactiva la conversión de precio para TODAS las criptomonedas!';
$_['disable_conversion_warn'] = 'Si marcas esto, el precio no se convertirá de la moneda de tu tienda a la criptomoneda elegida por el usuario, y se pedirá a los usuarios que paguen el mismo valor mostrado en tu tienda, independientemente de la criptomoneda elegida';


$_['api_key'] = 'Clave API BlockBee';
$_['api_key_info'] = "Introduce aquí tu clave API BlockBee. Puedes obtener una con BlockBee. Aviso: Si el permiso API 'Address Override' no está activo, debes establecer la dirección en el panel; de lo contrario, los pagos pueden fallar.";

$_['info_icon'] = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="ms-1 bi bi-info-circle" viewBox="0 0 16 16">
                                        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                                        <path d="m8.93 6.588-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533L8.93 6.588zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0z"/>
                                    </svg>';

$_['entry_cron_secret'] = 'Cron secret';
$_['help_cron_secret']  = 'Required to call the public cron URL over HTTP from a non-loopback host. Append &secret=<this value> to the cron URL. Leave empty to allow only CLI or direct-loopback (no-proxy) cron. If your store is behind a reverse proxy, you MUST set this — loopback is not trusted when proxy headers are present.';
