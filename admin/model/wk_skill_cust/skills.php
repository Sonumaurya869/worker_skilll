<?php
class ModelWkSkillCustSkills extends Model {


    public function install() {
        $this->db->query("
            CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "wk_skill_cust` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `skill` varchar(255) NOT NULL,
              `wk_status` tinyint(1) NOT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
        ");

       $this->db->query("
        CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "worker_reviews` (
            `review_id` INT(11) NOT NULL AUTO_INCREMENT,
            `customer_id` INT(11) NOT NULL,
            `worker_id` INT(11) NOT NULL,
            `rating` TINYINT(1) NOT NULL,
            `title` VARCHAR(255) NOT NULL,
            `review` TEXT NOT NULL,
            `recommend` TINYINT(1) NOT NULL DEFAULT 0,
            `status` INT(11) NOT NULL,
            `date_added` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`review_id`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8;
      ");

      $this->db->query("
        CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "worker_likes` (
            `id` INT(11) NOT NULL AUTO_INCREMENT,
            `worker_id` INT(11) NOT NULL,
            `user_id` INT(11) NOT NULL,
            `status` ENUM('like','dislike') NOT NULL,
            `date_added` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
    ");     
        $this->db->query("
        CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "worker_skill` (
            `id` INT(11) NOT NULL AUTO_INCREMENT,
            `customer_id` INT(11) NOT NULL,
            `occupation_id` INT(11) NOT NULL,
            `skill` VARCHAR(64) NOT NULL,
            `price_per_day` DECIMAL(10,2) NOT NULL,
            `date_of_birth` DATE NOT NULL,
            `image` VARCHAR(255) NOT NULL,
            `status` TINYINT(1) NOT NULL DEFAULT 1,
            `date_added` DATETIME NOT NULL,
            PRIMARY KEY (`id`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8
       ");

        $this->db->query("
            CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "hire_requests` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `worker_id` INT,
                `customer_name` VARCHAR(255),
                `customer_mobile` VARCHAR(50),
                `customer_address` TEXT,
                `seen` INT(10) NOT NULL,
                `popup_seen` INT(10) NOT NULL,
                `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ");

         $this->db->query("
        CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "worker_details` (
            `customer_id` INT(10) NOT NULL,
            `range_km` INT(10) NOT NULL,
            `experience` INT(10) NOT NULL,
            `busy_date` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
            `description` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
    ");

    $this->db->query("
        CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "worker_hire_history` (
            `history_id` INT(11) NOT NULL AUTO_INCREMENT,
            `customer_id` INT(11) NOT NULL,
            `customer_name` VARCHAR(255) NOT NULL,
            `customer_mobile` VARCHAR(50) NOT NULL,
            `customer_address` TEXT NOT NULL,
            `worker_id` INT(11) NOT NULL,
            `worker_name` VARCHAR(255) NOT NULL,
            `occupation_id` INT(11) NOT NULL,
            `skill` VARCHAR(64) NOT NULL,
            `price_per_day` DECIMAL(10,2) NOT NULL,
            `date_of_birth` DATE NOT NULL,
            `image` VARCHAR(255) NOT NULL,
            `range_km` INT(10) NOT NULL,
            `experience` INT(10) NOT NULL,
            `description` TEXT NOT NULL,
            `avg_rating` DECIMAL(2,1) NOT NULL DEFAULT 0.0,
            `review_count` INT(11) NOT NULL DEFAULT 0,
            `likes` INT(11) NOT NULL DEFAULT 0,
            `dislikes` INT(11) NOT NULL DEFAULT 0,
            `status` VARCHAR(50) NOT NULL DEFAULT 'pending',
            `seen` TINYINT(1) NOT NULL DEFAULT 0,
            `pop_seen` TINYINT(1) NOT NULL DEFAULT 0,
            `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            
            PRIMARY KEY (`history_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
    ");


    }
	
    public function uninstall() {
           $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "worker_skill`");
           $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "wk_skill_cust`");
           $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "hire_requests`");
           $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "worker_likes`");
           $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "worker_reviews`");
           $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "worker_details`");
           $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "worker_hire_history`");

      }
  
    public function getSkills($data = array()) {
        $sql = "SELECT * FROM " . DB_PREFIX . "wk_skill_cust";
        $sort_data = array(
            'skill',
            'status',
            'sort_order'
        );

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY skill";
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

    public function getTotalSkills($data = array()) {
           return $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "wk_skill_cust")->row['total'];
    }

    public function addSkill($data) {
        $this->db->query("INSERT INTO " . DB_PREFIX . "wk_skill_cust SET skill = '" . $this->db->escape($data['skill']) . "', status = '" . (int)$data['status'] . "'");
    }

    public function editSkill($skill_id, $data) {
        $this->db->query("UPDATE " . DB_PREFIX . "wk_skill_cust SET skill = '" . $this->db->escape($data['skill']) . "', status = '" . (int)$data['status'] . "' WHERE id = '" . (int)$skill_id . "'");
    }
    public function getSkill($skill_id) {
        $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "wk_skill_cust WHERE id = '" . (int)$skill_id . "'");
         return $query->row ;
    }

    public function deleteSkill($skill_id) {
      $this->db->query("DELETE FROM " . DB_PREFIX . "wk_skill_cust WHERE id = '" . (int)$skill_id . "'");

    }

     public function getEnableSkills() {
           $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "wk_skill_cust WHERE status = 1 ");
         return $query->rows ;
     }



}