<?php
class ControllerWkSkillCustAccount extends Controller {
	public function index($errors = []) {

        $this->load->language('wk_skill_cust/skills');
	    	$this->load->model('wk_skill_cust/skills');

          if (isset($errors['date_of_birth'])) {
            $data['date_of_birth_error'] =  $this->language->get($errors['date_of_birth']);
          } else {
            $data['date_of_birth_error'] = '';
          }

          if (isset($errors['occupation'])) {
            $data['occupation_error'] =  $this->language->get($errors['occupation']);
          } else {
            $data['occupation_error'] = '';
          }

          if (isset($errors['price_per_day'])) {
            $data['price_per_day_error'] =  $this->language->get($errors['price_per_day']);
          } else {
            $data['price_per_day_error'] = '';
          }

	    if (isset($this->request->post['worker']) && $this->request->post['worker'] == '1') {
           $data['worker'] = 1;
        } else {
            $data['worker'] = 0;
        }

        $occupation_data = $this->model_wk_skill_cust_skills->getSkills();
        $data['occupations'] = [];

        foreach ($occupation_data as $occupation) {
           if ($occupation['status']) {
                $data['occupations'][] = [
                    'id'    => $occupation['id'],
                    'skill' => $occupation['skill']
                  ];
              }
         }
           // Preserve selected value if POST
            $data['occupation_id'] = isset($this->request->post['occupation']) ? $this->request->post['occupation'] : '';
         $this->load->model('tool/image');

          if (!empty($this->request->post['worker_image']) && is_file(DIR_IMAGE . $this->request->post['worker_image'])) {
             $data['thumb'] = $this->model_tool_image->resize($this->request->post['worker_image'], 100, 100);
              $data['worker_image'] = $this->request->post['worker_image'];
          } elseif (!empty($worker_info['worker_image']) && is_file(DIR_IMAGE . $worker_info['worker_image'])) {
              $data['thumb'] = $this->model_tool_image->resize($worker_info['worker_image'], 100, 100);
              $data['worker_image'] = $worker_info['worker_image'];
           } else {
              $data['thumb'] = $this->model_tool_image->resize('no_image.png', 100, 100);
               $data['worker_image'] = '';
            }


       if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		return $this->load->view('wk_skill_cust/account', $data);
	}
}
