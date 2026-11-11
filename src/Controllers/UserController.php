<?php
namespace App\Controllers;

use App\Models\User;
use App\Models\Admin;
use App\Models\Member;

/**
 * UserController - handles user authentication and management
 */
class UserController {
    private string $storageFile;
    
    public function __construct() {
        $this->storageFile = STORAGE_PATH . '/users.json';
    }
    
    // Create default admin if no users exist
    public function createDefaultAdmin(): void {
        $users = $this->getAllUsers();
        if (empty($users)) {
            $admin = new Admin('admin@ebook.com', 'admin123', 'Administrator');
            $this->saveUser($admin);
        }
    }
    
    // Register new member
    public function register(string $email, string $password, string $name): array {
        // Validate email uniqueness
        if ($this->findUserByEmail($email)) {
            return ['success' => false, 'message' => 'Email sudah terdaftar'];
        }
        
        // Create new member
        $member = new Member($email, $password, $name);
        $this->saveUser($member);
        
        return ['success' => true, 'message' => 'Registrasi berhasil'];
    }
    
    // Login
    public function login(string $email, string $password): array {
        $user = $this->findUserByEmail($email);
        
        if (!$user) {
            return ['success' => false, 'message' => 'Email tidak ditemukan'];
        }
        
        if (!$user->verifyPassword($password)) {
            return ['success' => false, 'message' => 'Password salah'];
        }
        
        // Set session
        $_SESSION['user_id'] = $user->getId();
        $_SESSION['user_email'] = $user->getEmail();
        $_SESSION['user_name'] = $user->getName();
        $_SESSION['user_role'] = $user->getRole();
        
        return ['success' => true, 'message' => 'Login berhasil', 'user' => $user];
    }
    
    // Logout
    public function logout(): void {
        session_destroy();
    }
    
    // Get all users
    public function getAllUsers(): array {
        $data = json_decode(file_get_contents($this->storageFile), true);
        $users = [];
        
        foreach ($data as $userData) {
            $users[] = User::fromArray($userData);
        }
        
        return $users;
    }
    
    // Find user by email
    public function findUserByEmail(string $email): ?User {
        $users = $this->getAllUsers();
        
        foreach ($users as $user) {
            if ($user->getEmail() === $email) {
                return $user;
            }
        }
        
        return null;
    }
    
    // Find user by ID
    public function findUserById(string $id): ?User {
        $users = $this->getAllUsers();
        
        foreach ($users as $user) {
            if ($user->getId() === $id) {
                return $user;
            }
        }
        
        return null;
    }
    
    // Save user
    private function saveUser(User $user): void {
        $data = json_decode(file_get_contents($this->storageFile), true);
        $data[] = $user->toArray();
        file_put_contents($this->storageFile, json_encode($data, JSON_PRETTY_PRINT));
    }
    
    // Update user
    public function updateUser(User $user): void {
        $data = json_decode(file_get_contents($this->storageFile), true);
        
        foreach ($data as $key => $userData) {
            if ($userData['id'] === $user->getId()) {
                $data[$key] = $user->toArray();
                break;
            }
        }
        
        file_put_contents($this->storageFile, json_encode($data, JSON_PRETTY_PRINT));
    }
}
