<?php

// Heading
$_['heading_title'] = 'BlockBee';

$_['title'] = 'Titolo';

$_['blockchain_fees'] = 'Aggiungi la commissione blockchain all\'ordine';
$_['fees'] = 'Commissione di servizio da addebitare al cliente';

$_['never'] = 'Mai';

// Text
$_['text_extension'] = 'Estensioni';
$_['text_success'] = 'Successo: Hai modificato i dettagli BlockBee!';
$_['text_edit'] = 'Modifica BlockBee';
$_['text_blockbee'] = '<a target="_BLANK" href="https://blockbee.io/"><img src="' . HTTP_CATALOG . '/extension/blockbee/admin/view/image/payment/blockbee.png" alt="blockbee" title="blockbee" style="border: 1px solid #EEEEEE; height:37px" /></a>';
$_['text_connect_blockbee'] = 'Questo modulo ti permette di accettare pagamenti BlockBee in modo sicuro.';
$_['text_blockbee_image'] = '<a target="_BLANK" href="https://blockbee.io/"><img src="' . HTTP_CATALOG . '/extension/blockbee/admin/view/image/payment/blockbee.png" alt="blockbee" title="blockbee" style="height:50px" class="img-fluid" /></a>';
$_['text_blockbee_suppport'] = 'Se hai bisogno di aiuto o hai suggerimenti, contattaci tramite la live chat sul nostro <a target="_blank" href="https://blockbee.io">sito web</a>';
$_['text_blockchain_fees'] = 'Questo aggiungerà una stima della commissione blockchain al valore dell\'ordine';
$_['text_fees'] = 'Imposta la commissione di servizio BlockBee che vuoi addebitare al cliente. Nota: Commissione che vuoi addebitare ai tuoi clienti (per coprire interamente o parzialmente le commissioni BlockBee)';
$_['text_qrcode'] = 'Seleziona come mostrare il codice QR all\'utente. Puoi scegliere un predefinito da mostrare per primo o nasconderne uno.';
$_['text_btc'] = 'Bitcoin';
$_['text_refresh_values'] = 'Il sistema aggiornerà automaticamente il valore di conversione delle fatture (con dati in tempo reale), ogni X minuti. Questa funzione è utile quando un cliente impiega molto tempo per pagare una fattura generata e la cripto scelta è una moneta/token volatile (non stablecoin). Avviso: Impostare questa opzione su "Mai" può creare problemi di conversione; consigliamo di mantenerla a 5 minuti.';
$_['text_order_cancelation_timeout'] = 'Seleziona il tempo che l\'utente ha per pagare l\'ordine. Quando questo tempo è scaduto, l\'ordine sarà contrassegnato come "Annullato" e ogni valore pagato sarà ignorato. Nota: Se l\'utente invia comunque denaro all\'indirizzo generato, il valore verrà comunque reindirizzato a te. Avviso: Non consigliamo più di 1 ora.';

$_['text_tab_general'] = 'Generale';
$_['text_tab_crypto'] = 'Criptovalute';
$_['text_tab_advanced'] = 'Avanzato';

// Entry
$_['entry_cryptocurrencies'] = 'Criptovalute accettate';
$_['entry_btc_address'] = 'Indirizzo ' . $_['text_btc'];

$_['entry_order_status'] = 'Stato dell\'ordine';
$_['entry_paid_order_statuses'] = 'Stati ordine pagati';
$_['text_paid_order_statuses'] = 'Seleziona quali stati ordine contano come "pagato". Gli ordini in questi stati non saranno rielaborati dai callback né interrogati per pagamenti aggiuntivi. Tieni premuto Ctrl/Cmd per selezionarne più di uno.';
$_['entry_status'] = 'Stato';

$_['branding'] = 'Mostra il logo BlockBee e i crediti sotto il codice QR';

$_['qrcode_default'] = 'Mostra codice QR';
$_['qrcode'] = 'Codice QR da mostrare';
$_['qrcode_size'] = 'Dimensione codice QR';
$_['qrcode_without_ammount'] = 'Predefinito senza importo';
$_['qrcode_ammount'] = 'Predefinito con importo';
$_['qrcode_hide_ammount'] = 'Nascondi con importo';
$_['qrcode_hide_without_ammount'] = 'Nascondi senza importo';

$_['color_scheme'] = 'Schema colori';
$_['scheme_light'] = 'Chiaro';
$_['scheme_dark'] = 'Scuro';
$_['scheme_auto'] = 'Automatico';

$_['refresh_values'] = 'Aggiorna valore convertito';
$_['five_minutes'] = 'Ogni 5 minuti';
$_['ten_minutes'] = 'Ogni 10 minuti';
$_['fifteen_minutes'] = 'Ogni 15 minuti';
$_['thirty_minutes'] = 'Ogni 30 minuti';
$_['forty_five_minutes'] = 'Ogni 45 minuti';
$_['sixty_minutes'] = 'Ogni 60 minuti';

$_['order_cancelation_timeout'] = 'Timeout cancellazione ordine';
$_['fifteen_minutes_cancellation'] = '15 minuti';
$_['thirty_minutes_cancellation'] = '30 minuti';
$_['forty_five_minutes_cancellation'] = '45 minuti';
$_['one_hour'] = '1 ora';
$_['six_hours'] = '6 ore';
$_['twelve_hours'] = '12 ore';
$_['eighteen_hours'] = '18 ore';
$_['twenty_four_hours'] = '24 ore';

$_['entry_geo_zone'] = 'Zona geografica';
$_['entry_sort_order'] = 'Ordine di visualizzazione';

// Error
$_['error_permission'] = 'Avviso: Non hai il permesso di modificare il modulo di pagamento BlockBee';
$_['warning_currency_unsupported'] = 'La valuta del tuo negozio (%s) non è nell\'elenco delle valute fiat supportate da BlockBee. Le stime delle commissioni blockchain useranno USD. Consulta https://blockbee.io/fees/ per l\'elenco aggiornato.';

// Help hints
$_['help_cryptocurrencies'] = 'Se stai usando BlockBee puoi scegliere se impostare gli indirizzi di ricezione qui o nella pagina delle impostazioni BlockBee.<br/>Per impostare gli indirizzi nelle impostazioni del plugin, seleziona "Address Override" durante la creazione dell\'API Key.<br/>Per impostare gli indirizzi nelle impostazioni BlockBee, NON selezionare "Address Override" durante la creazione dell\'API Key.';
$_['help_cryptocurrency'] = 'Fai clic sulla casella per attivare la criptovaluta';


// Order page - payment tab
$_['text_payment_info'] = 'Informazioni di pagamento';

$_['disable_conversion'] = 'Disabilita conversione';
$_['disable_conversion_warn_bold'] = 'Attenzione: Questa opzione disabilita la conversione del prezzo per TUTTE le criptovalute!';
$_['disable_conversion_warn'] = 'Se selezioni questa opzione, il prezzo non verrà convertito dalla valuta del tuo negozio alla criptovaluta scelta dall\'utente, e agli utenti verrà richiesto di pagare lo stesso valore mostrato nel tuo negozio, indipendentemente dalla criptovaluta scelta';


$_['api_key'] = 'Chiave API BlockBee';
$_['api_key_info'] = "Inserisci qui la tua chiave API BlockBee. Puoi ottenerne una da BlockBee. Avviso: Se il permesso API 'Address Override' non è abilitato, devi impostare l'indirizzo nella dashboard, altrimenti i pagamenti potrebbero fallire.";

$_['info_icon'] = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="ms-1 bi bi-info-circle" viewBox="0 0 16 16">
                                        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                                        <path d="m8.93 6.588-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533L8.93 6.588zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0z"/>
                                    </svg>';
