<?php
class ControllerWkSkillCustButton extends Controller {
	public function index() {

        $this->load->language('wk_skill_cust/skills');
	    $this->load->model('wk_skill_cust/skills');
        $customer_id = $this->customer->getId();
        $worker_info = $this->model_wk_skill_cust_skills->getWorkerInfo($customer_id);
        
        if ($worker_info) {
           $data['worker'] = 1;
        } else {
            $data['worker'] = 0;
        }

        $data['update_profile_url'] = $this->url->link('wk_skill_cust/account_update', '', true);
        $data['view_profile_url'] = $this->url->link('wk_skill_cust/account_view', '', true);
		return $this->load->view('wk_skill_cust/button', $data);
	}
}
