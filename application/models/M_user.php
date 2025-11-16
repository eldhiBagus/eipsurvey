<?php

defined('BASEPATH') or exit('No direct script access allowed');

class M_user extends CI_Model
{
    var $table = 't_user'; //nama tabel dari database
    var $table2 = 't_user_detail'; //nama tabel dari database
    var $table3 = 't_loket'; //nama tabel dari database
    var $column_order = array(null, 'nik', 'nama_user', 'role'); //field yang ada di table user
    var $column_order1 = array(null, 'nama_user', 'tgl_booking', 'no_tiket', 'nama_pelayanan', 'nama_loket', 'stat_booking');
    var $column_search = array('nama_user', 'nik'); //field yang diizin untuk pencarian 
    var $column_search1 = array('nama_user', 'tgl_booking'); //field yang diizin untuk pencarian 
    var $order = array('id_user' => 'asc'); // default order 
    var $order1 = array('tgl_booking' => 'desc'); // default order 

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }
    private function _get_datatables_query()
    {
        $sts = $this->input->post('aktive');
        if ($sts == "" || $sts == '1') {
            $where = $this->db->where('t_user.stat_user', '1');
        } elseif ($sts == '0') {
            $where = $this->db->where('t_user.stat_user', '0');
        }
        $this->db->select('*');
        $this->db->from($this->table);
        $this->db->join($this->table2, 't_user_detail.user_id = t_user.id_user', 'inner');
        $where;
        $i = 0;

        foreach ($this->column_search as $item) // looping awal
        {
            if ($_POST['search']['value']) // jika datatable mengirimkan pencarian dengan metode POST
            {

                if ($i === 0) // looping awal
                {
                    $this->db->group_start();
                    $this->db->like($item, $_POST['search']['value']);
                } else {
                    $this->db->or_like($item, $_POST['search']['value']);
                }

                if (count($this->column_search) - 1 == $i)
                    $this->db->group_end();
            }
            $i++;
        }

        if (isset($_POST['order'])) {
            $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if (isset($this->order)) {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }
    private function _get_data_booking()
    {
        $awal = $this->input->post('awal');
        $akhir = $this->input->post('akhir');
        $tgl1 = date('Y-m-d', strtotime($awal));
        $tgl2 = date('Y-m-d', strtotime($akhir));
        if ($awal == "" && $akhir == "") {
            $where = "";
            $dan = "";
        } elseif ($awal != "" && $akhir == "") {
            $where = $this->db->where('t_booking.tgl_booking', $tgl1);
            $dan = "";
        } elseif ($akhir != null && $awal != null) {
            $where = $this->db->where("t_booking.tgl_booking >=", $tgl1);
            $dan = $this->db->where("t_booking.tgl_booking <=", $tgl2);
        }
        $this->db->select('*');
        $this->db->from('t_booking');
        $this->db->join('t_tiket', 't_tiket.booking_id = t_booking.id_booking', 'inner');
        $this->db->join('t_user', 't_user.id_user= t_booking.user_id', 'inner');
        $this->db->join('t_pelayanan', 't_pelayanan.id_pelayanan= t_booking.pelayanan_id', 'inner');
        $this->db->join('t_loket', 't_pelayanan.id_pelayanan= t_loket.pelayanan_id', 'inner');
        $where;
        $dan;
        $i = 0;

        foreach ($this->column_search1 as $item) // looping awal
        {
            if ($_POST['search']['value']) // jika datatable mengirimkan pencarian dengan metode POST
            {

                if ($i === 0) // looping awal
                {
                    $this->db->group_start();
                    $this->db->like($item, $_POST['search']['value']);
                } else {
                    $this->db->or_like($item, $_POST['search']['value']);
                }

                if (count($this->column_search1) - 1 == $i)
                    $this->db->group_end();
            }
            $i++;
        }

        if (isset($_POST['order'])) {
            $this->db->order_by($this->column_order1[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if (isset($this->order1)) {
            $order = $this->order1;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }

    function get_datatables()
    {
        $this->_get_datatables_query();
        if ($_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        return $query->result();
    }
    function get_data_booking()
    {
        $this->_get_data_booking();
        if ($_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        return $query->result();
    }

    function count_filtered()
    {
        $this->_get_datatables_query();
        $query = $this->db->get();
        return $query->num_rows();
    }
    function count_filtered1()
    {
        $this->_get_data_booking();
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function count_all1()
    {
        $this->db->from('t_booking');
        return $this->db->count_all_results();
    }
    public function count_all()
    {
        $this->db->from($this->table);
        return $this->db->count_all_results();
    }
    public function getUserById()
    {
        $this->db->select('*');
        $this->db->from('users');
        $this->db->where('id', $this->session->userdata('id'));
        return $query = $this->db->get()->row_array();
    }
    public function getUserId($id)
    {
        $this->db->select('*');
        $this->db->from('t_user');
        $this->db->join('t_user_detail', 't_user_detail.user_id = t_user.id_user');
        $this->db->join('t_loket', 't_loket.id_loket = t_user.loket_nama OR t_user.loket_nama IS NULL');
        $this->db->where('t_user.id_user', $id);
        return $query = $this->db->get()->row_array();
    }

    public function cekLoket($id_pelayanan)
    {
        return $this->db->get_where('t_loket', array('pelayanan_id' => $id_pelayanan));
    }

    public function getply()
    {
        return $this->db->get('t_pelayanan')->result_array();
    }

    public function cekBookingan($tgl)
    {
        $this->db->where('tgl_booking', $tgl);
        return $this->db->get('t_booking');
    }

    public function get_max_id($table, $field, $where)
    {
        $this->db->select_max($field);
        $this->db->where($where);
        $sql = $this->db->get($table);
        return $sql;
    }

    public function get_id($table, $where)
    {
        $this->db->where($where);
        $sql = $this->db->get($table);
        return $sql;
    }

    public function cekBookingById($id, $tgl)
    {
        $this->db->select('t_tiket.no_tiket');
        $this->db->from('t_tiket');
        $this->db->join('t_booking', 't_booking.id_booking = t_tiket.booking_id');
        $this->db->where('t_booking.tgl_booking', $tgl);
        $this->db->where('t_booking.user_id', $id);
        $query = $this->db->get();
        return $query;
    }

    public function getAntrian($id, $booking, $tgl)
    {
        $this->db->select('*');
        $this->db->from('t_user');
        $this->db->join('t_user_detail', 't_user_detail.user_id = t_user.id_user', 'inner');
        $this->db->join('t_booking', 't_booking.user_id = t_user.id_user', 'inner');
        $this->db->join('t_pelayanan', 't_pelayanan.id_pelayanan = t_booking.pelayanan_id', 'inner');
        $this->db->join('t_tiket', 't_tiket.booking_id = t_booking.id_booking', 'inner');
        $this->db->join('t_loket', 't_loket.pelayanan_id = t_pelayanan.id_pelayanan', 'inner');
        $this->db->where('t_booking.tgl_booking', $tgl);
        $this->db->where('t_booking.user_id', $id);
        $this->db->where('t_booking.id_booking', $booking);
        $query = $this->db->get();
        return $query;
    }

    public function loketuser()
    {
        $this->db->select('*');
        $this->db->from('t_loket');
        $this->db->join('t_user', 't_user.loket_nama = t_loket.id_loket', 'inner');
        $this->db->where('t_user.stat_user', '1');
        $this->db->where('t_user.loket_nama !=', null);
        $this->db->order_by('t_loket.nama_loket', 'asc');
        return $this->db->get();
    }

    public function cekUserLoket($loket)
    {
        $query = $this->db->get_where('t_user', array('loket_nama' => $loket));
        return $query;
    }
    public function antri_hari()
    {
        $this->db->where('tgl_booking', date('Y-m-d'));

        return $this->db->get('t_booking');
    }
    public function tot_pemohon()
    {
        $this->db->where('role', '400');

        return $this->db->get('t_user');
    }
    public function blm_proses()
    {
        $this->db->where('stat_booking', '0');
        $this->db->where('tgl_booking', date('Y-m-d'));
        return $this->db->get('t_booking');
    }

    public function getLayanan()
    {
        $this->db->select('*');
        $this->db->from('t_pelayanan');
        $this->db->join('t_loket', 't_loket.pelayanan_id = t_pelayanan.id_pelayanan', 'inner');
        $q = $this->db->get()->result_array();
        return $q;
    }
    public function getLoket()
    {
        return $this->db->get('t_loket')->result_array();
    }
    public function getBooking($awal, $akhir)
    {
        $tgl1 = date('Y-m-d', strtotime($awal));
        $tgl2 = date('Y-m-d', strtotime($akhir));
        if ($awal == "" && $akhir == "") {
            $where = "";
            $dan = "";
        } elseif ($awal != "" && $akhir == "") {
            $where = $this->db->where('t_booking.tgl_booking', $tgl1);
            $dan = "";
        } elseif ($akhir != null && $awal != null) {
            $where = $this->db->where("t_booking.tgl_booking >=", $tgl1);
            $dan = $this->db->where("t_booking.tgl_booking <=", $tgl2);
        }
        $this->db->select('*');
        $this->db->from('t_booking');
        $this->db->join('t_tiket', 't_tiket.booking_id = t_booking.id_booking', 'inner');
        $this->db->join('t_user', 't_user.id_user= t_booking.user_id', 'inner');
        $this->db->join('t_pelayanan', 't_pelayanan.id_pelayanan= t_booking.pelayanan_id', 'inner');
        $this->db->join('t_loket', 't_pelayanan.id_pelayanan= t_loket.pelayanan_id', 'inner');
        $where;
        $dan;
        $q = $this->db->get()->result_array();
        return $q;
    }

    public function updateUser($id, $data)
    {
        $this->db->where('id_user', $id);
        $this->db->update('t_user', $data);
    }
    public function updateDetail($id, $data)
    {
        $this->db->where('user_id', $id);
        $this->db->update('t_user_detail', $data);
    }
}

/* End of file M_user.php */