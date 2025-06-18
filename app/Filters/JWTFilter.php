<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JWTFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $key = getenv('JWT_SECRET');

        $authHeader = $request->getHeaderLine('Authorization');
        if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            return service('response')->setJSON(['message' => 'Token tidak ditemukan'])->setStatusCode(401);
        }

        $token = $matches[1];

        try {
            $decoded = JWT::decode($token, new Key($key, 'HS256'));

            // Cek role jika disediakan
            if ($arguments && !in_array($decoded->role, $arguments)) {
                return service('response')->setJSON(['message' => 'Akses ditolak'])->setStatusCode(403);
            }

            // Simpan data user ke request untuk digunakan di controller
            $request->user = $decoded;
        } catch (\Exception $e) {
            return service('response')->setJSON(['message' => 'Token tidak valid'])->setStatusCode(401);
        }

        return $request;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {}
}
