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

    $query = $this->db->query("
        SELECT wh.*, 
               c.email, 
               c.date_added AS customer_registered_date,
               he.end_day AS hire_end_date,
               he.total_cost
        FROM `" . DB_PREFIX . "worker_hire_history` wh
        LEFT JOIN `" . DB_PREFIX . "customer` c 
            ON c.customer_id = wh.customer_id
        LEFT JOIN `" . DB_PREFIX . "hire_end_dates` he 
            ON he.hire_id = wh.history_id
        WHERE wh.history_id = '" . $hire_id . "' 
          AND wh.customer_id = '" . $customer_id . "' 
        LIMIT 1
    ");

    if ($query->num_rows) {
        return $query->row;
    } else {
        return false;
    }
}


public function getHireDate($hire_id) {
       $customer_id = (int)$this->customer->getId();
       $hire_id = (int)$hire_id;

     $query = $this->db->query("SELECT wh.*
        FROM `" . DB_PREFIX . "worker_hire_history` wh
        WHERE wh.history_id = '" . $hire_id . "' 
        AND wh.customer_id = '" . $customer_id . "' 
        LIMIT 1");

    if ($query->num_rows) {
        return $query->row;
    } else {
        return false;
    }
}

public function updateHireEndDate($hire_id, $end_day) {
    $hire = $this->getHireDate($hire_id);
    $price_per_day  = (float)$hire['price_per_day'];
    $hire_date = $hire['created_at'];
    $start = new DateTime(date('Y-m-d', strtotime($hire_date))); // Remove time
    $end   = new DateTime($end_day);

    $interval = $start->diff($end);
    $days = $interval->days + 1;

    $total_cost = $days * $price_per_day;

    $query = $this->db->query("SELECT id FROM " . DB_PREFIX . "hire_end_dates WHERE hire_id = '" . (int)$hire_id . "'");

    if ($query->num_rows) {
        // Update
        $this->db->query("UPDATE " . DB_PREFIX . "hire_end_dates 
            SET end_day = '" . $this->db->escape($end_day) . "',
                total_cost = '" . (float)$total_cost . "'
            WHERE hire_id = '" . (int)$hire_id . "'");
    } else {
        // Insert
        $this->db->query("INSERT INTO " . DB_PREFIX . "hire_end_dates 
            SET hire_id = '" . (int)$hire_id . "',
                end_day = '" . $this->db->escape($end_day) . "',
                total_cost = '" . (float)$total_cost . "'");
    }

    return [
        'days' => $days,
        'total_cost' => $total_cost
    ];
}

public function getWorkerStatusList() {
   $query = $this->db->query("
    SELECT work_status_id, name 
    FROM " . DB_PREFIX . "work_status 
    WHERE language_id = '" . (int)$this->config->get('config_language_id') . "' 
    ORDER BY work_status_id ASC
   ");

    $statuses = $query->rows; // returns an array of all statuses

   return $statuses;
}




}