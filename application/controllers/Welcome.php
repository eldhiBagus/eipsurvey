<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Welcome extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model(['Survey_model', 'Questions_model']);
	}

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/userguide3/general/urls.html
	 */
	public function index()
	{
		$data['title'] = "EIP Survey | selamat datang.";
		$surveys = $this->Survey_model->get_by_posisi();
		$data['slug'] = $surveys->slug;
		$this->load->view('landing/header', $data);
		$this->load->view('landing/content', $data);
		$this->load->view('landing/footer');
	}

	private function get_token()
	{
		// cek cookie dulu agar bisa bertahan berhari-hari
		$cookie = get_cookie('survey_token');

		if ($cookie) {
			$this->session->set_userdata('survey_token', $cookie);
			return $cookie;
		}

		// jika session belum ada → buat token baru
		if (!$this->session->userdata('survey_token')) {
			$token = bin2hex(random_bytes(16));

			// simpan di session
			$this->session->set_userdata('survey_token', $token);

			// simpan di cookie 7 hari
			set_cookie('survey_token', $token, 60 * 60 * 24 * 7);

			return $token;
		}

		return $this->session->userdata('survey_token');
	}

	public function form($slug)
	{
		$survey = $this->Survey_model->get_by_slug($slug);
		if (!$survey) show_404();

		$token = $this->get_token();

		$questions  = $this->Questions_model->get_by_survey($survey->id);
		$response_id = $this->Survey_model->get_or_create_response($survey->id, $token);
		// $progress   = $this->Survey_model->get_progress($response_id);
		// $prev_survey = $this->Survey_model->get_prev_survey($survey->id);
		// $prev_slug = $prev_survey ? $prev_survey->slug : null;

		$data = [
			'title'     => $survey->title,
			'survey'    => $survey,
			'response_id'  => $response_id,
			'surveys'   => $this->Survey_model->get_all(),
			'questions' => $questions
		];
		$this->load->view('utama/header', $data);
		$this->load->view('utama/nav_utama', $data);
		$this->load->view('utama/utama', $data);
		$this->load->view('utama/footer');
	}

	public function save_step()
	{
		$survey_id = $this->input->post('survey_id');
		$response_id = $this->input->post('response_id');
		$post = $this->input->post();


		foreach ($post as $key => $val) {
			if (strpos($key, 'q_') === 0) {
				$qid = str_replace('q_', '', $key);

				// Jika checkbox → array, ubah menjadi string
				if (is_array($val)) {
					$val = implode(',', $val);
				}

				// CEK apakah jawaban sudah ada untuk response_id + question_id
				$exists = $this->db->where('response_id', $response_id)
					->where('question_id', $qid)
					->get('answers')
					->row();

				if ($exists) {
					// UPDATE data lama
					$this->db->where('response_id', $response_id)
						->where('question_id', $qid)
						->update('answers', [
							'value' => $val
						]);
				} else {
					// INSERT data baru
					$this->db->insert('answers', [
						'response_id' => $response_id,
						'question_id' => $qid,
						'value'       => $val
					]);
				}
			}
		}

		// redirect ke halaman berikutnya
		// $next = $this->Survey_model->get_next_survey($survey_id);

		// if (!is_null($next)) {
		// 	redirect('welcome/form/' . $next->slug);
		// } else {
		// 	redirect('welcome/form/' . $this->Survey_model->get_last_slug());
		// }
		redirect('welcome/finish');
	}

	public function finish()
	{
		$token = $this->get_token();

		$this->db->where('token', $token);
		$this->db->update('responses', ['status' => 'completed']);

		$this->session->unset_userdata('survey_token'); // reset
		$data = [
			'title' => "Survey Completed",
			'surveys' => $this->Survey_model->get_all()
		];
		$this->load->view('utama/header', $data);
		$this->load->view('utama/nav_utama', $data);
		$this->load->view('utama/thanks');
		$this->load->view('utama/footer');
	}



	public function submit()
	{
		$survey_id = $this->input->post('survey_id');
		$post = $this->input->post();

		// kumpulkan jawaban
		$answers = [];
		foreach ($post as $key => $val) {
			if (strpos($key, 'q_') === 0) {
				$qid = str_replace('q_', '', $key);
				$answers[$qid] = $val;
			}
		}

		// simpan hasil ke database
		$this->Survey_model->save_response($survey_id, $answers);
		redirect('welcome/next_halaman/' . $survey_id);
	}

	public function next_halaman($survey_id)
	{
		// $next = $this->Survey_model->get_next_survey($survey_id);
		// $data['surveys'] = $this->Survey_model->get_all();
		// $data['next'] = $next;
		// $survey = $this->Survey_model->get_by_id($survey_id);
		// $data['title'] = $survey->title;
		// $this->load->view('utama/header');
		// $this->load->view('utama/nav_utama', $data);
		// $this->load->view('utama/thanks', $data);
		// $this->load->view('utama/footer');
		$next = $this->Survey_model->get_next_survey($survey_id);
		if ($next) {
			redirect('welcome/form/' . $next->slug);
		} else {
			$this->load->view('utama/header', ['title' => 'EIP Survey | Terima Kasih']);
			$this->load->view('utama/nav_utama');
			$this->load->view('utama/thanks');
			$this->load->view('utama/footer');
		}
	}

	public function daftar()
	{
		$data['title'] = "EIP Survey | Buat Akun.";
		$this->load->view('utama/header', $data);
		$this->load->view('utama/nav_utama');
		$this->load->view('utama/daftar');
		$this->load->view('utama/footer');
	}
}
