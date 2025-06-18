<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\Mahasiswa;
use CodeIgniter\RESTful\ResourceController;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthController extends ResourceController
{
    protected $format = 'json';
    protected $userModel;
    protected $mahasiswaModel;
    protected $key;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->mahasiswaModel = new Mahasiswa();
        $this->key = getenv('JWT_SECRET');
    }

    private function generateJWT($user)
    {
        $payload = [
            'iat' => time(),
            'exp' => time() + 3600,
            'uid' => $user['id'],
            'username' => $user['username'],
            'role' => $user['role']
        ];

        return JWT::encode($payload, $this->key, 'HS256');
    }

    public function login()
    {
        $username = $this->request->getJSON()->username ?? '';
        $password = $this->request->getJSON()->password ?? '';

        $user = $this->userModel->where('username', $username)->first();

        if (!$user || !password_verify($password, $user['password'])) {
            return $this->failUnauthorized('Username atau password salah.');
        }

        $token = $this->generateJWT($user);

        return $this->respond([
            'status' => 'success',
            'token' => $token,
            'user' => [
                'id' => $user['id'],
                'username' => $user['username'],
                'email' => $user['email'],
                'role' => $user['role']
            ]
        ]);
    }

    public function register()
    {
        $data = $this->request->getJSON();

        $rules = [
            'username' => 'required|is_unique[users.username]',
            'email' => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[6]',
            'nim' => 'required|is_unique[mahasiswa.nim]',
            'nama' => 'required',
            'kelas' => 'required',
            'prodi' => 'required',
            'jurusan' => 'required'
        ];

        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $userData = [
            'username' => $data->username,
            'email' => $data->email,
            'password' => password_hash($data->password, PASSWORD_DEFAULT),
            'role' => 'mahasiswa'
        ];

        if (!$this->userModel->insert($userData)) {
    return $this->failServerError('Gagal membuat user.');
}

$userId = $this->userModel->getInsertID(); 

        $this->mahasiswaModel->insert([
            'nim' => $data->nim,
            'nama' => $data->nama,
            'kelas' => $data->kelas,
            'prodi' => $data->prodi,
            'jurusan' => $data->jurusan,
            'user_id' => $userId
        ]);

        return $this->respondCreated(['message' => 'Registrasi berhasil']);
    }

    public function logout()
    {
        // JWT logout tidak perlu server-side kecuali pakai blacklist
        return $this->respond(['message' => 'Logout berhasil (hapus token di frontend)']);
    }
}