<?php
class ControllerWkSkillCustWorkStatus extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('wk_skill_cust/work_status');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('wk_skill_cust/customer');

		$this->getList();
	}

	public function add() {
		$this->load->language('wk_skill_cust/work_status');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('wk_skill_cust/customer');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {

			$this->model_wk_skill_cust_customer->addWorkStatus($this->request->post);

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

			$this->response->redirect($this->url->link('wk_skill_cust/work_status', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function edit() {
		$this->load->language('wk_skill_cust/work_status');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('wk_skill_cust/customer');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_wk_skill_cust_customer->editWorkStatus($this->request->get['work_status_id'], $this->request->post);

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

			$this->response->redirect($this->url->link('wk_skill_cust/work_status', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('wk_skill_cust/work_status');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('wk_skill_cust/customer');

		if (isset($this->request->post['selected'])) {
			foreach ($this->request->post['selected'] as $work_status_id) {
				$this->model_wk_skill_cust_customer->deleteWorkStatus($work_status_id);
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

			$this->response->redirect($this->url->link('wk_skill_cust/work_status', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getList();
	}

	protected function getList() {
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'name';
		}

		if (isset($this->request->get['work'])) {
			$work = $this->request->get['work'];
		} else {
			$work = 'ASC';
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

		if (isset($this->request->get['work'])) {
			$url .= '&work=' . $this->request->get['work'];
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
			'href' => $this->url->link('wk_skill_cust/work_status', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['add'] = $this->url->link('wk_skill_cust/work_status/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['delete'] = $this->url->link('wk_skill_cust/work_status/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$data['work_statuses'] = array();

		$filter_data = array(
			'sort'  => $sort,
			'work' => $work,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);

		$work_status_total = $this->model_wk_skill_cust_customer->getTotalWorkStatuses();

		$results = $this->model_wk_skill_cust_customer->getWorkStatuses($filter_data);

		foreach ($results as $result) {
			$data['work_statuses'][] = array(
				'work_status_id' => $result['work_status_id'],
				'name'            => $result['name'] . (($result['work_status_id'] == $this->config->get('config_order_status_id')) ? $this->language->get('text_default') : null),
				'edit'            => $this->url->link('wk_skill_cust/work_status/edit', 'user_token=' . $this->session->data['user_token'] . '&work_status_id=' . $result['work_status_id'] . $url, true)
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

		if ($work == 'ASC') {
			$url .= '&work=DESC';
		} else {
			$url .= '&work=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_name'] = $this->url->link('wk_skill_cust/work_status', 'user_token=' . $this->session->data['user_token'] . '&sort=name' . $url, true);

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $work_status_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('wk_skill_cust/work_status', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($work_status_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($work_status_total - $this->config->get('config_limit_admin'))) ? $work_status_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $work_status_total, ceil($work_status_total / $this->config->get('config_limit_admin')));

		$data['sort'] = $sort;
		$data['work'] = $work;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('wk_skill_cust/work_status_list', $data));
	}

	protected function getForm() {
		$data['text_form'] = !isset($this->request->get['work_status_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

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

		if (isset($this->request->get['work'])) {
			$url .= '&work=' . $this->request->get['work'];
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
			'href' => $this->url->link('wk_skill_cust/work_status', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		if (!isset($this->request->get['work_status_id'])) {
			$data['action'] = $this->url->link('wk_skill_cust/work_status/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		} else {
			$data['action'] = $this->url->link('wk_skill_cust/work_status/edit', 'user_token=' . $this->session->data['user_token'] . '&work_status_id=' . $this->request->get['work_status_id'] . $url, true);
		}

		$data['cancel'] = $this->url->link('wk_skill_cust/work_status', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$this->load->model('localisation/language');

		$data['languages'] = $this->model_localisation_language->getLanguages();

		if (isset($this->request->post['work_status'])) {
			$data['work_status'] = $this->request->post['work_status'];
		} elseif (isset($this->request->get['work_status_id'])) {
			$data['work_status'] = $this->model_wk_skill_cust_customer->getWorkStatusDescriptions($this->request->get['work_status_id']);
		} else {
			$data['work_status'] = array();
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('wk_skill_cust/work_status_form', $data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'wk_skill_cust/work_status')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		foreach ($this->request->post['work_status'] as $language_id => $value) {
			if ((utf8_strlen($value['name']) < 3) || (utf8_strlen($value['name']) > 32)) {
				$this->error['name'][$language_id] = $this->language->get('error_name');
			}
		}

		return !$this->error;
	}

}
