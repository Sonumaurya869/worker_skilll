<?php
class ControllerWkSkillCustRechargeDuration extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('wk_skill_cust/recharge_duration');
		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('wk_skill_cust/recharge');
		$this->getList();
	}

	public function add() {
		$this->load->language('wk_skill_cust/recharge_duration');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('wk_skill_cust/recharge');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {

			$this->model_wk_skill_cust_recharge->addRechargePlan($this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('wk_skill_cust/recharge_duration', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function edit() {
		$this->load->language('wk_skill_cust/recharge_duration');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('wk_skill_cust/recharge');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_wk_skill_cust_recharge->editRechargePlan($this->request->get['plan_id'], $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('wk_skill_cust/recharge_duration', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('wk_skill_cust/recharge_duration');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('wk_skill_cust/recharge');

		if (isset($this->request->post['selected'])) {
			foreach ($this->request->post['selected'] as $plan_id) {
				$this->model_wk_skill_cust_recharge->deleteRechargePlan($plan_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('wk_skill_cust/recharge_duration', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getList();
	}

	protected function getList() {
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'name';
		}

		if (isset($this->request->get['plan'])) {
			$plan = $this->request->get['plan'];
		} else {
			$plan = 'ASC';
		}

		if (isset($this->request->get['page'])) {
			$page = (int)$this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['plan'])) {
			$url .= '&plan=' . $this->request->get['plan'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('wk_skill_cust/recharge_duration', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['add'] = $this->url->link('wk_skill_cust/recharge_duration/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['delete'] = $this->url->link('wk_skill_cust/recharge_duration/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$data['plan_statuses'] = array();

		$filter_data = array(
			'sort'  => $sort,
			'plan' => $plan,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);

		$plan_total = $this->model_wk_skill_cust_recharge->getTotalRechargePlan();

		$results = $this->model_wk_skill_cust_recharge->getRechargePlan($filter_data);

		foreach ($results as $result) {
			$data['plan_statuses'][] = array(
				'plan_id' => $result['plan_id'],
				'name'            => $result['plan_name'],
                'duration'            => $result['duration'],
                'status'            => $result['plan_status'],
				'edit'            => $this->url->link('wk_skill_cust/recharge_duration/edit', 'user_token=' . $this->session->data['user_token'] . '&plan_id=' . $result['plan_id'] . $url, true)
			);
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}

		$url = '';

		if ($plan == 'ASC') {
			$url .= '&plan=DESC';
		} else {
			$url .= '&plan=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_name'] = $this->url->link('wk_skill_cust/recharge_duration', 'user_token=' . $this->session->data['user_token'] . '&sort=name' . $url, true);

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $plan_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('wk_skill_cust/recharge_duration', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($plan_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($plan_total - $this->config->get('config_limit_admin'))) ? $plan_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $plan_total, ceil($plan_total / $this->config->get('config_limit_admin')));

		$data['sort'] = $sort;
		$data['plan'] = $plan;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('wk_skill_cust/recharge_duration', $data));
	}

	protected function getForm() {
		$data['text_form'] = !isset($this->request->get['plan_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['name'])) {
			$data['error_name'] = $this->error['name'];
		} else {
			$data['error_name'] = array();
		}

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['plan'])) {
			$url .= '&plan=' . $this->request->get['plan'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('wk_skill_cust/recharge_duration', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		if (!isset($this->request->get['plan_id'])) {
			$data['action'] = $this->url->link('wk_skill_cust/recharge_duration/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		} else {
			$data['action'] = $this->url->link('wk_skill_cust/recharge_duration/edit', 'user_token=' . $this->session->data['user_token'] . '&plan_id=' . $this->request->get['plan_id'] . $url, true);
		}

		$data['cancel'] = $this->url->link('wk_skill_cust/recharge_duration', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$this->load->model('localisation/language');

		$data['languages'] = $this->model_localisation_language->getLanguages();

		if (isset($this->request->post['plan_status'])) {
       $data['plan_status'] = $this->request->post['plan_status'];
     } elseif (isset($this->request->get['plan_id'])) {
        $plan_info = $this->model_wk_skill_cust_recharge->getRechargePlanById($this->request->get['plan_id']);
         if ($plan_info) {
          $data['plan_status'] = $plan_info['plan_status']; // Only assign the status field
          $data['plan_name']   = $plan_info['plan_name'];   // Assign name for edit
          $data['duration']    = $plan_info['duration'];    // Assign duration for edit
        } else {
           $data['plan_status'] = 1;  // Default enabled
           $data['plan_name']   = '';
             $data['duration']    = '';
        }
     } else {
       $data['plan_status'] = 1;  // Default enabled
       $data['plan_name']   = '';
       $data['duration']    = '';
     }


		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('wk_skill_cust/recharge_duration_form', $data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'wk_skill_cust/recharge_duration')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		foreach ($this->request->post['plan_status'] as $language_id => $value) {
			if ((utf8_strlen($value['name']) < 3) || (utf8_strlen($value['name']) > 32)) {
				$this->error['name'][$language_id] = $this->language->get('error_name');
			}
		}

		return !$this->error;
	}

}
