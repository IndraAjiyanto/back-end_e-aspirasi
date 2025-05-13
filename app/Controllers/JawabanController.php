<?php

namespace App\Controllers;
use App\Models\Jawaban;
use App\Models\Aspirasi;
use CodeIgniter\Controller;
use App\Controllers\BaseController;
use CodeIgniter\Exceptions\PageNotFoundException;


class JawabanController extends BaseController
{
    protected $jawabanModel;
    protected $aspirasiModel;

    public function __construct()
    {
        $this->jawabanModel = new Jawaban();
        $this->aspirasiModel = new Aspirasi();
    }
  
    public function create()
    {
        $validation = \Config\Services::validation();
        $validation->setRules([
            'isi'           => 'required',
            'aspirasi_id'   => 'required',
            'status'        => 'required'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return $this->response->setJSON(['errors' => $validation->getErrors()]);
        }

        $prosesJawab = $this->jawabanModel->insert([
            'isi'           => $this->request->getVar('isi'),
            'aspirasi_id'       => $this->request->getVar('aspirasi_id'),
            'created_at'    => date('Y-m-d H:i:s')
        ]);

        if($prosesJawab){
            $this->aspirasiModel->update($this->request->getVar('aspirasi_id'), [
                'status' => $this->request->getVar('status')
            ]);
        }

        return $this->response->setJSON(['message' => 'Jawaban berhasil dikirim']);
    }

    public function show($id){
        $data['jawaban'] = $this->jawabanModel->find($id);
        return $this->response->setJSON($data);
    }

    public function edit($id){
        $jawaban = $this->jawabanModel->find($id);
        if (!$jawaban) {
            return $this->response->setStatusCode(404)->setJSON(['message' => 'Data tidak ditemukan']);
        }
        return $this->response->setJSON($jawaban);
    }

    public function update($id)
    {
        $jawaban = $this->jawabanModel->find($id);
        if (!$jawaban) {
            return $this->response->setStatusCode(404)->setJSON(['message' => 'data tidak ditemukan']);
        }

        $this->jawabanModel->update($id, [
            'isi'        => $this->request->getVar('isi'),
            'aspirasi_id'    => $this->request->getVar('aspirasi_id'),
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
