<?php

namespace App\Controllers;

use App\Models\Unit;
use App\Models\Jawaban;
use App\Models\Aspirasi;
use CodeIgniter\Controller;
use App\Controllers\BaseController;
use CodeIgniter\Exceptions\PageNotFoundException;
use Myth\Auth\Entities\User;
use Myth\Auth\Models\UserModel;

class AspirasiController extends BaseController
{
    protected $aspirasiModel;
    protected $unitModel;
    protected $jawabanModel;

    public function __construct()
    {
        $this->aspirasiModel = new Aspirasi();
        $this->unitModel = new Unit();
        $this->jawabanModel = new Jawaban();
    }

  
    public function index()
    {
        $data['aspirasi'] = $this->aspirasiModel->findAll();
        return $this->response->setJSON($data);
    }

    public function insert(){
        $data['unit'] = $this->unitModel->findAll();
        return $this->response->setJSON($data);
    }


    public function create()
    {
        $validation = \Config\Services::validation();
        $validation->setRules([
            'mahasiswa_nim' => 'required',
            'isi'           => 'required',
            'unit_id'       => 'required'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return $this->response->setJSON(['errors' => $validation->getErrors()]);
        }

        $this->aspirasiModel->insert([
            'mahasiswa_nim' => $this->request->getVar('mahasiswa_nim'),
            'isi'           => $this->request->getVar('isi'),
            'unit_id'       => $this->request->getVar('unit_id'),
            'status'        => 'belum diproses',
            'created_at'    => date('Y-m-d H:i:s')
        ]);

        return $this->response->setJSON(['message' => 'Aspirasi berhasil dikirim']);
    }

    public function show($id){
        $data['aspirasi'] = $this->aspirasiModel->find($id);
        $data['jawaban'] = $this->jawabanModel->where('aspirasi_id', $id)->first();
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
            'updated_at' => date('Y-m-d H:i:s')
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


    public function updateStatus($id)
    {
        $status = $this->request->getPost('status'); 

        if (!$this->aspirasiModel->find($id)) {
            return $this->response->setStatusCode(404)->setJSON(['message' => 'Data tidak ditemukan']);
        }

        $this->aspirasiModel->update($id, [
            'status'     => $status,
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        return $this->response->setJSON(['message' => 'Status aspirasi berhasil diperbarui']);
    }
    public function myAspirasi()
    {
    $user = auth()->user();
    if (!$user) {
        return $this->response->setStatusCode(401)->setJSON(['message' => 'Unauthorized']);
    }

    // Ambil mahasiswa_id dari user
    $userModel = new UserModel();
    $userData = $userModel->find($user->id);
    $mahasiswa_id = $userData->mahasiswa_id;

    // Cek NIM mahasiswa dari tabel mahasiswa
    $db = \Config\Database::connect();
    $mahasiswa = $db->table('mahasiswa')->where('id', $mahasiswa_id)->get()->getRow();

    // Cari aspirasi berdasarkan nim
    $aspirasi = $this->aspirasiModel->where('mahasiswa_nim', $mahasiswa->nim)->findAll();

    return $this->response->setJSON(['aspirasi' => $aspirasi]);
    }
}
