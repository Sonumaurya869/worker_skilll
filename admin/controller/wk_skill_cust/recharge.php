<?php
class ControllerWkSkillCustRecharge extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('wk_skill_cust/recharge');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('wk_skill_cust/recharge');

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

		$data['recharges'] = array();

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

		$recharge_total = $this->model_wk_skill_cust_recharge->getTotalRecharges($filter_data);

		$results = $this->model_wk_skill_cust_recharge->getRecharges($filter_data);

		foreach ($results as $result) {

           $customer = $this->model_wk_skill_cust_recharge->getCustomerInfo($result['customer_id']);
           $plan = $this->model_wk_skill_cust_recharge->getPlanInfo($result['plan_id']);
           if($result['status']==1) {
			  $result['status']= 'Active';
		   } elseif($result['status']== -1){
			  $result['status']= 'Expired';
		   } else {
              $result['status']= 'Pending';
		   }

			 $data['recharges'][] = array(
                      'recharge_id' => $result['recharge_id'],
                      'customer_name' => $customer['firstname'] . ' ' . $customer['lastname'],
                      'customer_email' => $customer['email'],
                      'plan' => $plan['plan_name'],
                      'start_date' => $result['start_date'],
                      'end_date' => $result['end_date'],
                      'status' => $result['status'],
                      'view' => $this->url->link('wk_skill_cust/recharge/view', 'user_token=' . $this->session->data['user_token'] . '&recharge_id=' . $result['recharge_id'], true)
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

		$data['sort_recharge'] = $this->url->link('wk_skill_cust/recharge', 'user_token=' . $this->session->data['user_token'] . '&sort=r.id' . $url, true);
		$data['sort_name'] = $this->url->link('wk_skill_cust/recharge', 'user_token=' . $this->session->data['user_token'] . '&sort=c.name' . $url, true);
		$data['sort_start_date'] = $this->url->link('wk_skill_cust/recharge', 'user_token=' . $this->session->data['user_token'] . '&sort=st.date' . $url, true);
		$data['sort_end_date'] = $this->url->link('wk_skill_cust/recharge', 'user_token=' . $this->session->data['user_token'] . '&sort=ed.date' . $url, true);
		$data['sort_order'] = $this->url->link('wk_skill_cust/recharge', 'user_token=' . $this->session->data['user_token'] . '&sort=p.sort_order' . $url, true);

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
		$pagination->total = $recharge_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('wk_skill_cust/recharge', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($recharge_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($recharge_total - $this->config->get('config_limit_admin'))) ? $recharge_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $recharge_total, ceil($recharge_total / $this->config->get('config_limit_admin')));

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

		$this->response->setOutput($this->load->view('wk_skill_cust/recharge_list', $data));
	}

    public function view() {
        $this->load->language('wk_skill_cust/recharge_view');
        $this->load->model('wk_skill_cust/recharge');
        $this->load->model('sale/order');

        $this->document->setTitle($this->language->get('heading_view'));

        $recharge_id = isset($this->request->get['recharge_id']) ? (int)$this->request->get['recharge_id'] : 0;
        $user_token  = $this->session->data['user_token'];

        $data['user_token'] = $user_token;
        $data['back'] = $this->url->link('wk_skill_cust/recharge', 'user_token=' . $user_token, true);

        // Fetch recharge + related data
        $recharges = $this->model_wk_skill_cust_recharge->getRechargeDetails($recharge_id);
        $data['recharges'] = $recharges;

        if (!$recharges || empty($recharges['current'])) {
            $this->session->data['error_warning'] = 'Recharge not found!';
            $this->response->redirect($data['back']);
            return;
        }

        $current = $recharges['current'];
        $now = new DateTime();
        $start_date = new DateTime($current['start_date']);
        $end_date   = new DateTime($current['end_date']);

        // Determine status class/text — updated logic
        if ($end_date < $now) {
            $data['current_status_text']  = 'Expired';
            $data['current_status_class'] = 'danger';
        } elseif ($start_date > $now) {
            $data['current_status_text']  = 'Pending';
            $data['current_status_class'] = 'warning';
        } elseif ($current['status'] == 1) {
            $data['current_status_text']  = 'Active';
            $data['current_status_class'] = 'success';
        } else {
            $data['current_status_text']  = 'Pending';
            $data['current_status_class'] = 'secondary';
        }

        // Calculate usage progress (only for active)
        if ($data['current_status_text'] == 'Active') {
            $total_days = (int)$current['duration'];
            $passed_days = $now->diff($start_date)->days;
            $progress = round(($passed_days / max($total_days, 1)) * 100);
            if ($progress > 100) $progress = 100;
        } else {
            $progress = 0;
        }
        $data['progress'] = $progress;

        // Optional: load order details if not already included
        if (!empty($current['order_id'])) {
            $order_info = $this->model_sale_order->getOrder($current['order_id']);
            if ($order_info) {
                $data['recharges']['current']['order_date'] = $order_info['date_added'];
                $data['recharges']['current']['payment_method'] = $order_info['payment_method'];
                $data['recharges']['current']['order_total'] = $this->currency->format(
                    $order_info['total'],
                    $order_info['currency_code'],
                    $order_info['currency_value']
                );
            }
        }

        // Remove expired recharges from "related" unless we are viewing an expired one
        if ($data['current_status_text'] != 'Expired' && !empty($recharges['related'])) {
            $data['recharges']['related'] = array_filter($recharges['related'], function ($r) {
                $now = date('Y-m-d H:i:s');
                return $r['end_date'] > $now; // hide expired
            });
        }

        // Common UI text
        $data['heading_view']           = $this->language->get('heading_view');
        $data['text_customer']          = $this->language->get('text_customer');
        $data['text_order']             = $this->language->get('text_order');
        $data['text_current_recharge']  = $this->language->get('text_current_recharge');
        $data['text_related_recharge']  = $this->language->get('text_related_recharge');
        $data['button_back']            = $this->language->get('button_back');

        // Standard layout elements
        $data['header']       = $this->load->controller('common/header');
        $data['column_left']  = $this->load->controller('common/column_left');
        $data['footer']       = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('wk_skill_cust/recharge_view', $data));
    }







}

