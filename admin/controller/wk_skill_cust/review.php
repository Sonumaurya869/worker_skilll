<?php
class ControllerWkSkillCustReview extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('wk_skill_cust/skills');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('wk_skill_cust/skills');

		//$this->getList();
	}
}