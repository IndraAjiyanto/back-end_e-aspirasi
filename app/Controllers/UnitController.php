<?php

namespace App\Controllers;

use App\Models\Unit;
use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class UnitController extends BaseController
{
    public function __construct()
    {
        $this->unitModel = new Unit();
        $this->aspirasiModel = new Aspirasi();
    }

    public function show($id){
        $data['unit'] = $this->unitModel->find($id);
        $data['aspirasi'] = $this->aspirasiModel->where('unit_id', $id)->findAll();
        return $this->response->setJSON($data);
    }
}
