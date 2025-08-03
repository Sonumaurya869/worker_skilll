<?php
class ModelWkSkillCustHireHistory extends Model {

    public function getTotalHires() {
    $customer_id = (int)$this->customer->getId();

    $query = $this->db->query("SELECT COUNT(*) AS total 
        FROM `" . DB_PREFIX . "worker_hire_history` 
        WHERE customer_id = '" . $customer_id . "'");

    return (int)$query->row['total'];
}


    public function getHires($start = 0, $limit = 20) {
    if ($start < 0) {
        $start = 0;
    }

    if ($limit < 1) {
        $limit = 1;
    }

    $customer_id = (int)$this->customer->getId();

    $query = $this->db->query("SELECT wh.history_id, wh.worker_name,wh.skill, wh.status, wh.price_per_day, wh.created_at 
        FROM `" . DB_PREFIX . "worker_hire_history` wh 
        WHERE wh.customer_id = '" . $customer_id . "' 
        ORDER BY wh.history_id DESC 
        LIMIT " . (int)$start . ", " . (int)$limit);

    return $query->rows;
  }

 public function getHireInfo($hire_id) {
    $customer_id = (int)$this->customer->getId();
    $hire_id = (int)$hire_id;

    $query = $this->db->query("SELECT wh.*, c.email, c.date_added AS customer_registered_date
        FROM `" . DB_PREFIX . "worker_hire_history` wh
        LEFT JOIN `" . DB_PREFIX . "customer` c ON c.customer_id = wh.customer_id
        WHERE wh.history_id = '" . $hire_id . "' 
        AND wh.customer_id = '" . $customer_id . "' 
        LIMIT 1");

    if ($query->num_rows) {
        return $query->row;
    } else {
        return false;
    }
}




}