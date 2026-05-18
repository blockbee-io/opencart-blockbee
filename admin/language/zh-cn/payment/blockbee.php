<?php

// Heading
$_['heading_title'] = 'BlockBee';

$_['title'] = '标题';

$_['blockchain_fees'] = '将区块链费用添加到订单中';
$_['fees'] = '向客户收取的服务费';

$_['never'] = '从不';

// Text
$_['text_extension'] = '扩展';
$_['text_success'] = '成功：您已修改 BlockBee 配置！';
$_['text_edit'] = '编辑 BlockBee';
$_['text_blockbee'] = '<a target="_BLANK" href="https://blockbee.io/"><img src="' . HTTP_CATALOG . '/extension/blockbee/admin/view/image/payment/blockbee.png" alt="blockbee" title="blockbee" style="border: 1px solid #EEEEEE; height:37px" /></a>';
$_['text_connect_blockbee'] = '此模块允许您安全地接受 BlockBee 付款。';
$_['text_blockbee_image'] = '<a target="_BLANK" href="https://blockbee.io/"><img src="' . HTTP_CATALOG . '/extension/blockbee/admin/view/image/payment/blockbee.png" alt="blockbee" title="blockbee" style="height:50px" class="img-fluid" /></a>';
$_['text_blockbee_suppport'] = '如果您需要帮助或有任何建议，请通过我们的<a target="_blank" href="https://blockbee.io">网站</a>实时聊天联系我们';
$_['text_blockchain_fees'] = '这将为订单金额添加一项估算的区块链费用';
$_['text_fees'] = '设置您希望向客户收取的 BlockBee 服务费。注意：您希望向客户收取的费用（用于全部或部分覆盖 BlockBee 的费用）';
$_['text_qrcode'] = '选择如何向用户显示二维码。可以选择优先显示的默认选项，或隐藏其中一种。';
$_['text_btc'] = '比特币';
$_['text_refresh_values'] = '系统将每 X 分钟自动更新发票的转换值（使用实时数据）。当客户支付生成的发票花费较长时间，且所选加密货币为波动性币种/代币（非稳定币）时，此功能非常有用。警告：将此设置为"从不"可能造成转换问题；建议保持在 5 分钟。';
$_['text_order_cancelation_timeout'] = '选择用户支付订单的时间。该时间结束后，订单将被标记为"已取消"，所有已支付金额将被忽略。说明：如果用户仍向生成的地址发送款项，金额仍会转至您的钱包。警告：建议不超过 1 小时。';

$_['text_tab_general'] = '常规';
$_['text_tab_crypto'] = '加密货币';
$_['text_tab_advanced'] = '高级';

// Entry
$_['entry_cryptocurrencies'] = '接受的加密货币';
$_['entry_btc_address'] = $_['text_btc'] . '地址';

$_['entry_order_status'] = '订单状态';
$_['entry_paid_order_statuses'] = '已支付订单状态';
$_['text_paid_order_statuses'] = '选择哪些订单状态视为"已支付"。这些状态下的订单不会被回调重新处理，也不会被轮询以等待更多付款。按住 Ctrl/Cmd 可选择多个。';
$_['entry_status'] = '状态';

$_['branding'] = '在二维码下方显示 BlockBee 标志和说明';

$_['qrcode_default'] = '显示二维码';
$_['qrcode'] = '要显示的二维码';
$_['qrcode_size'] = '二维码大小';
$_['qrcode_without_ammount'] = '默认无金额';
$_['qrcode_ammount'] = '默认有金额';
$_['qrcode_hide_ammount'] = '隐藏有金额';
$_['qrcode_hide_without_ammount'] = '隐藏无金额';

$_['color_scheme'] = '配色方案';
$_['scheme_light'] = '浅色';
$_['scheme_dark'] = '深色';
$_['scheme_auto'] = '自动';

$_['refresh_values'] = '刷新转换后的值';
$_['five_minutes'] = '每 5 分钟';
$_['ten_minutes'] = '每 10 分钟';
$_['fifteen_minutes'] = '每 15 分钟';
$_['thirty_minutes'] = '每 30 分钟';
$_['forty_five_minutes'] = '每 45 分钟';
$_['sixty_minutes'] = '每 60 分钟';

$_['order_cancelation_timeout'] = '订单取消超时';
$_['fifteen_minutes_cancellation'] = '15 分钟';
$_['thirty_minutes_cancellation'] = '30 分钟';
$_['forty_five_minutes_cancellation'] = '45 分钟';
$_['one_hour'] = '1 小时';
$_['six_hours'] = '6 小时';
$_['twelve_hours'] = '12 小时';
$_['eighteen_hours'] = '18 小时';
$_['twenty_four_hours'] = '24 小时';

$_['entry_geo_zone'] = '地理区域';
$_['entry_sort_order'] = '排序顺序';

// Error
$_['error_permission'] = '警告：您没有权限修改 BlockBee 支付模块';
$_['warning_currency_unsupported'] = '您的商店货币（%s）不在 BlockBee 支持的法定货币列表中。区块链费用估算将回退至 USD。当前支持列表见 https://blockbee.io/fees/。';

// Help hints
$_['help_cryptocurrencies'] = '如果您使用 BlockBee，可以选择在此处设置接收地址，或在 BlockBee 设置页面设置。<br/>要在插件设置中设置地址，请在创建 API 密钥时选择"Address Override"。<br/>要在 BlockBee 设置中设置地址，请在创建 API 密钥时不要选择"Address Override"。';
$_['help_cryptocurrency'] = '勾选复选框以启用该加密货币';


// Order page - payment tab
$_['text_payment_info'] = '付款信息';

$_['disable_conversion'] = '禁用换算';
$_['disable_conversion_warn_bold'] = '注意：此选项将禁用所有加密货币的价格换算！';
$_['disable_conversion_warn'] = '如果勾选此项，价格将不会从您商店的货币换算为用户选择的加密货币，用户将被要求支付与您商店显示相同的金额，无论选择何种加密货币';


$_['api_key'] = 'BlockBee API 密钥';
$_['api_key_info'] = "在此输入您的 BlockBee API 密钥。可以从 BlockBee 获取。注意：如果未启用 API 权限 'Address Override'，则必须在仪表板中设置地址，否则付款可能失败。";

$_['info_icon'] = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="ms-1 bi bi-info-circle" viewBox="0 0 16 16">
                                        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                                        <path d="m8.93 6.588-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533L8.93 6.588zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0z"/>
                                    </svg>';
