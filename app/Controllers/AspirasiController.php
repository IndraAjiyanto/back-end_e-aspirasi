<?php

namespace App\Controllers;

use App\Models\Unit;
use App\Models\Jawaban;
use App\Models\Aspirasi;
use CodeIgniter\Controller;
use App\Controllers\BaseController;
use CodeIgniter\Exceptions\PageNotFoundException;

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
        $aspirasis = $this->aspirasiModel->orderBy('created_at', 'asc')->findAll();
        $data = [];

        foreach ($aspirasis as $aspirasi) {
            $unit = $this->unitModel->find($aspirasi['unit_id']);
            $aspirasi['unit_nama'] = $unit ? $unit['nama'] : 'Tidak diketahui';
            $data[] = $aspirasi;
        }
    
        return $this->response->setJSON($data);
    }

    public function create()
    {
        $validation = \Config\Services::validation();
        $validation->setRules([
            'mahasiswa_nim' => 'required|is_not_unique[mahasiswa.nim]',
            'isi'           => 'required',
            'unit_id'       => 'required',
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => $validation->getErrors()])->setStatusCode(422);
        }

        $this->aspirasiModel->insert([
            'mahasiswa_nim' => $this->request->getVar('mahasiswa_nim'),
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
            'mahasiswa_nim'        => $this->request->getVar('mahasiswa_nim'),
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

    public function getAspirasiUnit($unit){
        $aspirasi = $this->aspirasiModel->where('unit_id', $unit)->orderBy('created_at', 'asc')->findAll();
        return $this->response->setJSON($aspirasi);
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
