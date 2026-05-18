<?php
namespace Opencart\Admin\Controller\Extension\BlockBee\Payment;

use Opencart\System\Engine\Config;

class BlockBee extends \Opencart\System\Engine\Controller
{
    private $error = [];

    public function index()
    {
        require(DIR_EXTENSION . 'blockbee/system/library/blockbee.php');

        $this->load->language('extension/blockbee/payment/blockbee');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('extension/blockbee/payment/blockbee');

        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {

            $a = [];
            if (isset($_POST['payment_blockbee_cryptocurrencies'])) {
                foreach ($_POST['payment_blockbee_cryptocurrencies'] as $value) {
                    $a[$value] = $value;
                }
            }

            $this->request->post['payment_blockbee_cryptocurrencies'] = $a;

            $paid_statuses = [];
            if (isset($_POST['payment_blockbee_paid_order_status_ids'])) {
                foreach ($_POST['payment_blockbee_paid_order_status_ids'] as $value) {
                    $paid_statuses[] = (int)$value;
                }
            }
            $this->request->post['payment_blockbee_paid_order_status_ids'] = $paid_statuses;

            $this->model_setting_setting->editSetting('payment_blockbee', $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true));
        }

        $this->load->model('localisation/geo_zone');

        $data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

        $this->load->model('localisation/order_status');

        $orderStatuses = $this->model_localisation_order_status->getOrderStatuses();
        $orderStatusesFiltered = [];
        $orderStatusesIgnore = [
            'Canceled',
            'Canceled Reversal',
            'Chargeback',
            'Complete',
            'Denied',
            'Expired',
            'Failed',
            'Processed',
            'Processing',
            'Refunded',
            'Reversed',
            'Shipped',
            'Voided'
        ];
        foreach ($orderStatuses as $orderStatus) {
            if (!in_array($orderStatus['name'], $orderStatusesIgnore)) {
                $orderStatusesFiltered[] = $orderStatus;
            }
        }
        $data['order_statuses'] = $orderStatusesFiltered;
        $data['all_order_statuses'] = $orderStatuses;

        if (isset($this->request->post['payment_blockbee_paid_order_status_ids'])) {
            $data['payment_blockbee_paid_order_status_ids'] = $this->request->post['payment_blockbee_paid_order_status_ids'];
        } else {
            $saved_paid = $this->config->get('payment_blockbee_paid_order_status_ids');
            $data['payment_blockbee_paid_order_status_ids'] = is_array($saved_paid) ? $saved_paid : [2, 3, 15];
        }

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_extension'),
            'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/payment/blockbee', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['action'] = $this->url->link('extension/blockbee/payment/blockbee', 'user_token=' . $this->session->data['user_token']);
        $data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment');

        /**
         * Defining Cryptocurrencies
         */

        $supported_coins = \Opencart\Extension\BlockBee\System\Library\BlockBeeHelper::get_supported_coins();

        $data['payment_blockbee_cryptocurrencies_array'] = $supported_coins;

        if (isset($this->request->post['payment_blockbee_cryptocurrencies'])) {
            $data['payment_blockbee_cryptocurrencies'] = $this->request->post['payment_blockbee_cryptocurrencies'];
        } else {
            $data['payment_blockbee_cryptocurrencies'] = $this->config->get('payment_blockbee_cryptocurrencies');
        }

        if (isset($this->request->post['payment_blockbee_disable_conversion'])) {
            $data['payment_blockbee_disable_conversion'] = $this->request->post['payment_blockbee_disable_conversion'];
        } else {
            $data['payment_blockbee_disable_conversion'] = $this->config->get('payment_blockbee_disable_conversion');
        }

        if (isset($this->request->post['payment_blockbee_title'])) {
            $data['payment_blockbee_title'] = $this->request->post['payment_blockbee_title'];
        } else {
            $data['payment_blockbee_title'] = $this->config->get('payment_blockbee_title');
        }

        if (isset($this->request->post['payment_blockbee_api_key'])) {
            $data['payment_blockbee_api_key'] = $this->request->post['payment_blockbee_api_key'];
        } else {
            $data['payment_blockbee_api_key'] = $this->config->get('payment_blockbee_api_key');
        }

        if (isset($this->request->post['payment_blockbee_standard_geo_zone_id'])) {
            $data['payment_blockbee_standard_geo_zone_id'] = $this->request->post['payment_blockbee_standard_geo_zone_id'];
        } else {
            $data['payment_blockbee_standard_geo_zone_id'] = $this->config->get('payment_blockbee_standard_geo_zone_id');
        }

        if (isset($this->request->post['payment_blockbee_order_status_id'])) {
            $data['payment_blockbee_order_status_id'] = $this->request->post['payment_blockbee_order_status_id'];
        } else {
            $data['payment_blockbee_order_status_id'] = $this->config->get('payment_blockbee_order_status_id');
            if (!$data['payment_blockbee_order_status_id']) {
                $data['payment_blockbee_order_status_id'] = 1;
            }
        }

        if (isset($this->request->post['payment_blockbee_status'])) {
            $data['payment_blockbee_status'] = $this->request->post['payment_blockbee_status'];
        } else {
            $data['payment_blockbee_status'] = $this->config->get('payment_blockbee_status');
        }

        if (isset($this->request->post['payment_blockbee_blockchain_fees'])) {
            $data['payment_blockbee_blockchain_fees'] = $this->request->post['payment_blockbee_blockchain_fees'];
        } else {
            $data['payment_blockbee_blockchain_fees'] = $this->config->get('payment_blockbee_blockchain_fees');
        }

        if (isset($this->request->post['payment_blockbee_fees'])) {
            $data['payment_blockbee_fees'] = $this->request->post['payment_blockbee_fees'];
        } else {
            $data['payment_blockbee_fees'] = $this->config->get('payment_blockbee_fees');
        }

        if (isset($this->request->post['payment_blockbee_color_scheme'])) {
            $data['payment_blockbee_color_scheme'] = $this->request->post['payment_blockbee_color_scheme'];
        } else {
            $data['payment_blockbee_color_scheme'] = $this->config->get('payment_blockbee_color_scheme');
        }

        if (isset($this->request->post['payment_blockbee_refresh_values'])) {
            $data['payment_blockbee_refresh_values'] = $this->request->post['payment_blockbee_refresh_values'];
        } else {
            $data['payment_blockbee_refresh_values'] = $this->config->get('payment_blockbee_refresh_values');
        }

        if (isset($this->request->post['payment_blockbee_order_cancelation_timeout'])) {
            $data['payment_blockbee_order_cancelation_timeout'] = $this->request->post['payment_blockbee_order_cancelation_timeout'];
        } else {
            $data['payment_blockbee_order_cancelation_timeout'] = $this->config->get('payment_blockbee_order_cancelation_timeout');
        }

        if (isset($this->request->post['payment_blockbee_branding'])) {
            $data['payment_blockbee_branding'] = $this->request->post['payment_blockbee_branding'];
        } else {
            $data['payment_blockbee_branding'] = $this->config->get('payment_blockbee_branding');
        }

        if (isset($this->request->post['payment_blockbee_qrcode'])) {
            $data['payment_blockbee_qrcode'] = $this->request->post['payment_blockbee_qrcode'];
        } else {
            $data['payment_blockbee_qrcode'] = $this->config->get('payment_blockbee_qrcode');
        }

        if (isset($this->request->post['payment_blockbee_qrcode_default'])) {
            $data['payment_blockbee_qrcode_default'] = $this->request->post['payment_blockbee_qrcode_default'];
        } else {
            $data['payment_blockbee_qrcode_default'] = $this->config->get('payment_blockbee_qrcode_default');
        }

        if (isset($this->request->post['payment_blockbee_qrcode_size'])) {
            $data['payment_blockbee_qrcode_size'] = $this->request->post['payment_blockbee_qrcode_size'];
        } else {
            $data['payment_blockbee_qrcode_size'] = $this->config->get('payment_blockbee_qrcode_size');
        }

        if (isset($this->request->post['payment_blockbee_sort_order'])) {
            $data['payment_blockbee_sort_order'] = $this->request->post['payment_blockbee_sort_order'];
        } else {
            $data['payment_blockbee_sort_order'] = $this->config->get('payment_blockbee_sort_order');
        }

        $data['currency_warning'] = '';
        $store_currency = $this->config->get('config_currency');
        if (!empty($data['payment_blockbee_api_key']) && !empty($store_currency)) {
            $supported = $this->cache->get('blockbee.fiat_currencies');
            if (empty($supported) || !is_array($supported)) {
                $estimate = \Opencart\Extension\BlockBee\System\Library\BlockBeeHelper::get_estimate('btc', $data['payment_blockbee_api_key']);
                if (is_object($estimate)) {
                    $supported = array_keys(get_object_vars($estimate));
                    $this->cache->set('blockbee.fiat_currencies', $supported);
                }
            }
            if (is_array($supported) && !in_array($store_currency, $supported, true)) {
                $data['currency_warning'] = sprintf($this->language->get('warning_currency_unsupported'), $store_currency);
            }
        }

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/blockbee/payment/blockbee', $data));
    }

    public function order_info(&$route, &$data, &$output)
    {
        $order_id = $this->request->get['order_id'];
        $this->load->model('extension/blockbee/payment/blockbee');
        $order = $this->model_extension_blockbee_payment_blockbee->getOrder($order_id);
        if ($order) {
            $metaData = $order['response'];

            if (!empty($metaData)) {
                $metaData = json_decode($metaData, true);
                $fields = '';
                foreach ($metaData as $key => $val) {
                    if ($key === 'blockbee_qrcode_value' || $key === 'blockbee_qrcode') {
                        $fields .= '<tr><td>' . $key . '</td><td style="line-break: anywhere"><img width="100" class="img-fluid" src="data:image/png;base64,' . $val . '"/></td></tr>';
                    } else if ($key === 'blockbee_history') {
                        $history = json_decode($val);
                        $historyObj = '<table class="table table-bordered">';
                        foreach ($history as $h_key => $h_val) {
                            $historyObj .= '<tr><td colspan="2"><strong>UUID:</strong> ' . $h_key . '</td>';
                            foreach ($h_val as $hrow_key => $hrow_value) {
                                $historyObj .= '<tr><td>' . $hrow_key . '</td><td>' . $hrow_value . '</td>';
                            }
                            $historyObj .= '</tr>';
                        }
                        $historyObj .= '</table>';
                        $fields .= '<tr><td>' . $key . '</td><td>' . $historyObj . '</td></tr>';
                    } else if ($key === 'blockbee_last_price_update' || $key === 'blockbee_order_timestamp') {
                        $fields .= '<tr><td>' . $key . '</td><td style="line-break: anywhere">' . date('d-m-Y H:i:s', (int)$val) . '</td></tr>';
                    } else {
                        $fields .= '<tr><td>' . $key . '</td><td style="line-break: anywhere">' . $val . '</td></tr>';
                    }
                }

                if ($data['tabs'][0]['code'] === 'blockbee') {
                    $data['tabs'][0]['content'] = '<table style="font-size: 13px;" class="table table-bordered">' . $fields . '<table>';
                }
            }
        }
    }

    public function install(): void
    {
        $this->load->model('extension/blockbee/payment/blockbee');

        $this->model_extension_blockbee_payment_blockbee->install();
    }

    public function uninstall(): void
    {
        $this->load->model('extension/blockbee/payment/blockbee');

        $this->model_extension_blockbee_payment_blockbee->uninstall();
    }
}
