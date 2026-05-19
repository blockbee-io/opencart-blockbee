<?php

namespace Opencart\Catalog\Controller\Extension\BlockBee\Payment;

class BlockBee extends \Opencart\System\Engine\Controller
{
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
        $this->load->model('extension/blockbee/payment/blockbee');

        $this->session->data['blockbee_selected'] = $_POST['blockbee_coin'];
    }

    public function confirm()
    {
        // Library
        $this->load->language('extension/blockbee/payment/blockbee');
        require(DIR_EXTENSION . 'blockbee/system/library/blockbee.php');

        $json = array();
        $err_coin = '';

        if ($this->config->get('payment_blockbee_status')) {
            $this->load->model('checkout/order');
            $this->load->model('extension/blockbee/payment/blockbee');

            $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
            $cryptoFee = empty($this->session->data['blockbee_fee']) ? 0 : $this->session->data['blockbee_fee'];
            $total = $this->currency->format($order_info['total'] + $cryptoFee, $order_info['currency_code'], 1.00000, false);
            if (empty($this->request->post['blockbee_coin'])) {
                $err_coin = $this->language->get('error_coin');
            } else {

                $selected = $this->request->post['blockbee_coin'];

                $allowed = $this->config->get('payment_blockbee_cryptocurrencies');
                if (!is_array($allowed) || !in_array($selected, $allowed, true)) {
                    $err_coin = $this->language->get('error_coin');
                }

                $apiKey = $this->config->get('payment_blockbee_api_key');
                if (empty($apiKey)) {
                    $err_coin = $this->language->get('error_apikey');
                }
            }

            if (empty($err_coin) && !empty($apiKey)) {
                $disable_conversion = $this->config->get('payment_blockbee_disable_conversion');
                $qr_code_size = $this->config->get('payment_blockbee_qrcode_size');
                $info = \Opencart\Extension\BlockBee\System\Library\BlockBeeHelper::get_info($selected, false, $apiKey);
                $minTx = floatval($info->minimum_transaction_coin);

                $cryptoTotal = \Opencart\Extension\BlockBee\System\Library\BlockBeeHelper::get_conversion($order_info['currency_code'], $selected, $total, $disable_conversion, $apiKey);

                $callbackUrl = $this->url->link('extension/blockbee/payment/blockbee|callback', 'order_id=' . $this->session->data['order_id'], true);
                $callbackUrl = str_replace('&amp;', '&', $callbackUrl);

                $helper = new \Opencart\Extension\BlockBee\System\Library\BlockBeeHelper($selected, $apiKey, $callbackUrl, [], true);
                $addressIn = $helper->get_address();
                if (!isset($addressIn)) {
                    $err_coin = $this->language->get('error_adress');
                } else {
                    if (($cryptoTotal < $minTx)) {
                        $err_coin = $this->language->get('value_minim') . ' ' . $minTx . ' ' . strtoupper($selected);
                    }
                }

                if (empty($err_coin)) {
                    $qrCodeDataValue = $helper->get_qrcode($cryptoTotal, $qr_code_size);
                    $qrCodeData = $helper->get_qrcode('', $qr_code_size);
                    $paymentURL = $this->url->link('extension/blockbee/payment/blockbee|pay', 'order_id=' . $this->session->data['order_id'], true);

                    $paymentData = [
                        'blockbee_fee' => $cryptoFee,
                        'blockbee_address' => $addressIn,
                        'blockbee_total' => $cryptoTotal,
                        'blockbee_total_fiat' => $total,
                        'blockbee_currency' => $selected,
                        'blockbee_qrcode_value' => $qrCodeDataValue['qr_code'],
                        'blockbee_qrcode' => $qrCodeData['qr_code'],
                        'blockbee_last_price_update' => time(),
                        'blockbee_order_timestamp' => time(),
                        'blockbee_canceled' => '0',
                        'blockbee_min' => $minTx,
                        'blockbee_history' => json_encode([]),
                        'blockbee_payment_url' => $paymentURL
                    ];

                    $paymentData = json_encode($paymentData);
                    $this->model_extension_blockbee_payment_blockbee->addPaymentData($this->session->data['order_id'], $paymentData);

                    $this->model_checkout_order->addHistory($this->session->data['order_id'], $this->config->get('payment_blockbee_order_status_id'), '', true);

                    $this->sendPaymentInstructionsEmail($order_info, json_decode($paymentData, true), $paymentURL);

                    $json['redirect'] = $this->url->link('extension/blockbee/payment/blockbee|pay', 'order_id=' . $this->session->data['order_id'], true);
                } else {
                    $json['error']['warning'] = sprintf($this->language->get('error_payment'), $err_coin);
                }
            } else {
                $json['error']['warning'] = sprintf($this->language->get('error_payment'), $err_coin);
            }
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

        $order = $this->isBlockbeeOrder();

        if (!$order) {
            $this->response->redirect($this->url->link('common/home', '', true));
        }

        $this->load->model('extension/blockbee/payment/blockbee');
        $this->load->model('localisation/currency');

        $metaData = $this->model_extension_blockbee_payment_blockbee->getPaymentData($order['order_id']);

        if (!empty($metaData)) {
            $metaData = json_decode($metaData, true);
        }

        $total = $metaData['blockbee_total_fiat'];
        $currencySymbolLeft = $this->model_localisation_currency->getCurrencies()[$order['currency_code']]['symbol_left'];
        $currencySymbolRight = $this->model_localisation_currency->getCurrencies()[$order['currency_code']]['symbol_right'];

        $ajaxUrl = $this->url->link('extension/blockbee/payment/blockbee|status', 'order_id=' . $order['order_id'], true);
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
        return $this->response->redirect($this->url->link('extension/blockbee/payment/blockbee|pay', 'order_id=' . $order['order_id'], true));
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

        $order = $this->isBlockbeeOrder(true);

        if (!$order) {
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

        $counter_calc = (int)$metaData['blockbee_last_price_update'] + (int)$this->config->get('payment_blockbee_refresh_values') - time();
        if (!$this->isOrderPaid($order) && $counter_calc <= 0) {
            $this->cron(false);
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
            'counter' => (string)$counter_calc,
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

        $order_timeout = (int) $this->config->get('payment_blockbee_order_cancelation_timeout');
        $value_refresh = (int) $this->config->get('payment_blockbee_refresh_values');
        $qrcode_size = (int) $this->config->get('payment_blockbee_qrcode_size');

        $apiKey = $this->config->get('payment_blockbee_api_key');

        $response = $this->response->setOutput(json_encode(['status' => 'ok']));

        if ($order_timeout === 0 && $value_refresh === 0) {
            return $response;
        }

        $orders = $this->model_extension_blockbee_payment_blockbee->getOrders();

        if (empty($orders)) {
            return $response;
        }

        foreach ($orders as $order) {

            $order_id = $order['order_id'];

            $currency = $order['currency_code'];

            $metaData = json_decode($this->model_extension_blockbee_payment_blockbee->getPaymentData($order['order_id']), true);

            if (!empty($metaData['blockbee_last_price_update'])) {
                $last_price_update = $metaData['blockbee_last_price_update'];

                $history = json_decode($metaData['blockbee_history'], true);

                $min_tx = floatval($metaData['blockbee_min']);

                $calc = \Opencart\Extension\BlockBee\System\Library\BlockBeeHelper::calc_order($history, $metaData['blockbee_total'], floatval($metaData['blockbee_total_fiat']));

                $remaining = $calc['remaining'];
                $remaining_pending = $calc['remaining_pending'];
                $already_paid = $calc['already_paid'];

                if ($value_refresh !== 0 && $last_price_update + $value_refresh <= time()) {
                    if ($remaining === $remaining_pending) {
                        $blockbee_coin = $metaData['blockbee_currency'];

                        $crypto_total = \Opencart\Extension\BlockBee\System\Library\BlockBeeHelper::get_conversion($currency, $blockbee_coin, $metaData['blockbee_total_fiat'], $this->config->get('payment_blockbee_disable_conversion'), $this->config->get('payment_blockbee_api_key'));

                        $this->model_extension_blockbee_payment_blockbee->updatePaymentData($order_id, 'blockbee_total', $crypto_total);

                        $calc_cron = \Opencart\Extension\BlockBee\System\Library\BlockBeeHelper::calc_order($history, $crypto_total, $metaData['blockbee_total_fiat']);

                        $crypto_remaining_total = $calc_cron['remaining_pending'];

                        if ($remaining_pending <= $min_tx && $remaining_pending > 0) {
                            $qr_code_data_value = \Opencart\Extension\BlockBee\System\Library\BlockBeeHelper::get_static_qrcode($metaData['blockbee_address'], $blockbee_coin, $min_tx, $apiKey, $qrcode_size);
                        } else {
                            $qr_code_data_value = \Opencart\Extension\BlockBee\System\Library\BlockBeeHelper::get_static_qrcode($metaData['blockbee_address'], $blockbee_coin, $crypto_remaining_total, $apiKey, $qrcode_size);
                        }

                        $this->model_extension_blockbee_payment_blockbee->updatePaymentData($order_id, 'blockbee_qrcode_value', $qr_code_data_value['qr_code']);
                    }

                    $this->model_extension_blockbee_payment_blockbee->updatePaymentData($order_id, 'blockbee_last_price_update', time());
                }

                // Compute age via the database's clock to avoid PHP/MariaDB
                // timezone mismatches. `age_seconds` comes pre-computed in getOrders().
                $age_seconds = isset($order['age_seconds']) ? (int)$order['age_seconds'] : (time() - strtotime($order['date_added']));
                if ($order_timeout !== 0 && $age_seconds >= $order_timeout && $already_paid <= 0) {
                    $this->model_checkout_order->addHistory($order['order_id'], 7);
                    $this->model_extension_blockbee_payment_blockbee->updatePaymentData($order_id, 'blockbee_canceled', '1');
                }
            }
        }

        return $response;
    }

    public function callback()
    {
        // Library
        require(DIR_EXTENSION . 'blockbee/system/library/blockbee.php');

        $this->load->model('extension/blockbee/payment/blockbee');

        // Verify webhook signature before touching any state.
        // https://docs.blockbee.io/webhooks/verify-webhook-signature
        $pubkey = $this->cache->get('blockbee.pubkey');
        if (empty($pubkey)) {
            $pubkey = \Opencart\Extension\BlockBee\System\Library\BlockBeeHelper::fetch_pubkey();
            if (!empty($pubkey)) {
                $this->cache->set('blockbee.pubkey', $pubkey);
            }
        }

        $signature = $_SERVER['HTTP_X_CA_SIGNATURE'] ?? '';
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $scheme = $_SERVER['HTTP_X_FORWARDED_PROTO'] ?? $scheme;
        $host = $_SERVER['HTTP_X_FORWARDED_HOST'] ?? ($_SERVER['HTTP_HOST'] ?? '');
        $signed_url = $scheme . '://' . $host . ($_SERVER['REQUEST_URI'] ?? '');

        if (empty($pubkey) || !\Opencart\Extension\BlockBee\System\Library\BlockBeeHelper::verify_signature($signed_url, $signature, $pubkey)) {
            http_response_code(403);
            die('invalid signature');
        }

        $data = \Opencart\Extension\BlockBee\System\Library\BlockBeeHelper::process_callback($_GET);

        $this->load->model('checkout/order');

        $apiKey = $this->config->get('payment_blockbee_api_key');

        $order = $this->model_checkout_order->getOrder((int)$data['order_id']);

        $metaData = json_decode($this->model_extension_blockbee_payment_blockbee->getPaymentData($order['order_id']), true);

        if ($data["coin"] !== $metaData['blockbee_currency']) {
            die("*ok*");
        }

        if ($this->isOrderPaid($order)) {
            die("*ok*");
        }

        $disable_conversion = $this->config->get('payment_blockbee_disable_conversion');

        $qrcode_size = $this->config->get('payment_blockbee_qrcode_size');

        $paid = $data['value_coin'];

        $min_tx = floatval($metaData['blockbee_min']);

        $history = json_decode($metaData['blockbee_history'], true);

        if (empty($history[$data['uuid']])) {
            $fiat_conversion = \Opencart\Extension\BlockBee\System\Library\BlockBeeHelper::get_conversion($metaData['blockbee_currency'], $order['currency_code'], $paid, $disable_conversion, $apiKey);

            $history[$data['uuid']] = [
                'timestamp' => time(),
                'value_paid' => \Opencart\Extension\BlockBee\System\Library\BlockBeeHelper::sig_fig($paid, 6),
                'value_paid_fiat' => $fiat_conversion,
                'pending' => $data['pending']
            ];
        } else {
            $history[$data['uuid']]['pending'] = $data['pending'];
        }

        $this->model_extension_blockbee_payment_blockbee->updatePaymentData($order['order_id'], 'blockbee_history', json_encode($history));

        $metaData = json_decode($this->model_extension_blockbee_payment_blockbee->getPaymentData($order['order_id']), true);

        $history = json_decode($metaData['blockbee_history'], true);

        $calc = \Opencart\Extension\BlockBee\System\Library\BlockBeeHelper::calc_order($history, $metaData['blockbee_total'], $metaData['blockbee_total_fiat']);

        $remaining = $calc['remaining'];
        $remaining_pending = $calc['remaining_pending'];

        if ($remaining_pending <= 0) {
            if ($remaining <= 0) {
                $processing_state = 2;
                $this->model_checkout_order->addHistory($order['order_id'], $processing_state);
                $this->model_extension_blockbee_payment_blockbee->updatePaymentData($order['order_id'], 'blockbee_txid', $data['txid_in']);
            }
            die('*ok*');
        }

        if ($remaining_pending <= $min_tx) {
            $qrcode_conv = \Opencart\Extension\BlockBee\System\Library\BlockBeeHelper::get_static_qrcode($metaData['blockbee_address'], $metaData['blockbee_currency'], $min_tx, $apiKey, $qrcode_size)['qr_code'];
        } else {
            $qrcode_conv = \Opencart\Extension\BlockBee\System\Library\BlockBeeHelper::get_static_qrcode($metaData['blockbee_address'], $metaData['blockbee_currency'], $remaining_pending, $apiKey, $qrcode_size)['qr_code'];
        }

        $this->model_extension_blockbee_payment_blockbee->updatePaymentData($order['order_id'], 'blockbee_qrcode_value', $qrcode_conv);

        die("*ok*");
    }

    function order_pay_button(&$route, &$data, &$output)
    {
        $order_id = $this->request->get['order_id'];

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
            $data['continue'] = $orderObj->blockbee_payment_url;
        }
    }
}
