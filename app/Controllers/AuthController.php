<?php

namespace App\Controllers;

use Config\Database;
use App\Models\Mahasiswa;
use Myth\Auth\Entities\User;
use Myth\Auth\Models\UserModel;
use CodeIgniter\RESTful\ResourceController;

class AuthController extends ResourceController
{
    protected $format = 'json';
    protected $auth;
    protected $config;
    protected $users;
    protected $userModel;
    protected $mahasiswaModel;
    protected $db;

    public function __construct()
    {
        $this->auth = service('authentication');
        $this->config = config('Auth');
        $this->users = model(UserModel::class);
        $this->userModel = new UserModel();
        $this->mahasiswaModel = new Mahasiswa();
        $this->db = Database::connect();
    }

    public function login()
    {
        $rules = [
            'username' => 'required',
            'password' => 'required',
        ];

        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $login = $this->request->getJsonVar('username') ?? $this->request->getPost('username');
        $password = $this->request->getJsonVar('password') ?? $this->request->getPost('password');
        $type = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        if (!$this->auth->attempt([$type => $login, 'password' => $password])) {
            return $this->failUnauthorized('Login gagal. Username atau password salah.');
        }

        $user = $this->auth->user();

        return $this->respond([
            'status' => 'success',
            'message' => 'Login berhasil',
            'user' => [
                'id' => $user->id,
                'username' => $user->username,
                'email' => $user->email,
            ],
            'token' => session()->get('__ci_last_regenerate'), // opsional token
        ]);
    }

    public function logout()
    {
        if ($this->auth->check()) {
            $this->auth->logout();
            session()->destroy();
        }

        return $this->respond(['message' => 'Logout berhasil']);
    }

public function register()
{
    $rulesUser = [
        'username'     => 'required|min_length[5]|is_unique[users.username]',
        'email'        => 'required|valid_email|is_unique[users.email]',
        'password'     => 'required|min_length[8]',
        'pass_confirm' => 'required|matches[password]',
    ];

    $rulesMahasiswa = [
        'nim'     => 'required|min_length[5]|is_unique[mahasiswa.nim]',
        'nama'    => 'required|min_length[3]',
        'kelas'   => 'required|min_length[3]',
        'prodi'   => 'required',
        'jurusan' => 'required',
    ];

    $rules = array_merge($rulesUser, $rulesMahasiswa);

    if (!$this->validate($rules)) {
        return $this->failValidationErrors($this->validator->getErrors());
    }

    $password = $this->request->getJsonVar('password');
    // Simpan user
    $user = new User([
        'username' => $this->request->getJsonVar('username'),
        'email'    => $this->request->getJsonVar('email'),
        'password' => password_hash($password, PASSWORD_DEFAULT),
        'active'   => 1
    ]);

    if (!$this->userModel->save($user)) {
        return $this->failServerError('Gagal menyimpan user: ' . json_encode($this->userModel->errors()));
    }

    $userId = $this->userModel->getInsertID();

    // Simpan data mahasiswa
    $mahasiswa = [
        'nim'     => $this->request->getJsonVar('nim'),
        'nama'    => $this->request->getJsonVar('nama'),
        'kelas'   => $this->request->getJsonVar('kelas'),
        'prodi'   => $this->request->getJsonVar('prodi'),
        'jurusan' => $this->request->getJsonVar('jurusan'),
        'user_id' => $userId,
    ];

    if (!$this->mahasiswaModel->save($mahasiswa)) {
        return $this->failServerError('Gagal menyimpan data mahasiswa: ' . json_encode($this->mahasiswaModel->errors()));
    }

    // Masukkan ke grup "mahasiswa"
    $this->db->table('auth_groups_users')->insert([
        'user_id'  => $userId,
        'group_id' => 4 // id grup mahasiswa
    ]);

    return $this->respondCreated([
        'status'  => 'success',
        'message' => 'Registrasi berhasil',
    ]);
}

}
