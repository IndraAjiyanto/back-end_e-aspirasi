<?php

namespace App\Controllers;

use App\Models\Aspirasi;
use CodeIgniter\Controller;
use CodeIgniter\Exceptions\PageNotFoundException;

class AspirasiController extends BaseController
{
    protected $aspirasiModel;

    public function __construct()
    {
        $this->aspirasiModel = new Aspirasi();
    }

  
    public function index()
    {
        $data['aspirasi'] = $this->aspirasiModel->findAll();
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
            'mahasiswa_nim' => $this->request->getPost('mahasiswa_nim'),
            'isi'           => $this->request->getPost('isi'),
            'unit_id'       => $this->request->getPost('unit_id'),
            'status'        => 'belum diproses',
            'created_at'    => date('Y-m-d H:i:s')
        ]);

        return $this->response->setJSON(['message' => 'Aspirasi berhasil dikirim']);
    }


    public function update($id)
    {
        $aspirasi = $this->aspirasiModel->find($id);
        if (!$aspirasi) {
            return $this->response->setStatusCode(404)->setJSON(['message' => 'Data tidak ditemukan']);
        }

        $this->aspirasiModel->update($id, [
            'isi'        => $this->request->getPost('isi'),
            'unit_id'    => $this->request->getPost('unit_id'),
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
}
