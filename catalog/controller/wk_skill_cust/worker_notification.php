<?php
class ControllerWkSkillCustWorkerNotification extends Controller {
	public function index() {
    $this->load->language('wk_skill_cust/skills');
    $data['unseen_requests'] = [];

    if ($this->customer->isLogged()) {
        $this->load->model('wk_skill_cust/skills');
        $data['unseen_requests'] = $this->model_wk_skill_cust_skills->getUnseenRequests($this->customer->getId());
    }

    return $this->load->view('wk_skill_cust/worker_notification', $data);
  }

   
  public function markAsSeen() {
    $json = json_decode(file_get_contents('php://input'), true);

    if (!empty($json['id']) && $this->customer->isLogged()) {
        $request_id = (int)$json['id'];

        $this->load->model('wk_skill_cust/skills');
        $this->model_wk_skill_cust_skills->markAsSeen($request_id);
        $this->model_wk_skill_cust_skills->markRequestSeen($request_id);
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode(['success' => true]));
    } else {
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode(['success' => false, 'error' => 'Invalid request']));
    }
}


    public function markAllAsSeen() {
        $this->load->model('wk_skill_cust/skills');
        $this->model_wk_skill_cust_skills->markAllAsSeen();
        $this->response->redirect($this->url->link('wk_skill_cust/worker_notification'));
    }

    public function view () {
        $this->load->language('wk_skill_cust/skills');
	    $this->load->model('wk_skill_cust/skills');

        	$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_account'),
			'href' => $this->url->link('common/home')
		);

        $data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_account_update'),
			'href' => $this->url->link('wk_skill_cust/worker_notification', '', true)
		);

        $notification_list = $this->model_wk_skill_cust_skills->getAllNotifications($this->customer->getId());

        $data['notification_list'] = $notification_list;

        $data['back_url'] = $this->url->link('common/home', '', true);
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['column_right'] = $this->load->controller('common/column_right');
        $data['content_top'] = $this->load->controller('common/content_top');
        $data['content_bottom'] = $this->load->controller('common/content_bottom');
        $data['footer'] = $this->load->controller('common/footer');
        $data['header'] = $this->load->controller('common/header');

        $this->response->setOutput($this->load->view('wk_skill_cust/worker_notification_list', $data));
    }

  public function accept() {
    $json = json_decode(file_get_contents('php://input'), true);

    if (!empty($json['id']) && $this->customer->isLogged()) {
        $request_id = (int)$json['id'];

        $this->load->model('wk_skill_cust/skills');
        $this->model_wk_skill_cust_skills->updateRequestStatus($request_id, 'accepted');
        $this->model_wk_skill_cust_skills->addCustomerNotification($request_id, 'accepted');

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode(['success' => true]));
    } else {
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode(['success' => false, 'error' => 'Invalid request']));
    }
}

public function reject() {
    $json = json_decode(file_get_contents('php://input'), true);

    if (!empty($json['id']) && $this->customer->isLogged()) {
        $request_id = (int)$json['id'];

        $this->load->model('wk_skill_cust/skills');
        $this->model_wk_skill_cust_skills->updateRequestStatus($request_id, 'rejected');
        $this->model_wk_skill_cust_skills->addCustomerNotification($request_id, 'rejected');

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode(['success' => true]));
    } else {
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode(['success' => false, 'error' => 'Invalid request']));
    }
}

public function delete() {
    $this->load->language('wk_skill_cust/skills');
    $json = json_decode(file_get_contents('php://input'), true);

    if (!empty($json['id']) && $this->customer->isLogged()) {
        $request_id = (int)$json['id'];

        $this->load->model('wk_skill_cust/skills');
        $this->model_wk_skill_cust_skills->deleteRequest($request_id, $this->customer->getId());

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode(['success' => true]));
    } else {
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode([
            'success' => false,
            'error' => 'Invalid request or not logged in.'
        ]));
    }
}


}














