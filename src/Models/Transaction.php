<?php
namespace App\Models;

/**
 * Transaction class - demonstrates Encapsulation
 */
class Transaction {
    private string $id;
    private string $userId;
    private string $bookId;
    private string $type; // 'borrow' or 'return'
    private string $borrowDate;
    private ?string $dueDate;
    private ?string $returnDate;
    private string $status; // 'active', 'returned', 'overdue'
    private float $amount;
    private float $fine;
    
    public function __construct(
        string $userId,
        string $bookId,
        string $type,
        float $amount
    ) {
        $this->id = $this->generateId();
        $this->userId = $userId;
        $this->bookId = $bookId;
        $this->type = $type;
        $this->borrowDate = date('Y-m-d H:i:s');
        $this->dueDate = $type === 'borrow' ? date('Y-m-d H:i:s', strtotime('+14 days')) : null;
        $this->returnDate = null;
        $this->status = 'active';
        $this->amount = $amount;
        $this->fine = 0;
    }
    
    // Getters
    public function getId(): string {
        return $this->id;
    }
    
    public function getUserId(): string {
        return $this->userId;
    }
    
    public function getBookId(): string {
        return $this->bookId;
    }
    
    public function getType(): string {
        return $this->type;
    }
    
    public function getBorrowDate(): string {
        return $this->borrowDate;
    }
    
    public function getDueDate(): ?string {
        return $this->dueDate;
    }
    
    public function getReturnDate(): ?string {
        return $this->returnDate;
    }
    
    public function getStatus(): string {
        return $this->status;
    }
    
    public function getAmount(): float {
        return $this->amount;
    }
    
    public function getFine(): float {
        return $this->fine;
    }
    
    // Business logic
    public function markAsReturned(): void {
        $this->returnDate = date('Y-m-d H:i:s');
        $this->status = 'returned';
        
        // Calculate fine if overdue
        if ($this->dueDate && strtotime($this->returnDate) > strtotime($this->dueDate)) {
            $daysLate = ceil((strtotime($this->returnDate) - strtotime($this->dueDate)) / 86400);
            $this->fine = $daysLate * 1000; // Rp 1000 per day
        }
    }
    
    public function checkOverdue(): bool {
        if ($this->status === 'active' && $this->dueDate) {
            if (strtotime('now') > strtotime($this->dueDate)) {
                $this->status = 'overdue';
                return true;
            }
        }
        return false;
    }
    
    public function toArray(): array {
        return [
            'id' => $this->id,
            'userId' => $this->userId,
            'bookId' => $this->bookId,
            'type' => $this->type,
            'borrowDate' => $this->borrowDate,
            'dueDate' => $this->dueDate,
            'returnDate' => $this->returnDate,
            'status' => $this->status,
            'amount' => $this->amount,
            'fine' => $this->fine
        ];
    }
    
    private function generateId(): string {
        return uniqid('trans_', true);
    }
    
    public static function fromArray(array $data): Transaction {
        $transaction = new self(
            $data['userId'],
            $data['bookId'],
            $data['type'],
            $data['amount']
        );
        
        $transaction->id = $data['id'];
        $transaction->borrowDate = $data['borrowDate'];
        $transaction->dueDate = $data['dueDate'];
        $transaction->returnDate = $data['returnDate'];
        $transaction->status = $data['status'];
        $transaction->fine = $data['fine'];
        
        return $transaction;
    }
}
