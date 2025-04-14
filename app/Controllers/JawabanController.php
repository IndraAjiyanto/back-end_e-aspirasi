<?php

namespace App\Controllers;
use CodeIgniter\Controller;
use App\Models\Jawaban;
use CodeIgniter\Exceptions\PageNotFoundException;
use App\Controllers\BaseController;


class JawabanController extends BaseController
{
    protected $jawabanModel;

    public function __construct()
    {
        $this->jawabanModel = new Jawaban();
    }

  
    public function index()
    {
        $data['jawaban'] = $this->jawabanModel->findAll();
        return $this->response->setJSON($data);
    }

  
    public function create()
    {
        $validation = \Config\Services::validation();
        $validation->setRules([
            'isi'           => 'required',
            'aspirasi_id' => 'required'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return $this->response->setJSON(['errors' => $validation->getErrors()]);
        }

        $this->jawabanModel->insert([
            'isi'           => $this->request->getPost('isi'),
            'aspirasi_id'       => $this->request->getPost('aspirasi_id'),
            'created_at'    => date('Y-m-d H:i:s')
        ]);

        return $this->response->setJSON(['message' => 'Jawaban berhasil dikirim']);
    }


    public function update($id)
    {
        $jawaban = $this->jawabanModel->find($id);
        if (!$jawaban) {
            return $this->response->setStatusCode(404)->setJSON(['message' => 'data tidak ditemukan']);
        }

        $this->jawabanModel->update($id, [
            'isi'        => $this->request->getPost('isi'),
            'aspirasi_id'    => $this->request->getPost('aspirasi_id'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        return $this->response->setJSON(['message' => 'Aspirasi berhasil diupdate']);
    }

    public function delete($id)
    {
        if (!$this->jawabanModel->find($id)) {
            return $this->response->setStatusCode(404)->setJSON(['message' => 'Data tidak ditemukan']);
        }

        $this->jawabanModel->delete($id);
        return $this->response->setJSON(['message' => 'Aspirasi berhasil dihapus']);
    }

}
