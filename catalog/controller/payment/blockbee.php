<?php

namespace Opencart\Catalog\Controller\Extension\BlockBee\Payment;

class BlockBee extends \Opencart\System\Engine\Controller
{
    /**
     * Authorize a customer view of an order's payment state.
     * Self-sufficient — loads the payment model itself so it is safe to call
     * before the caller has loaded it (status() previously loaded it AFTER the gate).
     */
    private function authorizeOrderAccess(array $order): bool
    {
        $order_id = (int)($order['order_id'] ?? 0);
        if ($order_id <= 0) {
            return false;
        }
        $this->load->model('extension/blockbee/payment/blockbee');

        // (a) same-session owner: confirm->pay redirect + live pay-page polling.
        if ((int)($this->session->data['order_id'] ?? 0) === $order_id) {
            return true;
        }
        // (a') logged-in customer owns the order. In OC4 $this->customer is a
        // registry magic-property, so isset() is unreliable — call isLogged() directly.
        $customer_id = (int)($order['customer_id'] ?? 0);
        if ($customer_id > 0 && $this->customer->isLogged() && (int)$this->customer->getId() === $customer_id) {
            return true;
        }
        // (b) per-order access token: guest checkout / email link / account button / new session.
        $token = (string)($this->request->get['token'] ?? '');
        if ($token !== '') {
            $meta = json_decode((string)$this->model_extension_blockbee_payment_blockbee->getPaymentData($order_id), true);
            $stored = is_array($meta) ? (string)($meta['blockbee_token'] ?? '') : '';
            if ($stored !== '' && hash_equals($stored, $token)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Gate the public |cron route.
     * CLI always; a configured secret always; loopback ONLY when the request
     * did not arrive through a proxy (no forwarding headers).
     */
    private function isCronAuthorized(): bool
    {
        if (PHP_SAPI === 'cli' || php_sapi_name() === 'cli') {
            return true;
        }
        // Configured secret works everywhere (including behind proxies).
        $secret = (string)$this->config->get('payment_blockbee_cron_secret');
        if ($secret !== '') {
            $provided = (string)($this->request->get['secret'] ?? '');
            if ($provided !== '' && hash_equals($secret, $provided)) {
                return true;
            }
        }
        // Trust loopback ONLY when the request did NOT arrive through a proxy.
        // Behind a same-host reverse proxy REMOTE_ADDR is loopback for ALL external
        // traffic, so require the absence of forwarding headers.
        $behind_proxy = !empty($_SERVER['HTTP_X_FORWARDED_FOR'])
            || !empty($_SERVER['HTTP_X_FORWARDED_HOST'])
            || !empty($_SERVER['HTTP_X_REAL_IP'])
            || !empty($_SERVER['HTTP_FORWARDED']);
        $remote = $_SERVER['REMOTE_ADDR'] ?? '';
        if (!$behind_proxy && in_array($remote, ['127.0.0.1', '::1', '::ffff:127.0.0.1'], true)) {
            return true;
        }
        return false;
    }

    /**
     * Per-order refresh + cancel (extracted from the old cron loop).
     * Requires the library to be loaded by the caller.
     */
    private function refreshOrder(array $order): void
    {
        $order_timeout = (int)$this->config->get('payment_blockbee_order_cancelation_timeout');
        $value_refresh = (int)$this->config->get('payment_blockbee_refresh_values');
        $qrcode_size   = (int)$this->config->get('payment_blockbee_qrcode_size');
        $apiKey        = $this->config->get('payment_blockbee_api_key');
        $lib           = '\\Opencart\\Extension\\BlockBee\\System\\Library\\BlockBeeHelper';

        if ($order_timeout === 0 && $value_refresh === 0) {
            return;
        }

        $order_id = (int)$order['order_id'];
        $currency = $order['currency_code'];
        $metaData = json_decode($this->model_extension_blockbee_payment_blockbee->getPaymentData($order_id), true);
        if (empty($metaData['blockbee_last_price_update'])) {
            return;
        }

        $last_price_update = $metaData['blockbee_last_price_update'];
        $history = json_decode($metaData['blockbee_history'], true) ?: [];
        $min_tx  = floatval($metaData['blockbee_min']);

        $calc = $lib::calc_order($history, $metaData['blockbee_total'], floatval($metaData['blockbee_total_fiat']));
        $remaining         = $calc['remaining'];
        $remaining_pending = $calc['remaining_pending'];
        $already_paid      = $calc['already_paid'];

        if ($value_refresh !== 0 && $last_price_update + $value_refresh <= time()) {
            if ($remaining === $remaining_pending) {
                $blockbee_coin = $metaData['blockbee_currency'];
                $crypto_total  = $lib::get_conversion($currency, $blockbee_coin, $metaData['blockbee_total_fiat'], $this->config->get('payment_blockbee_disable_conversion'), $apiKey);

                // Never persist a null/non-positive re-priced total; keep prior good value.
                if ($lib::is_positive_number($crypto_total)) {
                    $this->model_extension_blockbee_payment_blockbee->updatePaymentData($order_id, 'blockbee_total', $crypto_total);
                    $calc_cron = $lib::calc_order($history, $crypto_total, $metaData['blockbee_total_fiat']);
                    $crypto_remaining_total = $calc_cron['remaining_pending'];
                    if ($remaining_pending <= $min_tx && $remaining_pending > 0) {
                        $qr = $lib::get_static_qrcode($metaData['blockbee_address'], $blockbee_coin, $min_tx, $apiKey, $qrcode_size);
                    } else {
                        $qr = $lib::get_static_qrcode($metaData['blockbee_address'], $blockbee_coin, $crypto_remaining_total, $apiKey, $qrcode_size);
                    }
                    if (is_array($qr) && isset($qr['qr_code'])) {
                        $this->model_extension_blockbee_payment_blockbee->updatePaymentData($order_id, 'blockbee_qrcode_value', $qr['qr_code']);
                    }
                }
            }
            $this->model_extension_blockbee_payment_blockbee->updatePaymentData($order_id, 'blockbee_last_price_update', time());
        }

        $age_seconds = isset($order['age_seconds']) ? (int)$order['age_seconds'] : (time() - strtotime($order['date_added']));
        if ($order_timeout !== 0 && $age_seconds >= $order_timeout && $already_paid <= 0) {
            $this->model_checkout_order->addHistory($order_id, 7);
            $this->model_extension_blockbee_payment_blockbee->updatePaymentData($order_id, 'blockbee_canceled', '1');
        }
    }

    public function index(): string
    {
        if ($this->config->get('payment_blockbee_status')) {
            // Library
            require(DIR_EXTENSION . 'blockbee/system/library/blockbee.php');

            $this->load->language('extension/blockbee/payment/blockbee');
            $this->load->model('extension/blockbee/payment/blockbee');
            $this->load->model('localisation/country');
            $this->load->model('checkout/order');

            $data['title'] = $this->config->get('payment_blockbee_title');

            $data['cryptocurrencies'] = array();

            $order = $this->model_checkout_order->getOrder($this->session->data['order_id']);

            $order_total = floatval($order['total']);

            $apiKey = $this->config->get('payment_blockbee_api_key');

            foreach ($this->config->get('payment_blockbee_cryptocurrencies') as $selected) {
                foreach (json_decode(html_entity_decode($this->config->get('payment_blockbee_cryptocurrencies_array_cache'), ENT_QUOTES | ENT_HTML5, 'UTF-8'), true) as $token => $coin) {
                    if ($selected === $token) {
                        $data['cryptocurrencies'] += [
                            $token => $coin,
                        ];
                    }
                }
            }

            // Fee
            $fee = $this->config->get('payment_blockbee_fees');
            $blockchain_fee = $this->config->get('payment_blockbee_blockchain_fees');
            $currency = $order['currency_code'];
            $currencySymbolLeft = $this->model_localisation_currency->getCurrencies()[$order['currency_code']]['symbol_left'];
            $currencySymbolRight = $this->model_localisation_currency->getCurrencies()[$order['currency_code']]['symbol_right'];
            $data['symbol_left'] = $currencySymbolLeft;
            $data['symbol_right'] = $currencySymbolRight;
            $selected = $this->session->data['blockbee_selected'] ?? '';
            $blockbeeFee = 0;

            if ($selected) {
                if ($fee !== 0) {
                    $blockbeeFee += floatval($fee) * $order_total;
                }

                if ($blockchain_fee) {
                    $estimate = \Opencart\Extension\BlockBee\System\Library\BlockBeeHelper::get_estimate($this->session->data['blockbee_selected'], $apiKey);
                    if (is_object($estimate) && isset($estimate->$currency)) {
                        $blockbeeFee += floatval($estimate->$currency);
                    } elseif (is_object($estimate) && isset($estimate->USD)) {
                        $blockbeeFee += floatval($this->currency->convert($estimate->USD, 'USD', $currency));
                    }
                }
            }

            $data['fee'] = $fee;
            $data['blockchain_fee'] = $blockchain_fee;
            $data['blockbee_fee'] = $this->currency->format($blockbeeFee, $currency, 1.00000, false);
            $data['total'] = $this->currency->format($order_total + $blockbeeFee, $currency, 1.00000, false);
            $data['language'] = $this->config->get('config_language');
            $data['selected'] = $selected;

            $this->session->data['blockbee_fee'] = round($blockbeeFee, 2);

            $this->load->model('checkout/order');

            return $this->load->view('extension/blockbee/payment/blockbee', $data);
        }
        return false;
    }

    public function sel_crypto()
    {
        // Validate the posted coin against the configured allow-list
        // before it can reach the outbound API URL builder via index().
        $this->load->model('extension/blockbee/payment/blockbee');
        $selected = (string)($this->request->post['blockbee_coin'] ?? '');
        $allowed  = $this->config->get('payment_blockbee_cryptocurrencies');
        if (is_array($allowed) && in_array($selected, $allowed, true)) {
            $this->session->data['blockbee_selected'] = $selected;
        }
    }

    public function confirm()
    {
        $this->load->language('extension/blockbee/payment/blockbee');
        require(DIR_EXTENSION . 'blockbee/system/library/blockbee.php');
        $lib = '\\Opencart\\Extension\\BlockBee\\System\\Library\\BlockBeeHelper';

        $json = [];
        $err_coin = '';

        if (!$this->config->get('payment_blockbee_status')) {
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        $this->load->model('checkout/order');
        $this->load->model('extension/blockbee/payment/blockbee');

        $order_id = (int)($this->session->data['order_id'] ?? 0);
        $order_info = $this->model_checkout_order->getOrder($order_id);
        if (empty($order_info)) {
            $json['error']['warning'] = sprintf($this->language->get('error_payment'), $this->language->get('error_coin'));
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        // Idempotency: never clobber a paid / partially-paid order.
        $prevAddresses = [];
        $existingRaw = $this->model_extension_blockbee_payment_blockbee->getPaymentData($order_id);
        if (!empty($existingRaw)) {
            $em   = json_decode($existingRaw, true) ?: [];
            $hist = json_decode($em['blockbee_history'] ?? '[]', true) ?: [];
            $alreadyPaid = $this->isOrderPaid($order_info) || (!empty($em['blockbee_paid']) && (string)$em['blockbee_paid'] === '1');
            $hasPayments = is_array($hist) && count($hist) > 0;
            if ($alreadyPaid || $hasPayments) {
                $redir = $em['blockbee_payment_url']
                    ?? $this->url->link('extension/blockbee/payment/blockbee|pay', 'order_id=' . $order_id . '&token=' . ($em['blockbee_token'] ?? ''), true);
                $json['redirect'] = str_replace('&amp;', '&', $redir);
                $this->response->addHeader('Content-Type: application/json');
                $this->response->setOutput(json_encode($json));
                return;
            }
            // Unpaid + empty history => re-selection allowed. Preserve prior address(es)
            // so a payment already sent to a now-replaced address is still creditable.
            $prevAddresses = is_array($em['blockbee_prev_addresses'] ?? null) ? $em['blockbee_prev_addresses'] : [];
            if (!empty($em['blockbee_address'])) {
                $prevAddresses[] = (string)$em['blockbee_address'];
            }
            $prevAddresses = array_values(array_unique(array_filter($prevAddresses, 'strlen')));
        }

        $apiKey = $this->config->get('payment_blockbee_api_key');

        // Coin allow-list.
        if (empty($this->request->post['blockbee_coin'])) {
            $err_coin = $this->language->get('error_coin');
        } else {
            $selected = $this->request->post['blockbee_coin'];
            $allowed  = $this->config->get('payment_blockbee_cryptocurrencies');
            if (!is_array($allowed) || !in_array($selected, $allowed, true)) {
                $err_coin = $this->language->get('error_coin');
            }
            if (empty($apiKey)) {
                $err_coin = $this->language->get('error_apikey');
            }
        }

        if (empty($err_coin) && !empty($apiKey)) {
            $disable_conversion = $this->config->get('payment_blockbee_disable_conversion');
            $qr_code_size = $this->config->get('payment_blockbee_qrcode_size');
            $currency = $order_info['currency_code'];

            // Server-side fee (never trust session['blockbee_fee']).
            $order_total = floatval($order_info['total']);
            $fee = $this->config->get('payment_blockbee_fees');
            $blockchain_fee = $this->config->get('payment_blockbee_blockchain_fees');
            $blockbeeFee = 0;
            if ($fee !== 0) {
                $blockbeeFee += floatval($fee) * $order_total;
            }
            if ($blockchain_fee) {
                $estimate = $lib::get_estimate($selected, $apiKey);
                if (is_object($estimate) && isset($estimate->$currency)) {
                    $blockbeeFee += floatval($estimate->$currency);
                } elseif (is_object($estimate) && isset($estimate->USD)) {
                    $blockbeeFee += floatval($this->currency->convert($estimate->USD, 'USD', $currency));
                }
            }
            $cryptoFee = round($blockbeeFee, 2);
            $total = $this->currency->format($order_info['total'] + $cryptoFee, $currency, 1.00000, false);

            $info  = $lib::get_info($selected, false, $apiKey);
            $minTx = floatval($info->minimum_transaction_coin ?? 0);
            $cryptoTotal = $lib::get_conversion($currency, $selected, $total, $disable_conversion, $apiKey);

            // Refuse to create an order with a non-positive total.
            if (!$lib::is_positive_number($cryptoTotal)) {
                $err_coin = $this->language->get('error_conversion');
            }

            if (empty($err_coin)) {
                // Per-order nonce + access token (distinct CSPRNG secrets).
                $nonce = bin2hex(random_bytes(16));
                $token = bin2hex(random_bytes(16));

                // Registered callback URL — server-fixed base; decode &amp; so
                // the registered string, the signed string, and REQUEST_URI all agree.
                $callbackUrl = $this->url->link('extension/blockbee/payment/blockbee|callback', 'order_id=' . $order_id . '&nonce=' . $nonce, true);
                $callbackUrl = str_replace('&amp;', '&', $callbackUrl);

                $helper = new \Opencart\Extension\BlockBee\System\Library\BlockBeeHelper($selected, $apiKey, $callbackUrl, [], true);
                $addressIn = $helper->get_address();
                if (!isset($addressIn)) {
                    $err_coin = $this->language->get('error_adress');
                } elseif ($cryptoTotal < $minTx) {
                    $err_coin = $this->language->get('value_minim') . ' ' . $minTx . ' ' . strtoupper($selected);
                }

                if (empty($err_coin)) {
                    $qrCodeDataValue = $helper->get_qrcode($cryptoTotal, $qr_code_size);
                    $qrCodeData      = $helper->get_qrcode('', $qr_code_size);

                    // Token baked into pay URL (email + account button + redirect).
                    // Two params now => MUST decode &amp;.
                    $paymentURL = $this->url->link('extension/blockbee/payment/blockbee|pay', 'order_id=' . $order_id . '&token=' . $token, true);
                    $paymentURL = str_replace('&amp;', '&', $paymentURL);

                    $paymentData = [
                        'blockbee_fee'               => $cryptoFee,
                        'blockbee_address'           => $addressIn,
                        'blockbee_total'             => $cryptoTotal,
                        'blockbee_total_fiat'        => $total,
                        'blockbee_currency'          => $selected,
                        'blockbee_qrcode_value'      => $qrCodeDataValue['qr_code'],
                        'blockbee_qrcode'            => $qrCodeData['qr_code'],
                        'blockbee_last_price_update' => time(),
                        'blockbee_order_timestamp'   => time(),
                        'blockbee_canceled'          => '0',
                        'blockbee_min'               => $minTx,
                        'blockbee_history'           => json_encode([]),
                        'blockbee_payment_url'       => $paymentURL,
                        'blockbee_nonce'             => $nonce,          // provider-shared callback secret
                        'blockbee_token'             => $token,          // customer-facing access token
                        'blockbee_callback_url'      => $callbackUrl,    // server-fixed base
                        'blockbee_paid'              => '0',             // atomic paid marker
                        'blockbee_prev_addresses'    => $prevAddresses,  // re-selection safety
                    ];

                    $encoded = json_encode($paymentData);
                    $this->model_extension_blockbee_payment_blockbee->addPaymentData($order_id, $encoded);
                    $this->model_checkout_order->addHistory($order_id, $this->config->get('payment_blockbee_order_status_id'), '', true);
                    $this->sendPaymentInstructionsEmail($order_info, json_decode($encoded, true), $paymentURL);

                    $json['redirect'] = $paymentURL;   // already &amp;-decoded
                } else {
                    $json['error']['warning'] = sprintf($this->language->get('error_payment'), $err_coin);
                }
            } else {
                $json['error']['warning'] = sprintf($this->language->get('error_payment'), $err_coin);
            }
        } else {
            $json['error']['warning'] = sprintf($this->language->get('error_payment'), $err_coin);
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function isBlockbeeOrder($status = false)
    {
        $order = false;
        if (isset($this->request->get['order_id'])) {
            $order_id = (int)($this->request->get['order_id']);
        }

        if (isset($order_id)) {
            $this->load->model('checkout/order');
            $order = $this->model_checkout_order->getOrder($order_id);

            // OpenCart 4.x: getOrder() auto-decodes payment_method JSON.
            // The stored code is the full `<method>.<option>` form.
            $payment_code = $order['payment_method']['code'] ?? '';
            if ($order && $payment_code !== 'blockbee.blockbee') {
                $order = false;
            }

            if (!$status && $order && $order['order_status_id'] != $this->config->get('payment_blockbee_order_status_id')) {
                $order = false;
            }
        }
        return $order;
    }

    public function pay()
    {
        // In case the extension is disabled, do nothing
        if (!$this->config->get('payment_blockbee_status')) {
            $this->response->redirect($this->url->link('common/home', '', true));
        }

        // Library
        require(DIR_EXTENSION . 'blockbee/system/library/blockbee.php');

        $this->load->language('extension/blockbee/payment/blockbee');

        // Don't leak ?token= via the Referer header.
        $this->response->addHeader('Referrer-Policy: no-referrer');

        $this->load->model('extension/blockbee/payment/blockbee');

        $order = $this->isBlockbeeOrder();

        if (!$order || !$this->authorizeOrderAccess($order)) {
            $this->response->redirect($this->url->link('common/home', '', true));
        }

        $this->load->model('localisation/currency');

        $metaData = $this->model_extension_blockbee_payment_blockbee->getPaymentData($order['order_id']);

        if (!empty($metaData)) {
            $metaData = json_decode($metaData, true);
        }

        $total = $metaData['blockbee_total_fiat'];
        $currencySymbolLeft = $this->model_localisation_currency->getCurrencies()[$order['currency_code']]['symbol_left'];
        $currencySymbolRight = $this->model_localisation_currency->getCurrencies()[$order['currency_code']]['symbol_right'];

        // Bake the per-order token into the polling URL; two params => decode &amp;.
        $token   = is_array($metaData) ? (string)($metaData['blockbee_token'] ?? '') : '';
        $ajaxUrl = $this->url->link('extension/blockbee/payment/blockbee|status', 'order_id=' . $order['order_id'] . '&token=' . $token, true);
        $ajaxUrl = str_replace('&amp;', '&', $ajaxUrl);

        $allowed_to_value = array(
            'btc',
            'eth',
            'bch',
            'ltc',
            'miota',
            'xmr',
        );

        $cryptoCoin = $metaData['blockbee_currency'];

        $crypto_allowed_value = false;

        if (in_array($cryptoCoin, $allowed_to_value, true)) {
            $crypto_allowed_value = true;
        }

        $conversion_timer = ((int)$metaData['blockbee_last_price_update'] + (int)$this->config->get('payment_blockbee_refresh_values')) - time();
        $cancel_timer = (int)$metaData['blockbee_order_timestamp'] + (int)$this->config->get('payment_blockbee_order_cancelation_timeout') - time();

        $params = [
            'module_path' => HTTP_SERVER . 'extension/blockbee/catalog/view/image/',
            'header' => $this->load->controller('common/header'),
            'footer' => $this->load->controller('common/footer'),
            'currency_symbol_left' => $currencySymbolLeft,
            'currency_symbol_right' => $currencySymbolRight,
            'total' => floatval($total) < 0 ? 0 : floatval($total),
            'address_in' => $metaData['blockbee_address'],
            'crypto_coin' => $cryptoCoin,
            'crypto_value' => $metaData['blockbee_total'],
            'ajax_url' => $ajaxUrl,
            'qr_code_size' => $this->config->get('payment_blockbee_qrcode_size'),
            'qr_code' => $metaData['blockbee_qrcode'],
            'qr_code_value' => $metaData['blockbee_qrcode_value'],
            'show_branding' => $this->config->get('payment_blockbee_branding'),
            'branding_logo' => HTTP_SERVER . 'extension/blockbee/catalog/view/image/payment.png',
            'qr_code_setting' => $this->config->get('payment_blockbee_qrcode'),
            'order_cancelation_timeout' => $this->config->get('payment_blockbee_order_cancelation_timeout'),
            'refresh_value_interval' => $this->config->get('payment_blockbee_refresh_values'),
            'last_price_update' => $metaData['blockbee_last_price_update'],
            'min_tx' => $metaData['blockbee_min'],
            'min_tx_notice' => (string)$metaData['blockbee_min'] . ' ' . strtoupper($cryptoCoin),
            'color_scheme' => $this->config->get('payment_blockbee_color_scheme'),
            'conversion_timer' => (int)$conversion_timer,
            'cancel_timer' => (int)$cancel_timer,
            'crypto_allowed_value' => $crypto_allowed_value,
        ];

        return $this->response->setOutput($this->load->view('extension/blockbee/payment/blockbee_success', $params));
    }

    /**
     * Fallback redirect for customers landing on /checkout/success directly
     * (e.g., back-button or bookmarked link). Primary flow redirects from
     * confirm() straight to the pay page, so this rarely fires.
     */
    public function after_purchase(&$route, &$data, &$output)
    {
        if (!$this->config->get('payment_blockbee_status')) {
            return;
        }
        $order = $this->isBlockbeeOrder();
        if (!$order) {
            return;
        }
        $this->load->model('extension/blockbee/payment/blockbee');
        $meta = json_decode((string)$this->model_extension_blockbee_payment_blockbee->getPaymentData($order['order_id']), true);
        // Prefer the stored payment URL (already &amp;-decoded and carries the token).
        if (is_array($meta) && !empty($meta['blockbee_payment_url'])) {
            return $this->response->redirect($meta['blockbee_payment_url']);
        }
        $token = is_array($meta) ? (string)($meta['blockbee_token'] ?? '') : '';
        $url = $this->url->link('extension/blockbee/payment/blockbee|pay', 'order_id=' . $order['order_id'] . '&token=' . $token, true);
        return $this->response->redirect(str_replace('&amp;', '&', $url));   // two params => decode &amp;
    }

    /**
     * Sends the payment-instructions email with the link back to the pay page.
     * Called from confirm() after payment data is persisted.
     * Silently fails if SMTP is not configured.
     */
    private function sendPaymentInstructionsEmail(array $order, array $metaData, string $paymentURL): void
    {
        try {
            $mail = new \Opencart\System\Library\Mail($this->config->get('config_mail_engine'));
            $mail->parameter = $this->config->get('config_mail_parameter');
            $mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
            $mail->smtp_username = $this->config->get('config_mail_smtp_username');
            $mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
            $mail->smtp_port = $this->config->get('config_mail_smtp_port');
            $mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

            $coin = strtoupper($metaData['blockbee_currency'] ?? '');
            $subject = sprintf($this->language->get('order_subject'), $order['order_id'], $coin);

            $data = [
                'order_greeting' => sprintf($this->language->get('order_greeting'), $order['order_id'], $coin),
                'order_url'      => $paymentURL,
                'store'          => html_entity_decode($order['store_name'], ENT_QUOTES, 'UTF-8'),
                'store_url'      => $order['store_url'],
            ];

            $mail->setTo($order['email']);
            $mail->setFrom($this->config->get('config_email'));
            $mail->setSender(html_entity_decode($order['store_name'], ENT_QUOTES, 'UTF-8'));
            $mail->setSubject(html_entity_decode($subject, ENT_QUOTES, 'UTF-8'));
            $mail->setHtml($this->load->view('extension/blockbee/payment/blockbee_email', $data));
            $mail->send();
        } catch (\Exception $exception) {
            // Silent — SMTP not configured.
        }
    }


    public function isOrderPaid($order)
    {
        $configured = $this->config->get('payment_blockbee_paid_order_status_ids');
        $successOrderStatuses = is_array($configured) && !empty($configured)
            ? array_map('intval', $configured)
            : [2, 3, 15];

        return in_array((int)$order['order_status_id'], $successOrderStatuses, true) ? 1 : 0;
    }

    public function status()
    {

        // Library
        require(DIR_EXTENSION . 'blockbee/system/library/blockbee.php');

        // Don't leak ?token= via the Referer header.
        $this->response->addHeader('Referrer-Policy: no-referrer');

        $order = $this->isBlockbeeOrder(true);                 // keep true: paid/cancelled view needed

        if (!$order || !$this->authorizeOrderAccess($order)) { // authorizeOrderAccess loads the model
            return false;
        }

        $this->load->model('extension/blockbee/payment/blockbee');
        $this->load->model('localisation/currency');

        $metaData = $this->model_extension_blockbee_payment_blockbee->getPaymentData($order['order_id']);

        if (!empty($metaData)) {
            $metaData = json_decode($metaData, true);
        }

        $currencySymbolLeft = $this->model_localisation_currency->getCurrencies()[$order['currency_code']]['symbol_left'];
        $currencySymbolRight = $this->model_localisation_currency->getCurrencies()[$order['currency_code']]['symbol_right'];

        $showMinFee = 0;

        $history = json_decode($metaData['blockbee_history'], true);

        $calc = \Opencart\Extension\BlockBee\System\Library\BlockBeeHelper::calc_order($history, $metaData['blockbee_total'], $metaData['blockbee_total_fiat']);

        $already_paid = $calc['already_paid'];
        $already_paid_fiat = $calc['already_paid_fiat'] <= 0 ? 0 : $calc['already_paid_fiat'];

        $min_tx = floatval($metaData['blockbee_min']);

        $remaining_pending = $calc['remaining_pending'];
        $remaining_fiat = $calc['remaining_fiat'];

        $blockbee_pending = 0;
        if ($remaining_pending <= 0 && !$this->isOrderPaid($order)) {
            $blockbee_pending = 1;
        }

        // Refresh ONLY this already-authorized order — not a global sweep.
        $refresh_values = (int)$this->config->get('payment_blockbee_refresh_values');
        $counter_calc = (int)$metaData['blockbee_last_price_update'] + $refresh_values - time();
        if (!$this->isOrderPaid($order) && $counter_calc <= 0) {
            $this->refreshOrder($order);
            // refreshOrder() may have advanced last_price_update; recompute so the
            // client gets the fresh remaining time, not the stale (<= 0) value that
            // would make the countdown reset to ~0 instead of the full interval.
            $fresh = json_decode((string)$this->model_extension_blockbee_payment_blockbee->getPaymentData($order['order_id']), true);
            if (is_array($fresh) && isset($fresh['blockbee_last_price_update'])) {
                $counter_calc = (int)$fresh['blockbee_last_price_update'] + $refresh_values - time();
            }
        }

        if ($remaining_pending <= $min_tx && $remaining_pending > 0) {
            $remaining_pending = $min_tx;
            $showMinFee = 1;
        }

        $data = [
            'is_paid' => $this->isOrderPaid($order),
            'is_pending' => $blockbee_pending,
            'crypto_total' => floatval($metaData['blockbee_total']),
            'qr_code_value' => $metaData['blockbee_qrcode_value'],
            'canceled' => (int)$metaData['blockbee_canceled'],
            'remaining' => $remaining_pending < 0 ? 0 : $remaining_pending,
            'fiat_remaining' => $currencySymbolLeft . ($remaining_fiat < 0 ? 0 : $remaining_fiat) . $currencySymbolRight,
            'coin' => strtoupper($metaData['blockbee_currency']),
            'show_min_fee' => $showMinFee,
            'order_history' => $history,
            'already_paid' => $currencySymbolLeft . $already_paid . $currencySymbolRight,
            'already_paid_fiat' => floatval($already_paid_fiat) <= 0 ? 0 : floatval($already_paid_fiat),
            'counter' => (string)max(0, $counter_calc),
            'fiat_symbol_left' => $currencySymbolLeft,
            'fiat_symbol_right' => $currencySymbolRight,
        ];

        $this->response->addHeader('Content-Type: application/json');

        return $this->response->setOutput(json_encode($data));
    }

    public function cron($load_class = true)
    {
        if ($load_class) {
            // Library
            require(DIR_EXTENSION . 'blockbee/system/library/blockbee.php');
        }
        $this->load->model('checkout/order');
        $this->load->model('extension/blockbee/payment/blockbee');
        $this->response->addHeader('Content-Type: application/json');

        // Gate the public route (CLI / secret / proxy-safe loopback).
        if (!$this->isCronAuthorized()) {
            http_response_code(403);
            return $this->response->setOutput(json_encode(['status' => 'forbidden']));
        }

        $response = $this->response->setOutput(json_encode(['status' => 'ok']));

        $order_timeout = (int)$this->config->get('payment_blockbee_order_cancelation_timeout');
        $value_refresh = (int)$this->config->get('payment_blockbee_refresh_values');
        if ($order_timeout === 0 && $value_refresh === 0) {
            return $response;
        }

        $orders = $this->model_extension_blockbee_payment_blockbee->getOrders();
        if (empty($orders)) {
            return $response;
        }
        foreach ($orders as $order) {
            $this->refreshOrder($order);
        }
        return $response;
    }

    public function callback()
    {
        require(DIR_EXTENSION . 'blockbee/system/library/blockbee.php');
        $lib = '\\Opencart\\Extension\\BlockBee\\System\\Library\\BlockBeeHelper';

        $this->load->model('extension/blockbee/payment/blockbee');
        $this->load->model('checkout/order');

        $data = $lib::process_callback($_GET);

        // Resolve the order from the deposit address; fall back to the signed order_id.
        $bound = $this->model_extension_blockbee_payment_blockbee->getOrderByAddress((string)($data['address_in'] ?? ''));
        $order_id = !empty($bound['order_id']) ? (int)$bound['order_id'] : (int)($data['order_id'] ?? 0);

        $metaRaw = $this->model_extension_blockbee_payment_blockbee->getPaymentData($order_id);
        if (empty($metaRaw)) { http_response_code(403); die('unknown order'); }
        $metaData = json_decode($metaRaw, true);
        if (!is_array($metaData)) { http_response_code(403); die('bad order data'); }

        // Verify signature over a SERVER-FIXED reconstructed URL (no header trust).
        $pubkey = $this->cache->get('blockbee.pubkey');
        if (empty($pubkey)) {
            $pubkey = $lib::fetch_pubkey();
            if (!empty($pubkey)) { $this->cache->set('blockbee.pubkey', $pubkey); }
        }
        $base = !empty($metaData['blockbee_callback_url'])
            ? $metaData['blockbee_callback_url']
            : (defined('HTTPS_SERVER') ? HTTPS_SERVER : (defined('HTTP_SERVER') ? HTTP_SERVER : ''));
        $signed_url = $lib::build_signed_url($base, $_SERVER['REQUEST_URI'] ?? '');
        $signature  = $_SERVER['HTTP_X_CA_SIGNATURE'] ?? '';
        if (empty($pubkey) || $signed_url === '' || !$lib::verify_signature($signed_url, $signature, $pubkey)) {
            http_response_code(403); die('invalid signature');
        }

        // Per-order nonce, BEFORE any state change (empty==empty passes for legacy rows).
        if (!hash_equals((string)($metaData['blockbee_nonce'] ?? ''), (string)($data['nonce'] ?? ''))) {
            http_response_code(403); die('invalid nonce');
        }

        // Address binding (current or a prior re-selected address) + order_id consistency.
        $cb_addr     = strtolower(trim((string)($data['address_in'] ?? '')));
        $stored_addr = strtolower(trim((string)($metaData['blockbee_address'] ?? '')));
        $addr_ok = ($cb_addr !== '' && $stored_addr !== '' && hash_equals($stored_addr, $cb_addr));
        if (!$addr_ok && $cb_addr !== '' && !empty($metaData['blockbee_prev_addresses']) && is_array($metaData['blockbee_prev_addresses'])) {
            foreach ($metaData['blockbee_prev_addresses'] as $prev) {
                if (hash_equals(strtolower(trim((string)$prev)), $cb_addr)) { $addr_ok = true; break; }
            }
        }
        if (!$addr_ok) { http_response_code(403); die('address mismatch'); }
        if ((int)($data['order_id'] ?? 0) !== $order_id) {
            http_response_code(403); die('order mismatch');
        }

        $order = $this->model_checkout_order->getOrder($order_id);

        if (($data['coin'] ?? null) !== ($metaData['blockbee_currency'] ?? null)) {
            die('*ok*');
        }
        if ($this->isOrderPaid($order) || (!empty($metaData['blockbee_paid']) && (string)$metaData['blockbee_paid'] === '1')) {
            die('*ok*');
        }

        // Required total must be positive.
        if (!$lib::is_positive_number($metaData['blockbee_total'] ?? null)) {
            http_response_code(403); die('total not set');
        }

        $apiKey = $this->config->get('payment_blockbee_api_key');
        $disable_conversion = $this->config->get('payment_blockbee_disable_conversion');
        $qrcode_size = $this->config->get('payment_blockbee_qrcode_size');

        $paid   = $data['value_coin'];
        $min_tx = floatval($metaData['blockbee_min']);
        $uuid   = (string)($data['uuid'] ?? '');
        if ($uuid === '') { http_response_code(403); die('missing uuid'); }

        // Build the entry, then atomic per-uuid merge.
        $history = json_decode($metaData['blockbee_history'], true) ?: [];
        if (empty($history[$uuid])) {
            $fiat_conversion = $lib::get_conversion($metaData['blockbee_currency'], $order['currency_code'], $paid, $disable_conversion, $apiKey);
            $entry = [
                'timestamp'       => time(),
                'value_paid'      => $lib::sig_fig($paid, 6),
                'value_paid_fiat' => $fiat_conversion,
                'pending'         => $data['pending'],
            ];
        } else {
            $entry = ['pending' => $data['pending']];
        }
        $this->model_extension_blockbee_payment_blockbee->addHistoryEntry($order_id, $uuid, $entry);

        // Re-read merged state.
        $metaData = json_decode($this->model_extension_blockbee_payment_blockbee->getPaymentData($order_id), true);
        $history  = json_decode($metaData['blockbee_history'], true) ?: [];
        $calc = $lib::calc_order($history, $metaData['blockbee_total'], $metaData['blockbee_total_fiat']);
        $remaining         = $calc['remaining'];
        $remaining_pending = $calc['remaining_pending'];

        if ($remaining_pending <= 0) {
            if ($remaining <= 0) {
                // Mark paid at most once; release the claim if side effects fail.
                if ($this->model_extension_blockbee_payment_blockbee->claimPaidTransition($order_id)) {
                    try {
                        $this->model_checkout_order->addHistory($order_id, 2);
                        $this->model_extension_blockbee_payment_blockbee->updatePaymentData($order_id, 'blockbee_txid', $data['txid_in']);
                    } catch (\Throwable $e) {
                        $this->model_extension_blockbee_payment_blockbee->updatePaymentData($order_id, 'blockbee_paid', '0');
                        http_response_code(500); die('processing error');   // provider will retry
                    }
                }
            }
            die('*ok*');
        }

        if ($remaining_pending <= $min_tx) {
            $qrcode_conv = $lib::get_static_qrcode($metaData['blockbee_address'], $metaData['blockbee_currency'], $min_tx, $apiKey, $qrcode_size)['qr_code'];
        } else {
            $qrcode_conv = $lib::get_static_qrcode($metaData['blockbee_address'], $metaData['blockbee_currency'], $remaining_pending, $apiKey, $qrcode_size)['qr_code'];
        }
        $this->model_extension_blockbee_payment_blockbee->updatePaymentData($order_id, 'blockbee_qrcode_value', $qrcode_conv);
        die('*ok*');
    }

    function order_pay_button(&$route, &$data, &$output)
    {
        $order_id = (int)($this->request->get['order_id'] ?? 0);
        if ($order_id <= 0) {
            return;
        }

        $this->load->model('extension/blockbee/payment/blockbee');
        $this->load->model('checkout/order');

        $orderFetch = $this->model_checkout_order->getOrder($order_id);
        $order = $this->model_extension_blockbee_payment_blockbee->getOrder($order_id);

        $orderObj = isset($order['response']) ? json_decode($order['response']) : '';

        if (!$orderObj) {
            return;
        }

        if ((int)$orderObj->blockbee_canceled === 0 && isset($orderObj->blockbee_payment_url) && (int)$orderFetch['order_status_id'] === 1) {
            $data['button_continue'] = 'Pay Order';
            $data['continue'] = $orderObj->blockbee_payment_url;   // already &amp;-decoded + token
        }
    }
}
