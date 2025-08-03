<?php
class ModelWkSkillCustSkills extends Model {

     public function getSkills() {
           $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "wk_skill_cust WHERE status = 1 ");
         return $query->rows ;
     }

    public function addWorkerSkill($customer_id, $data) {

      $skill= $this->getSkill($data['occupation']);

      $this->db->query("INSERT INTO `" . DB_PREFIX . "worker_skill` SET 
        customer_id = '" . (int)$customer_id . "',
        occupation_id = '" . (int)$data['occupation'] . "',
        skill = '" . $this->db->escape($skill) . "',
        price_per_day = '" . (float)$data['price_per_day'] . "',
        date_of_birth = '" . $this->db->escape($data['date_of_birth']) . "',
        image = '" . $this->db->escape($data['worker_image']) . "',
        wk_status = '" . 1 . "',
        date_added = NOW()
     ");
    }

    public function getWorkerSkills($customer_id) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "worker_skill WHERE customer_id = '" . (int)$customer_id . "'");
        return $query->rows;
    }

    public function getSkill($skill_id) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "wk_skill_cust WHERE id = '" . (int)$skill_id . "'");
        return $query->row['skill'];
    }

    public function getWorkerInfo($customer_id) {
       $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "worker_skill ws LEFT JOIN " . DB_PREFIX . "worker_details wd ON ws.customer_id = wd.customer_id WHERE ws.customer_id = '" . (int)$customer_id . "'");



        if (!empty($query->row)) {
          return $query->row;
        } else {
          return false;
        }
    }


  
      public function getWorkers() {
        $sql = "SELECT c.*, ws.*, wd.*, CONCAT(c.firstname, ' ', c.lastname) AS name FROM " . DB_PREFIX . "worker_skill ws LEFT JOIN " . DB_PREFIX . "customer c ON (c.customer_id = ws.customer_id) LEFT JOIN " . DB_PREFIX . "worker_details wd ON (c.customer_id = wd.customer_id) WHERE 1=1";
        $query = $this->db->query($sql);
        if (!empty($query->rows)) {
          return $query->rows;
        } else {
          return false;
        }
    }

  public function updateWorkerInfo($customer_id, $data) {

    $skill = $this->getSkill($data['occupation']);

    $this->db->query("UPDATE `" . DB_PREFIX . "worker_skill` SET 
        occupation_id = '" . (int)$data['occupation'] . "',
        skill = '" . $this->db->escape($skill) . "',
        price_per_day = '" . (float)$data['price_per_day'] . "',
        date_of_birth = '" . $this->db->escape($data['date_of_birth']) . "',
        image = '" . $this->db->escape($data['worker_image']) . "',
        wk_status = '" . $this->db->escape($data['wk_status']) . "'
        WHERE customer_id = '" . (int)$customer_id . "'
    ");
 
   $details = $this->db->query("SELECT * FROM " . DB_PREFIX . "worker_details WHERE customer_id = '" . (int)$customer_id . "'");
 
   if (!empty($details->row)) {
     $this->db->query("UPDATE `" . DB_PREFIX . "worker_details` SET 
        range_km = '" . (int)$data['range'] . "',
        experience = '" . (float)$data['experience'] . "',
        busy_date = '" . $data['busy_dates'] . "',
        description = '" . $this->db->escape($data['work_description']) . "'
        WHERE customer_id = '" . (int)$customer_id . "'
      ");
    } else {
       $this->db->query("INSERT INTO `" . DB_PREFIX . "worker_details` SET 
        customer_id = '" . (int)$customer_id . "',
        range_km = '" . (int)$data['range'] . "',
        experience = '" . (float)$data['experience'] . "',
        busy_date = '" . $data['busy_dates'] . "',
        description = '" . $this->db->escape($data['work_description']) . "'
       ");
    }
  }

  public function setLikeStatus($worker_id, $user_id, $action) {
$this->db->query("DELETE FROM `" . DB_PREFIX . "worker_likes` WHERE worker_id = '" . (int)$worker_id . "' AND user_id = '" . (int)$user_id . "'");

    if (in_array($action, ['like', 'dislike'])) {
       $this->db->query("INSERT INTO `" . DB_PREFIX . "worker_likes` SET 
        worker_id = '" . (int)$worker_id . "', 
        user_id = '" . (int)$user_id . "', 
        status = '" . $this->db->escape($action) . "',
        date_added = NOW()");

    }
  }

  public function getLikesCount($worker_id) {
    $query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "worker_likes` WHERE worker_id = '" . (int)$worker_id . "' AND status = 'like'");

    return $query->row['total'];
 }

  public function getDislikesCount($worker_id) {
    $query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "worker_likes` WHERE worker_id = '" . (int)$worker_id . "' AND status = 'dislike'");
    return $query->row['total'];
 }
  
  public function addReview($worker_id, $customer_id, $rating, $title, $review, $recommend) {
    $this->db->query("INSERT INTO " . DB_PREFIX . "worker_reviews SET 
        worker_id = '" . (int)$worker_id . "',
        customer_id = '" . (int)$customer_id . "',
        rating = '" . (int)$rating . "',
        title = '" . $this->db->escape($title) . "',
        review = '" . $this->db->escape($review) . "',
        recommend = '" . (int)$recommend . "',
        status = '1',
        date_added = NOW()");
   }

   public function getReviews($worker_id) {
     $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "worker_reviews WHERE worker_id = '" . (int)$worker_id . "' ORDER BY date_added DESC");
     return $query->rows;
   }

   public function getReview($review_id) {
     $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "worker_reviews WHERE id = '" . (int)$review_id . "'");
     return $query->row;
   }
   public function getRating($worker_id) {
     $query = $this->db->query("SELECT AVG(rating) AS rating FROM " . DB_PREFIX . "worker_reviews WHERE worker_id = '" . (int)$worker_id . "'");
     return $query->row['rating'];
   }

 public function getReviewCount($worker_id) {
    $query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "worker_reviews WHERE worker_id = '" . (int)$worker_id . "'");
    return (int)$query->row['total'];
}

public function getWorkerName($customer_id) {
    $query = $this->db->query("SELECT firstname, lastname FROM " . DB_PREFIX . "customer WHERE customer_id = '" . (int)$customer_id . "'");
    
    if ($query->num_rows) {
        return $query->row['firstname'] . ' ' . $query->row['lastname'];
    } else {
        return '';
    }
}



   public function addRequest($data) {
    //  $this->addNotification($data);
     $this->addHireHistory($data); 
   }

 public function addHireHistory($data) {
    // Load worker info and stats
    $workerInfo     = $this->getWorkerInfo($data['worker_id']);
    $likeCount      = $this->getLikesCount($data['worker_id']);
    $dislikesCount  = $this->getDislikesCount($data['worker_id']);
    $rating         = $this->getRating($data['worker_id']);
    $reviewCount    = $this->getReviewCount($data['worker_id']);
    $workerName = $this->getWorkerName($workerInfo['customer_id']);  // Get full name
    $customer_id = 0;
    if ($this->customer->isLogged()) {
        $customer_id = (int)$this->customer->getId();
    }

    $this->db->query("
        INSERT INTO `" . DB_PREFIX . "worker_hire_history` 
        SET 
            customer_id = '" . (int)$customer_id . "',
            customer_name = '" . $this->db->escape($data['customer_name']) . "',
            customer_mobile = '" . $this->db->escape($data['customer_mobile']) . "',
            customer_address = '" . $this->db->escape($data['customer_address']) . "',
            worker_id = '" . (int)$workerInfo['customer_id'] . "',
            worker_name = '" . $this->db->escape($workerName) . "', 
            occupation_id = '" . (int)$workerInfo['occupation_id'] . "',
            skill = '" . $this->db->escape($workerInfo['skill']) . "',
            price_per_day = '" . (float)$workerInfo['price_per_day'] . "',
            date_of_birth = '" . $this->db->escape($workerInfo['date_of_birth']) . "',
            image = '" . $this->db->escape($workerInfo['image']) . "',
            range_km = '" . (int)$workerInfo['range_km'] . "',
            experience = '" . (int)$workerInfo['experience'] . "',
            description = '" . $this->db->escape($workerInfo['description']) . "',  
            avg_rating = '" . round((float)$rating, 1) . "',
            review_count = '" . (int)$reviewCount . "',
            likes = '" . (int)$likeCount . "',
            dislikes = '" . (int)$dislikesCount . "',
            status = 'pending',
            seen = 0,
            pop_seen = 0,
            created_at = NOW()
    ");
}

   public function addNotification($data) {

         $this->db->query("INSERT INTO `" . DB_PREFIX . "hire_requests` SET worker_id = '" . (int)$data['worker_id'] . "', customer_name = '" . $this->db->escape($data['customer_name']) . "', customer_mobile = '" . $this->db->escape($data['customer_mobile']) . "', customer_address = '" . $this->db->escape($data['customer_address']) . "', seen = '0', popup_seen = '0', status = 'pending'");

   }


   public function getUnseenRequests($worker_id) {
     $query = $this->db->query("
    SELECT * FROM `" . DB_PREFIX . "hire_requests`
    WHERE worker_id = '" . (int)$worker_id . "' AND popup_seen = 0
    ORDER BY created_at DESC
      ");
  return $query->rows;
  }

 public function markAsSeen($request_id) {
   $this->db->query("
    UPDATE `" . DB_PREFIX . "hire_requests`
    SET seen = 1
    WHERE id = '" . (int)$request_id . "'
  ");
}

 public function markRequestSeen($request_id) {
   $this->db->query("
    UPDATE `" . DB_PREFIX . "hire_requests`
    SET popup_seen = 1
    WHERE id = '" . (int)$request_id . "'
  ");
}

  public function countUnseenNotification($worker_id) {
    $query = $this->db->query("
        SELECT COUNT(*) AS total 
        FROM `" . DB_PREFIX . "hire_requests`
        WHERE worker_id = '" . (int)$worker_id . "' AND seen = 0
    ");
    
    return (int)$query->row['total'];
}

public function getAllNotifications($worker_id) {
  $query = $this->db->query("
      SELECT *
      FROM `" . DB_PREFIX . "hire_requests`
      WHERE worker_id = '" . (int)$worker_id . "'
      ORDER BY created_at DESC
  ");

  return $query->rows;

}

public function updateRequestStatus($request_id, $status) {
    $this->db->query("UPDATE `" . DB_PREFIX . "hire_requests` SET status = '" . $this->db->escape($status) . "', seen = 1 WHERE id = '" . (int)$request_id . "'");
}

public function addCustomerNotification($request_id, $type) {
    // Get basic request info
    $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "hire_requests` WHERE id = '" . (int)$request_id . "'");
    
    if ($query->num_rows) {
        $data = $query->row;
        $customer_id = $data['worker_id']; // adjust if customer_id is stored elsewhere

        $this->db->query("INSERT INTO `" . DB_PREFIX . "customer_notifications` 
        SET customer_id = '" . (int)$customer_id . "', 
            request_id = '" . (int)$request_id . "', 
            message = '" . $this->db->escape('Your request has been ' . $type) . "', 
            status = 'unread',
            created_at = NOW()");
    }
}

public function deleteRequest($request_id, $worker_id) {
    $this->db->query("DELETE FROM `" . DB_PREFIX . "hire_requests` WHERE id = '" . (int)$request_id . "' AND worker_id = '" . (int)$worker_id . "'");
}


}