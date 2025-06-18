<?php

namespace App\Controllers;

use App\Models\Unit;
use App\Models\Jawaban;
use App\Models\Aspirasi;
use App\Models\Mahasiswa;
use CodeIgniter\Controller;
use Myth\Auth\Entities\User;
use Myth\Auth\Models\UserModel;
use App\Controllers\BaseController;
use CodeIgniter\Exceptions\PageNotFoundException;

class AspirasiController extends BaseController
{
    protected $aspirasiModel;
    protected $unitModel;
    protected $jawabanModel;
    protected $mahasiswaModel;

    public function __construct()
    {
        $this->aspirasiModel = new Aspirasi();
        $this->unitModel = new Unit();
        $this->jawabanModel = new Jawaban();
        $this->mahasiswaModel = new Mahasiswa();
    }


    public function create()
    {
        $validation = \Config\Services::validation();
        $validation->setRules([
            'isi'           => 'required',
            'unit_id'       => 'required',
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => $validation->getErrors()])->setStatusCode(422);
        }

        $mahasiswa = $this->mahasiswaModel->where('user_id', $this->request->getVar('user_id'))->first();

        $this->aspirasiModel->insert([
            'mahasiswa_id' => $mahasiswa['id'],
            'isi'           => $this->request->getVar('isi'),
            'unit_id'       => $this->request->getVar('unit_id'),
            'status'        => 'diproses',
            'created_at'    => date('Y-m-d H:i:s')
        ]);

        return $this->response->setJSON(['status' => 'success']);
    }

    public function show($id){
        $data['aspirasi'] = $this->aspirasiModel->find($id);
        $data['jawaban'] = $this->jawabanModel->where('aspirasi_id', $id)->orderBy('created_at', 'asc')->findAll();
        $data['unit'] = $this->unitModel->find($data['aspirasi']['unit_id']);
        return $this->response->setJSON($data);
    }

    public function edit($id){
        $aspirasi = $this->aspirasiModel->find($id);
        if (!$aspirasi) {
            return $this->response->setStatusCode(404)->setJSON(['message' => 'Data tidak ditemukan']);
        }
        return $this->response->setJSON($aspirasi);
    }


    public function update($id)
    {
        $aspirasi = $this->aspirasiModel->find($id);
        if (!$aspirasi) {
            return $this->response->setStatusCode(404)->setJSON(['message' => 'Data tidak ditemukan']);
        }

        $this->aspirasiModel->update($id, [
            'isi'        => $this->request->getVar('isi'),
            'unit_id'    => $this->request->getVar('unit_id'),
        ]);

        return $this->response->setJSON(['message' => 'Aspirasi berhasil diupdate']);
    }

    public function delete($id)
    {
        
        if (!$this->aspirasiModel->find($id)) {
            return $this->response->setStatusCode(404)->setJSON(['message' => 'Data tidak ditemukan']);
        }

        $this->aspirasiModel->delete($id);
        return $this->response->setJSON(['message' => 'Aspirasi berhasil dihapus']);
    }

public function getAspirasi($id)
{
    $data = [];

    // Cari data mahasiswa berdasarkan user_id
    $mahasiswa = $this->mahasiswaModel->where('user_id', $id)->first();

    if (!$mahasiswa) {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Mahasiswa tidak ditemukan',
        ]);
    }

    // Ambil semua aspirasi milik mahasiswa tersebut
    $aspirasis = $this->aspirasiModel
        ->where('mahasiswa_id', $mahasiswa['id'])
        ->findAll();

    foreach ($aspirasis as $aspirasi) {
        // Ambil data unit
        $unit = $this->unitModel->find($aspirasi['unit_id']);

        // Cek apakah aspirasi sudah dijawab
        $jawaban = $this->jawabanModel->where('aspirasi_id', $aspirasi['id'])->first();

        if (!$jawaban && $aspirasi['status'] !== 'diproses') {
            // Update status di database jika belum diproses
            $this->aspirasiModel->update($aspirasi['id'], ['status' => 'diproses']);
            $aspirasi['status'] = 'diproses';
        }

        // Tambahkan nama unit ke data aspirasi
        $aspirasi['unit_nama'] = $unit ? $unit['nama'] : 'Tidak diketahui';

        $data[] = $aspirasi;
    }

    return $this->response->setJSON([
        'status' => 'success',
        'aspirasi' => $data,
    ]);
}



}
