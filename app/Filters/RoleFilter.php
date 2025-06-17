<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Myth\Auth\Exceptions\PermissionException;

class RoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $auth = service('authentication');
        $authorize = service('authorization');

        if (! $auth->check()) {
            session()->set('redirect_url', current_url());
            return redirect()->to('/login');
        }

        $userId = $auth->id();

        if (empty($arguments)) {
            return;
        }

        foreach ($arguments as $group) {
            if ($authorize->inGroup($group, $userId)) {
                return;
            }
        }

        return redirect()->to('/blocked')->with('error', 'Akses ditolak. Kamu tidak punya hak untuk mengakses halaman ini.');
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Tidak digunakan
    }
}
