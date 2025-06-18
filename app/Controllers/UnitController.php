<?php

namespace App\Controllers;

use App\Models\Jawaban;
use App\Models\Unit;
use App\Models\Aspirasi;
use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class UnitController extends BaseController
{
    protected $unitModel;
    protected $aspirasiModel;
    protected $jawabanModel;

    public function __construct()
    {
        $this->unitModel = new Unit();
        $this->aspirasiModel = new Aspirasi();
        $this->jawabanModel = new Jawaban();
    }

    public function index(){
            $data = $this->unitModel->findAll();
            return $this->response->setJSON($data);
    }

    public function show($id){
        $data['unit'] = $this->unitModel->find($id);
        $data['aspirasi'] = $this->aspirasiModel->where('unit_id', $id)->findAll();
        return $this->response->setJSON($data);
    }

public function getAspirasiUnit($user_id){
{
    // Ambil data unit berdasarkan user_id
    $unit = $this->unitModel->where('user_id', $user_id)->first();

    // Ambil aspirasi yang terkait unit
    $aspirasis = $this->aspirasiModel
        ->where('unit_id', $unit['id'])
        ->orderBy('created_at', 'asc')
        ->findAll();

    $data = [];

    foreach ($aspirasis as $aspirasi) {
        // Cek apakah aspirasi ini memiliki jawaban
        $jawaban = $this->jawabanModel->where('aspirasi_id', $aspirasi['id'])->first();

        if (!$jawaban) {
            // Update status di database jika belum ada jawaban dan status belum 'diproses'
            $this->aspirasiModel->update($aspirasi['id'], ['status' => 'diproses']);
        }

        $data[] = $aspirasi;
    }

    return $this->response->setJSON([
        'status' => 'success',
        'aspirasi' => $data,
    ]);
}

}
}
