<?php
namespace Opencart\Admin\Model\Extension\BlockBee\Payment;

class BlockBee extends \Opencart\System\Engine\Model {
    // Events from releases before 1.3.0. The admin tab now comes from the payment
    // controller's order() method, and the success-page redirect never fired.
    private const RETIRED_EVENTS = ['blockbee_order_info', 'blockbee_after_purchase'];

    public function install(): void {
        $this->syncEvents();

        // Create order db table
        $this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "blockbee_order` (
			  `order_id` INT(11) NOT NULL,
			  `address_in` VARCHAR(255) NOT NULL DEFAULT '',
			  `response` TEXT,
			  PRIMARY KEY (`order_id`),
			  UNIQUE KEY `idx_address_in` (`address_in`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

        $this->migrate();
    }

    public function uninstall(): void
    {
        $this->load->model('setting/event');

        foreach (array_merge(['blockbee_order_button', 'blockbee_order_tab'], self::RETIRED_EVENTS) as $code) {
            $this->model_setting_event->deleteEventByCode($code);
        }

        // `oc_blockbee_order` is intentionally not dropped — keeps order history
        // available if the extension is reinstalled later.
    }

    /**
     * Make the stored events match this OpenCart version. Safe to call on every
     * settings page load: it only writes when something is wrong.
     */
    public function syncEvents(): void
    {
        $this->load->model('setting/event');

        // OC 4.0.2+ splits event actions on '.', older versions on '|'. A stored
        // action with the wrong separator resolves to a missing class and core
        // skips it without an error.
        $separator = version_compare(VERSION, '4.0.2.0', '>=') ? '.' : '|';

        $wanted = [
            'blockbee_order_button' => ['catalog/view/account/order_info/before', 'order_pay_button'],
        ];

        // OC 4.0.0.0 builds the admin tab route without the extension folder, so
        // core never calls our order(). Add the tab from a view event there only.
        if (version_compare(VERSION, '4.0.1.0', '<')) {
            $wanted['blockbee_order_tab'] = ['admin/view/sale/order_info/before', 'order_tab'];
        }

        foreach (array_merge(self::RETIRED_EVENTS, ['blockbee_order_tab']) as $code) {
            if (!isset($wanted[$code]) && $this->model_setting_event->getEventByCode($code)) {
                $this->model_setting_event->deleteEventByCode($code);
            }
        }

        foreach ($wanted as $code => [$trigger, $method]) {
            $action = 'extension/blockbee/payment/blockbee' . $separator . $method;

            $event = $this->model_setting_event->getEventByCode($code);
            if ($event && ($event['action'] ?? '') === $action && ($event['trigger'] ?? '') === $trigger) {
                continue;
            }
            if ($event) {
                $this->model_setting_event->deleteEventByCode($code);
            }

            $this->addEvent($code, $trigger, $action);
        }
    }

    // OC 4.0.0.0 takes positional arguments; 4.0.1.0+ takes an array.
    private function addEvent(string $code, string $trigger, string $action): void
    {
        if (version_compare(VERSION, '4.0.1.0', '>=')) {
            $this->model_setting_event->addEvent(['code' => $code, 'description' => '', 'trigger' => $trigger, 'action' => $action, 'status' => 1, 'sort_order' => 1]);
        } else {
            $this->model_setting_event->addEvent($code, '', $trigger, $action, true, 1);
        }
    }

    /**
     * Idempotent schema upgrade for installs that ran an older version.
     * Adds the `address_in` column, backfills it from the JSON blob, and
     * adds the primary + unique keys if missing.
     */
    private function migrate(): void
    {
        $columns = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "blockbee_order` LIKE 'address_in'");
        if (!$columns->num_rows) {
            $this->db->query("ALTER TABLE `" . DB_PREFIX . "blockbee_order` ADD COLUMN `address_in` VARCHAR(255) NOT NULL DEFAULT ''");

            $rows = $this->db->query("SELECT `order_id`, `response` FROM `" . DB_PREFIX . "blockbee_order`");
            foreach ($rows->rows as $row) {
                $meta = json_decode($row['response'], true);
                $address = $meta['blockbee_address'] ?? '';
                if ($address !== '') {
                    $this->db->query("UPDATE `" . DB_PREFIX . "blockbee_order` SET `address_in` = '" . $this->db->escape($address) . "' WHERE `order_id` = " . (int)$row['order_id']);
                }
            }
        }

        $keys = $this->db->query("SHOW KEYS FROM `" . DB_PREFIX . "blockbee_order` WHERE Key_name = 'PRIMARY'");
        if (!$keys->num_rows) {
            $this->db->query("ALTER TABLE `" . DB_PREFIX . "blockbee_order` ADD PRIMARY KEY (`order_id`)");
        }

        $unique = $this->db->query("SHOW KEYS FROM `" . DB_PREFIX . "blockbee_order` WHERE Key_name = 'idx_address_in'");
        if (!$unique->num_rows) {
            // Empty address_in values would collide on the unique index; skip if any rows still have empty addresses.
            $empties = $this->db->query("SELECT COUNT(*) AS n FROM `" . DB_PREFIX . "blockbee_order` WHERE `address_in` = ''");
            if ((int)$empties->row['n'] <= 1) {
                $this->db->query("ALTER TABLE `" . DB_PREFIX . "blockbee_order` ADD UNIQUE KEY `idx_address_in` (`address_in`)");
            }
        }
    }

    public function getOrder($order_id): array
    {
        $qry = $this->db->query("SELECT * FROM `" . DB_PREFIX . "blockbee_order` WHERE `order_id` = '" . (int)$order_id . "' LIMIT 1");

        if ($qry->num_rows) {
            $order = $qry->row;
            return $order;
        } else {
            return [];
        }
    }
}