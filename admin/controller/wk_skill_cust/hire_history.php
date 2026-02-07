<?php
class ControllerWkSkillCustHireHistory extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('wk_skill_cust/hire_history');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('wk_skill_cust/hire_history');

		$this->getList();
	}

	protected function getList() {
		if (isset($this->request->get['filter_name'])) {
			$filter_name = $this->request->get['filter_name'];
		} else {
			$filter_name = '';
		}

		if (isset($this->request->get['filter_email'])) {
			$filter_email = $this->request->get['filter_email'];
		} else {
			$filter_email = '';
		}

		if (isset($this->request->get['filter_plan'])) {
			$filter_plan = $this->request->get['filter_plan'];
		} else {
			$filter_plan = '';
		}

		if (isset($this->request->get['filter_status'])) {
			$filter_status = $this->request->get['filter_status'];
		} else {
			$filter_status = '';
		}

		if (isset($this->request->get['filter_start_date'])) {
			$filter_start_date = $this->request->get['filter_start_date'];
		} else {
			$filter_start_date = '';
		}

		if (isset($this->request->get['filter_end_date'])) {
			$filter_end_date = $this->request->get['filter_end_date'];
		} else {
			$filter_end_date = '';
		}

		if (isset($this->request->get['filter_recharge_id'])) {
			$filter_recharge_id = $this->request->get['filter_recharge_id'];
		} else {
			$filter_recharge_id = '';
		}

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'r.id';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
		}    

		if (isset($this->request->get['page'])) {
			$page = (int)$this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_email'])) {
			$url .= '&filter_email=' . urlencode(html_entity_decode($this->request->get['filter_email'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_plan'])) {
			$url .= '&filter_plan=' . $this->request->get['filter_plane'];
		}

		if (isset($this->request->get['filter_start_date'])) {
			$url .= '&filter_start_date=' . $this->request->get['filter_start_date'];
		}

		if (isset($this->request->get['filter_end_date'])) {
			$url .= '&filter_end_date=' . $this->request->get['filter_end_date'];
		}

	    if (isset($this->request->get['filter_recharge_id'])) {
			$url .= '&filter_recharge_id=' . $this->request->get['filter_recharge_id'];
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
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
			'href' => $this->url->link('wk_skill_cust/recharge', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['delete'] = $this->url->link('wk_skill_cust/recharge/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$data['hires'] = array();

		$filter_data = array(
			'filter_name'	  => $filter_name,
			'filter_email'	  => $filter_email,
			'filter_plan'	  => $filter_plan,
			'filter_end_date' => $filter_end_date,
			'filter_start_date' => $filter_start_date,
			'filter_recharge_id' => $filter_recharge_id,
			'filter_status'   => $filter_status,
			'sort'            => $sort,
			'order'           => $order,
			'start'           => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'           => $this->config->get('config_limit_admin')
		);

		$this->load->model('tool/image');

		$hire_total = $this->model_wk_skill_cust_hire_history->getTotalHires($filter_data);

		$results = $this->model_wk_skill_cust_hire_history->getHires($filter_data);

        foreach ($results as $result) {
			
			$data['hires'][] = array(
				'history_id'   => $result['history_id'],
                'customer_name'       => $result['customer_name'],
				'worker_name'       => $result['worker_name'],
				'skill'      => $result['skill'],
				'request_status'     => $result['status'],
				'date_added' => date($this->language->get('date_format_short'), strtotime($result['created_at'])),
				'view'       => $this->url->link('wk_skill_cust/hire_history/info', 'user_token=' . $this->session->data['user_token'] .  '&hire_id=' . $result['history_id'], true),
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

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_email'])) {
			$url .= '&filter_email=' . urlencode(html_entity_decode($this->request->get['filter_email'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_plan'])) {
			$url .= '&filter_plan=' . $this->request->get['filter_plan'];
		}

		if (isset($this->request->get['filter_start_date'])) {
			$url .= '&filter_start_date=' . $this->request->get['filter_start_date'];
		}

		if (isset($this->request->get['filter_end_date'])) {
			$url .= '&filter_end_date=' . $this->request->get['filter_end_date'];
		}

		if (isset($this->request->get['filter_recharge_id'])) {
			$url .= '&filter_recharge_id=' . $this->request->get['filter_recharge_id'];
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

		$data['sort_recharge'] = $this->url->link('wk_skill_cust/hire_history', 'user_token=' . $this->session->data['user_token'] . '&sort=r.id' . $url, true);
		$data['sort_name'] = $this->url->link('wk_skill_cust/hire_history', 'user_token=' . $this->session->data['user_token'] . '&sort=c.name' . $url, true);
		$data['sort_start_date'] = $this->url->link('wk_skill_cust/hire_history', 'user_token=' . $this->session->data['user_token'] . '&sort=st.date' . $url, true);
		$data['sort_end_date'] = $this->url->link('wk_skill_cust/hire_history', 'user_token=' . $this->session->data['user_token'] . '&sort=ed.date' . $url, true);
		$data['sort_order'] = $this->url->link('wk_skill_cust/hire_history', 'user_token=' . $this->session->data['user_token'] . '&sort=p.sort_order' . $url, true);

		$url = '';
        if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_email'])) {
			$url .= '&filter_email=' . urlencode(html_entity_decode($this->request->get['filter_email'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_plan'])) {
			$url .= '&filter_plan=' . $this->request->get['filter_plan'];
		}

		if (isset($this->request->get['filter_start_date'])) {
			$url .= '&filter_start_date=' . $this->request->get['filter_start_date'];
		}

		if (isset($this->request->get['filter_end_date'])) {
			$url .= '&filter_end_date=' . $this->request->get['filter_end_date'];
		}

		if (isset($this->request->get['filter_recharge_id'])) {
			$url .= '&filter_recharge_id=' . $this->request->get['filter_recharge_id'];
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
		$pagination->total = $hire_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('wk_skill_cust/hire_history', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($hire_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($hire_total - $this->config->get('config_limit_admin'))) ? $hire_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $hire_total, ceil($hire_total / $this->config->get('config_limit_admin')));

		$data['filter_name'] = $filter_name;
		$data['filter_email'] = $filter_email;
		$data['filter_plan'] = $filter_plan;
		$data['filter_start_date'] = $filter_start_date;
		$data['filter_end_date'] = $filter_end_date;
		$data['filter_recharge_id'] = $filter_recharge_id;
		$data['filter_status'] = $filter_status;

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('wk_skill_cust/hire_list', $data));
	}

    public function info() {
        $this->load->language('wk_skill_cust/hire_history');
        $this->load->model('wk_skill_cust/hire_history');
        $this->load->model('sale/order');

        $this->document->setTitle($this->language->get('heading_view'));

        $hire_id = isset($this->request->get['hire_id']) ? (int)$this->request->get['hire_id'] : 0;
        $user_token  = $this->session->data['user_token'];

        $data['user_token'] = $user_token;
        $data['back'] = $this->url->link('wk_skill_cust/hire_history', 'user_token=' . $user_token, true);

        if ($this->request->server['REQUEST_METHOD'] == 'POST' && isset($this->request->post['hire_end_date'])) {
           $hire_end_date = $this->request->post['hire_end_date'];

            // Optional: validate the date
              if (strtotime($hire_end_date) === false) {
                   $this->session->data['error'] = $this->language->get('error_invalid_date');
                 } else {
                   $this->model_wk_skill_cust_hire_history->updateHireEndDate($hire_id, $hire_end_date);
                   $this->session->data['success'] = $this->language->get('text_end_date_added');
                }

              $this->response->redirect($this->url->link('wk_skill_cust/hire_history/info', 'hire_id=' . $hire_id, true));
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
			'href' => $this->url->link('wk_skill_cust/hire_history', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);


		$data['cancel'] = $this->url->link('wk_skill_cust/hire_history', 'user_token=' . $this->session->data['user_token'] . $url, true);


        $hire_info = $this->model_wk_skill_cust_hire_history->getHireInfo($hire_id);

		$data['workerStatusList'] = $this->model_wk_skill_cust_hire_history->getWorkerStatusList();

        if ($hire_info) {
			$this->document->setTitle($this->language->get('text_hire'));

			if (isset($this->session->data['error'])) {
				$data['error_warning'] = $this->session->data['error'];

				unset($this->session->data['error']);
			} else {
				$data['error_warning'] = '';
			}

			if (isset($this->session->data['success'])) {
				$data['success'] = $this->session->data['success'];

				unset($this->session->data['success']);
			} else {
				$data['success'] = '';
			}
			$data['hire_id'] = (int)$this->request->get['hire_id'];
              $this->load->model('tool/image');
		
            $data['hire_id'] = $hire_info['history_id'];
            
            $data['hire_reference'] = $hire_info['history_id'] ? 'HIRE-' . $hire_info['history_id'] : '';

             $data['customer_name'] = $hire_info['customer_name'];
             $data['customer_mobile'] = $hire_info['customer_mobile'] ?: 'N/A';
             $data['customer_address'] = nl2br($hire_info['customer_address']);

             $data['email'] = $hire_info['email'] ?: 'N/A';
             $data['customer_registered_date'] = date($this->language->get('date_format_short'), strtotime($hire_info['customer_registered_date']));

            $data['worker_name'] = $hire_info['worker_name'];
            $data['occupation'] = $hire_info['skill'];
            $data['price_per_day'] = $this->currency->format($hire_info['price_per_day'], $this->session->data['currency']);

            $data['date_of_birth'] = date($this->language->get('date_format_short'), strtotime($hire_info['date_of_birth']));
            $data['range_km'] = $hire_info['range_km'];
            $data['experience'] = $hire_info['experience'];
             $data['description'] = $hire_info['description'];
              $data['status'] = $hire_info['status'];
              $data['avg_rating'] = $hire_info['avg_rating'];
              $data['review_count'] = $hire_info['review_count'];
              $data['likes'] = $hire_info['likes'];
              $data['dislikes'] = $hire_info['dislikes'];

              $data['worker_profile_link'] = $this->url->link('wk_skill_cust/profile', 'worker_id=' . (int)$hire_info['worker_id'], true);
              $data['worker_phone'] = !empty($hire_info['worker_mobile']) ? $hire_info['worker_mobile'] : 'N/A';


              // Format end date for display
               $data['hire_end_date'] = $hire_info['hire_end_date'] ? date($this->language->get('date_format_short'), strtotime($hire_info['hire_end_date'])) : null;

      // Calculate total hire days if end date exists
              if (!empty($hire_info['hire_end_date'])) {
                $start = new DateTime(date('Y-m-d', strtotime($hire_info['created_at'])));
                $end   = new DateTime($hire_info['hire_end_date']);
                $data['hire_days'] = $start->diff($end)->days + 1; // +1 to include start date
               } else {
                   $data['hire_days'] = null;
                 }

// Format total cost for display
                $data['total_cost'] = isset($hire_info['total_cost']) 
                ? $this->currency->format($hire_info['total_cost'], $this->session->data['currency']) 
                : null;

               $data['add_end_date_action'] = $this->url->link('wk_skill_cust/hire_history/info', 'hire_id=' . $hire_id, true);



              $data['created_at'] = $hire_info['created_at'] ? date($this->language->get('date_format_short'), strtotime($hire_info['created_at'])) : '';

              if (!empty($hire_info['image']) && is_file(DIR_IMAGE . $hire_info['image'])) {
                  $data['worker_image'] = $this->model_tool_image->resize($hire_info['image'], 100, 100);
              } else {
                  $data['worker_image'] = $this->model_tool_image->resize('placeholder.png', 100, 100);
              }
        }
        // Standard layout elements
        $data['header']       = $this->load->controller('common/header');
        $data['column_left']  = $this->load->controller('common/column_left');
        $data['footer']       = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('wk_skill_cust/hire_info', $data));
    }
 
}

