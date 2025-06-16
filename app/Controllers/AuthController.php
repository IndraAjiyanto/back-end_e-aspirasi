<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\Session\Session;
use Myth\Auth\Config\Auth as AuthConfig;
use Myth\Auth\Entities\User;
use Myth\Auth\Models\UserModel;

class AuthController extends Controller
{
    protected $auth;

    /**
     * @var AuthConfig
     */
    protected $config;

    /**
     * @var Session
     */
    protected $session;

    protected $users;
    protected $userModel;

    public function __construct()
    {
        $this->session = service('session');
        $this->config = config('Auth');
        $this->auth   = service('authentication');
        $this->users  = model(UserModel::class);
        $this->userModel = new UserModel(); // Tambahkan ini agar tidak error
    }

    public function login()
    {
        if ($this->auth->check()) {
            $redirectURL = session('redirect_url') ?? site_url('/');
            unset($_SESSION['redirect_url']);
            return redirect()->to($redirectURL);
        }

        $_SESSION['redirect_url'] = session('redirect_url') ?? previous_url() ?? site_url('/');
        return $this->_render($this->config->views['login'], ['config' => $this->config]);
    }

    public function attemptLogin()
        {
            $rules = [
                'login'    => 'required',
                'password' => 'required',
            ];
            if ($this->config->validFields === ['email']) {
                $rules['login'] .= '|valid_email';
            }

            if (!$this->validate($rules)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            $login    = $this->request->getPost('login');
            $password = $this->request->getPost('password');
            $remember = (bool)$this->request->getPost('remember');
            $type     = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

            if (!$this->auth->attempt([$type => $login, 'password' => $password], $remember)) {
                return redirect()->back()->withInput()->with('error', $this->auth->error() ?? lang('Auth.badAttempt'));
            }

            if ($this->auth->user()->force_pass_reset === true) {
                return redirect()
                    ->to(route_to('reset-password') . '?token=' . $this->auth->user()->reset_hash)
                    ->withCookies();
            }

            helper('auth'); 

            if (in_groups('admin')) {
                return redirect()->to('/admin/dashboard')->with('message', lang('Auth.loginSuccess'));
            } elseif (in_groups('mahasiswa')) {
                return redirect()->to('/mahasiswa/dashboard')->with('message', lang('Auth.loginSuccess'));
            }

            // fallback kalau gak punya grup
            return redirect()->to('/')->with('message', lang('Auth.loginSuccess'));
        }


        public function logout()
        {
            if ($this->auth->check()) {
            $this->auth->logout();
            }

            return redirect()->to(site_url('/'));
        }

    public function attemptRegister()
    {
        $validation = \Config\Services::validation();

        if (!$this->validate([
            'username'      => 'required|min_length[5]|is_unique[users.username]',
            'email'         => 'required|valid_email|is_unique[users.email]',
            'password'      => 'required|min_length[8]',
            'pass_confirm'  => 'required|matches[password]',
        ])) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Validation failed',
                'errors'  => $validation->getErrors()
            ]);
        }

        $data = [
            'username'  => $this->request->getPost('username'),
            'email'     => $this->request->getPost('email'),
            'password'  => $this->request->getPost('password'), // ini akan di-hash otomatis oleh Myth:Auth
            'active'    => 1
        ];

        $user = new User($data);

        if (!$this->userModel->save($user)) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Gagal menyimpan user',
                'errors'  => $this->userModel->errors()
            ]);
        }

        // Tambahkan ke grup mahasiswa
        $db = \Config\Database::connect();
        $db->table('auth_groups_users')->insert([
            'user_id' => $this->userModel->getInsertID(),
            'group_id' => 2
        ]);

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Registrasi berhasil! Silakan login.'
        ]);
    }

    public function forgotPassword()
    {
        if ($this->config->activeResetter === null) {
            return redirect()->route('login')->with('error', lang('Auth.forgotDisabled'));
        }

        return $this->_render($this->config->views['forgot'], ['config' => $this->config]);
    }

    public function attemptForgot()
    {
        if ($this->config->activeResetter === null) {
            return redirect()->route('login')->with('error', lang('Auth.forgotDisabled'));
        }

        $rules = [
            'email' => [
                'label' => lang('Auth.emailAddress'),
                'rules' => 'required|valid_email',
            ],
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $user = $this->users->where('email', $this->request->getPost('email'))->first();

        if (null === $user) {
            return redirect()->back()->with('error', lang('Auth.forgotNoUser'));
        }

        $user->generateResetHash();
        $this->users->save($user);

        $resetter = service('resetter');
        $sent = $resetter->send($user);

        if (!$sent) {
            return redirect()->back()->withInput()->with('error', $resetter->error() ?? lang('Auth.unknownError'));
        }

        return redirect()->route('reset-password')->with('message', lang('Auth.forgotEmailSent'));
    }

    public function resetPassword()
    {
        if ($this->config->activeResetter === null) {
            return redirect()->route('login')->with('error', lang('Auth.forgotDisabled'));
        }

        $token = $this->request->getGet('token');

        return $this->_render($this->config->views['reset'], [
            'config' => $this->config,
            'token'  => $token,
        ]);
    }

    public function attemptReset()
    {
        if ($this->config->activeResetter === null) {
            return redirect()->route('login')->with('error', lang('Auth.forgotDisabled'));
        }

        $rules = [
            'token'        => 'required',
            'email'        => 'required|valid_email',
            'password'     => 'required|strong_password',
            'pass_confirm' => 'required|matches[password]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $user = $this->users->where('email', $this->request->getPost('email'))
                            ->where('reset_hash', $this->request->getPost('token'))
                            ->first();

        if (null === $user) {
            return redirect()->back()->with('error', lang('Auth.forgotNoUser'));
        }

        if (!empty($user->reset_expires) && time() > $user->reset_expires->getTimestamp()) {
            return redirect()->back()->withInput()->with('error', lang('Auth.resetTokenExpired'));
        }

        $user->password        = $this->request->getPost('password');
        $user->reset_hash      = null;
        $user->reset_at        = date('Y-m-d H:i:s');
        $user->reset_expires   = null;
        $user->force_pass_reset = false;

        $this->users->save($user);

        return redirect()->route('login')->with('message', lang('Auth.resetSuccess'));
    }

    public function activateAccount()
    {
        $throttler = service('throttler');

        if ($throttler->check(md5($this->request->getIPAddress()), 2, MINUTE) === false) {
            return service('response')->setStatusCode(429)->setBody(lang('Auth.tooManyRequests', [$throttler->getTokentime()]));
        }

        $user = $this->users->where('activate_hash', $this->request->getGet('token'))
                            ->where('active', 0)
                            ->first();

        if (null === $user) {
            return redirect()->route('login')->with('error', lang('Auth.activationNoUser'));
        }

        $user->activate();
        $this->users->save($user);

        return redirect()->route('login')->with('message', lang('Auth.registerSuccess'));
    }

    public function resendActivateAccount()
    {
        if ($this->config->requireActivation === null) {
            return redirect()->route('login');
        }

        $throttler = service('throttler');

        if ($throttler->check(md5($this->request->getIPAddress()), 2, MINUTE) === false) {
            return service('response')->setStatusCode(429)->setBody(lang('Auth.tooManyRequests', [$throttler->getTokentime()]));
        }

        $login = urldecode($this->request->getGet('login'));
        $type = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $user = $this->users->where($type, $login)
                            ->where('active', 0)
                            ->first();

        if (null === $user) {
            return redirect()->route('login')->with('error', lang('Auth.activationNoUser'));
        }

        $activator = service('activator');
        $sent = $activator->send($user);

        if (!$sent) {
            return redirect()->back()->withInput()->with('error', $activator->error() ?? lang('Auth.unknownError'));
        }

        return redirect()->route('login')->with('message', lang('Auth.activationSuccess'));
    }

    protected function _render(string $view, array $data = [])
    {
        return view($view, $data);
    }

//     public function testUserBaru()
// {
//     $user = new \Myth\Auth\Entities\User([
//         'username' => 'loginuji',
//         'email'    => 'loginuji@example.com',
//         'password' => 'tesaja123',
//         'active'   => 1
//     ]);

//     $userModel = new \Myth\Auth\Models\UserModel();
//     $userModel->save($user);

//     // Tambahkan ke grup mahasiswa
//     $db = \Config\Database::connect();
//     $db->table('auth_groups_users')->insert([
//         'user_id' => $userModel->getInsertID(),
//         'group_id' => 2 // mahasiswa
//     ]);

//     return 'User loginuji dibuat. Silakan login.';
// }

}
