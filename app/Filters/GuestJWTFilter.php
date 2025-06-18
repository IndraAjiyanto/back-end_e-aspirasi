<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use App\Libraries\JWT;

class GuestJWTFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $authHeader = $request->getHeaderLine('Authorization');
        $token = str_replace('Bearer ', '', $authHeader);

        if ($token) {
            try {
                $jwt = new JWT();
                $jwt->decode($token); // jika berhasil decode, berarti sudah login
                return service('response')->setJSON([
                    'status' => 'error',
                    'message' => 'Anda sudah login',
                ])->setStatusCode(403);
            } catch (\Exception $e) {
                // Token tidak valid → lanjut ke controller
                return;
            }
        }

        // Tidak ada token → lanjut (belum login)
        return;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        //
    }
}
