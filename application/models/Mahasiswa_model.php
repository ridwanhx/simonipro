<?php 
class Mahasiswa_model extends CI_Model {    

    // count
    public function count_absensi_bimbingan($mahasiswa_id) {
        // Build the query
        $this->db->select('COUNT(*) AS jumlah_hadir');
        $this->db->from('absensi_bimbingan');
        $this->db->where('(status = "hadir" OR status = "rekomendasi")');
        $this->db->where('mahasiswa_id', $mahasiswa_id);
        
        // Execute the query
        $query = $this->db->get();
        
        // Return the result as an array
        return $query->row_array();
    }

    // Kelola Absensi
    public function get_all_kelompok_match_current_mhs($username)
    {
        // Ambil data mahasiswa berdasarkan username
        $this->db->select('mahasiswa.id as mahasiswa_id, mahasiswa.nama as nama_mahasiswa, mahasiswa.npm, mahasiswa.ipk, kelompok.id as kelompok_id, kelompok.kode_kelompok, kelompok.dosen_pembimbing_id, kelompok.kelas_id, kelompok.semester, kelompok.tahun_ajaran');
        $this->db->from('user');
        $this->db->join('mahasiswa', 'mahasiswa.user_id = user.id');
        $this->db->join('plotting', 'plotting.mahasiswa_id = mahasiswa.id');
        $this->db->join('kelompok', 'kelompok.id = plotting.kelompok_id');
        $this->db->where('user.username', $username);
        $this->db->limit(1);  // Hanya ambil satu data
        $mahasiswa = $this->db->get()->row();

        if ($mahasiswa) {
            // Ambil data kelompok dan dosen pembimbing berdasarkan kelompok_id
            $this->db->select('kelompok.*, kelas.nama_kelas, prodi.nama_prodi, prodi.jenjang, dosen.nama as nama_dosen_pembimbing');
            $this->db->from('kelompok');
            $this->db->join('kelas', 'kelas.id = kelompok.kelas_id');
            $this->db->join('mahasiswa', 'mahasiswa.kelas_id = kelompok.kelas_id');  // Join dengan mahasiswa untuk mengambil prodi_id
            $this->db->join('prodi', 'prodi.id = mahasiswa.prodi_id');  // Ambil nama_prodi dari prodi_id mahasiswa
            $this->db->join('dosen', 'dosen.id = kelompok.dosen_pembimbing_id');  // Join dengan tabel dosen untuk mendapatkan nama dosen pembimbing
            $this->db->where('kelompok.id', $mahasiswa->kelompok_id);
            $kelompok = $this->db->get()->row_array();

            // Tambahkan data mahasiswa ke dalam array $kelompok
            $kelompok['mahasiswa_id'] = $mahasiswa->mahasiswa_id;
            $kelompok['nama_mahasiswa'] = $mahasiswa->nama_mahasiswa;
            $kelompok['npm'] = $mahasiswa->npm;
            $kelompok['ipk'] = $mahasiswa->ipk;

            return $kelompok;  // Mengembalikan data sebagai array tunggal
        } else {
            return array();
        }
    }

    public function get_all_absensi_bimbingan_by_user($username)
    {
        // Ambil data user berdasarkan username
        $this->db->select('user.id as user_id, mahasiswa.id as mahasiswa_id');
        $this->db->from('user');
        $this->db->join('mahasiswa', 'mahasiswa.user_id = user.id');
        $this->db->where('user.username', $username);
        $user = $this->db->get()->row();

        if ($user) {
            // Ambil data absensi bimbingan berdasarkan mahasiswa_id
            $this->db->select('*');
            $this->db->from('absensi_bimbingan');
            $this->db->where('mahasiswa_id', $user->mahasiswa_id);
            $query = $this->db->get();
            return $query->result_array();
        } else {
            return array();
        }
    }

    public function get_all_absensi_bimbingan_by_id($absensi_id)
    {
        $this->db->select('*');
        $this->db->from('absensi_bimbingan');
        $this->db->where('id', $absensi_id);
        $query = $this->db->get();

        return $query->result_array();
    }

    public function get_mahasiswa_by_username($username)
    {
        $this->db->select('mahasiswa.*, user.username, role.nama_role, prodi.nama_prodi, prodi.jenjang, kelas.nama_kelas');
        $this->db->from('mahasiswa');
        $this->db->join('user', 'mahasiswa.user_id = user.id');
        $this->db->join('role', 'user.role_id = role.id');
        $this->db->join('prodi', 'mahasiswa.prodi_id = prodi.id'); // Join dengan tabel prodi menggunakan kolom prodi_id
        $this->db->join('kelas', 'mahasiswa.kelas_id = kelas.id');
        $this->db->where('mahasiswa.npm', $username);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function get_last_absensi_status($username)
    {
        // Ambil data user berdasarkan username
        $this->db->select('user.id as user_id, mahasiswa.id as mahasiswa_id');
        $this->db->from('user');
        $this->db->join('mahasiswa', 'mahasiswa.user_id = user.id');
        $this->db->where('user.username', $username);
        $user = $this->db->get()->row();

        if ($user) {
            // Ambil status terakhir absensi bimbingan berdasarkan mahasiswa_id
            $this->db->select('status, tgl_bimbingan');
            $this->db->from('absensi_bimbingan');
            $this->db->where('mahasiswa_id', $user->mahasiswa_id);
            $this->db->where('status', 'rekomendasi');
            $this->db->order_by('tgl_bimbingan', 'DESC');
            $this->db->limit(1);
            $query = $this->db->get();
            return $query->row_array();
        } else {
            return array();
        }
    }

    public function insert_absensi($data)
    {
        $this->db->insert('absensi_bimbingan', $data);
    }

    public function update_absensi($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update('absensi_bimbingan', $data);
    }

    public function insert_draft($data)
    {
        $this->db->insert('draft_sidang', $data);
    }

    // Akhir kelola Absensi

    // Upload draft
    // Fungsi untuk mendapatkan status upload draft mahasiswa
    public function has_uploaded_draft($mahasiswa_id) {
        $this->db->select('id');
        $this->db->from('draft_sidang');
        $this->db->where('mahasiswa_id', $mahasiswa_id);
        $query = $this->db->get();
        return $query->num_rows() > 0;
    }

    // Fungsi untuk mendapatkan mahasiswa_id dari username
    public function get_mahasiswa_id_by_username($username) {
        $this->db->select('mahasiswa.id');
        $this->db->from('mahasiswa');
        $this->db->join('user', 'mahasiswa.user_id = user.id');
        $this->db->where('user.username', $username);
        $query = $this->db->get();
        return $query->row()->id;
    }

    // Ambil semua draft sidang yang sesuai dengan mahasiswa saat ini
    public function get_all_draft_match_current_mhs($username) {
        $this->db->select('draft_sidang.*, mahasiswa.nama as nama_mahasiswa, kelompok.kode_kelompok');
        $this->db->from('draft_sidang');
        $this->db->join('mahasiswa', 'mahasiswa.id = draft_sidang.mahasiswa_id');
        $this->db->join('kelompok', 'kelompok.id = draft_sidang.kelompok_id');
        $this->db->join('user', 'user.id = mahasiswa.user_id');
        $this->db->where('user.username', $username);
        $result = $this->db->get()->result_array();
        return $result;
    }

    // Cek apakah mahasiswa sudah submit draft
    public function cek_submitted($mahasiswa_id) {
        $this->db->select('is_submitted');
        $this->db->from('draft_sidang');
        $this->db->where('mahasiswa_id', $mahasiswa_id);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $result = $query->row();
            return $result->is_submitted == 1; // Mengembalikan true jika is_submitted bernilai 1
        } else {
            return false; // Jika tidak ada data, return false
        }
    }

    public function get_draft_by_id($id)
    {
        // Ambil data draft berdasarkan ID
        $this->db->where('id', $id);
        $query = $this->db->get('draft_sidang'); // Sesuaikan nama tabel dengan yang Anda gunakan
        return $query->row_array();
    }

    public function update_draft($id, $data)
    {
        // Update data draft berdasarkan ID
        $this->db->where('id', $id);
        $this->db->update('draft_sidang', $data); // Sesuaikan nama tabel dengan yang Anda gunakan
    }

    // Akhir upload draft

    // Jadwal sidang
    // Method to get jadwal sidang by username
    public function get_jadwal_sidang($username)
    {
        $this->db->select('js.*, d1.nama as penguji_1_nama, d2.nama as penguji_2_nama, m.npm as mahasiswa_npm, m.nama as mahasiswa_nama, k.nama_kelas as nama_kelas');
        $this->db->from('jadwal_sidang js');
        $this->db->join('plotting pl', 'js.plotting_id = pl.id');
        $this->db->join('mahasiswa m', 'pl.mahasiswa_id = m.id');
        $this->db->join('kelas k', 'm.kelas_id = k.id');
        $this->db->join('dosen d1', 'pl.dosen_penguji_1_id = d1.id', 'left');
        $this->db->join('dosen d2', 'pl.dosen_penguji_2_id = d2.id', 'left');
        $this->db->join('user u', 'm.user_id = u.id');
        $this->db->where('u.username', $username);
        return $this->db->get()->row_array();
    }
    // Akhir jadwal sidang

}