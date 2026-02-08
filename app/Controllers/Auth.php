<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Libraries\JWTLibrary;

class Auth extends BaseController
{
    protected $userModel;
    protected $jwt;

    public function __construct()
    {
        // Inisialisasi model User dan library JWT.
        $this->userModel = new UserModel();
        $this->jwt = new JWTLibrary();
    }

    public function login()
    {
        // Menampilkan halaman login, atau redirect ke dashboard jika sudah login.
        if (session()->has('isLoggedIn') && session()->get('role') === 'admin') {
            return redirect()->to('/admin/dashboard');
        }

        return view('auth/login');
    }

    public function processLogin()
    {
        // Memproses permintaan login dari pengguna.
        $rules = [
            'username' => 'required',
            'password' => 'required|min_length[6]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');
        $remember = $this->request->getPost('remember') ? true : false;

        // Verify credentials
        $user = $this->userModel->verifyCredentials($username, $password);

        if (!$user) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Username atau password salah!');
        }

        // Check if user is admin (login hanya untuk admin)
        if ($user['role'] !== 'admin') {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Akses ditolak! Login hanya diperuntukkan untuk Administrator.');
        }

        // Check if user is active
        if ($user['is_active'] != 1) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Akun Anda tidak aktif. Hubungi administrator!');
        }

        // Get IP address
        $ipAddress = $this->request->getIPAddress();

        // Update last login
        $this->userModel->updateLastLogin($user['id'], $ipAddress);

        // Generate JWT token
        $tokenData = [
            'user_id'   => $user['id'],
            'username'  => $user['username'],
            'email'     => $user['email'],
            'role'      => $user['role'],
            'ip'        => $ipAddress
        ];

        // Set expiration based on remember me
        if ($remember) {
            $this->jwt->setExpirationTime(2592000); // 30 days
        }

        $token = $this->jwt->generateToken($tokenData);

        // Set session
        $sessionData = [
            'isLoggedIn' => true,
            'user_id'    => $user['id'],
            'username'   => $user['username'],
            'email'      => $user['email'],
            'full_name'  => $user['full_name'],
            'role'       => $user['role'],
            'jwt_token'  => $token,
            'login_time' => time()
        ];

        session()->set($sessionData);

        // Store token in cookie if remember me
        if ($remember) {
            $this->response->setCookie([
                'name'     => 'jwt_token',
                'value'    => $token,
                'expire'   => 2592000, // 30 days
                'secure'   => false,   // Set to true in production with HTTPS
                'httponly' => true,
                'samesite' => 'Lax'
            ]);
        }

        // Log successful login
        log_message('info', "[AUTH] Admin {$user['username']} logged in from IP: {$ipAddress}");

        // Redirect ke admin dashboard (hanya admin yang bisa login)
        return redirect()->to('/admin/dashboard')
            ->with('success', 'Selamat datang, ' . $user['full_name'] . '!');
    }

    public function logout()
    {
        // Memproses logout pengguna dan menghancurkan sesi.
        $username = session()->get('username');

        // Log logout
        if ($username) {
            log_message('info', "[AUTH] User {$username} logged out");
        }

        // Destroy session
        session()->destroy();

        // Delete JWT cookie
        $this->response->deleteCookie('jwt_token');

        return redirect()->to('/auth/login')
            ->with('success', 'Anda telah berhasil logout');
    }

    public function accessDenied()
    {
        // Menampilkan halaman "Akses Ditolak".
        return view('auth/access_denied');
    }

    public function validateToken()
    {
        // [API] Memvalidasi token JWT yang diberikan.
        $token = $this->request->getHeaderLine('Authorization');

        if (empty($token)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Token tidak ditemukan'
            ])->setStatusCode(401);
        }

        // Remove "Bearer " prefix if exists
        $token = str_replace('Bearer ', '', $token);

        $userData = $this->jwt->verifyToken($token);

        if (!$userData) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Token tidak valid atau sudah expired'
            ])->setStatusCode(401);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'data' => $userData
        ]);
    }

    public function refreshToken()
    {
        // [API] Memperbarui token JWT yang sudah ada.
        $oldToken = $this->request->getHeaderLine('Authorization');

        if (empty($oldToken)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Token tidak ditemukan'
            ])->setStatusCode(401);
        }

        $oldToken = str_replace('Bearer ', '', $oldToken);
        $userData = $this->jwt->verifyToken($oldToken);

        if (!$userData) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Token tidak valid'
            ])->setStatusCode(401);
        }

        // Generate new token
        $newToken = $this->jwt->generateToken($userData);

        return $this->response->setJSON([
            'status' => 'success',
            'token' => $newToken
        ]);
    }
}
