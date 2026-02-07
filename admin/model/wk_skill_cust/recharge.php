<?php
class ModelWkSkillCustRecharge extends Model {

    // Get total number of recharge plans (for pagination)
    public function getTotalRechargePlan() {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "wk_recharge_plan` WHERE plan_status = '1'");
        return $query->row['total'];
    }

    // Fetch recharge plans with filters, sort & pagination
    public function getRechargePlan($data = array()) {
        $sql = "SELECT * FROM `" . DB_PREFIX . "wk_recharge_plan` WHERE plan_status = '1'";

        // Sorting
        $sort_data = array(
            'plan_id',
            'plan_name',
            'duration',
            'plan_status'
        );

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY plan_id";
        }

        if (isset($data['order']) && ($data['order'] == 'DESC')) {
            $sql .= " DESC";
        } else {
            $sql .= " ASC";
        }

        // Pagination
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

       // Add new Recharge Plan
    public function addRechargePlan($data) {
        $this->db->query("INSERT INTO `" . DB_PREFIX . "wk_recharge_plan` SET 
            plan_name = '" . $this->db->escape($data['plan_name']) . "',
            duration = '" . (int)$data['duration'] . "',
            plan_status = '" . (isset($data['plan_status']) ? (int)$data['plan_status'] : 1) . "'
        ");
        
        return $this->db->getLastId();
    }

    // Edit existing Recharge Plan
    public function editRechargePlan($plan_id, $data) {
        $this->db->query("UPDATE `" . DB_PREFIX . "wk_recharge_plan` SET 
            plan_name = '" . $this->db->escape($data['plan_name']) . "',
            duration = '" . (int)$data['duration'] . "',
            plan_status = '" . (isset($data['plan_status']) ? (int)$data['plan_status'] : 1) . "'
            WHERE plan_id = '" . (int)$plan_id . "'
        ");
    }

    // Optional: Delete Plan
    public function deleteRechargePlan($plan_id) {
        $this->db->query("DELETE FROM `" . DB_PREFIX . "wk_recharge_plan` WHERE plan_id = '" . (int)$plan_id . "'");
    }

    // Fetch single plan
    public function getRechargePlanById($plan_id) {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "wk_recharge_plan` WHERE plan_id = '" . (int)$plan_id . "'");
        return $query->row;
    }

    // Add plans to a product
    public function addProductPlans($product_id, $plan_id) {
        $this->db->query("DELETE FROM `" . DB_PREFIX . "product_recharge_plan` WHERE product_id = '" . (int)$product_id . "'");
            $this->db->query("INSERT INTO `" . DB_PREFIX . "product_recharge_plan` SET 
                product_id = '" . (int)$product_id . "',
                plan_id = '" . (int)$plan_id . "'
            ");
    }

    // Get plans assigned to a product
    public function getProductPlans($product_id) {
        $query = $this->db->query("SELECT plan_id FROM `" . DB_PREFIX . "product_recharge_plan` WHERE product_id = '" . (int)$product_id . "'");
      
        if($query->row) {
          
           return $query->row['plan_id'];
        }
        return 0;
    }

    // Remove all plans from a product
    public function deleteProductPlans($product_id) {
        $this->db->query("DELETE FROM `" . DB_PREFIX . "product_recharge_plan` WHERE product_id = '" . (int)$product_id . "'");
    }

  public function getTotalRecharges($data = array()) {
    $sql = "SELECT COUNT(*) AS total 
            FROM `" . DB_PREFIX . "wk_worker_recharge` wr
            LEFT JOIN `" . DB_PREFIX . "customer` c ON (wr.customer_id = c.customer_id)
            LEFT JOIN `" . DB_PREFIX . "wk_recharge_plan` p ON (wr.plan_id = p.plan_id)
            WHERE 1";

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

    $query = $this->db->query($sql);

    return $query->row['total'];
}
  
public function getRecharges($data = array()) {
    $sql = "SELECT wr.*, c.firstname, c.lastname, c.email, p.plan_name 
            FROM `" . DB_PREFIX . "wk_worker_recharge` wr
            LEFT JOIN `" . DB_PREFIX . "customer` c ON (wr.customer_id = c.customer_id)
            LEFT JOIN `" . DB_PREFIX . "wk_recharge_plan` p ON (wr.plan_id = p.plan_id)
            WHERE 1";

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