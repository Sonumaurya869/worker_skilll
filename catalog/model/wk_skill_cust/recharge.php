<?php
class ModelWkSkillCustRecharge extends Model {
    
   public function addRecharge($orderId) {
    $customerId = $this->getCustomerIdByOrder($orderId);
    $productIds = $this->getProductsByOrder($orderId);
    foreach ($productIds as $productId) {
        $planIds = $this->getPlanIdsByProduct($productId);
        foreach ($planIds as $planId) {
            $plan = $this->getPlanDetails($planId);
            $lastRecharge = $this->getLastActiveRecharge($customerId, $planId);
            $dates = $this->calculateRechargeDates($plan['duration'], $lastRecharge);
            $status = $lastRecharge ? 'pending' : 'running';
            $this->insertRecharge($customerId,$orderId, $planId, $dates['start_date'], $dates['end_date'], $status);
        }
    }
   }


  private function getCustomerIdByOrder($orderId) {
    $query = $this->db->query("SELECT customer_id FROM `" . DB_PREFIX . "order` WHERE order_id = '" . (int)$orderId . "'");
    return $query->row['customer_id'] ?? null;
 }

  private function getProductsByOrder($orderId) {
    $query = $this->db->query("SELECT product_id FROM `" . DB_PREFIX . "order_product` WHERE order_id = '" . (int)$orderId . "'");
    return array_column($query->rows, 'product_id');
  }

  private function getPlanIdsByProduct($productId) {
    $query = $this->db->query("SELECT plan_id FROM `" . DB_PREFIX . "product_recharge_plan` WHERE product_id = '" . (int)$productId . "'");
    return array_column($query->rows, 'plan_id');
  }

  private function getPlanDetails($planId) {
    $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "wk_recharge_plan` WHERE plan_id = '" . (int)$planId . "'");
    return $query->row;
  }

  private function getLastActiveRecharge($customerId, $planId) {
    $query = $this->db->query("
        SELECT * FROM `" . DB_PREFIX . "wk_worker_recharge`
        WHERE customer_id = '" . (int)$customerId . "'
        ORDER BY recharge_id DESC
        LIMIT 1
    ");
    return $query->row ?? null;
  }


private function calculateRechargeDates($duration, $lastRecharge = null)
{
    date_default_timezone_set('Asia/Kolkata');

    if ($lastRecharge && ($lastRecharge['status'] == 1 || $lastRecharge['status'] == 0)) {
        $startDate = date('Y-m-d H:i:s', strtotime($lastRecharge['end_date']));
        $status = 'pending';
    } else {
        $startDate = date('Y-m-d H:i:s');
        $status = 'running';
    }

    $endDate = date('Y-m-d H:i:s', strtotime("+$duration days", strtotime($startDate)));

    return [
        'start_date' => $startDate,
        'end_date'   => $endDate,
        'status'     => $status
    ];
}



 private function insertRecharge($customerId, $orderId, $planId, $startDate, $endDate, $status) {
    // Map status to integer
    switch (strtolower($status)) {
        case 'running':
        case 'active':
            $statusValue = 1;
            break;
        case 'pending':
            $statusValue = 0;
            break;
        case 'expired':
            $statusValue = -1;
            break;
        default:
            $statusValue = 0; // default to pending
    }


    $this->db->query("
        INSERT INTO `" . DB_PREFIX . "wk_worker_recharge`
        SET customer_id = '" . (int)$customerId . "',
            order_id = '" . (int)$orderId . "',
            plan_id = '" . (int)$planId . "',
            start_date = '" . $this->db->escape($startDate) . "',
            end_date = '" . $this->db->escape($endDate) . "',
            status = '" . (int)$statusValue . "'
    ");
}

public function getTotalRecharge() {
    $customer_id = (int)$this->customer->getId();

    $query = $this->db->query("
        SELECT COUNT(*) AS total
        FROM " . DB_PREFIX . "wk_worker_recharge
        WHERE customer_id = '" . $customer_id . "'
    ");

    return $query->row['total'];
}

public function getRecharges($data = array()) {
    $customer_id = (int)$this->customer->getId();

    $sql = "SELECT wr.*, c.firstname, c.lastname, c.email, p.plan_name
        FROM `" . DB_PREFIX . "wk_worker_recharge` wr
        LEFT JOIN `" . DB_PREFIX . "customer` c 
            ON wr.customer_id = c.customer_id
        LEFT JOIN `" . DB_PREFIX . "wk_recharge_plan` p 
            ON wr.plan_id = p.plan_id
        WHERE wr.customer_id = '" . $customer_id . "'";

    // ==== Filters ====
    if (!empty($data['filter_name'])) {
        $sql .= " AND CONCAT(c.firstname, ' ', c.lastname) LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
    }

    if (!empty($data['filter_email'])) {
        $sql .= " AND c.email LIKE '%" . $this->db->escape($data['filter_email']) . "%'";
    }

    if (!empty($data['filter_plan'])) {
        $sql .= " AND p.plan_name LIKE '%" . $this->db->escape($data['filter_plan']) . "%'";
    }

    if (!empty($data['filter_start_date'])) {
        $sql .= " AND DATE(wr.start_date) >= DATE('" . $this->db->escape($data['filter_start_date']) . "')";
    }

    if (!empty($data['filter_end_date'])) {
        $sql .= " AND DATE(wr.end_date) <= DATE('" . $this->db->escape($data['filter_end_date']) . "')";
    }

    if (isset($data['filter_status']) && $data['filter_status'] !== '') {
        $sql .= " AND wr.status = '" . (int)$data['filter_status'] . "'";
    }

    // ==== Sorting ====
    $sort_data = array(
        'wr.recharge_id',
        'c.firstname',
        'c.email',
        'p.plan_name',
        'wr.start_date',
        'wr.end_date',
        'wr.status'
    );

    if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
        $sql .= " ORDER BY " . $data['sort'];
    } else {
        $sql .= " ORDER BY wr.recharge_id";
    }

    if (isset($data['order']) && ($data['order'] == 'DESC')) {
        $sql .= " DESC";
    } else {
        $sql .= " ASC";
    }

    // ==== Pagination ====
    if (isset($data['start']) || isset($data['limit'])) {
        if ($data['start'] < 0) {
            $data['start'] = 0;
        }
        if ($data['limit'] < 1) {
            $data['limit'] = 20;
        }

        $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
    }

    $query = $this->db->query($sql);

    return $query->rows;
}

public function getCustomerInfo($customer_id) {
    $query = $this->db->query("SELECT firstname, lastname, email FROM `" . DB_PREFIX . "customer` WHERE customer_id = '" . (int)$customer_id . "'");
    return $query->row;
}

public function getPlanInfo($plan_id) {
    $query = $this->db->query("SELECT plan_name FROM `" . DB_PREFIX . "wk_recharge_plan` WHERE plan_id = '" . (int)$plan_id . "'");
    return $query->row;
}

public function getRechargeDetails($recharge_id) {
    $query = $this->db->query("
        SELECT r.*, c.firstname, c.lastname, c.email, c.telephone, 
               o.order_id, o.date_added AS order_date, o.total AS order_total, o.payment_method, 
               p.plan_name, p.duration
        FROM " . DB_PREFIX . "wk_worker_recharge r
        LEFT JOIN " . DB_PREFIX . "customer c ON (r.customer_id = c.customer_id)
        LEFT JOIN " . DB_PREFIX . "order o ON (r.order_id = o.order_id)
        LEFT JOIN " . DB_PREFIX . "wk_recharge_plan p ON (r.plan_id = p.plan_id)
        WHERE r.recharge_id = '" . (int)$recharge_id . "'
    ");

    $current = $query->row;

    if (!$current) return [];

    $customer_id = (int)$current['customer_id'];

    // Base query for other recharges (same customer)
    $sql = "
        SELECT r.*, p.plan_name, p.duration
        FROM " . DB_PREFIX . "wk_worker_recharge r
        LEFT JOIN " . DB_PREFIX . "wk_recharge_plan p ON (r.plan_id = p.plan_id)
        WHERE r.customer_id = '" . $customer_id . "'
          AND r.recharge_id != '" . (int)$recharge_id . "'
        ORDER BY r.start_date DESC
    ";

    $recharges = $this->db->query($sql)->rows;

    $now = date('Y-m-d H:i:s');
    $related = [];

    foreach ($recharges as $r) {
        $is_active = ($r['status'] == 1 && $r['end_date'] >= $now);
        $is_pending = ($r['status'] == 0 && $r['start_date'] > $now);
        $is_expired = ($r['end_date'] < $now);

        // Logic separation
        if ($current['end_date'] < $now) {
            // Viewing expired recharge → show only expired
            if ($is_expired) $related[] = $r;
        } elseif ($current['status'] == 1) {
            // Viewing active recharge → show pending only
            if ($is_pending) $related[] = $r;
        } elseif ($current['status'] == 0) {
            // Viewing pending recharge → show active only
            if ($is_active) $related[] = $r;
        }
    }

    return [
        'current' => $current,
        'related' => $related
    ];
}





}