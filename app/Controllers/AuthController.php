<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use Myth\Auth\Entities\User;
use Myth\Auth\Models\UserModel;

class AuthController extends ResourceController
{
    protected $format = 'json';
    protected $auth;
    protected $config;
    protected $users;
    protected $userModel;

    public function __construct()
    {
        $this->auth = service('authentication');
        $this->config = config('Auth');
        $this->users = model(UserModel::class);
        $this->userModel = new UserModel();
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
        $rules = [
            'username'     => 'required|min_length[5]|is_unique[users.username]',
            'email'        => 'required|valid_email|is_unique[users.email]',
            'password'     => 'required|min_length[8]',
            'pass_confirm' => 'required|matches[password]',
        ];

        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $user = new User([
            'username' => $this->request->getJsonVar('username'),
            'email' => $this->request->getJsonVar('email'),
            'password' => $this->request->getJsonVar('password'),
            'active' => 1
        ]);

        if (!$this->userModel->save($user)) {
            return $this->failServerError('Gagal menyimpan user', $this->userModel->errors());
        }

        // Tambahkan ke grup jika perlu
        $db = \Config\Database::connect();
        $db->table('auth_groups_users')->insert([
            'user_id' => $this->userModel->getInsertID(),
            'group_id' => 2 // contoh: mahasiswa
        ]);

        return $this->respondCreated([
            'status' => 'success',
            'message' => 'Registrasi berhasil',
        ]);
    }
}
