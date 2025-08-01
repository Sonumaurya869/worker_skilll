<?php
class ControllerWkSkillCustAccountUpdate extends Controller {
	public function index() {

        $this->load->language('wk_skill_cust/skills');
	    $this->load->model('wk_skill_cust/skills');
		$this->load->model('account/customer');
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_account_customer->editCustomer($this->customer->getId(), $this->request->post);
            $this->model_wk_skill_cust_skills->updateWorkerInfo($this->customer->getId(), $this->request->post);
			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('account/account', '', true));
		}

        $customer_id = $this->customer->getId();
        $worker_info = $this->model_wk_skill_cust_skills->getWorkerInfo($customer_id);
		$customer_info = $this->model_account_customer->getCustomer($this->customer->getId());
        


        $this->document->setTitle($this->language->get('heading_title'));

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_account'),
			'href' => $this->url->link('account/account')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_account_update'),
			'href' => $this->url->link('wk_skill_cust/account_update', '', true)
		);

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		} 
	    
          if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['firstname'])) {
			$data['error_firstname'] = $this->error['firstname'];
		} else {
			$data['error_firstname'] = '';
		}

		if (isset($this->error['lastname'])) {
			$data['error_lastname'] = $this->error['lastname'];
		} else {
			$data['error_lastname'] = '';
		}

		if (isset($this->error['email'])) {
			$data['error_email'] = $this->error['email'];
		} else {
			$data['error_email'] = '';
		}

		if (isset($this->error['telephone'])) {
			$data['error_telephone'] = $this->error['telephone'];
		} else {
			$data['error_telephone'] = '';
		}


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


		 $this->load->model('tool/image');

        if (!empty($this->request->post['worker_image']) && is_file(DIR_IMAGE . $this->request->post['worker_image'])) {
             $data['thumb'] = $this->model_tool_image->resize($this->request->post['worker_image'], 100, 100);
              $data['worker_image'] = $this->request->post['worker_image'];
        } elseif (!empty($worker_info['image']) && is_file(DIR_IMAGE . $worker_info['image'])) {
              $data['thumb'] = $this->model_tool_image->resize($worker_info['image'], 100, 100);
              $data['worker_image'] = $worker_info['image'];
        } else {
              $data['thumb'] = $this->model_tool_image->resize('no_image.png', 100, 100);
               $data['worker_image'] = '';
        }

		if (isset($this->request->post['firstname'])) {
			$data['firstname'] = $this->request->post['firstname'];
		} elseif (!empty($customer_info)) {
			$data['firstname'] = $customer_info['firstname'];
		} else {
			$data['firstname'] = '';
		}

		if (isset($this->request->post['lastname'])) {
			$data['lastname'] = $this->request->post['lastname'];
		} elseif (!empty($customer_info)) {
			$data['lastname'] = $customer_info['lastname'];
		} else {
			$data['lastname'] = '';
		}

		if (isset($this->request->post['email'])) {
			$data['email'] = $this->request->post['email'];
		} elseif (!empty($customer_info)) {
			$data['email'] = $customer_info['email'];
		} else {
			$data['email'] = '';
		}

		if (isset($this->request->post['telephone'])) {
			$data['telephone'] = $this->request->post['telephone'];
		} elseif (!empty($customer_info)) {
			$data['telephone'] = $customer_info['telephone'];
		} else {
			$data['telephone'] = '';
		}

		if (isset($this->request->post['occupation_id'])) {
			$data['occupation_id'] = $this->request->post['occupation_id'];
		} elseif (!empty($worker_info['occupation_id'])) {
			$data['occupation_id'] = $worker_info['occupation_id'];
		} else {
			$data['occupation_id'] = '';
		}

	    if (isset($this->request->post['date_of_birth'])) {
			$data['date_of_birth'] = $this->request->post['date_of_birth'];
		} elseif (!empty($worker_info['date_of_birth'])) {
			$data['date_of_birth'] = $worker_info['date_of_birth'];
		} else {
			$data['date_of_birth'] = '';
		}

		if (isset($this->request->post['gender'])) {
			$data['gender'] = $this->request->post['gender'];
		} elseif (!empty($worker_info['gender'])) {
			$data['gender'] = $worker_info['gender'];
		} else {
			$data['gender'] = '';
		}

	    if (isset($this->request->post['range'])) {
			$data['range'] = $this->request->post['range'];
		} elseif (!empty($worker_info['range_km'])) {
			$data['range'] = $worker_info['range_km'];
		} else {
			$data['range'] = '';
		}

		if (isset($this->request->post['experience'])) {
			$data['experience'] = $this->request->post['experience'];
		} elseif (!empty($worker_info['experience'])) {
			$data['experience'] = $worker_info['experience'];
		} else {
			$data['experience'] = '';
		}

		// Get busy dates from form submission
       if (isset($this->request->post['selected_busy_dates'])) {
            $data['busy_dates'] = $this->request->post['selected_busy_dates'];
       } elseif (!empty($worker_info['busy_date'])) {
             $data['busy_dates'] = $worker_info['busy_date'];
        } else {
             $data['busy_dates'] = '';
        }

        // For displaying back in the form (convert back to array if needed)
       if (!empty($data['busy_dates'])) {
          // If stored as comma-separated string, convert to array for display
            $data['busy_dates_array'] = explode(',', $data['busy_dates']);
        } else {
             $data['busy_dates_array'] = array();
          }
//work_description

        if (isset($this->request->post['work_description'])) {
			$data['work_description'] = $this->request->post['work_description'];
		} elseif (!empty($worker_info['description'])) {
			$data['work_description'] = $worker_info['description'];
		} else {
			$data['work_description'] = '';
		}

		if (isset($this->request->post['wk_status'])) {
			$data['wk_status'] = $this->request->post['wk_status'];
		} elseif (!empty($worker_info['wk_status'])) {
			$data['wk_status'] = $worker_info['wk_status'];
		} else {
			$data['wk_status'] = '';
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

		if (isset($this->request->post['price_per_day'])) {
			$data['price_per_day'] = $this->request->post['price_per_day'];
		} elseif (!empty($worker_info['price_per_day'])) {
			$data['price_per_day'] = $worker_info['price_per_day'];
		} else  {
			$data['price_per_day'] = '';
		}

		if (isset($this->request->post['date_of_birth'])) {
			$data['date_of_birth'] = $this->request->post['date_of_birth'];
		} elseif (!empty($worker_info['price_per_day'])) {
			$data['date_of_birth'] = $worker_info['date_of_birth'];
		} else {
			$data['date_of_birth'] = '';
		}


        $data['back_url'] = $this->url->link('account/account', '', true);
		$data['action'] = $this->url->link('wk_skill_cust/account_update', '', true);
        		
        $data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');
        $this->response->setOutput($this->load->view('wk_skill_cust/account_update', $data));

	}

	protected function validate() {
        if (empty($this->request->post['date_of_birth']) || strtotime($this->request->post['date_of_birth']) === false) {
          $this->error['date_of_birth'] = $this->language->get('error_date_of_birth');
        } else {
          $age = (int)date_diff(date_create($this->request->post['date_of_birth']), date_create('today'))->y;
         if ($age < 18) {
            $this->error['date_of_birth'] = $this->language->get('error_age');
        }
      }

      // Validate occupation (must not be empty or 0)
       if (empty($this->request->post['occupation']) || !is_numeric($this->request->post['occupation'])) {
        $this->error['occupation'] = $this->language->get('error_occupation');
       }

       // Validate price_per_day (must be numeric and positive)
       if (!isset($this->request->post['price_per_day']) || !is_numeric($this->request->post['price_per_day']) || $this->request->post['price_per_day'] <= 0) {
         $this->error['price_per_day'] = $this->language->get('error_price_per_day');
       }

		if ((utf8_strlen(trim($this->request->post['firstname'])) < 1) || (utf8_strlen(trim($this->request->post['firstname'])) > 32)) {
			$this->error['firstname'] = $this->language->get('error_firstname');
		}

		if ((utf8_strlen(trim($this->request->post['lastname'])) < 1) || (utf8_strlen(trim($this->request->post['lastname'])) > 32)) {
			$this->error['lastname'] = $this->language->get('error_lastname');
		}

		if ((utf8_strlen($this->request->post['email']) > 96) || !filter_var($this->request->post['email'], FILTER_VALIDATE_EMAIL)) {
			$this->error['email'] = $this->language->get('error_email');
		}

		if (($this->customer->getEmail() != $this->request->post['email']) && $this->model_account_customer->getTotalCustomersByEmail($this->request->post['email'])) {
			$this->error['warning'] = $this->language->get('error_exists');
		}

		if ((utf8_strlen($this->request->post['telephone']) < 3) || (utf8_strlen($this->request->post['telephone']) > 32)) {
			$this->error['telephone'] = $this->language->get('error_telephone');
		}

		return !$this->error;
	}
}
