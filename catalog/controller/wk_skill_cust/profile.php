<?php
class ControllerWkSkillCustProfile extends Controller {
    public function index() {
        $this->load->language('wk_skill_cust/skills');
        $this->load->model('wk_skill_cust/skills');
        $this->load->model('account/customer');
        $this->load->model('tool/image');

        // Get worker ID from URL
        $worker_id = isset($this->request->get['worker_id']) ? (int)$this->request->get['worker_id'] : 0;

        if (!$worker_id) {
            $this->response->redirect($this->url->link('common/home'));
        }

        // Load worker and customer info
        $worker_info = $this->model_wk_skill_cust_skills->getWorkerInfo($worker_id);
        $customer_info = $this->model_account_customer->getCustomer($worker_id);

        if (!$customer_info) {
            $this->response->redirect($this->url->link('common/home'));
        }

        $this->document->setTitle($this->language->get('heading_title'));

        // Breadcrumbs
        $data['breadcrumbs'] = [
            [
                'text' => $this->language->get('text_home'),
                'href' => $this->url->link('common/home')
            ],
            [
                'text' => $this->language->get('text_profile'),
                'href' => $this->url->link('wk_skill_cust/profile', 'worker_id=' . $worker_id, true)
            ]
        ];

        // Display data
        $data['firstname'] = $customer_info['firstname'];
        $data['lastname'] = $customer_info['lastname'];
        $data['email'] = $customer_info['email'];
        $data['telephone'] = $customer_info['telephone'];

        $data['occupation'] = $worker_info['occupation_id'] ?? '';
        $data['date_of_birth'] = $worker_info['date_of_birth'] ?? '';
        $data['gender'] = $worker_info['gender'] ?? '';
        $data['range'] = $worker_info['range_km'] ?? '';
        $data['experience'] = $worker_info['experience'] ?? '';
        $data['description'] = $worker_info['description'] ?? '';
        $data['price_per_day'] = $worker_info['price_per_day'] ?? '';
        $data['wk_status'] = $worker_info['wk_status'] ?? '';
        $data['busy_dates'] = explode(',', $worker_info['busy_date'] ?? '');

        // Worker image
        $worker_image = $worker_info['image'] ?? '';
        if ($worker_image && is_file(DIR_IMAGE . $worker_image)) {
            $data['thumb'] = $this->model_tool_image->resize($worker_image, 100, 100);
        } else {
            $data['thumb'] = $this->model_tool_image->resize('no_image.png', 100, 100);
        }

        // Occupation label
        $occupations = $this->model_wk_skill_cust_skills->getSkills();
        $occupation_lookup = [];
        foreach ($occupations as $occupation) {
            $occupation_lookup[$occupation['id']] = $occupation['skill'];
        }
        $data['occupation_name'] = $occupation_lookup[$data['occupation']] ?? 'N/A';

        $reviews = $this->model_wk_skill_cust_skills->getReviews($worker_id);
        $data['average_rating'] = $this->model_wk_skill_cust_skills->getRating($worker_id);
        $data['total_reviews'] = count($reviews);

    $data['reviews'] = [];
          foreach ($reviews as $review) {
            $customer_info = $this->model_account_customer->getCustomer($review['customer_id']);
             $data['reviews'][] = [
                'review_id'   => $review['review_id'],
                'customer_name'        => $customer_info['firstname'] . ' ' . $customer_info['lastname'],
                'worker_id'   => $review['worker_id'],
                'customer_id' => $review['customer_id'],
                'rating'      => $review['rating'],
                'title'       => $review['title'],
                'review'      => $review['review'],
                'recommend'   => $review['recommend'],
                'date_added'  => $review['date_added']
              ];
           }



        // Layout
        $data['back_url'] = $this->url->link('common/home', '', true);
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['column_right'] = $this->load->controller('common/column_right');
        $data['content_top'] = $this->load->controller('common/content_top');
        $data['content_bottom'] = $this->load->controller('common/content_bottom');
        $data['footer'] = $this->load->controller('common/footer');
        $data['header'] = $this->load->controller('common/header');

        $this->response->setOutput($this->load->view('wk_skill_cust/profile', $data));
    }
}
