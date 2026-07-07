<?php

// Heading
$_['heading_title'] = 'BlockBee';

$_['title'] = 'Titre';

$_['blockchain_fees'] = 'Ajouter les frais de blockchain à la commande';
$_['fees'] = 'Frais de service à facturer au client';

$_['never'] = 'Jamais';

// Text
$_['text_extension'] = 'Extensions';
$_['text_success'] = 'Succès : Vos paramètres BlockBee ont été modifiés !';
$_['text_edit'] = 'Modifier BlockBee';
$_['text_blockbee'] = '<a target="_BLANK" href="https://blockbee.io/"><img src="' . HTTP_CATALOG . '/extension/blockbee/admin/view/image/payment/blockbee.png" alt="blockbee" title="blockbee" style="border: 1px solid #EEEEEE; height:37px" /></a>';
$_['text_connect_blockbee'] = 'Ce module vous permet d\'accepter les paiements BlockBee en toute sécurité.';
$_['text_blockbee_image'] = '<a target="_BLANK" href="https://blockbee.io/"><img src="' . HTTP_CATALOG . '/extension/blockbee/admin/view/image/payment/blockbee.png" alt="blockbee" title="blockbee" style="height:50px" class="img-fluid" /></a>';
$_['text_blockbee_suppport'] = 'Si vous avez besoin d\'aide ou avez des suggestions, contactez-nous via le chat en direct sur notre <a target="_blank" href="https://blockbee.io">site web</a>';
$_['text_blockchain_fees'] = 'Ceci ajoutera une estimation des frais de blockchain au montant de la commande';
$_['text_fees'] = 'Définissez les frais de service BlockBee à facturer au client. Note : Frais que vous souhaitez facturer à vos clients (pour couvrir tout ou partie des frais BlockBee)';
$_['text_qrcode'] = 'Sélectionnez comment afficher le code QR à l\'utilisateur. Vous pouvez choisir un par défaut à afficher en premier ou en masquer un.';
$_['text_btc'] = 'Bitcoin';
$_['text_refresh_values'] = 'Le système mettra à jour automatiquement la valeur de conversion des factures (avec des données en temps réel), toutes les X minutes. Cette fonctionnalité est utile lorsqu\'un client met du temps à payer une facture générée et que la crypto choisie est une monnaie/jeton volatile (pas un stablecoin). Avertissement : Définir ce paramètre sur "Jamais" peut créer des problèmes de conversion ; nous recommandons de le maintenir à 5 minutes.';
$_['text_order_cancelation_timeout'] = 'Sélectionne le temps dont dispose l\'utilisateur pour payer la commande. Une fois ce temps écoulé, la commande sera marquée comme "Annulée" et toute valeur payée sera ignorée. Note : Si l\'utilisateur envoie de l\'argent à l\'adresse générée, la valeur vous sera tout de même redirigée. Avertissement : Nous ne recommandons pas plus d\'1 heure.';

$_['text_tab_general'] = 'Général';
$_['text_tab_crypto'] = 'Cryptomonnaies';
$_['text_tab_advanced'] = 'Avancé';

// Entry
$_['entry_cryptocurrencies'] = 'Cryptomonnaies acceptées';
$_['entry_btc_address'] = 'Adresse ' . $_['text_btc'];

$_['entry_order_status'] = 'Statut de la commande';
$_['entry_paid_order_statuses'] = 'Statuts de commande payés';
$_['text_paid_order_statuses'] = 'Sélectionnez quels statuts de commande comptent comme "payé". Les commandes dans ces statuts ne seront pas retraitées par les callbacks ni interrogées pour des paiements supplémentaires. Maintenez Ctrl/Cmd pour sélectionner plusieurs.';
$_['entry_status'] = 'Statut';

$_['branding'] = 'Afficher le logo BlockBee et les crédits sous le code QR';

$_['qrcode_default'] = 'Afficher le code QR';
$_['qrcode'] = 'Code QR à afficher';
$_['qrcode_size'] = 'Taille du code QR';
$_['qrcode_without_ammount'] = 'Par défaut sans montant';
$_['qrcode_ammount'] = 'Par défaut avec montant';
$_['qrcode_hide_ammount'] = 'Masquer avec montant';
$_['qrcode_hide_without_ammount'] = 'Masquer sans montant';

$_['color_scheme'] = 'Schéma de couleurs';
$_['scheme_light'] = 'Clair';
$_['scheme_dark'] = 'Sombre';
$_['scheme_auto'] = 'Automatique';

$_['refresh_values'] = 'Actualiser la valeur convertie';
$_['five_minutes'] = 'Toutes les 5 minutes';
$_['ten_minutes'] = 'Toutes les 10 minutes';
$_['fifteen_minutes'] = 'Toutes les 15 minutes';
$_['thirty_minutes'] = 'Toutes les 30 minutes';
$_['forty_five_minutes'] = 'Toutes les 45 minutes';
$_['sixty_minutes'] = 'Toutes les 60 minutes';

$_['order_cancelation_timeout'] = 'Délai d\'annulation de commande';
$_['fifteen_minutes_cancellation'] = '15 minutes';
$_['thirty_minutes_cancellation'] = '30 minutes';
$_['forty_five_minutes_cancellation'] = '45 minutes';
$_['one_hour'] = '1 heure';
$_['six_hours'] = '6 heures';
$_['twelve_hours'] = '12 heures';
$_['eighteen_hours'] = '18 heures';
$_['twenty_four_hours'] = '24 heures';

$_['entry_geo_zone'] = 'Zone géographique';
$_['entry_sort_order'] = 'Ordre de tri';

// Error
$_['error_permission'] = 'Avertissement : Vous n\'avez pas l\'autorisation de modifier le module de paiement BlockBee';
$_['warning_currency_unsupported'] = 'La devise de votre boutique (%s) ne figure pas dans la liste des devises fiat prises en charge par BlockBee. Les estimations de frais de blockchain utiliseront l\'USD. Consultez https://blockbee.io/fees/ pour la liste actuelle.';

// Help hints
$_['help_cryptocurrencies'] = 'Si vous utilisez BlockBee, vous pouvez choisir de définir les adresses de réception ici ou sur votre page de paramètres BlockBee.<br/>Pour définir les adresses dans les paramètres du plugin, sélectionnez "Address Override" lors de la création de la clé API.<br/>Pour définir les adresses dans les paramètres BlockBee, NE sélectionnez PAS "Address Override" lors de la création de la clé API.';
$_['help_cryptocurrency'] = 'Cliquez sur la case pour activer la cryptomonnaie';


// Order page - payment tab
$_['text_payment_info'] = 'Informations de paiement';

$_['disable_conversion'] = 'Désactiver la conversion';
$_['disable_conversion_warn_bold'] = 'Attention : Cette option désactive la conversion de prix pour TOUTES les cryptomonnaies !';
$_['disable_conversion_warn'] = 'Si vous cochez ceci, le prix ne sera pas converti de la devise de votre boutique à la cryptomonnaie choisie par l\'utilisateur, et il sera demandé aux utilisateurs de payer la même valeur affichée dans votre boutique, indépendamment de la cryptomonnaie choisie';


$_['api_key'] = 'Clé API BlockBee';
$_['api_key_info'] = "Insérez ici votre clé API BlockBee. Vous pouvez en obtenir une auprès de BlockBee. Avis : Si l'autorisation API 'Address Override' n'est pas activée, vous devez définir l'adresse dans le tableau de bord, sinon les paiements peuvent échouer.";

$_['info_icon'] = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="ms-1 bi bi-info-circle" viewBox="0 0 16 16">
                                        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                                        <path d="m8.93 6.588-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533L8.93 6.588zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0z"/>
                                    </svg>';

$_['entry_cron_secret'] = 'Cron secret';
$_['help_cron_secret']  = 'Required to call the public cron URL over HTTP from a non-loopback host. Append &secret=<this value> to the cron URL. Leave empty to allow only CLI or direct-loopback (no-proxy) cron. If your store is behind a reverse proxy, you MUST set this — loopback is not trusted when proxy headers are present.';
