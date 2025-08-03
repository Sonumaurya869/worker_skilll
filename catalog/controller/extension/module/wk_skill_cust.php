<?php
class ControllerExtensionModuleWkSkillCust extends Controller {
	public function index() {
		$this->load->language('extension/module/wk_skill_cust');

		$this->load->model('wk_skill_cust/skills');

		$this->load->model('tool/image');

		$data['workers'] = array();

		$workers = $this->model_wk_skill_cust_skills->getWorkers();
       if (!empty($workers)) {
        foreach ($workers as $worker) {
               $age = '';
          if ($worker['date_of_birth']) {
             $birthDate = new DateTime($worker['date_of_birth']);
             $today = new DateTime('today');
             $age = $birthDate->diff($today)->y;
            }

			if ($worker['image']) {
				$image = $this->model_tool_image->resize($worker['image'], 150, 150);
			} else {
				$image = $this->model_tool_image->resize('placeholder.png', 150, 150);
			}
             $rating= $this->model_wk_skill_cust_skills->getRating($worker['customer_id']);
            $data['workers'][] = array(
				'customer_id' => $worker['customer_id'],
                'skill'     => $worker['skill'],
				'name'     => $worker['name'],
				'image'    => $image,
                'age'      => $age,
				'likes_count'=> $this->model_wk_skill_cust_skills->getLikesCount($worker['customer_id']),
				'dislikes_count'=> $this->model_wk_skill_cust_skills->getDislikesCount($worker['customer_id']),
				'telephone'   => $worker['telephone'],
                'location'    => 'Delhi',
                'date_of_birth'=> $worker['date_of_birth'],
				'price_per_day'    =>$worker['price_per_day'],
                'rating'   => isset($rating) ? $rating : 0,
				'experience'  => $worker['experience'],
				'description'      => $worker['description'],
				'status'   => $worker['status'],
				'href'     => $this->url->link('wk_skill_cust/skills/worker', 'worker_id=' . $worker['customer_id'])
			);
        }
       }
		if ($data['workers']) {
			return $this->load->view('extension/module/wk_skill_cust', $data);
		}
	}

  public function like() {

    $json = [];
     
    if ($this->request->server['REQUEST_METHOD'] == 'POST') {
        $worker_id = (int)$this->request->post['worker_id'];
        $action = $this->request->post['action']; // 'like' or 'dislike'

        // You must get the current user ID from the session or auth
        $user_id = $this->customer->getId(); // example for OpenCart

        if (!$user_id) {
            $json['success'] = false;
            $json['error'] = 'You must be logged in to like or dislike.';
        } else {
           $this->load->model('wk_skill_cust/skills');
            $this->model_wk_skill_cust_skills->setLikeStatus($worker_id, $user_id, $action);

            $json['success'] = true;
            $json['message'] = 'Your reaction has been saved.';
            $json['liked'] = ($action == 'like');

            // Return updated counts
            $json['likes_count'] = $this->model_wk_skill_cust_skills->getLikesCount($worker_id);
            $json['dislikes_count'] = $this->model_wk_skill_cust_skills->getDislikesCount($worker_id);
        }
    }

    $this->response->addHeader('Content-Type: application/json');
    $this->response->setOutput(json_encode($json));
  }

 public function review() {
    $this->load->language('extension/module/wk_skill_cust');
    $json = [];

    if (!$this->customer->isLogged()) {
        $json['error'] = 'You must be logged in to submit a review.';
    } elseif ($this->request->server['REQUEST_METHOD'] == 'POST') {
        $worker_id = (int)$this->request->post['worker_id'];
        $rating = (int)$this->request->post['rating'];
        $title = $this->db->escape($this->request->post['title']);
        $review = $this->db->escape($this->request->post['review']);
        $recommend = isset($this->request->post['recommend']) ? (int)$this->request->post['recommend'] : 0;
        $customer_id = (int)$this->customer->getId();
        if (!$worker_id || !$rating || !$title || !$review) {
            $json['error'] = 'Please fill all required fields.';
        } else {
            $this->load->model('wk_skill_cust/skills');
            $this->model_wk_skill_cust_skills->addReview($worker_id, $customer_id, $rating, $title, $review, $recommend);

            $json['success'] = true;
        }
    } else {
        $json['error'] = 'Invalid request method';
    }

    $this->response->addHeader('Content-Type: application/json');
    $this->response->setOutput(json_encode($json));
}

   public function hire() {
        $this->load->model('wk_skill_cust/skills');
        $json = [];
        if ($this->request->server['REQUEST_METHOD'] == 'POST') {
            $post = $this->request->post;
            if (!$post['customer_name'] || !$post['customer_mobile'] || !$post['customer_address']) {
                $json['error'] = 'All fields are required.';
            } else {
                $this->model_wk_skill_cust_skills->addRequest($post);
                $json['success'] = true;
            }
        } else {
            $json['error'] = 'Invalid request.';
        }
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }


}