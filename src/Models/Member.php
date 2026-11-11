<?php
namespace App\Models;

/**
 * Member class - demonstrates Inheritance
 */
class Member extends User {
    private array $borrowedBooks = [];
    private int $borrowLimit = 3;
    
    public function __construct(string $email, string $password, string $name) {
        parent::__construct($email, $password, $name, 'member');
    }
    
    // Polymorphism - implementation of abstract method
    public function getPermissions(): array {
        return [
            'borrow_book',
            'view_catalog',
            'view_transactions'
        ];
    }
    
    // Member-specific methods
    public function canBorrow(): bool {
        return count($this->borrowedBooks) < $this->borrowLimit;
    }
    
    public function getBorrowedBooks(): array {
        return $this->borrowedBooks;
    }
    
    public function addBorrowedBook(string $bookId): void {
        if (!in_array($bookId, $this->borrowedBooks)) {
            $this->borrowedBooks[] = $bookId;
        }
    }
    
    public function removeBorrowedBook(string $bookId): void {
        $this->borrowedBooks = array_filter($this->borrowedBooks, fn($id) => $id !== $bookId);
    }
    
    public function getBorrowLimit(): int {
        return $this->borrowLimit;
    }
    
    public function toArray(): array {
        $data = parent::toArray();
        $data['borrowedBooks'] = $this->borrowedBooks;
        $data['borrowLimit'] = $this->borrowLimit;
        return $data;
    }
}
