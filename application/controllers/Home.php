<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {

	// =============================== TEMPLATE BARU ==============================
	public function index()
	{
		
		$this->template->front_end('front_end/v_home');
	}

	public function input_pesan_tamu()
	{
        $nama_tamu = $this->input->post('nama_tamu', TRUE);
        $kontak = $this->input->post('kontak', TRUE);
        $email = $this->input->post('email', TRUE);
        $pesan = $this->input->post('pesan', TRUE);
        $ip_user = $this->get_ip_user();

        $input_data = array(
            'nama_tamu' => $nama_tamu,
            'kontak' => $kontak,
            'email' => $email,
            'pesan' => $pesan,
            'ip_user' => $ip_user,
            'status' => 1
         );

        $this->DataHandle->insert('tbl_tamu', $input_data);
        $this->email_sender($email, $nama_tamu);
        redirect('Home/contact');
	}

	function get_ip_user()
	{
		if(!empty($_SERVER['HTTP_CLIENT_IP'])){
			//ip from share internet
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		}elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
			//ip pass from proxy
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}else{
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		return $ip;
	}

	public function about_us()
	{
		$data['data_sekolah'] = $this->DataHandle->getAllWhere('tbl_sekolah', '*', "status = '1' AND id_sekolah != '0'");
		$this->template->front_endnew('front_endnew/v_about', $data);
	}

	public function all_pengumuman()
	{
		$data['data_pengumuman'] = $this->DataHandle->get_two_o('tbl_pengumuman', 'tbl_user', 'tbl_pengumuman.*, tbl_user.username', 'tbl_pengumuman.created_by = tbl_user.id_user', "tbl_pengumuman.status = '1'", "tbl_pengumuman.id_pengumuman");
		$data['data_sekolah'] = $this->DataHandle->getAllWhere('tbl_sekolah', '*', "status = '1' AND id_sekolah != '0'");
		$this->template->front_endnew('front_endnew/v_all_pengumuman', $data);
	}

	public function detail_pengumuman($id_pengumuman)
	{
		$data['data_pengumuman_detail'] = $this->DataHandle->get_two_o('tbl_pengumuman', 'tbl_user', 'tbl_pengumuman.*, tbl_user.username', 'tbl_pengumuman.created_by = tbl_user.id_user', "tbl_pengumuman.status = '1' AND tbl_pengumuman.id_pengumuman =".$id_pengumuman."", "tbl_pengumuman.id_pengumuman");
		$data['data_pengumuman'] = $this->DataHandle->get_two_o('tbl_pengumuman', 'tbl_user', 'tbl_pengumuman.*, tbl_user.username', 'tbl_pengumuman.created_by = tbl_user.id_user', "tbl_pengumuman.status = '1'", "tbl_pengumuman.id_pengumuman");
		$this->template->front_endnew('front_endnew/v_detail_pengumuman', $data);
	}

	public function all_artikel()
	{
		$data['data_artikel'] = $this->DataHandle->get_two_o('tbl_artikel', 'tbl_user', 'tbl_artikel.*, tbl_user.username', 'tbl_artikel.created_by = tbl_user.id_user', "tbl_artikel.status = '1'", "tbl_artikel.id_artikel");
		$this->template->front_endnew('front_endnew/v_all_artikel', $data);
	}

	public function detail_artikel($id_artikel)
	{
		$data['data_artikel'] = $this->DataHandle->get_two_o('tbl_artikel', 'tbl_user', 'tbl_artikel.*, tbl_user.username', 'tbl_artikel.created_by = tbl_user.id_user', "tbl_artikel.status = '1'", "tbl_artikel.id_artikel");
		$data['data_artikel_detail'] = $this->DataHandle->get_two_o('tbl_artikel', 'tbl_user', 'tbl_artikel.*, tbl_user.username', 'tbl_artikel.created_by = tbl_user.id_user', "tbl_artikel.status = '1' AND tbl_artikel.id_artikel = '".$id_artikel."'", "tbl_artikel.id_artikel");
		$this->template->front_endnew('front_endnew/v_detail_artikel', $data);
	}

	public function sekolah()
	{
		$data['data_sekolah'] = $this->DataHandle->getAllWhere('tbl_sekolah', '*', "status = '1' AND id_sekolah != '0'");
		$this->template->front_endnew('front_endnew/v_daftar_sekolah', $data);
	}

	public function contact()
	{
        $data['data_yayasan'] = $this->DataHandle->getAllWhere('tbl_yayasan', '*', "status = '1'")->row_array();
		$this->template->front_endnew('front_endnew/v_contact', $data);
	}

	public function faq()
	{
		$data['data_faq'] = $this->DataHandle->get_two_o('tbl_faq', 'tbl_user', 'tbl_faq.*, tbl_user.username', 'tbl_faq.created_by = tbl_user.id_user', "tbl_faq.status = '1'", "tbl_faq.id_faq");
		$data['data_artikel'] = $this->DataHandle->get2lim6('tbl_artikel', 'tbl_user', 'tbl_artikel.*, tbl_user.username', 'tbl_artikel.created_by = tbl_user.id_user', "tbl_artikel.status = '1'", "tbl_artikel.id_artikel");
		$data['data_pengumuman'] = $this->DataHandle->get2lim6('tbl_pengumuman', 'tbl_user', 'tbl_pengumuman.*, tbl_user.username', 'tbl_pengumuman.created_by = tbl_user.id_user', "tbl_pengumuman.status = '1'", "tbl_pengumuman.id_pengumuman");
		$this->template->front_endnew('front_endnew/v_faq', $data);
	}

	public function download($nama_file = null)
	{
		if ($nama_file != null) 
		{
			$this->load->helper('download');			
    		force_download('assets/plugins/file/'.$nama_file, null);

		}
		$data['data_file'] = $this->DataHandle->get_three('tbl_file', 'tbl_user', 'tbl_sekolah', 'tbl_file.*, tbl_user.username, tbl_sekolah.nama', 'tbl_file.created_by = tbl_user.id_user','tbl_file.id_sekolah = tbl_sekolah.id_sekolah', "tbl_file.status = '1'",  "tbl_file.id_file");
		$data['data_artikel'] = $this->DataHandle->get2lim6('tbl_artikel', 'tbl_user', 'tbl_artikel.*, tbl_user.username', 'tbl_artikel.created_by = tbl_user.id_user', "tbl_artikel.status = '1'", "tbl_artikel.id_artikel");
		$data['data_pengumuman'] = $this->DataHandle->get2lim6('tbl_pengumuman', 'tbl_user', 'tbl_pengumuman.*, tbl_user.username', 'tbl_pengumuman.created_by = tbl_user.id_user', "tbl_pengumuman.status = '1'", "tbl_pengumuman.id_pengumuman");
		$this->template->front_endnew('front_endnew/v_download', $data);
	}

	public function MentariIlmu($id_sekolah = "")
	{
		$data['galeri_one'] = $this->DataHandle->get2lim('tbl_galeri', 'tbl_user', 'tbl_galeri.*, tbl_user.username', 'tbl_galeri.created_by = tbl_user.id_user', "tbl_galeri.status = '1' and tbl_galeri.id_sekolah = '$id_sekolah'", "tbl_galeri.id_galeri", '1');
		$data['data_galeri'] = $this->DataHandle->get2lim('tbl_galeri', 'tbl_user', 'tbl_galeri.*, tbl_user.username', 'tbl_galeri.created_by = tbl_user.id_user', "tbl_galeri.status = '1' and tbl_galeri.id_sekolah = '$id_sekolah'", "tbl_galeri.id_galeri", '4');
		$data['data_video'] = $this->DataHandle->get2lim('tbl_video', 'tbl_user', 'tbl_video.*, tbl_user.username', 'tbl_video.created_by = tbl_user.id_user', "tbl_video.status = '1' and tbl_video.id_sekolah = '$id_sekolah'", "tbl_video.id_video", '2');
		$data['data_sekolah'] = $this->DataHandle->getAllWhere('tbl_sekolah', '*', "id_sekolah = '$id_sekolah'");
		$data['data_artikel'] = $this->DataHandle->get_two_o('tbl_artikel', 'tbl_user', 'tbl_artikel.*, tbl_user.username', 'tbl_artikel.created_by = tbl_user.id_user', "tbl_artikel.status = '1' and tbl_artikel.id_sekolah = '$id_sekolah'", "tbl_artikel.id_artikel");
		$data['data_pengumuman'] = $this->DataHandle->get_two_o('tbl_pengumuman', 'tbl_user', 'tbl_pengumuman.*, tbl_user.username', 'tbl_pengumuman.created_by = tbl_user.id_user', "tbl_pengumuman.status = '1'", "tbl_pengumuman.id_pengumuman");
		$data['data_profile'] = $this->DataHandle->getAllWhere('tbl_profil', '*', "id_sekolah = '$id_sekolah' AND status = 1");
		$data['data_kegiatan'] = $this->DataHandle->getAllWhere('tbl_kegiatan', '*', "id_sekolah = '$id_sekolah'");
		$data['data_ekskul'] = $this->DataHandle->getAllWhere('tbl_ekskul', '*', "id_sekolah = '$id_sekolah'");
		$data['data_fasilitas'] = $this->DataHandle->getAllWhere('tbl_fasilitas', '*', "id_sekolah = '$id_sekolah'");
		$this->template->front_endnew('front_endnew/v_mentariilmu', $data);
	}

	public function email_sender($email,$nama_tamu) {

			$config = Array(
			    'protocol' => 'smtp',
			    'smtp_host' => 'smtp.gmail.com',
			    'smtp_port' => 465,
			    'smtp_user' => 'fazri.rramadhanh@gmail.com',
			    'smtp_pass' => 'tarixjabrixx789',
			    'mailtype'  => 'html', 
   				'smtp_crypto'=>'ssl',
			    'charset'   => 'iso-8859-1'
			);

			$this->load->library('email', $config);
			$this->email->set_newline("\r\n");

              $this->email->from('admin.mentariilmu@mentariilmu.sch.id','Admin Yayasan Mentari Ilmu - Karawang'); 
              $this->email->to($email); 
              $this->email->subject('Terimakasih Atas Kunjungannya (Yayasan Mentari Ilmu)');
		    $mailContent = "
					<hr>
					<p>Hay <b>".$nama_tamu."</b>, Terimakasih telah  mengirimi kami pesan. Kunjungi langsung kami di :</p>
					<p><b>Yayasan Mentari Ilmu Karawang</b></p>
					<p>Alamat : <b>Jl. Perum Karaba Indah 1 Kp. Pintu Air Wadas Karawang Indonesia</b></p>
					<p>Kontak : <b>(0267) 840333</b></p>
					<p>E-mail : <b>smait.mentariilmu@gmail.com</b></p>
					<hr>
					<i>Terimakasih. Admin Yayasan Mentari Ilmu - Karawang</i> ";
              $this->email->message($mailContent);

        if($this->email->send()) {
		        // $this->session->set_flashdata('msg', '
		        // <div class="alert alert-success alert-dismissable">
		        //     <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
		        //     &times;</button>
		        //     Berhasil...
		        // </div>');  
		} 
		else {
			show_error($this->email->print_debugger());
		}
	}

	public function tatatertib($id_sekolah = "")
	{
		$data['data_sekolah'] = $this->DataHandle->getAllWhere('tbl_sekolah', '*', "id_sekolah = '$id_sekolah'");
		$data['data_tatatertib'] = $this->DataHandle->getAllWhere('tbl_tata_tertib', '*', "id_sekolah = '$id_sekolah'");
		$this->template->front_endnew('front_endnew/v_tatatertib', $data);
	}

	public function file($id_file, $downloaded)
	{
        $file_download = $this->DataHandle->getAllWhere('tbl_file', '*', "status = '1' AND id_file = '".$id_file."'")->row_array();        
        $downloaded = $downloaded+1;

        $edit_data = array(
        	'downloaded' => $downloaded
         );
        $where = array(
            'id_file' => $id_file
         );
        // var_dump($downloaded);die;

        $this->DataHandle->edit('tbl_file', $edit_data, $where);
        redirect('Home/download/'.$file_download['value']);
	}



	// =============================== TEMPLATE BARU ==============================
}
