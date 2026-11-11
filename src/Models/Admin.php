<?php
namespace App\Models;

/**
 * Admin class - demonstrates Inheritance and Polymorphism
 */
class Admin extends User {
    private array $managedCategories = [];
    
    public function __construct(string $email, string $password, string $name) {
        parent::__construct($email, $password, $name, 'admin');
    }
    
    // Polymorphism - different implementation from Member
    public function getPermissions(): array {
        return [
            'borrow_book',
            'view_catalog',
            'view_transactions',
            'add_book',
            'edit_book',
            'delete_book',
            'manage_users',
            'view_reports'
        ];
    }
    
    // Admin-specific methods
    public function canManageBooks(): bool {
        return true;
    }
    
    public function canManageUsers(): bool {
        return true;
    }
    
    public function getManagedCategories(): array {
        return $this->managedCategories;
    }
    
    public function addManagedCategory(string $category): void {
        if (!in_array($category, $this->managedCategories)) {
            $this->managedCategories[] = $category;
        }
    }
    
    public function toArray(): array {
        $data = parent::toArray();
        $data['managedCategories'] = $this->managedCategories;
        return $data;
    }
}
