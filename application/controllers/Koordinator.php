<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Koordinator extends CI_Controller {
    // construct method
    public function __construct() {
        parent::__construct();

        // set local timezone
        date_default_timezone_set('Asia/jakarta');

        // Pengecekan apakah user sudah login
        if (!$this->session->userdata('is_logged_in')) {
            // Set pesan flashdata untuk ditampilkan di halaman login
            $this->session->set_flashdata('error', 'Silahkan login terlebih dahulu');
            // Redirect ke halaman login
            redirect('auth');
        }

        // Pengecekan apakah user memiliki role 'koordinator'
        if ($this->session->userdata('nama_role') != 'koordinator') {
            // Redirect ke halaman lain jika bukan koordinator
            redirect('auth');
        }

        // hubungkan dengan models
        $this->load->model('staff_model');
        $this->load->model('jadwal_model');
        $this->load->model('koordinator_model');

    }

	public function index()
	{
        $data['events'] = $this->jadwal_model->get_all_events();

        // count data from tables
        $data['count_mhs'] = $this->koordinator_model->count_all_mahasiswa();
        $data['count_dsn'] = $this->koordinator_model->count_all_dosen();
        $data['count_rekomendasi'] = $this->koordinator_model->count_mahasiswa_rekomendasi();
        $data['count_siap_sidang'] = $this->koordinator_model->count_draft_sidang_approved();
        $data['count_belum_terpenuhi'] = $this->koordinator_model->count_mahasiswa_belum_terpenuhi();

        // Calculate percentages
        $total_mahasiswa = $data['count_mhs'];
        $data['percent_siap_sidang'] = ($total_mahasiswa > 0) ? ($data['count_siap_sidang'] / $total_mahasiswa) * 100 : 0;
        $data['percent_rekomendasi'] = ($total_mahasiswa > 0) ? ($data['count_rekomendasi'] / $total_mahasiswa) * 100 : 0;
        $data['percent_belum_terpenuhi'] = ($total_mahasiswa > 0) ? ($data['count_belum_terpenuhi'] / $total_mahasiswa) * 100 : 0;

        $data['title'] = 'Dashboard | Koordinator';
        $data['active'] = 'dashboard';
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar');
        $this->load->view('koordinator/v_dashboard');
        $this->load->view('templates/footer', $data);
	}

    // Kelola Project
    public function kelola_project() {
        $data['title'] = 'Kelola Project';
        // Ambil data project
        $data['projects'] = $this->koordinator_model->get_all_projects();
        $data['prodi'] = $this->staff_model->get_all_prodi();
        $data['active'] = 'kelola_project';
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar');
        $this->load->view('koordinator/project/v_kelola_project', $data);
        $this->load->view('templates/footer');
    }

    // Tambah Project
    public function tambah_project() 
    {
        // Set form validation rules
        $this->form_validation->set_rules('nama_project', 'Nama Project', 'required|trim', [
            'required' => 'Field {field} harus diisi.',
        ]);
        $this->form_validation->set_rules('deskripsi', 'Deskripsi', 'required|trim|max_length[255]', [
            'required' => 'Field {field} harus diisi.',
            'max_length' => 'Melebihi batas karakter yang diperbolehkan.'
        ]);
        $this->form_validation->set_rules('tgl_mulai', 'Tanggal_Mulai', 'required|trim', [
            'required' => 'Field {field} harus diisi.',
        ]);
        $this->form_validation->set_rules('tgl_selesai', 'Tanggal_Selesai', 'required|trim', [
            'required' => 'Field {field} harus diisi.',
        ]);
        $this->form_validation->set_rules('prodi_id', 'Prodi_ID', 'required|trim', [
            'required' => 'Field {field} harus diisi.',
        ]);

        // Jalankan form validation, dan jika bernilai false, maka
        if ($this->form_validation->run() == FALSE) {
            // Beri pesan kesalahan
            $this->session->set_flashdata('pesan', '<div class="alert alert-danger" role="alert">Project baru gagal ditambahkan!</div>');
            // Kembalikan ke halaman kelola project
            $this->kelola_project();
        } else {
            $data = [
                'nama_project' => htmlspecialchars($this->input->post('nama_project')),
                'deskripsi' => htmlspecialchars($this->input->post('deskripsi')),
                'tgl_mulai' => htmlspecialchars($this->input->post('tgl_mulai')),
                'tgl_selesai' => htmlspecialchars($this->input->post('tgl_selesai')),
                'prodi_id' => htmlspecialchars($this->input->post('prodi_id'))
            ];

            $this->koordinator_model->insert_project($data);
            $this->session->set_flashdata('pesan', '<div class="alert alert-success" role="alert">Project baru berhasil ditambahkan!</div>');
            redirect('koordinator/kelola_project');
        }
    }

    public function detail_project($id)
    {
        $data['title'] = 'Detail Prodi | Staff';
        $data['projects'] = $this->koordinator_model->get_project_by_id($id);
        $data['active'] = 'kelola_project';
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar');
        $this->load->view('koordinator/project/v_detail_project', $data);
        $this->load->view('templates/footer');
    }

    public function ubah_project($id)
    {
        $data['title'] = 'Ubah Mahasiswa | Staff';
        $data['prodi'] = $this->staff_model->get_all_prodi();
        $data['projects'] = $this->koordinator_model->get_project_by_id($id);
        $data['active'] = 'kelola_project';
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar');
        $this->load->view('koordinator/project/v_ubah_project', $data);
        $this->load->view('templates/footer');
    }

    public function update_project()
    {
        $id = $this->input->post('id');
        $current_project = $this->koordinator_model->get_project_by_id($id);

        // Set validation rules
        $this->form_validation->set_rules('nama_project', 'Nama_Project', 'required|trim', [
            'required' => 'Field {field} harus diisi.',
        ]);
        $this->form_validation->set_rules('deskripsi', 'Deskripsi', 'required|trim', [
            'required' => 'Field {field} harus diisi.',
        ]);
        $this->form_validation->set_rules('tgl_mulai', 'Tanggal_Mulai', 'required|trim', [
            'required' => 'Field {field} harus diisi.',
        ]);
        $this->form_validation->set_rules('tgl_selesai', 'Tanggal_Selesai', 'required|trim', [
            'required' => 'Field {field} harus diisi.',
        ]);
        $this->form_validation->set_rules('prodi_id', 'Prodi_ID', 'required|trim', [
            'required' => 'Field {field} harus diisi.',
        ]);

        if ($this->form_validation->run() == FALSE) {
            $this->ubah_project($id); // Kembali ke halaman ubah project jika validasi gagal
        } else {
            $data = [
                'nama_project' => htmlspecialchars($this->input->post('nama_project')),
                'deskripsi' => htmlspecialchars($this->input->post('deskripsi')),
                'tgl_mulai' => htmlspecialchars($this->input->post('tgl_mulai')),
                'tgl_selesai' => htmlspecialchars($this->input->post('tgl_selesai')),
                'prodi_id' => htmlspecialchars($this->input->post('prodi_id')),
            ];

            $update = $this->koordinator_model->update_project($id, $data);

            if ($update) {
                // If update is successful
                $this->session->set_flashdata('pesan', '<div class="alert alert-success" role="alert">Data project berhasil diubah!</div>');
                redirect('koordinator/kelola_project'); // Adjust the redirect path as needed
            } else {
                // If update fails
                $this->session->set_flashdata('pesan', '<div class="alert alert-danger" role="alert">Gagal melakukan update project!</div>');
                redirect('koordinator/ubah_project/' . $id);
            }
        }
    }
    // Akhir kelola project

    // Kelola jadwal
    public function kelola_jadwal() {
        $data['title'] = 'Kelola Jadwal | Koordinator';
        $data['jadwal'] = $this->jadwal_model->get_all_jadwal();
        $data['active'] = 'kelola_jadwal';

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar');
        $this->load->view('koordinator/jadwal/v_kelola_jadwal', $data);
        $this->load->view('templates/footer');
    }

    public function tambah_jadwal() {
        $this->form_validation->set_rules('tahun_akademik', 'Tahun Akademik', 'required|trim', [
            'required' => 'Field {field} harus diisi.',
        ]);
        $this->form_validation->set_rules('nama_jadwal', 'Nama Jadwal', 'required|trim', [
            'required' => 'Field {field} harus diisi.',
        ]);

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('pesan', '<div class="alert alert-danger" role="alert">Jadwal baru gagal ditambahkan!</div>');
            $this->kelola_jadwal();
        } else {
            $data = [
                'tahun_akademik' => htmlspecialchars($this->input->post('tahun_akademik')),
                'nama_jadwal' => htmlspecialchars($this->input->post('nama_jadwal')),
                'status' => 'draft',
                'created_at' => time(),
                'updated_at' => time()
            ];

            $this->jadwal_model->insert_jadwal($data);
            $this->session->set_flashdata('pesan', '<div class="alert alert-success" role="alert">Jadwal baru berhasil ditambahkan!</div>');
            redirect('koordinator/kelola_jadwal');
        }
    }

    public function detail_jadwal($id)
    {
        $data['title'] = 'Detail Jadwal | Koordinator';
        $data['jadwal'] = $this->jadwal_model->get_jadwal_by_id($id);
        $data['kegiatan'] = $this->jadwal_model->get_kegiatan_by_jadwal_id($id);  // Menambahkan data kegiatan

        $data['active'] = 'kelola_jadwal';
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar');
        $this->load->view('koordinator/jadwal/v_detail_jadwal', $data);
        $this->load->view('templates/footer');
    }

    public function do_publish_jadwal($id) {
        $data = array(
            'status' => 'published',
            'updated_at' => time()
        );

        $result = $this->jadwal_model->update_jadwal($id, $data);

        if ($result) {
            $this->session->set_flashdata('pesan', '<div class="alert alert-success" role="alert">Jadwal berhasil dipublikasikan!</div>');
        } else {
            $this->session->set_flashdata('pesan', '<div class="alert alert-danger" role="alert">Gagal mempublikasikan jadwal!</div>');
        }

        redirect('koordinator/kelola_jadwal');
    }
    // akhir kelola jadwal

    // Kelola Kegiatan
    public function kelola_kegiatan() {
        $data['title'] = 'Kelola Kegiatan | Koordinator';
        $data['kegiatan'] = $this->jadwal_model->get_all_kegiatan();
        $data['jadwal'] = $this->jadwal_model->get_all_jadwal();
        $data['active'] = 'kelola_kegiatan';

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar');
        $this->load->view('koordinator/kegiatan/v_kelola_kegiatan', $data);
        $this->load->view('templates/footer');
    }

    public function tambah_kegiatan() 
    {
        // set_rules
        $this->form_validation->set_rules('jadwal_id', 'Jadwal_ID', 'required|trim', [
            'required' => 'Field {field} harus diisi.',
        ]);
        $this->form_validation->set_rules('tgl_awal', 'tgl_awal', 'required|trim', [
            'required' => 'Field {field} harus diisi.',
        ]);
        $this->form_validation->set_rules('tgl_selesai', 'Tgl_Selesai', 'required|trim', [
            'required' => 'Field {field} harus diisi.',
        ]);
        $this->form_validation->set_rules('nama_kegiatan', 'Nama_Kegiatan', 'required|trim', [
            'required' => 'Field {field} harus diisi.',
        ]);
        $this->form_validation->set_rules('deskripsi', 'Deskripsi', 'required|trim', [
            'required' => 'Field {field} harus diisi.',
        ]);

        // jalankan form validation, dan jika bernilai false, maka
        if ($this->form_validation->run() == FALSE) {
            // beri pesan kesalahan
            $this->session->set_flashdata('pesan', '<div class="alert alert-danger" role="alert">Kegiatan baru gagal ditambahkan!</div>');
            // kembalikan ke halaman kelola kegiatan
            $this->kelola_kegiatan();
        } else {
            $data = [
                'jadwal_id' => htmlspecialchars($this->input->post('jadwal_id')),
                'tgl_awal' => htmlspecialchars($this->input->post('tgl_awal')),
                'tgl_selesai' => htmlspecialchars($this->input->post('tgl_selesai')),
                'nama_kegiatan' => htmlspecialchars($this->input->post('nama_kegiatan')),
                'deskripsi' => htmlspecialchars($this->input->post('deskripsi')),
            ];

            $this->jadwal_model->insert_kegiatan($data);
            $this->session->set_flashdata('pesan', '<div class="alert alert-success" user="alert">Kegiatan baru berhasil ditambahkan!</div>');
            redirect('koordinator/kelola_kegiatan');
        }
    }

    public function ubah_kegiatan($id)
    {
        $data['title'] = 'Detail Kegiatan | Koordinator';
        $data['jadwal'] = $this->jadwal_model->get_all_jadwal();
        $data['kegiatan'] = $this->jadwal_model->get_kegiatan_by_id($id);
        $data['active'] = 'kelola_kegiatan';
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar');
        $this->load->view('koordinator/kegiatan/v_ubah_kegiatan', $data);
        $this->load->view('templates/footer');
    }

    public function update_kegiatan()
    {
        $id = $this->input->post('id');

        // set_rules
        $this->form_validation->set_rules('jadwal_id', 'Jadwal_ID', 'required|trim', [
            'required' => 'Field {field} harus diisi.',
        ]);
        $this->form_validation->set_rules('nama_kegiatan', 'Nama_kegiatan', 'required|trim', [
            'required' => 'Field {field} harus diisi.',
        ]);
        $this->form_validation->set_rules('tgl_awal', 'Tgl_Awal', 'required|trim', [
            'required' => 'Field {field} harus diisi.',
        ]);
        $this->form_validation->set_rules('tgl_selesai', 'Tgl_Selesai', 'required|trim', [
            'required' => 'Field {field} harus diisi.',
        ]);

        if ($this->form_validation->run() == FALSE) {
            $this->ubah_kegiatan($id); // Kembali ke halaman ubah kegiatan jika validasi gagal
        } else {
            $data = [
                'jadwal_id' => htmlspecialchars($this->input->post('jadwal_id')),
                'tgl_awal' => htmlspecialchars($this->input->post('tgl_awal')),
                'tgl_selesai' => htmlspecialchars($this->input->post('tgl_selesai')),
                'nama_kegiatan' => htmlspecialchars($this->input->post('nama_kegiatan')),
                'deskripsi' => htmlspecialchars($this->input->post('deskripsi')),
            ];

            $update = $this->jadwal_model->update_kegiatan($id, $data);

            if ($update) {
                // If update is successful
                $this->session->set_flashdata('pesan', '<div class="alert alert-success" role="alert">Data kegiatan berhasil di ubah!</div>');
                redirect('koordinator/kelola_kegiatan'); // Adjust the redirect path as needed
            } else {
                // If update fails
                $this->session->set_flashdata('pesan', '<div class="alert alert-danger" role="alert">Gagal melakukan update kegiatan!</div>');
                redirect('koordinator/ubah_kegiatan/' . $id);
            }
        }
    }

    public function hapus_kegiatan($id)
    {
        $this->jadwal_model->delete_kegiatan($id);
        $this->session->set_flashdata('pesan', '<div class="alert alert-success" role="alert">Kegiatan berhasil dihapus!</div>');
        redirect('koordinator/kelola_kegiatan');
    }
    // Akhir kelola kegiatan

    // Kelola kelompok
    public function kelola_kelompok() {
        $data['title'] = 'Kelola Kelompok | Koordinator';
        // Ambil data kelompok
        $data['kelompok'] = $this->koordinator_model->get_all_kelompok();
        $data['kelas'] = $this->koordinator_model->get_all_kelas();
        $data['kode_kelompok'] = $this->generate_kode_kelompok(); // Generate kode kelompok berikutnya
        $selectedUserIds = $this->koordinator_model->get_selected_user_dsn_ids();
        $data['dosen'] = $this->koordinator_model->get_all_user_match_by_role_as_dosen($selectedUserIds);
        $data['active'] = 'kelola_kelompok';
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar');
        $this->load->view('koordinator/kelompok/v_kelola_kelompok', $data);
        $this->load->view('templates/footer');
    }
    
    public function generate_kode_kelompok() 
    {
        $last_kelompok = $this->koordinator_model->get_last_kelompok();
        if ($last_kelompok) {
            $next_code = intval($last_kelompok->kode_kelompok) + 1;
        } else {
            $next_code = 10; // Mulai dari 11 jika belum ada data
        }

        return str_pad($next_code, 2, '0', STR_PAD_LEFT); // Format menjadi 2 digit
    }

    // Tambah Kelompok
    public function tambah_kelompok() 
    {
        // Set form validation rules
        $this->form_validation->set_rules('dosen_pembimbing_id', 'Dosen_Pembimbing_ID', 'required|trim', [
            'required' => 'Field {field} harus diisi.',
        ]);
        $this->form_validation->set_rules('semester', 'Semester', 'required|trim', [
            'required' => 'Field {field} harus diisi.',
        ]);
        $this->form_validation->set_rules('tahun_ajaran', 'Tahun_Ajaran', 'required|trim', [
            'required' => 'Field {field} harus diisi.',
        ]);
        $this->form_validation->set_rules('kelas_id', 'Kelas_ID', 'required|trim', [
            'required' => 'Field {field} harus diisi.',
        ]);
        $this->form_validation->set_rules('kode_kelompok', 'Kode_Kelompok', 'required|trim|exact_length[2]|is_unique[kelompok.kode_kelompok]', [
            'required' => 'Field {field} harus diisi.',
            'exact_length' => 'Kode kelompok harus berisikan 2 digit karakter.',
            'is_unique' => 'Kode kelompok sudah ada.',
        ]);

        // Jalankan form validation, dan jika bernilai false, maka
        if ($this->form_validation->run() == FALSE) {
            // Beri pesan kesalahan
            $this->session->set_flashdata('pesan', '<div class="alert alert-danger" role="alert">Kelompok baru gagal ditambahkan!</div>');
            // Kembalikan ke halaman kelola kelompok
            $this->kelola_kelompok();
        } else {
            $data = [
                'dosen_pembimbing_id' => htmlspecialchars($this->input->post('dosen_pembimbing_id')),
                'semester' => htmlspecialchars($this->input->post('semester')),
                'tahun_ajaran' => htmlspecialchars($this->input->post('tahun_ajaran')),
                'kelas_id' => htmlspecialchars($this->input->post('kelas_id')),
                'kode_kelompok' => htmlspecialchars($this->input->post('kode_kelompok')),
            ];

            $this->koordinator_model->insert_kelompok($data);
            $this->session->set_flashdata('pesan', '<div class="alert alert-success" role="alert">Kelompok baru berhasil ditambahkan!</div>');
            redirect('koordinator/kelola_kelompok');
        }
    }

    public function detail_kelompok($id)
    {
        $data['title'] = 'Detail Kelompok | Koordinator';
        $data['kelompok'] = $this->koordinator_model->get_kelompok_by_id($id);
        $data['anggota_kelompok'] = $this->koordinator_model->get_plotting_by_kelompok_id($id);  // Menambahkan data plotting


        $data['active'] = 'kelola_kelompok';
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar');
        $this->load->view('koordinator/kelompok/v_detail_kelompok', $data);
        $this->load->view('templates/footer');
    }

    public function ubah_kelompok($id)
    {
        $data['title'] = 'Ubah Data Kelompok | Koordinator';
        $data['kelompok'] = $this->koordinator_model->get_kelompok_by_id($id);
        $data['kelas'] = $this->koordinator_model->get_all_kelas();
        $data['dosen'] = $this->koordinator_model->get_all_dosen();
        $data['active'] = 'kelola_kelompok';
        
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar');
        $this->load->view('koordinator/kelompok/v_ubah_kelompok', $data);
        $this->load->view('templates/footer');
    }

    public function update_kelompok()
    {
        $id = $this->input->post('id');

        // set_rules
        $this->form_validation->set_rules('dosen_pembimbing_id', 'Dosen Pembimbing', 'required|trim', [
            'required' => 'Field {field} harus diisi.',
        ]);
        $this->form_validation->set_rules('semester', 'Semester', 'required|trim|max_length[2]', [
            'required' => 'Field {field} harus diisi.',
        ]);
        $this->form_validation->set_rules('tahun_ajaran', 'Tahun Ajaran', 'required|trim', [
            'required' => 'Field {field} harus diisi.',
        ]);
        $this->form_validation->set_rules('kelas_id', 'Kelas', 'required|trim', [
            'required' => 'Field {field} harus diisi.',
        ]);
        $this->form_validation->set_rules('kode_kelompok', 'Kode Kelompok', 'required|trim', [
            'required' => 'Field {field} harus diisi.',
        ]);

        if ($this->form_validation->run() == FALSE) {
            $this->ubah_kelompok($id); // Kembali ke halaman ubah kelompok jika validasi gagal
        } else {
            $data = [
                'dosen_pembimbing_id' => htmlspecialchars($this->input->post('dosen_pembimbing_id')),
                'semester' => htmlspecialchars($this->input->post('semester')),
                'tahun_ajaran' => htmlspecialchars($this->input->post('tahun_ajaran')),
                'kelas_id' => htmlspecialchars($this->input->post('kelas_id')),
                'kode_kelompok' => htmlspecialchars($this->input->post('kode_kelompok')),
            ];

            $update = $this->koordinator_model->update_kelompok($id, $data);

            if ($update) {
                // If update is successful
                $this->session->set_flashdata('pesan', '<div class="alert alert-success" role="alert">Data kelompok berhasil diubah!</div>');
                redirect('koordinator/kelola_kelompok');
            } else {
                // If update fails
                $this->session->set_flashdata('pesan', '<div class="alert alert-danger" role="alert">Gagal melakukan update kelompok!</div>');
                redirect('koordinator/ubah_kelompok/' . $id);
            }
        }
    }
    // Akhir kelola kelompok

    // Kelola Plotting
    public function kelola_plotting() {
        $data['plotting'] = $this->koordinator_model->getAllPlotting();
        // data from other tables
        $username = $this->session->userdata['username'];
        $data['koordinator'] = $this->koordinator_model->get_current_koordinator($username);

        $data['dosen'] = $this->staff_model->get_all_dosen();
        $data['projects'] = $this->koordinator_model->get_all_projects();
        $data['jenis_plotting'] = $this->koordinator_model->get_all_jenis_plotting();
        $data['kelompok'] = $this->koordinator_model->get_all_kelompok(); // Ambil data kelompok
        // Ambil user id mahasiswa yang sudah dipilih
        $selectedUserIds = $this->koordinator_model->get_selected_user_mhs_ids();
        // Kirim user id yang sudah dipilih ke fungsi get_all_user_match_by_role_as_mahasiswa
        $data['mahasiswa'] = $this->koordinator_model->get_all_user_match_by_role_as_mahasiswa($selectedUserIds);

        $data['title'] = 'Kelola Plotting | Koordinator';
        $data['active'] = 'kelola_plotting';

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar');
        $this->load->view('koordinator/plotting/v_kelola_plotting', $data);
        $this->load->view('templates/footer');
    }

    public function tambah_plotting() 
    {
        // Set rules for common fields
        $this->form_validation->set_rules('kelompok_id', 'Kelompok_ID', 'required|trim', [
            'required' => 'Field {field} harus diisi.',
        ]);
        $this->form_validation->set_rules('mahasiswa_id', 'Mahasiswa_ID', 'required|trim', [
            'required' => 'Field {field} harus diisi.',
        ]);
        $this->form_validation->set_rules('project_id', 'Project_ID', 'required|trim', [
            'required' => 'Field {field} harus diisi.',
        ]);
        $this->form_validation->set_rules('jenis_plotting_id', 'Jenis_Plotting_ID', 'required|trim', [
            'required' => 'Field {field} harus diisi.',
        ]);

        // Get jenis_plotting_id value
        $jenis_plotting_id = $this->input->post('jenis_plotting_id');

        // If jenis_plotting_id is 'Penguji', set additional rules
        if ($jenis_plotting_id == '2') { // Replace '2' with the actual ID for 'Penguji'
            $this->form_validation->set_rules('dosen_penguji_1_id', 'Dosen_Penguji_1_ID', 'required|trim', [
                'required' => 'Field {field} harus diisi.',
            ]);
            $this->form_validation->set_rules('dosen_penguji_2_id', 'Dosen_Penguji_2_ID', 'required|trim', [
                'required' => 'Field {field} harus diisi.',
            ]);
        }

        // Run form validation, if false, return with error message
        if ($this->form_validation->run() == FALSE) {
            // Set error message
            $this->session->set_flashdata('pesan', '<div class="alert alert-danger" role="alert">Data plotting baru gagal ditambahkan!</div>');
            // Redirect back to kelola plotting page
            $this->kelola_plotting();
        } else {
            // Prepare data to be inserted
            $data = [
                'koordinator_id' => htmlspecialchars($this->input->post('koordinator_id')),
                'kelompok_id' => htmlspecialchars($this->input->post('kelompok_id')),
                'mahasiswa_id' => htmlspecialchars($this->input->post('mahasiswa_id')),
                'project_id' => htmlspecialchars($this->input->post('project_id')),
                'jenis_plotting_id' => htmlspecialchars($this->input->post('jenis_plotting_id')),
                'created_at' => time(),
                'updated_at' => time()
            ];

            // If jenis_plotting_id is 'Penguji', add dosen_penguji_1_id and dosen_penguji_2_id to the data
            if ($jenis_plotting_id == '2') { // Replace '2' with the actual ID for 'Penguji'
                $data['dosen_penguji_1_id'] = htmlspecialchars($this->input->post('dosen_penguji_1_id'));
                $data['dosen_penguji_2_id'] = htmlspecialchars($this->input->post('dosen_penguji_2_id'));
            }

            // Insert data into database
            $this->koordinator_model->insert_plotting($data);
            // Set success message
            $this->session->set_flashdata('pesan', '<div class="alert alert-success" role="alert">Data plotting baru berhasil ditambahkan!</div>');
            // Redirect to kelola plotting page
            redirect('koordinator/kelola_plotting');
        }
    }

    public function detail_plotting($id)
    {
        $data['title'] = 'Detail Plotting | Koordinator';
        $data['plotting'] = $this->koordinator_model->getPlottingById($id);
        $data['active'] = 'kelola_plotting';
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar');
        $this->load->view('koordinator/plotting/v_detail_plotting', $data);
        $this->load->view('templates/footer');
    }

    public function ubah_plotting($id) {
        $data['title'] = 'Ubah Plotting | Koordinator';
        $data['plotting'] = $this->koordinator_model->getPlottingById($id);
        $data['dosen'] = $this->staff_model->get_all_dosen();
        $data['jenis_plotting'] = $this->koordinator_model->get_all_jenis_plotting();
        $data['kelompok'] = $this->koordinator_model->get_all_kelompok();
        $data['active'] = 'kelola_plotting';
    
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar');
        $this->load->view('koordinator/plotting/v_ubah_plotting', $data);
        $this->load->view('templates/footer');
    }

    public function update_plotting() {
        // Set rules for form validation
        $this->form_validation->set_rules('jenis_plotting_id', 'Jenis Plotting', 'required|trim', [
            'required' => 'Field {field} harus diisi.'
        ]);
        $this->form_validation->set_rules('kelompok_id', 'Dosen Pembimbing', 'required|trim', [
            'required' => 'Field {field} harus diisi.'
        ]);

        // Fetch form data
        $jenis_plotting_id = $this->input->post('jenis_plotting_id');
        $kelompok_id = $this->input->post('kelompok_id');
        $dosen_penguji_1_id = $this->input->post('dosen_penguji_1_id');
        $dosen_penguji_2_id = $this->input->post('dosen_penguji_2_id');

        // Check if `jenis_plotting_id` is 2 to add rules for penguji
        if ($jenis_plotting_id == '2') {
            $this->form_validation->set_rules('dosen_penguji_1_id', 'Dosen Penguji 1', 'required|trim', [
                'required' => 'Field {field} harus diisi.'
            ]);
            $this->form_validation->set_rules('dosen_penguji_2_id', 'Dosen Penguji 2', 'required|trim', [
                'required' => 'Field {field} harus diisi.'
            ]);
        }

        // Check if the form validation is successful
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('pesan', '<div class="alert alert-danger" role="alert">Data plotting gagal diubah.</div>');
            redirect('koordinator/ubah_plotting/' . $this->input->post('id'));
        } else {
            // Prepare data for update
            $data = [
                'jenis_plotting_id' => htmlspecialchars($jenis_plotting_id),
                'kelompok_id' => htmlspecialchars($kelompok_id),
                'updated_at' => time()
            ];

            if ($jenis_plotting_id == '2') {
                $data['dosen_penguji_1_id'] = htmlspecialchars($dosen_penguji_1_id);
                $data['dosen_penguji_2_id'] = htmlspecialchars($dosen_penguji_2_id);
            } else {
                $data['dosen_penguji_1_id'] = null;
                $data['dosen_penguji_2_id'] = null;
            }

            $result = $this->koordinator_model->updatePlotting($this->input->post('id'), $data);

            if ($result) {
                $this->session->set_flashdata('pesan', '<div class="alert alert-success" role="alert">Data plotting berhasil diubah.</div>');
            } else {
                $this->session->set_flashdata('pesan', '<div class="alert alert-danger" role="alert">Data plotting gagal diubah.</div>');
            }

            redirect('koordinator/kelola_plotting');
        }
    }
    
    // Akhir kelola plotting

    // Kelola Draft
    public function kelola_draft() {
        $data['title'] = 'Kelola Draft | Koordinator';
        // Ambil data draft
        $data['drafts'] = $this->koordinator_model->get_all_draft();
        $data['active'] = 'kelola_draft';
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar');
        $this->load->view('koordinator/draft/v_kelola_draft', $data);
        $this->load->view('templates/footer');
    }

    public function detail_draft($id) {
        $data['title'] = 'Detail Draft | Koordinator';
        // Ambil data draft
        $data['draft'] = $this->koordinator_model->get_all_draft_by_id($id);
        $data['active'] = 'kelola_draft';
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar');
        $this->load->view('koordinator/draft/v_detail_draft', $data);
        $this->load->view('templates/footer');
    }

    public function do_validasi_draft($id) {
        // Set rules for form validation
        $this->form_validation->set_rules('status', 'Status Validasi', 'required|trim', [
            'required' => 'Field {field} harus diisi.'
        ]);

        if ($this->form_validation->run() == FALSE) {
            // Jika validasi form gagal
            $this->session->set_flashdata('pesan', '<div class="alert alert-danger" role="alert">Gagal memvalidasi draft, periksa kembali form input!</div>');
            redirect('koordinator/detail_draft/' . $id); // Sesuaikan dengan route yang tepat
            return; // Hentikan eksekusi lebih lanjut
        }
    
        // Ambil data draft yang ada berdasarkan ID
        $current_draft = $this->koordinator_model->get_all_draft_by_id($id);
        if (!$current_draft) {
            // Jika draft tidak ditemukan
            $this->session->set_flashdata('pesan', '<div class="alert alert-danger" role="alert">Draft tidak ditemukan!</div>');
            redirect('koordinator/kelola_draft'); // Sesuaikan dengan route yang tepat
            return; // Hentikan eksekusi lebih lanjut
        }
    
        // Ambil input
        $status = $this->input->post('status');
        $catatan_penolakan = ($status === 'rejected') ? $this->input->post('catatan_penolakan') : NULL;
    
        // Set data untuk update
        $data = [
            'status' => $status,
            'catatan_penolakan' => $catatan_penolakan,
        ];
    
        if ($status === 'rejected') {
            $data['is_submitted'] = 0;
            $data['submitted_at'] = NULL;
        }
    
        // Update data draft
        $this->koordinator_model->update_draft($id, $data);
        $this->session->set_flashdata('pesan', '<div class="alert alert-success" role="alert">Draft berhasil divalidasi!</div>');
        redirect('koordinator/kelola_draft'); // Sesuaikan dengan route yang tepat
    }
    
    public function do_download_file_laporan($id)
    {
        // Ambil data draft berdasarkan ID
        $draft = $this->koordinator_model->get_draft_by_id($id);

        // Periksa apakah draft ditemukan
        if (!$draft) {
            // Jika draft tidak ditemukan, tampilkan pesan error
            $this->session->set_flashdata('pesan', '<div class="alert alert-danger" role="alert">Draft tidak ditemukan!</div>');
            redirect('koordinator/detail_draft' . $id); // Sesuaikan dengan route yang tepat
            return;
        }

        // Tentukan path file yang akan di-download
        $file_path = './assets/docs/drafts/' . $draft['file_laporan'];

        // Periksa apakah file ada di path yang ditentukan
        if (file_exists($file_path)) {
            // Set header untuk file download
            $this->load->helper('download');
            force_download($file_path, NULL);
        } else {
            // Jika file tidak ditemukan, tampilkan pesan error
            $this->session->set_flashdata('pesan', '<div class="alert alert-danger" role="alert">File tidak ditemukan!</div>');
            redirect('koordinator/detail_draft/' . $id); // Sesuaikan dengan route yang tepat
        }
    }

    public function do_download_file_dpl($id)
    {
        // Ambil data draft berdasarkan ID
        $draft = $this->koordinator_model->get_draft_by_id($id);

        // Periksa apakah draft ditemukan
        if (!$draft) {
            // Jika draft tidak ditemukan, tampilkan pesan error
            $this->session->set_flashdata('pesan', '<div class="alert alert-danger" role="alert">Draft tidak ditemukan!</div>');
            redirect('koordinator/detail_draft/' . $id); // Sesuaikan dengan route yang tepat
            return;
        }

        // Tentukan path file yang akan di-download
        $file_path = './assets/docs/drafts/' . $draft['file_dpl'];

        // Periksa apakah file ada di path yang ditentukan
        if (file_exists($file_path)) {
            // Set header untuk file download
            $this->load->helper('download');
            force_download($file_path, NULL);
        } else {
            // Jika file tidak ditemukan, tampilkan pesan error
            $this->session->set_flashdata('pesan', '<div class="alert alert-danger" role="alert">File tidak ditemukan!</div>');
            redirect('koordinator/detail_draft/' . $id); // Sesuaikan dengan route yang tepat
        }
    }
    // Akhir kelola draft

    // Kelola Jadwal Sidang
    public function kelola_jadwal_sidang() {
        $data['title'] = 'Kelola Jadwal Sidang | Koordinator';
        $data['projects'] = $this->koordinator_model->get_all_projects();
        $data['dosen'] = $this->koordinator_model->get_all_dosen();
        
         // Ambil data mahasiswa yang sudah memiliki jadwal sidang
        $mahasiswaWithJadwalSidang = $this->koordinator_model->get_mahasiswa_with_jadwal_sidang();
        
        // Ambil data mahasiswa yang belum memiliki jadwal sidang
        $selectedMhsIds = $this->koordinator_model->get_selected_mhs_ids();
        $data['mahasiswa'] = $this->koordinator_model->get_all_mhs_already_plotting_penguji($selectedMhsIds, $mahasiswaWithJadwalSidang);

        $data['jadwal_sidang'] = $this->koordinator_model->get_all_jadwal_sidang();
        $data['active'] = 'kelola_jadwal_sidang';
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar');
        $this->load->view('koordinator/sidang/v_kelola_jadwal_sidang', $data);
        $this->load->view('templates/footer');
    }

    // Function to get penguji by mahasiswa ID (for AJAX)
    public function get_penguji_by_mahasiswa_id($mahasiswa_id) {
        $data = $this->koordinator_model->get_penguji_by_mahasiswa_id($mahasiswa_id);
        echo json_encode($data);
    }

    public function tambah_jadwal_sidang() {
        
        // Validasi input
        $this->form_validation->set_rules('project_id', 'Nama Project', 'required', [
            'required' => 'Field {field} harus diisi.',
        ]);
        $this->form_validation->set_rules('mahasiswa_id', 'Mahasiswa ID', 'required', [
            'required' => 'Field {field} harus diisi.',
        ]);
        $this->form_validation->set_rules('tgl_sidang', 'Tanggal Sidang', 'required', [
            'required' => 'Field {field} harus diisi.',
        ]);
        $this->form_validation->set_rules('waktu_sidang', 'Waktu Sidang', 'required', [
            'required' => 'Field {field} harus diisi.',
        ]);
        $this->form_validation->set_rules('no_ruangan', 'No. Ruangan', 'required|numeric|max_length[3]', [
            'required' => 'Field {field} harus diisi.',
            'numeric' => 'Field {field} hanya menerima input berupa angka.',
            'max_length' => 'Field {field} tidak boleh melebihi {param} karakter.',
        ]);
        $this->form_validation->set_rules('nama_ruangan', 'Nama Ruangan', 'required', [
            'required' => 'Field {field} harus diisi.',
        ]);
    
        if ($this->form_validation->run() == FALSE) {
            // Jika validasi gagal, kembali ke halaman form dengan pesan error
            $this->session->set_flashdata('pesan', '<div class="alert alert-danger" role="alert">Jadwal Sidang baru gagal ditambahkan!</div>');
            redirect('koordinator/kelola_jadwal_sidang'); // Ganti dengan URL yang sesuai
        } else {
            // Ambil data dari form
            $data = [
                'project_id' => $this->input->post('project_id'),
                'plotting_id' => $this->input->post('plotting_id'), // Mengambil nilai hidden field
                'no_ruangan' => $this->input->post('no_ruangan'),
                'nama_ruangan' => $this->input->post('nama_ruangan'),
                'tgl_sidang' => $this->input->post('tgl_sidang'),
                'waktu_sidang' => $this->input->post('waktu_sidang'),
                'created_at' => time()
            ];
    
            if ($this->koordinator_model->insert_jadwal_sidang($data)) {
                $this->session->set_flashdata('pesan', '<div class="alert alert-success" role="alert">Jadwal Sidang baru berhasil ditambahkan.</div>');
            } else {
                $this->session->set_flashdata('pesan', '<div class="alert alert-danger" role="alert">Jadwal Sidang baru gagal ditambahkan!</div>');
            }
            redirect('koordinator/kelola_jadwal_sidang'); // Ganti dengan URL yang sesuai
        }
    }

    public function detail_jadwal_sidang($id) {
        $data['title'] = 'Detail Jadwal Sidang | Koordinator';
        $data['jadwal_sidang'] = $this->jadwal_model->get_jadwal_sidang_by_id($id);

        $data['active'] = 'kelola_jadwal_sidang';
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar');
        $this->load->view('koordinator/sidang/v_detail_jadwal_sidang', $data);
        $this->load->view('templates/footer');
    }

    public function ubah_jadwal_sidang($id) {
        $data['title'] = 'Ubah Jadwal Sidang | Koordinator';
        $data['jadwal_sidang'] = $this->jadwal_model->get_jadwal_sidang_by_id($id);

        $data['active'] = 'kelola_jadwal_sidang';
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar');
        $this->load->view('koordinator/sidang/v_ubah_jadwal_sidang', $data);
        $this->load->view('templates/footer');
    }

    public function update_jadwal_sidang()
    {
        $id = $this->input->post('id');

        // Set validation rules
        $this->form_validation->set_rules('tgl_sidang', 'Tanggal Sidang', 'required|trim', [
            'required' => 'Field {field} harus diisi.',
        ]);
        $this->form_validation->set_rules('waktu_sidang', 'Waktu Sidang', 'required|trim', [
            'required' => 'Field {field} harus diisi.',
        ]);
        $this->form_validation->set_rules('no_ruangan', 'Nomor Ruangan', 'required|trim', [
            'required' => 'Field {field} harus diisi.',
        ]);
        $this->form_validation->set_rules('nama_ruangan', 'Nama Ruangan', 'required|trim', [
            'required' => 'Field {field} harus diisi.',
        ]);

        if ($this->form_validation->run() == FALSE) {
            $this->ubah_jadwal_sidang($id); // Kembali ke halaman ubah jadwal sidang jika validasi gagal
        } else {
            $data = [
                'tgl_sidang' => htmlspecialchars($this->input->post('tgl_sidang')),
                'waktu_sidang' => htmlspecialchars($this->input->post('waktu_sidang')),
                'no_ruangan' => htmlspecialchars($this->input->post('no_ruangan')),
                'nama_ruangan' => htmlspecialchars($this->input->post('nama_ruangan')),
                'updated_at' => time()
            ];

            $update = $this->koordinator_model->update_jadwal_sidang($id, $data);

            if ($update) {
                // If update is successful
                $this->session->set_flashdata('pesan', '<div class="alert alert-success" role="alert">Jadwal sidang berhasil diubah!</div>');
                redirect('koordinator/kelola_jadwal_sidang'); // Adjust the redirect path as needed
            } else {
                // If update fails
                $this->session->set_flashdata('pesan', '<div class="alert alert-danger" role="alert">Gagal melakukan update jadwal sidang!</div>');
                redirect('koordinator/ubah_jadwal_sidang/' . $id);
            }
        }
    }
    
    // Akhir kelola jadwal sidang

    // Kelola Penilaian
    public function kelola_penilaian()
    {
        // Set data untuk halaman
        $data['title'] = 'Kelola Penilaian | Koordinator';
        $data['active'] = 'kelola_penilaian';

        // Ambil data penilaian
        $data['penilaian'] = $this->koordinator_model->get_all_penilaian();

        // Render halaman
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar');
        $this->load->view('koordinator/penilaian/v_kelola_penilaian', $data);
        $this->load->view('templates/footer');
    }

    public function detail_penilaian($id)
    {
        // Set data untuk halaman
        $data['title'] = 'Kelola Penilaian | Koordinator';
        $data['active'] = 'kelola_penilaian';

        // Ambil data penilaian
        $data['penilaian'] = $this->koordinator_model->get_penilaian_by_id($id);

        // Render halaman
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar');
        $this->load->view('koordinator/penilaian/v_detail_penilaian', $data);
        $this->load->view('templates/footer');
    }
    // Akhir kelola penilaian

    // Print berita acara proyek 2
    public function print_berita_acara($penilaian_id)
    {
        // Ambil data penilaian
        $data['penilaian'] = $this->koordinator_model->get_penilaian_by_id($penilaian_id);

        // Load view untuk berita acara
        $html = $this->load->view('pdfGenerator/v_print_berita_acara', $data, true);

        // Load library PDF
        $this->load->library('pdf');

        // Render HTML ke PDF
        $this->pdf->load_html($html);
        $this->pdf->render();

        $penilaian_id = uniqid();

        // Simpan file PDF ke lokasi sementara
        $output = $this->pdf->output();
        file_put_contents('./assets/docs/pdf/berita_acara_' . $penilaian_id . '.pdf', $output);

        // Download PDF
        $this->pdf->stream("berita_acara_" . $penilaian_id . ".pdf");
    }
    // Akhir print berita acara proyek 2

    // print kalender proyek 2
    public function print_kalender()
    {
        // Mengambil jadwal_id yang statusnya 'published'
        $jadwal_published = $this->jadwal_model->get_published_jadwal();

        $jadwal_id = $jadwal_published['id'];

        $data['jadwal'] = $this->jadwal_model->get_jadwal_by_id($jadwal_id);
        $data['kegiatan'] = $this->jadwal_model->get_kegiatan_by_jadwal_id($jadwal_id);  // Menambahkan data kegiatan

        $html = $this->load->view('pdfGenerator/v_print_kalender_pdf', $data, true);

        $this->pdf->load_html($html);
        $this->pdf->render();

        // Save PDF file to a temporary location
        $output = $this->pdf->output();
        file_put_contents('./assets/docs/pdf/kalender.pdf', $output);

        // Optional: Download PDF instead of saving
        $this->pdf->stream("kalender.pdf");
    }
}
