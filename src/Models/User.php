<?php
namespace App\Models;

/**
 * Base User class - demonstrates Encapsulation
 */
abstract class User {
    protected string $id;
    protected string $email;
    protected string $password;
    protected string $name;
    protected string $role;
    protected string $createdAt;
    
    public function __construct(string $email, string $password, string $name, string $role) {
        $this->id = $this->generateId();
        $this->email = $email;
        $this->password = password_hash($password, PASSWORD_BCRYPT);
        $this->name = $name;
        $this->role = $role;
        $this->createdAt = date('Y-m-d H:i:s');
    }
    
    // Getters (Encapsulation)
    public function getId(): string {
        return $this->id;
    }
    
    public function getEmail(): string {
        return $this->email;
    }
    
    public function getName(): string {
        return $this->name;
    }
    
    public function getRole(): string {
        return $this->role;
    }
    
    public function getCreatedAt(): string {
        return $this->createdAt;
    }
    
    // Setters (Encapsulation)
    public function setName(string $name): void {
        $this->name = $name;
    }
    
    public function setEmail(string $email): void {
        $this->email = $email;
    }
    
    // Password verification
    public function verifyPassword(string $password): bool {
        return password_verify($password, $this->password);
    }
    
    // Abstract method - must be implemented by child classes (Polymorphism)
    abstract public function getPermissions(): array;
    
    // Convert to array for storage
    public function toArray(): array {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'password' => $this->password,
            'name' => $this->name,
            'role' => $this->role,
            'createdAt' => $this->createdAt
        ];
    }
    
    // Generate unique ID
    protected function generateId(): string {
        return uniqid('user_', true);
    }
    
    // Factory method to create user from array
    public static function fromArray(array $data): User {
        $role = $data['role'];
        
        if ($role === 'admin') {
            $user = new Admin($data['email'], '', $data['name']);
        } else {
            $user = new Member($data['email'], '', $data['name']);
        }
        
        // Restore original data
        $user->id = $data['id'];
        $user->password = $data['password']; // Already hashed
        $user->createdAt = $data['createdAt'];
        
        return $user;
    }
}
