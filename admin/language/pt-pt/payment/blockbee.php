<?php

// Heading
$_['heading_title'] = 'BlockBee';

$_['title'] = 'Título';

$_['blockchain_fees'] = 'Adicionar a taxa de blockchain ao pedido';
$_['fees'] = 'Taxa de serviço a cobrar ao cliente';

$_['never'] = 'Nunca';

// Text
$_['text_extension'] = 'Extensões';
$_['text_success'] = 'Sucesso: Os detalhes do BlockBee foram modificados!';
$_['text_edit'] = 'Editar BlockBee';
$_['text_blockbee'] = '<a target="_BLANK" href="https://blockbee.io/"><img src="' . HTTP_CATALOG . '/extension/blockbee/admin/view/image/payment/blockbee.png" alt="blockbee" title="blockbee" style="border: 1px solid #EEEEEE; height:37px" /></a>';
$_['text_connect_blockbee'] = 'Este módulo permite-lhe aceitar pagamentos BlockBee de forma segura.';
$_['text_blockbee_image'] = '<a target="_BLANK" href="https://blockbee.io/"><img src="' . HTTP_CATALOG . '/extension/blockbee/admin/view/image/payment/blockbee.png" alt="blockbee" title="blockbee" style="height:50px" class="img-fluid" /></a>';
$_['text_blockbee_suppport'] = 'Se precisar de ajuda ou tiver alguma sugestão, contacte-nos através do chat ao vivo no nosso <a target="_blank" href="https://blockbee.io">website</a>';
$_['text_blockchain_fees'] = 'Isto adicionará uma estimativa da taxa de blockchain ao valor do pedido';
$_['text_fees'] = 'Defina a taxa de serviço BlockBee que pretende cobrar ao cliente. Nota: Taxa que pretende cobrar aos seus clientes (para cobrir total ou parcialmente as taxas do BlockBee)';
$_['text_qrcode'] = 'Selecione como pretende mostrar o código QR ao utilizador. Pode escolher um padrão para mostrar primeiro ou ocultar um deles.';
$_['text_btc'] = 'Bitcoin';
$_['text_refresh_values'] = 'O sistema atualizará automaticamente o valor de conversão das faturas (com dados em tempo real), a cada X minutos. Esta funcionalidade é útil sempre que um cliente demore muito tempo a pagar uma fatura gerada e a cripto escolhida seja uma moeda/token volátil (não stablecoin). Aviso: Definir esta opção como "Nunca" pode causar problemas de conversão; aconselhamos manter em 5 minutos.';
$_['text_order_cancelation_timeout'] = 'Define o tempo que o utilizador tem para pagar o pedido. Quando este tempo terminar, o pedido será marcado como "Cancelado" e qualquer valor pago será ignorado. Aviso: Se o utilizador ainda enviar dinheiro para o endereço gerado, o valor será mesmo assim encaminhado para si. Aviso: Não recomendamos mais de 1 hora.';

$_['text_tab_general'] = 'Geral';
$_['text_tab_crypto'] = 'Criptomoedas';
$_['text_tab_advanced'] = 'Avançado';

// Entry
$_['entry_cryptocurrencies'] = 'Criptomoedas aceites';
$_['entry_btc_address'] = $_['text_btc'] . ' Endereço';

$_['entry_order_status'] = 'Estado do pedido';
$_['entry_paid_order_statuses'] = 'Estados de pedido pagos';
$_['text_paid_order_statuses'] = 'Selecione quais estados de pedido contam como "pago". Pedidos nestes estados não serão reprocessados por callbacks nem consultados para pagamentos adicionais. Mantenha Ctrl/Cmd para selecionar múltiplos.';
$_['entry_status'] = 'Estado';

$_['branding'] = 'Mostrar o logótipo BlockBee e créditos por baixo do código QR';

$_['qrcode_default'] = 'Mostrar código QR';
$_['qrcode'] = 'Código QR a mostrar';
$_['qrcode_size'] = 'Tamanho do código QR';
$_['qrcode_without_ammount'] = 'Padrão sem valor';
$_['qrcode_ammount'] = 'Padrão com valor';
$_['qrcode_hide_ammount'] = 'Ocultar com valor';
$_['qrcode_hide_without_ammount'] = 'Ocultar sem valor';

$_['color_scheme'] = 'Esquema de cores';
$_['scheme_light'] = 'Claro';
$_['scheme_dark'] = 'Escuro';
$_['scheme_auto'] = 'Automático';

$_['refresh_values'] = 'Atualizar valor convertido';
$_['five_minutes'] = 'A cada 5 minutos';
$_['ten_minutes'] = 'A cada 10 minutos';
$_['fifteen_minutes'] = 'A cada 15 minutos';
$_['thirty_minutes'] = 'A cada 30 minutos';
$_['forty_five_minutes'] = 'A cada 45 minutos';
$_['sixty_minutes'] = 'A cada 60 minutos';

$_['order_cancelation_timeout'] = 'Tempo limite para cancelamento do pedido';
$_['fifteen_minutes_cancellation'] = '15 minutos';
$_['thirty_minutes_cancellation'] = '30 minutos';
$_['forty_five_minutes_cancellation'] = '45 minutos';
$_['one_hour'] = '1 hora';
$_['six_hours'] = '6 horas';
$_['twelve_hours'] = '12 horas';
$_['eighteen_hours'] = '18 horas';
$_['twenty_four_hours'] = '24 horas';

$_['entry_geo_zone'] = 'Zona geográfica';
$_['entry_sort_order'] = 'Ordem de classificação';

// Error
$_['error_permission'] = 'Aviso: Não tem permissão para modificar o módulo de pagamento BlockBee';
$_['warning_currency_unsupported'] = 'A moeda da sua loja (%s) não está na lista de moedas suportadas pelo BlockBee. As estimativas de taxa de blockchain irão recorrer ao USD. Consulte https://blockbee.io/fees/ para a lista atual.';

// Help hints
$_['help_cryptocurrencies'] = 'Se está a usar o BlockBee pode escolher se define os endereços de receção aqui ou na sua página de definições BlockBee.<br/>Para definir os endereços nas definições do plugin, deve selecionar "Address Override" ao criar a API Key.<br/>Para definir os endereços nas definições BlockBee, NÃO deve selecionar "Address Override" ao criar a API Key.';
$_['help_cryptocurrency'] = 'Clique na caixa para ativar a criptomoeda';


// Order page - payment tab
$_['text_payment_info'] = 'Informação de pagamento';

$_['disable_conversion'] = 'Desativar conversão';
$_['disable_conversion_warn_bold'] = 'Atenção: Esta opção desativa a conversão de preço para TODAS as criptomoedas!';
$_['disable_conversion_warn'] = 'Se marcar isto, o preço não será convertido da moeda da sua loja para a criptomoeda escolhida pelo utilizador, e será pedido aos utilizadores que paguem o mesmo valor exibido na sua loja, independentemente da criptomoeda escolhida';


$_['api_key'] = 'Chave API BlockBee';
$_['api_key_info'] = "Insira aqui a sua chave API BlockBee. Pode obter uma com o BlockBee. Aviso: Se a permissão API 'Address Override' não estiver ativa, deve definir o endereço no dashboard, caso contrário os pagamentos podem falhar.";

$_['info_icon'] = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="ms-1 bi bi-info-circle" viewBox="0 0 16 16">
                                        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                                        <path d="m8.93 6.588-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533L8.93 6.588zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0z"/>
                                    </svg>';
