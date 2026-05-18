<?php
namespace Opencart\Admin\Model\Extension\BlockBee\Payment;

class BlockBee extends \Opencart\System\Engine\Model {

    public function install(): void {
        // Create events
        $this->load->model('setting/event');


        if (!$this->model_setting_event->getEventByCode('blockbee_order_info')) {
            $this->model_setting_event->addEvent(['code' => 'blockbee_order_info', 'description' => '', 'trigger' => 'admin/view/sale/order_info/before', 'action' => 'extension/blockbee/payment/blockbee|order_info', 'status' => 1, 'sort_order' => '1']);
        }

        if (!$this->model_setting_event->getEventByCode('blockbee_order_button')) {
            $this->model_setting_event->addEvent(['code' => 'blockbee_order_button', 'description' => '', 'trigger' => 'catalog/view/account/order_info/before', 'action' => 'extension/blockbee/payment/blockbee|order_pay_button', 'status' => 1, 'sort_order' => '1']);
        }

        if (!$this->model_setting_event->getEventByCode('blockbee_after_purchase')) {
            $this->model_setting_event->addEvent(['code' => 'blockbee_after_purchase', 'description' => '', 'trigger' => 'catalog/view/common/success/after', 'action' => 'extension/blockbee/payment/blockbee|after_purchase', 'status' => 1, 'sort_order' => '1']);
        }

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

        foreach (['blockbee_order_info', 'blockbee_order_button', 'blockbee_after_purchase'] as $code) {
            $this->model_setting_event->deleteEventByCode($code);
        }

        // `oc_blockbee_order` is intentionally not dropped — keeps order history
        // available if the extension is reinstalled later.
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