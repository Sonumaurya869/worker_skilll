<?php
class ControllerWkSkillCustSkills extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('wk_skill_cust/skills');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('wk_skill_cust/skills');

		$this->getList();
	}

	public function add() {
		$this->load->language('wk_skill_cust/skills');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('wk_skill_cust/skills');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_wk_skill_cust_skills->addSkill($this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_skill'])) {
				$url .= '&filter_skill=' . urlencode(html_entity_decode($this->request->get['filter_skill'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			}


			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('wk_skill_cust/skills', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function edit() {
		$this->load->language('wk_skill_cust/skills');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('wk_skill_cust/skills');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_wk_skill_cust_skills->editSkill($this->request->get['skill_id'], $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_skill'])) {
				$url .= '&filter_skill=' . urlencode(html_entity_decode($this->request->get['filter_skill'], ENT_QUOTES, 'UTF-8'));
			}

			
			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			}

			
			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('wk_skill_cust/skills', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('wk_skill_cust/skills');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('wk_skill_cust/skills');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $skill_id) {
				$this->model_wk_skill_cust_skills->deleteSkill($skill_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_skill'])) {
				$url .= '&filter_skill=' . urlencode(html_entity_decode($this->request->get['filter_skill'], ENT_QUOTES, 'UTF-8'));
			}


			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			}


			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('wk_skill_cust/skills', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getList();
	}

	protected function getList() {
		if (isset($this->request->get['filter_skill'])) {
			$filter_skill = $this->request->get['filter_skill'];
		} else {
			$filter_skill = '';
		}


		if (isset($this->request->get['filter_status'])) {
			$filter_status = $this->request->get['filter_status'];
		} else {
			$filter_status = '';
		}


		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'DESC';
		}

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'r.date_added';
		}

		if (isset($this->request->get['page'])) {
			$page = (int)$this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($this->request->get['filter_skill'])) {
			$url .= '&filter_skill=' . urlencode(html_entity_decode($this->request->get['filter_skill'], ENT_QUOTES, 'UTF-8'));
		}


		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}


		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
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
			'href' => $this->url->link('wk_skill_cust/skills', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['add'] = $this->url->link('wk_skill_cust/skills/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['delete'] = $this->url->link('wk_skill_cust/skills/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$data['skills'] = array();

		$filter_data = array(
			'filter_skill'    => $filter_skill,
			'filter_status'     => $filter_status,
			'sort'              => $sort,
			'order'             => $order,
			'start'             => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'             => $this->config->get('config_limit_admin')
		);

		$skill_total = $this->model_wk_skill_cust_skills->getTotalSkills($filter_data);

		$results = $this->model_wk_skill_cust_skills->getSkills($filter_data);

		foreach ($results as $result) {
			$data['skills'][] = array(
				'skill_id'  => $result['id'],
				'name'       => $result['skill'],
				'status'     => ($result['status']) ? $this->language->get('text_enabled') : $this->language->get('text_disabled'),
				'edit'       => $this->url->link('wk_skill_cust/skills/edit', 'user_token=' . $this->session->data['user_token'] . '&skill_id=' . $result['id'] . $url, true)
			);
		}

		$data['user_token'] = $this->session->data['user_token'];

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

		if (isset($this->request->get['filter_skill'])) {
			$url .= '&filter_skill=' . urlencode(html_entity_decode($this->request->get['filter_skill'], ENT_QUOTES, 'UTF-8'));
		}


		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_product'] = $this->url->link('catalog/review', 'user_token=' . $this->session->data['user_token'] . '&sort=pd.skill' . $url, true);
		$data['sort_status'] = $this->url->link('catalog/review', 'user_token=' . $this->session->data['user_token'] . '&sort=r.status' . $url, true);

		$url = '';

		if (isset($this->request->get['filter_skill'])) {
			$url .= '&filter_skill=' . urlencode(html_entity_decode($this->request->get['filter_skill'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		
		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $skill_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('wk_skill_cust/skills', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($skill_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($skill_total - $this->config->get('config_limit_admin'))) ? $skill_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $skill_total, ceil($skill_total / $this->config->get('config_limit_admin')));

		$data['filter_skill'] = $filter_skill;
		$data['filter_status'] = $filter_status;
		
		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('wk_skill_cust/skill_list', $data));
	}

	protected function getForm() {
		$data['text_form'] = !isset($this->request->get['skill_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['skill'])) {
			$data['error_skill'] = $this->error['skill'];
		} else {
			$data['error_skill'] = '';
		}



		$url = '';

		if (isset($this->request->get['filter_skill'])) {
			$url .= '&filter_skill=' . urlencode(html_entity_decode($this->request->get['filter_skill'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}


		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
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
			'href' => $this->url->link('wk_skill_cust/skills', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		if (!isset($this->request->get['skill_id'])) {
			$data['action'] = $this->url->link('wk_skill_cust/skills/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		} else {
			$data['action'] = $this->url->link('wk_skill_cust/skills/edit', 'user_token=' . $this->session->data['user_token'] . '&skill_id=' . $this->request->get['skill_id'] . $url, true);
		}

		$data['cancel'] = $this->url->link('wk_skill_cust/skills', 'user_token=' . $this->session->data['user_token'] . $url, true);

		if (isset($this->request->get['skill_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$skill_info = $this->model_wk_skill_cust_skills->getSkill($this->request->get['skill_id']);
		}

	
		$data['user_token'] = $this->session->data['user_token'];

		$this->load->model('wk_skill_cust/skills');

		if (isset($this->request->post['skill_id'])) {
			$data['skill_id'] = $this->request->post['skill_id'];
		} elseif (!empty($skill_info)) {
			$data['skill_id'] = $skill_info['id'];
		} else {
			$data['skill_id'] = '';
		}

		if (isset($this->request->post['skill'])) {
			$data['skill'] = $this->request->post['skill'];
		} elseif (!empty($skill_info)) {
			$data['skill'] = $skill_info['skill'];
		} else {
			$data['skill'] = '';
		}

		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($skill_info)) {
			$data['status'] = $skill_info['status'];
		} else {
			$data['status'] = '';
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('wk_skill_cust/skill_form', $data));
	}

	protected function validateForm() {

	
		if (!$this->user->hasPermission('modify', 'wk_skill_cust/skills')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

	     if (!isset($this->request->post['skill']) || utf8_strlen(trim($this->request->post['skill'])) < 3 || utf8_strlen($this->request->post['skill']) > 15) {
              $this->error['skill'] = $this->language->get('error_skill');
           }


		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'wk_skill_cust/skills')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}
