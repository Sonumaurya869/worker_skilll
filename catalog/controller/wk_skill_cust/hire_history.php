<?php
class ControllerWkSkillCustHireHistory extends Controller {
	public function index() {
		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('wk_skill_cust/hire_history', '', true);

			$this->response->redirect($this->url->link('account/login', '', true));
		}

		$this->load->language('wk_skill_cust/hire_history');

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
			'href' => $this->url->link('wk_skill_cust/hire_history', $url, true)
		);

		if (isset($this->request->get['page'])) {
			$page = (int)$this->request->get['page'];
		} else {
			$page = 1;
		}

		$limit = 10;

		$data['hires'] = array();

		$this->load->model('wk_skill_cust/hire_history');

		$hire_total = $this->model_wk_skill_cust_hire_history->getTotalHires();

		$results = $this->model_wk_skill_cust_hire_history->getHires(($page - 1) * $limit, $limit);

		foreach ($results as $result) {
			
			$data['hires'][] = array(
				'history_id'   => $result['history_id'],
				'name'       => $result['worker_name'],
				'skill'      => $result['skill'],
				'price_per_day' => $result['price_per_day'],
				'status'     => $result['status'],
				'date_added' => date($this->language->get('date_format_short'), strtotime($result['created_at'])),
				'view'       => $this->url->link('wk_skill_cust/hire_history/info', 'hire_id=' . $result['history_id'], true),
			);
		}

		$pagination = new Pagination();
		$pagination->total = $hire_total;
		$pagination->page = $page;
		$pagination->limit = $limit;
		$pagination->url = $this->url->link('wk_skill_cust/hire_history', 'page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($hire_total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($hire_total - $limit)) ? $hire_total : ((($page - 1) * $limit) + $limit), $hire_total, ceil($hire_total / $limit));

		$data['continue'] = $this->url->link('account/account', '', true);

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('wk_skill_cust/hire_list', $data));
	}

	public function info() {
		$this->load->language('wk_skill_cust/hire_history');
		$this->load->model('wk_skill_cust/hire_history');


		if (isset($this->request->get['hire_id'])) {
			$hire_id = $this->request->get['hire_id'];
		} else {
			$hire_id = 0;
		}

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

		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('wk_skill_cust/hire_history/info', 'order_id=' . $order_id, true);

			$this->response->redirect($this->url->link('account/login', '', true));
		}

		

		$hire_info = $this->model_wk_skill_cust_hire_history->getHireInfo($hire_id);

		$data['workerStatusList'] = $this->model_wk_skill_cust_hire_history->getWorkerStatusList();

		if ($hire_info) {
			$this->document->setTitle($this->language->get('text_hire'));

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
				'href' => $this->url->link('wk_skill_cust/hire_history', $url, true)
			);

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_hire_info'),
				'href' => $this->url->link('wk_skill_cust/hire_history/info', 'hire_id=' . $this->request->get['hire_id'] . $url, true)
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

			  // Worker profile link (proper OpenCart URL)
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

			$data['continue'] = $this->url->link('wk_skill_cust/hire_history', '', true);

			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');
			$this->response->setOutput($this->load->view('wk_skill_cust/hire_history_info', $data));
		} else {
			return new Action('error/not_found');
		}
	}

	
}
