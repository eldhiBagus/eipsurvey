<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Survey_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function get_all()
    {
        return $this->db->order_by('posisi', 'asc')->get('surveys')->result();
    }

    public function get_progress($response_id)
    {
        $this->db->where('response_id', $response_id);
        $rows = $this->db->get('answers')->result();

        $output = [];
        foreach ($rows as $r) {
            $output[$r->question_id] = $r->value;
        }

        return $output;
    }

    public function get_or_create_response($survey_id, $token)
    {
        // cek existing response
        $this->db->where('survey_id', $survey_id);
        $this->db->where('token', $token);
        $res = $this->db->get('responses')->row();

        if ($res) return $res->id;

        // insert baru
        $this->db->insert('responses', [
            'survey_id' => $survey_id,
            'token'     => $token,
            'status'    => 'in_progress'
        ]);

        return $this->db->insert_id();
    }


    public function get_last_slug()
    {
        $this->db->select('slug');
        $this->db->from('surveys');
        $this->db->order_by('posisi', 'DESC'); // urutkan dari posisi terbesar
        $this->db->limit(1);

        $row = $this->db->get()->row();

        return $row ? $row->slug : null;
    }


    public function get_survey_number($survey_id)
    {
        // ambil semua survey urut berdasarkan posisi
        $surveys = $this->db->order_by('posisi', 'ASC')->get('surveys')->result();

        $num = 1;
        foreach ($surveys as $s) {
            if ($s->id == $survey_id) {
                return $num; // survei ke berapa
            }
            $num++;
        }

        return 1; // default jika tidak ditemukan
    }

    public function get_total_surveys()
    {
        return $this->db->count_all('surveys');
    }

    public function get_prev_survey($survey_id)
    {
        return $this->db->where(
            'posisi <',
            $this->get_posisi($survey_id)
        )
            ->order_by('posisi', 'DESC')
            ->limit(1)
            ->get('surveys')->row();
    }


    public function get_posisi($survey_id)
    {
        $this->db->select('posisi');
        $this->db->where('id', $survey_id);
        $row = $this->db->get('surveys')->row();

        return $row ? $row->posisi : null;
    }

    public function get_by_posisi()
    {
        return $this->db->where('posisi', '1')->get('surveys')->row();
    }

    public function get_by_slug($slug)
    {
        return $this->db->get_where('surveys', ['slug' => $slug])->row();
    }

    public function get_by_id($id)
    {
        return $this->db->get_where('surveys', ['id' => $id])->row();
    }

    public function insert($data)
    {
        $this->db->insert('surveys', $data);
        return $this->db->insert_id();
    }

    public function update($id, $data)
    {
        $this->db->where('id', $id)->update('surveys', $data);
    }

    public function save_response($survey_id, $answers)
    {
        $this->db->insert('responses', ['survey_id' => $survey_id]);
        $response_id = $this->db->insert_id();

        foreach ($answers as $qid => $ans) {
            if (is_array($ans)) $ans = implode(', ', $ans);
            $this->db->insert('answers', [
                'response_id' => $response_id,
                'question_id' => $qid,
                'value' => $ans
            ]);
        }
    }

    public function get_first_incomplete_survey($token)
    {
        // Ambil semua response user berdasarkan token
        $this->db->where('token', $token);
        $responses = $this->db->order_by('survey_id', 'ASC')->get('responses')->result();

        foreach ($responses as $res) {

            // total pertanyaan pada survei ini
            $total_q = $this->db->where('survey_id', $res->survey_id)->count_all_results('questions');

            // total jawaban user
            $answered = $this->db->where('response_id', $res->id)->count_all_results('answers');

            if ($answered < $total_q) {
                return $this->get_by_id($res->survey_id); // kembali survei
            }
        }

        return null; // semuanya lengkap
    }


    public function get_next_survey($current_id)
    {
        $current = $this->db->get_where('surveys', ['id' => $current_id])->row();
        if (!$current) return null;

        $next = $this->db
            ->order_by('posisi', 'asc')
            ->where('posisi >', $current->posisi)
            ->get('surveys')
            ->row();

        return $next;
    }

    public function delete($id)
    {
        $this->db->delete('surveys', ['id' => $id]);
    }
}