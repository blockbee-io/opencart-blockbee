<?php

// Heading
$_['heading_title'] = 'BlockBee';

$_['title'] = 'Название';

$_['blockchain_fees'] = 'Добавить комиссию блокчейна к заказу';
$_['fees'] = 'Комиссия сервиса, взимаемая с клиента';

$_['never'] = 'Никогда';

// Text
$_['text_extension'] = 'Расширения';
$_['text_success'] = 'Успех: настройки BlockBee изменены!';
$_['text_edit'] = 'Редактировать BlockBee';
$_['text_blockbee'] = '<a target="_BLANK" href="https://blockbee.io/"><img src="' . HTTP_CATALOG . '/extension/blockbee/admin/view/image/payment/blockbee.png" alt="blockbee" title="blockbee" style="border: 1px solid #EEEEEE; height:37px" /></a>';
$_['text_connect_blockbee'] = 'Этот модуль позволяет безопасно принимать платежи BlockBee.';
$_['text_blockbee_image'] = '<a target="_BLANK" href="https://blockbee.io/"><img src="' . HTTP_CATALOG . '/extension/blockbee/admin/view/image/payment/blockbee.png" alt="blockbee" title="blockbee" style="height:50px" class="img-fluid" /></a>';
$_['text_blockbee_suppport'] = 'Если вам нужна помощь или есть предложения, свяжитесь с нами через онлайн-чат на нашем <a target="_blank" href="https://blockbee.io">сайте</a>';
$_['text_blockchain_fees'] = 'Это добавит примерную комиссию блокчейна к сумме заказа';
$_['text_fees'] = 'Укажите комиссию сервиса BlockBee, которую вы хотите взимать с клиента. Примечание: комиссия, которую вы хотите взимать с ваших клиентов (для полного или частичного покрытия комиссий BlockBee)';
$_['text_qrcode'] = 'Выберите, как показывать QR-код пользователю. Можно выбрать вариант по умолчанию для первого отображения или скрыть один из них.';
$_['text_btc'] = 'Bitcoin';
$_['text_refresh_values'] = 'Система будет автоматически обновлять стоимость конвертации счетов (по данным в реальном времени) каждые X минут. Эта функция полезна, когда клиент долго оплачивает сгенерированный счет, а выбранная крипта — волатильная монета/токен (не стейблкоин). Внимание: установка этого параметра на "Никогда" может вызвать проблемы конвертации; рекомендуем оставить 5 минут.';
$_['text_order_cancelation_timeout'] = 'Выбирает время, которое есть у пользователя для оплаты заказа. По истечении этого времени заказ будет помечен как "Отменён", и все оплаченные суммы будут проигнорированы. Примечание: если пользователь всё же отправит средства на сгенерированный адрес, сумма будет перенаправлена вам. Внимание: не рекомендуем больше 1 часа.';

$_['text_tab_general'] = 'Общие';
$_['text_tab_crypto'] = 'Криптовалюты';
$_['text_tab_advanced'] = 'Расширенные';

// Entry
$_['entry_cryptocurrencies'] = 'Принимаемые криптовалюты';
$_['entry_btc_address'] = 'Адрес ' . $_['text_btc'];

$_['entry_order_status'] = 'Статус заказа';
$_['entry_paid_order_statuses'] = 'Статусы оплаченных заказов';
$_['text_paid_order_statuses'] = 'Выберите, какие статусы заказа считаются "оплаченными". Заказы с такими статусами не будут повторно обрабатываться обратными вызовами и не будут опрашиваться на дальнейшие платежи. Удерживайте Ctrl/Cmd для выбора нескольких.';
$_['entry_status'] = 'Статус';

$_['branding'] = 'Показывать логотип BlockBee и кредиты под QR-кодом';

$_['qrcode_default'] = 'Показывать QR-код';
$_['qrcode'] = 'Какой QR-код показывать';
$_['qrcode_size'] = 'Размер QR-кода';
$_['qrcode_without_ammount'] = 'По умолчанию без суммы';
$_['qrcode_ammount'] = 'По умолчанию с суммой';
$_['qrcode_hide_ammount'] = 'Скрыть с суммой';
$_['qrcode_hide_without_ammount'] = 'Скрыть без суммы';

$_['color_scheme'] = 'Цветовая схема';
$_['scheme_light'] = 'Светлая';
$_['scheme_dark'] = 'Тёмная';
$_['scheme_auto'] = 'Автоматическая';

$_['refresh_values'] = 'Обновлять конвертированное значение';
$_['five_minutes'] = 'Каждые 5 минут';
$_['ten_minutes'] = 'Каждые 10 минут';
$_['fifteen_minutes'] = 'Каждые 15 минут';
$_['thirty_minutes'] = 'Каждые 30 минут';
$_['forty_five_minutes'] = 'Каждые 45 минут';
$_['sixty_minutes'] = 'Каждые 60 минут';

$_['order_cancelation_timeout'] = 'Тайм-аут отмены заказа';
$_['fifteen_minutes_cancellation'] = '15 минут';
$_['thirty_minutes_cancellation'] = '30 минут';
$_['forty_five_minutes_cancellation'] = '45 минут';
$_['one_hour'] = '1 час';
$_['six_hours'] = '6 часов';
$_['twelve_hours'] = '12 часов';
$_['eighteen_hours'] = '18 часов';
$_['twenty_four_hours'] = '24 часа';

$_['entry_geo_zone'] = 'Гео-зона';
$_['entry_sort_order'] = 'Порядок сортировки';

// Error
$_['error_permission'] = 'Внимание: у вас нет прав на изменение платёжного модуля BlockBee';
$_['warning_currency_unsupported'] = 'Валюта вашего магазина (%s) не в списке фиатных валют, поддерживаемых BlockBee. Оценка комиссий блокчейна будет производиться через USD. Актуальный список: https://blockbee.io/fees/';

// Help hints
$_['help_cryptocurrencies'] = 'Если вы используете BlockBee, вы можете выбрать, задавать ли адреса получения здесь или на странице настроек BlockBee.<br/>Чтобы задать адреса в настройках плагина, выберите "Address Override" при создании API-ключа.<br/>Чтобы задать адреса в настройках BlockBee, НЕ выбирайте "Address Override" при создании API-ключа.';
$_['help_cryptocurrency'] = 'Установите флажок, чтобы включить криптовалюту';


// Order page - payment tab
$_['text_payment_info'] = 'Информация об оплате';

$_['disable_conversion'] = 'Отключить конвертацию';
$_['disable_conversion_warn_bold'] = 'Внимание: эта опция отключает конвертацию цены для ВСЕХ криптовалют!';
$_['disable_conversion_warn'] = 'Если вы это включите, цена не будет конвертироваться из валюты вашего магазина в выбранную пользователем криптовалюту, и пользователям будет предложено оплатить ту же сумму, что показана в вашем магазине, независимо от выбранной криптовалюты';


$_['api_key'] = 'API-ключ BlockBee';
$_['api_key_info'] = "Введите здесь ваш API-ключ BlockBee. Получить его можно у BlockBee. Внимание: если разрешение API 'Address Override' не включено, необходимо задать адрес в панели управления, иначе платежи могут не пройти.";

$_['info_icon'] = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="ms-1 bi bi-info-circle" viewBox="0 0 16 16">
                                        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                                        <path d="m8.93 6.588-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533L8.93 6.588zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0z"/>
                                    </svg>';

$_['entry_cron_secret'] = 'Cron secret';
$_['help_cron_secret']  = 'Required to call the public cron URL over HTTP from a non-loopback host. Append &secret=<this value> to the cron URL. Leave empty to allow only CLI or direct-loopback (no-proxy) cron. If your store is behind a reverse proxy, you MUST set this — loopback is not trusted when proxy headers are present.';
