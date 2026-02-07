<?php
class ControllerWkSkillCustWorkerRecharge extends Controller {
	public function index() {
		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('wk_skill_cust/worker_recharge', '', true);

			$this->response->redirect($this->url->link('account/login', '', true));
		}

		$this->load->language('wk_skill_cust/worker_recharge');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$url = '';

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}
		
		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_account'),
			'href' => $this->url->link('account/account', '', true)
		);
		
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('wk_skill_cust/worker_recharge', $url, true)
		);

		if (isset($this->request->get['page'])) {
			$page = (int)$this->request->get['page'];
		} else {
			$page = 1;
		}

		$limit = 10;

		$data['recharges'] = array();

		$this->load->model('wk_skill_cust/recharge');

		$recharge_total = $this->model_wk_skill_cust_recharge->getTotalRecharge();

		$results = $this->model_wk_skill_cust_recharge->getRecharges(($page - 1) * $limit, $limit);

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
                      'view' => $this->url->link('wk_skill_cust/worker_recharge/info', 'user_token=' . '&recharge_id=' . $result['recharge_id'], true)
         );

		}

		$pagination = new Pagination();
		$pagination->total = $recharge_total;
		$pagination->page = $page;
		$pagination->limit = $limit;
		$pagination->url = $this->url->link('wk_skill_cust/worker_recharge', 'page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($recharge_total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($recharge_total - $limit)) ? $recharge_total : ((($page - 1) * $limit) + $limit), $recharge_total, ceil($recharge_total / $limit));

		$data['continue'] = $this->url->link('account/account', '', true);

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');
		$this->response->setOutput($this->load->view('wk_skill_cust/worker_recharge_list', $data));
	}

	public function info() {
		$this->load->language('wk_skill_cust/worker_recharge_view');
        $this->load->model('wk_skill_cust/recharge');
		if (isset($this->request->get['recharge_id'])) {
			$recharge_id = $this->request->get['recharge_id'];
		} else {
			$recharge_id = 0;
		}

		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('wk_skill_cust/worker_recharge/info', 'recharge_id=' . $recharge_id, true);

			$this->response->redirect($this->url->link('account/login', '', true));
		}

		$this->load->model('account/order');

	
	    $recharges = $this->model_wk_skill_cust_recharge->getRechargeDetails($recharge_id);

		if ($recharges) {
			$this->document->setTitle($this->language->get('text_order'));

			$url = '';

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$data['breadcrumbs'] = array();

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_home'),
				'href' => $this->url->link('common/home')
			);

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_account'),
				'href' => $this->url->link('account/account', '', true)
			);

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('wk_skill_cust/worker_recharge', $url, true)
			);

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_order'),
				'href' => $this->url->link('wk_skill_cust/worker_recharge/info', 'recharge_id=' . $this->request->get['recharge_id'] . $url, true)
			);

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
            
			        // Fetch recharge + related data

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
            $order_info = $this->model_account_order->getOrder($current['order_id']);
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

			$data['continue'] = $this->url->link('wk_skill_cust/worker_recharge', '', true);
			$data['back'] = $this->url->link('wk_skill_cust/worker_recharge', '', true);

			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');
			$this->response->setOutput($this->load->view('wk_skill_cust/worker_recharge_info', $data));
		} else {
			return new Action('error/not_found');
		}
	}

	public function reorder() {
		$this->load->language('account/order');

		if (isset($this->request->get['order_id'])) {
			$order_id = $this->request->get['order_id'];
		} else {
			$order_id = 0;
		}

		$this->load->model('account/order');

		$order_info = $this->model_account_order->getOrder($order_id);

		if ($order_info) {
			if (isset($this->request->get['order_product_id'])) {
				$order_product_id = $this->request->get['order_product_id'];
			} else {
				$order_product_id = 0;
			}

			$order_product_info = $this->model_account_order->getOrderProduct($order_id, $order_product_id);

			if ($order_product_info) {
				$this->load->model('catalog/product');

				$product_info = $this->model_catalog_product->getProduct($order_product_info['product_id']);

				if ($product_info) {
					$option_data = array();

					$order_options = $this->model_account_order->getOrderOptions($order_product_info['order_id'], $order_product_id);

					foreach ($order_options as $order_option) {
						if ($order_option['type'] == 'select' || $order_option['type'] == 'radio' || $order_option['type'] == 'image') {
							$option_data[$order_option['product_option_id']] = $order_option['product_option_value_id'];
						} elseif ($order_option['type'] == 'checkbox') {
							$option_data[$order_option['product_option_id']][] = $order_option['product_option_value_id'];
						} elseif ($order_option['type'] == 'text' || $order_option['type'] == 'textarea' || $order_option['type'] == 'date' || $order_option['type'] == 'datetime' || $order_option['type'] == 'time') {
							$option_data[$order_option['product_option_id']] = $order_option['value'];
						} elseif ($order_option['type'] == 'file') {
							$option_data[$order_option['product_option_id']] = $order_option['value'];
						}
					}

					$this->cart->add($order_product_info['product_id'], $order_product_info['quantity'], $option_data);

					$this->session->data['success'] = sprintf($this->language->get('text_success'), $this->url->link('product/product', 'product_id=' . $product_info['product_id']), $product_info['name'], $this->url->link('checkout/cart'));

					unset($this->session->data['shipping_method']);
					unset($this->session->data['shipping_methods']);
					unset($this->session->data['payment_method']);
					unset($this->session->data['payment_methods']);
				} else {
					$this->session->data['error'] = sprintf($this->language->get('error_reorder'), $order_product_info['name']);
				}
			}
		}

		$this->response->redirect($this->url->link('account/order/info', 'order_id=' . $order_id));
	}
}
