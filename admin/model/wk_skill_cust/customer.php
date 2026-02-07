<?php
class ModelWkSkillCustCustomer extends Model {
	public function addCustomer($data) {
	  $skill= $this->getSkill($data['occupation_id']);

      $this->db->query("INSERT INTO `" . DB_PREFIX . "worker_skill` SET 
        customer_id = '" . (int)$data['customer_id'] . "',
        occupation_id = '" . (int)$data['occupation_id'] . "',
        skill = '" . $this->db->escape($skill) . "',
        price_per_day = '" . (float)$data['price_per_day'] . "',
        date_of_birth = '" . $this->db->escape($data['date_of_birth']) . "',
        image = '" . $this->db->escape($data['image']) . "',
        wk_status = '" . $this->db->escape($data['wk_status']) . "',
        date_added = NOW()
     ");

	  $customer_id = $this->db->getLastId();

	    $this->db->query("INSERT INTO `" . DB_PREFIX . "worker_details` SET 
        customer_id = '" . (int)$data['customer_id'] . "',
        range_km = '" . (int)$data['range'] . "',
        experience = '" . (float)$data['experience'] . "',
        busy_date = '" . $data['busy_dates'] . "',
        description = '" . $this->db->escape($data['work_description']) . "'
       ");
		
		return $customer_id;
	}


	
	public function editSkill($skill_id, $data) {
		 $skill= $this->getSkill($data['occupation_id']);
         $this->db->query("UPDATE " . DB_PREFIX . "worker_skill SET occupation_id = '" . (int)$data['occupation_id'] . "', skill = '" . $this->db->escape($skill) . "', price_per_day = '" . (float)$data['price_per_day'] . "', date_of_birth = '" . $this->db->escape($data['date_of_birth']) . "', image = '" . $this->db->escape($data['image']) . "', wk_status = '" .$data['wk_status'] . "' WHERE customer_id = '" . (int)$skill_id . "'");
	
		  $details = $this->db->query("SELECT * FROM " . DB_PREFIX . "worker_details WHERE customer_id = '" . (int)$skill_id . "'");
 
   if (!empty($details->row)) {
     $this->db->query("UPDATE `" . DB_PREFIX . "worker_details` SET 
        range_km = '" . (int)$data['range'] . "',
        experience = '" . (float)$data['experience'] . "',
        busy_date = '" . $data['busy_dates'] . "',
        description = '" . $this->db->escape($data['work_description']) . "'
        WHERE customer_id = '" . (int)$skill_id . "'
      ");
    } else {
       $this->db->query("INSERT INTO `" . DB_PREFIX . "worker_details` SET 
        customer_id = '" . (int)$skill_id . "',
        range_km = '" . (int)$data['range'] . "',
        experience = '" . (float)$data['experience'] . "',
        busy_date = '" . $data['busy_dates'] . "',
        description = '" . $this->db->escape($data['work_description']) . "'
       ");
    }
  }


     public function getSkill($skill_id) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "wk_skill_cust WHERE id = '" . (int)$skill_id . "'");
        return $query->row['skill'];
    }

	public function editToken($customer_id, $token) {
		$this->db->query("UPDATE " . DB_PREFIX . "customer SET token = '" . $this->db->escape($token) . "' WHERE customer_id = '" . (int)$customer_id . "'");
	}

	public function deleteWorker($customer_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "worker_reviews WHERE worker_id = '" . (int)$customer_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "worker_likes WHERE worker_id = '" . (int)$customer_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "worker_skill WHERE customer_id = '" . (int)$customer_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "worker_details WHERE customer_id = '" . (int)$customer_id . "'");
	}

	public function getCustomer($customer_id) {
       $query = $this->db->query("
         SELECT ws.*, wd.*, c.firstname, c.lastname, c.email 
        FROM " . DB_PREFIX . "worker_skill ws 
         LEFT JOIN " . DB_PREFIX . "worker_details wd 
        ON ws.customer_id = wd.customer_id 
         LEFT JOIN " . DB_PREFIX . "customer c 
        ON ws.customer_id = c.customer_id 
         WHERE ws.customer_id = '" . (int)$customer_id . "'
       ");
		return $query->row;
	}

	public function getCustomerByEmail($email) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "customer WHERE LCASE(email) = '" . $this->db->escape(utf8_strtolower($email)) . "'");

		return $query->row;
	}
	
	public function getCustomers($data = array()) {
		$sql = "SELECT *, CONCAT(c.firstname, ' ', c.lastname) AS name FROM " . DB_PREFIX . "worker_skill ws LEFT JOIN " . DB_PREFIX . "customer c ON (c.customer_id = ws.customer_id)";
				
		$implode = array();

		if (!empty($data['filter_name'])) {
			$implode[] = "CONCAT(c.firstname, ' ', c.lastname) LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
		}

		if (!empty($data['filter_email'])) {
			$implode[] = "c.email LIKE '" . $this->db->escape($data['filter_email']) . "%'";
		}

	    if (isset($data['filter_skill']) && !is_null($data['filter_skill'])) {
			$implode[] = "ws.skill LIKE '%" . $this->db->escape($data['filter_skill']) . "%'";
		}
	
		if (isset($data['filter_status']) && $data['filter_status'] !== '') {
			$implode[] = "c.status = '" . (int)$data['filter_status'] . "'";
		}

		if (!empty($data['filter_date_added'])) {
			$implode[] = "DATE(c.date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}

		if ($implode) {
			$sql .= " AND " . implode(" AND ", $implode);
		}

		$sort_data = array(
			'name',
			'c.email',
			'ws.skill',
			'c.status',
			'c.date_added'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY name";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}

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

	
	public function getTotalCustomers($data = array()) {
		$sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "worker_skill c";

		$implode = array();

		if (!empty($data['filter_name'])) {
			$implode[] = "CONCAT(firstname, ' ', lastname) LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
		}

		if (!empty($data['filter_email'])) {
			$implode[] = "email LIKE '" . $this->db->escape($data['filter_email']) . "%'";
		}

		if (isset($data['filter_status']) && $data['filter_status'] !== '') {
			$implode[] = "status = '" . (int)$data['filter_status'] . "'";
		}

		if (!empty($data['filter_date_added'])) {
			$implode[] = "DATE(date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}

		if ($implode) {
			$sql .= " WHERE " . implode(" AND ", $implode);
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}
        
    public function getTotalLoginAttempts($email) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "customer_login` WHERE `email` = '" . $this->db->escape($email) . "'");

		return $query->row;
	}

	public function deleteLoginAttempts($email) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "customer_login` WHERE `email` = '" . $this->db->escape($email) . "'");
	}


	public function addWorkStatus($data) {
		foreach ($data['work_status'] as $language_id => $value) {
			if (isset($work_status_id)) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "work_status SET work_status_id = '" . (int)$work_status_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "'");
			} else {
				$this->db->query("INSERT INTO " . DB_PREFIX . "work_status SET language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "'");

				$work_status_id = $this->db->getLastId();
			}
		}

		$this->cache->delete('work_status');
		
		return $work_status_id;
	}

	public function editWorkStatus($work_status_id, $data) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "work_status WHERE work_status_id = '" . (int)$work_status_id . "'");

		foreach ($data['work_status'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "work_status SET work_status_id = '" . (int)$work_status_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "'");
		}

		$this->cache->delete('work_status');
	}

	public function deleteWorkStatus($work_status_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "work_status WHERE work_status_id = '" . (int)$work_status_id . "'");

		$this->cache->delete('work_status');
	}

	public function getOrderStatus($work_status_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "work_status WHERE work_status_id = '" . (int)$work_status_id . "' AND language_id = '" . (int)$this->config->get('config_language_id') . "'");

		return $query->row;
	}

	public function getWorkStatuses($data = array()) {
		if ($data) {
			$sql = "SELECT * FROM " . DB_PREFIX . "work_status WHERE language_id = '" . (int)$this->config->get('config_language_id') . "'";

			$sql .= " ORDER BY name";

			if (isset($data['work']) && ($data['work'] == 'DESC')) {
				$sql .= " DESC";
			} else {
				$sql .= " ASC";
			}

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
		} else {
			$work_status_data = $this->cache->get('work_status.' . (int)$this->config->get('config_language_id'));

			if (!$work_status_data) {
				$query = $this->db->query("SELECT work_status_id, name FROM " . DB_PREFIX . "work_status WHERE language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY name");

				$order_status_data = $query->rows;

				$this->cache->set('work_status.' . (int)$this->config->get('config_language_id'), $work_status_data);
			}

			return $work_status_data;
		}
	}

	public function getWorkStatusDescriptions($work_status_id) {
		$work_status_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "work_status WHERE work_status_id = '" . (int)$work_status_id . "'");

		foreach ($query->rows as $result) {
			$work_status_data[$result['language_id']] = array('name' => $result['name']);
		}

		return $work_status_data;
	}

	public function getTotalWorkStatuses() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "work_status WHERE language_id = '" . (int)$this->config->get('config_language_id') . "'");

		return $query->row['total'];
	}
}
