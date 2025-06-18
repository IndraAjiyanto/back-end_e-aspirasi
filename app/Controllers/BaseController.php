<?php

namespace App\Controllers;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Models\UserModel;
use CodeIgniter\Controller;
use Psr\Log\LoggerInterface;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var list<string>
     */
    protected $helpers = [];

    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */
    // protected $session;

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.

        // E.g.: $this->session = service('session');
    }

protected function getAuthenticatedUser()
{
    // Ambil header Authorization
    $authHeader = $this->request->getHeaderLine('Authorization');

    if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
        $this->response->setStatusCode(401)->setJSON(['message' => 'Unauthorized'])->send();
        exit;
    }

    $token = $matches[1];
    $secretKey = getenv('JWT_SECRET'); // atau env('JWT_SECRET') kalau kamu punya helper env()
// Ganti dengan secret key JWT kamu

    try {
        // Decode token JWT
        $decoded = JWT::decode($token, new Key($secretKey, 'HS256'));

        // Ambil user id dari token, misal di claim 'sub'
        $userId = $decoded->sub ?? null;
        if (!$userId) {
            throw new \Exception('Invalid token payload');
        }

        // Cari user di database
        $userModel = new UserModel();
        $user = $userModel->find($userId);

        if (!$user) {
            $this->response->setStatusCode(401)->setJSON(['message' => 'Unauthorized'])->send();
            exit;
        }

        // Return data user (bisa array/object sesuai model)
        return $user;

    } catch (\Exception $e) {
        $this->response->setStatusCode(401)->setJSON(['message' => 'Unauthorized'])->send();
        exit;
    }

}
}
