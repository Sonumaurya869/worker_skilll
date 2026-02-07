<?php
class ModelWkSkillCustHireHistory extends Model {

  public function getTotalHires(){
     $query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "worker_hire_history`");
        return $query->row['total'];
  }

   public function getHires($data=array()) {
  
        $start = 0;
        $limit = 100;


    $query = $this->db->query("SELECT wh.history_id,wh.customer_name, wh.worker_name,wh.skill, wh.status, wh.price_per_day, wh.created_at 
        FROM `" . DB_PREFIX . "worker_hire_history` wh  
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


