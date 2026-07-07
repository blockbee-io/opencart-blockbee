<?php

// Heading
$_['heading_title'] = 'BlockBee';

$_['title'] = 'Titel';

$_['blockchain_fees'] = 'Die Blockchain-Gebühr zur Bestellung hinzufügen';
$_['fees'] = 'Dem Kunden zu berechnende Servicegebühr';

$_['never'] = 'Nie';

// Text
$_['text_extension'] = 'Erweiterungen';
$_['text_success'] = 'Erfolg: Sie haben Ihre BlockBee-Einstellungen geändert!';
$_['text_edit'] = 'BlockBee bearbeiten';
$_['text_blockbee'] = '<a target="_BLANK" href="https://blockbee.io/"><img src="' . HTTP_CATALOG . '/extension/blockbee/admin/view/image/payment/blockbee.png" alt="blockbee" title="blockbee" style="border: 1px solid #EEEEEE; height:37px" /></a>';
$_['text_connect_blockbee'] = 'Mit diesem Modul können Sie BlockBee-Zahlungen sicher entgegennehmen.';
$_['text_blockbee_image'] = '<a target="_BLANK" href="https://blockbee.io/"><img src="' . HTTP_CATALOG . '/extension/blockbee/admin/view/image/payment/blockbee.png" alt="blockbee" title="blockbee" style="height:50px" class="img-fluid" /></a>';
$_['text_blockbee_suppport'] = 'Wenn Sie Hilfe benötigen oder Vorschläge haben, kontaktieren Sie uns über den Live-Chat auf unserer <a target="_blank" href="https://blockbee.io">Website</a>';
$_['text_blockchain_fees'] = 'Dies fügt eine Schätzung der Blockchain-Gebühr zum Bestellwert hinzu';
$_['text_fees'] = 'Legen Sie die BlockBee-Servicegebühr fest, die Sie dem Kunden berechnen möchten. Hinweis: Gebühr, die Sie Ihren Kunden berechnen möchten (um die BlockBee-Gebühren ganz oder teilweise zu decken)';
$_['text_qrcode'] = 'Wählen Sie, wie der QR-Code dem Benutzer angezeigt werden soll. Sie können einen Standard zur ersten Anzeige auswählen oder einen ausblenden.';
$_['text_btc'] = 'Bitcoin';
$_['text_refresh_values'] = 'Das System aktualisiert automatisch den Umrechnungswert der Rechnungen (mit Echtzeitdaten) alle X Minuten. Diese Funktion ist nützlich, wenn ein Kunde lange braucht, um eine erstellte Rechnung zu bezahlen, und die gewählte Krypto eine volatile Münze/Token ist (kein Stablecoin). Warnung: Die Einstellung auf "Nie" kann zu Umrechnungsproblemen führen; wir empfehlen, sie auf 5 Minuten zu belassen.';
$_['text_order_cancelation_timeout'] = 'Wählt die Zeit, die der Benutzer für die Bezahlung der Bestellung hat. Wenn diese Zeit abgelaufen ist, wird die Bestellung als "Storniert" markiert und alle gezahlten Werte werden ignoriert. Hinweis: Wenn der Benutzer dennoch Geld an die generierte Adresse sendet, wird der Wert trotzdem an Sie weitergeleitet. Warnung: Wir empfehlen nicht mehr als 1 Stunde.';

$_['text_tab_general'] = 'Allgemein';
$_['text_tab_crypto'] = 'Kryptowährungen';
$_['text_tab_advanced'] = 'Erweitert';

// Entry
$_['entry_cryptocurrencies'] = 'Akzeptierte Kryptowährungen';
$_['entry_btc_address'] = $_['text_btc'] . '-Adresse';

$_['entry_order_status'] = 'Bestellstatus';
$_['entry_paid_order_statuses'] = 'Bezahlte Bestellstatus';
$_['text_paid_order_statuses'] = 'Wählen Sie, welche Bestellstatus als "bezahlt" gelten. Bestellungen in diesen Status werden nicht durch Callbacks erneut verarbeitet oder auf weitere Zahlungen abgefragt. Halten Sie Strg/Cmd gedrückt, um mehrere auszuwählen.';
$_['entry_status'] = 'Status';

$_['branding'] = 'BlockBee-Logo und Credits unter dem QR-Code anzeigen';

$_['qrcode_default'] = 'QR-Code anzeigen';
$_['qrcode'] = 'Anzuzeigender QR-Code';
$_['qrcode_size'] = 'QR-Code-Größe';
$_['qrcode_without_ammount'] = 'Standard ohne Betrag';
$_['qrcode_ammount'] = 'Standard mit Betrag';
$_['qrcode_hide_ammount'] = 'Mit Betrag ausblenden';
$_['qrcode_hide_without_ammount'] = 'Ohne Betrag ausblenden';

$_['color_scheme'] = 'Farbschema';
$_['scheme_light'] = 'Hell';
$_['scheme_dark'] = 'Dunkel';
$_['scheme_auto'] = 'Automatisch';

$_['refresh_values'] = 'Umgerechneten Wert aktualisieren';
$_['five_minutes'] = 'Alle 5 Minuten';
$_['ten_minutes'] = 'Alle 10 Minuten';
$_['fifteen_minutes'] = 'Alle 15 Minuten';
$_['thirty_minutes'] = 'Alle 30 Minuten';
$_['forty_five_minutes'] = 'Alle 45 Minuten';
$_['sixty_minutes'] = 'Alle 60 Minuten';

$_['order_cancelation_timeout'] = 'Zeitlimit für Bestellstornierung';
$_['fifteen_minutes_cancellation'] = '15 Minuten';
$_['thirty_minutes_cancellation'] = '30 Minuten';
$_['forty_five_minutes_cancellation'] = '45 Minuten';
$_['one_hour'] = '1 Stunde';
$_['six_hours'] = '6 Stunden';
$_['twelve_hours'] = '12 Stunden';
$_['eighteen_hours'] = '18 Stunden';
$_['twenty_four_hours'] = '24 Stunden';

$_['entry_geo_zone'] = 'Geozone';
$_['entry_sort_order'] = 'Sortierreihenfolge';

// Error
$_['error_permission'] = 'Warnung: Sie haben keine Berechtigung, das BlockBee-Zahlungsmodul zu ändern';
$_['warning_currency_unsupported'] = 'Die Währung Ihres Shops (%s) ist nicht in der von BlockBee unterstützten Fiat-Liste enthalten. Schätzungen der Blockchain-Gebühren werden auf USD zurückfallen. Aktuelle Liste unter https://blockbee.io/fees/.';

// Help hints
$_['help_cryptocurrencies'] = 'Wenn Sie BlockBee verwenden, können Sie wählen, ob Sie die Empfangsadressen hier oder auf Ihrer BlockBee-Einstellungsseite festlegen.<br/>Um die Adressen in den Plugin-Einstellungen festzulegen, wählen Sie "Address Override" beim Erstellen des API-Schlüssels.<br/>Um die Adressen in den BlockBee-Einstellungen festzulegen, wählen Sie KEIN "Address Override" beim Erstellen des API-Schlüssels.';
$_['help_cryptocurrency'] = 'Klicken Sie auf das Kontrollkästchen, um die Kryptowährung zu aktivieren';


// Order page - payment tab
$_['text_payment_info'] = 'Zahlungsinformationen';

$_['disable_conversion'] = 'Umrechnung deaktivieren';
$_['disable_conversion_warn_bold'] = 'Achtung: Diese Option deaktiviert die Preisumrechnung für ALLE Kryptowährungen!';
$_['disable_conversion_warn'] = 'Wenn Sie dies aktivieren, wird der Preis nicht von der Währung Ihres Shops in die vom Benutzer gewählte Kryptowährung umgerechnet, und die Benutzer werden aufgefordert, den gleichen Wert zu zahlen, der in Ihrem Shop angezeigt wird, unabhängig von der gewählten Kryptowährung';


$_['api_key'] = 'BlockBee API-Schlüssel';
$_['api_key_info'] = "Geben Sie hier Ihren BlockBee API-Schlüssel ein. Sie können einen bei BlockBee erhalten. Hinweis: Wenn die API-Berechtigung 'Address Override' nicht aktiviert ist, müssen Sie die Adresse im Dashboard festlegen, andernfalls können Zahlungen fehlschlagen.";

$_['info_icon'] = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="ms-1 bi bi-info-circle" viewBox="0 0 16 16">
                                        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                                        <path d="m8.93 6.588-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533L8.93 6.588zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0z"/>
                                    </svg>';

$_['entry_cron_secret'] = 'Cron secret';
$_['help_cron_secret']  = 'Required to call the public cron URL over HTTP from a non-loopback host. Append &secret=<this value> to the cron URL. Leave empty to allow only CLI or direct-loopback (no-proxy) cron. If your store is behind a reverse proxy, you MUST set this — loopback is not trusted when proxy headers are present.';
