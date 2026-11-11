<?php
namespace App\Controllers;

use App\Models\Transaction;
use App\Models\Member;

/**
 * TransactionController - handles borrowing and returning books
 */
class TransactionController {
    private string $storageFile;
    private EBookController $bookController;
    private UserController $userController;
    
    public function __construct() {
        $this->storageFile = STORAGE_PATH . '/transactions.json';
        $this->bookController = new EBookController();
        $this->userController = new UserController();
    }
    
    // Borrow a book
    public function borrowBook(string $userId, string $bookId): array {
        // Get user
        $user = $this->userController->findUserById($userId);
        if (!$user || !($user instanceof Member)) {
            return ['success' => false, 'message' => 'User tidak valid'];
        }
        
        // Check borrow limit
        if (!$user->canBorrow()) {
            return ['success' => false, 'message' => 'Anda sudah mencapai batas pinjaman (maksimal ' . $user->getBorrowLimit() . ' buku)'];
        }
        
        // Get book
        $book = $this->bookController->findBookById($bookId);
        if (!$book) {
            return ['success' => false, 'message' => 'Buku tidak ditemukan'];
        }
        
        // Check availability
        if (!$book->isAvailable()) {
            return ['success' => false, 'message' => 'Buku tidak tersedia'];
        }
        
        // Check if already borrowed
        $activeTransactions = $this->getUserActiveTransactions($userId);
        foreach ($activeTransactions as $trans) {
            if ($trans->getBookId() === $bookId) {
                return ['success' => false, 'message' => 'Anda sudah meminjam buku ini'];
            }
        }
        
        // Create transaction
        $transaction = new Transaction($userId, $bookId, 'borrow', $book->getRentalPrice());
        $this->saveTransaction($transaction);
        
        // Update book availability
        $book->borrow();
        $this->bookController->updateBookInStorage($book);
        
        // Update user borrowed books
        $user->addBorrowedBook($bookId);
        $this->userController->updateUser($user);
        
        return [
            'success' => true, 
            'message' => 'Buku berhasil dipinjam',
            'transaction' => $transaction
        ];
    }
    
    // Return a book
    public function returnBook(string $transactionId): array {
        $transaction = $this->findTransactionById($transactionId);
        
        if (!$transaction) {
            return ['success' => false, 'message' => 'Transaksi tidak ditemukan'];
        }
        
        if ($transaction->getStatus() === 'returned') {
            return ['success' => false, 'message' => 'Buku sudah dikembalikan'];
        }
        
        // Mark as returned
        $transaction->markAsReturned();
        $this->updateTransaction($transaction);
        
        // Update book availability
        $book = $this->bookController->findBookById($transaction->getBookId());
        if ($book) {
            $book->returnBook();
            $this->bookController->updateBookInStorage($book);
        }
        
        // Update user borrowed books
        $user = $this->userController->findUserById($transaction->getUserId());
        if ($user instanceof Member) {
            $user->removeBorrowedBook($transaction->getBookId());
            $this->userController->updateUser($user);
        }
        
        $message = 'Buku berhasil dikembalikan';
        if ($transaction->getFine() > 0) {
            $message .= '. Denda keterlambatan: Rp ' . number_format($transaction->getFine(), 0, ',', '.');
        }
        
        return [
            'success' => true,
            'message' => $message,
            'fine' => $transaction->getFine()
        ];
    }
    
    // Get all transactions
    public function getAllTransactions(): array {
        $data = json_decode(file_get_contents($this->storageFile), true);
        $transactions = [];
        
        foreach ($data as $transData) {
            $trans = Transaction::fromArray($transData);
            $trans->checkOverdue(); // Update status if overdue
            $transactions[] = $trans;
        }
        
        return $transactions;
    }
    
    // Get user transactions
    public function getUserTransactions(string $userId): array {
        $allTransactions = $this->getAllTransactions();
        $userTransactions = [];
        
        foreach ($allTransactions as $trans) {
            if ($trans->getUserId() === $userId) {
                $userTransactions[] = $trans;
            }
        }
        
        return $userTransactions;
    }
    
    // Get user active transactions
    public function getUserActiveTransactions(string $userId): array {
        $transactions = $this->getUserTransactions($userId);
        $active = [];
        
        foreach ($transactions as $trans) {
            if ($trans->getStatus() === 'active' || $trans->getStatus() === 'overdue') {
                $active[] = $trans;
            }
        }
        
        return $active;
    }
    
    // Find transaction by ID
    public function findTransactionById(string $id): ?Transaction {
        $transactions = $this->getAllTransactions();
        
        foreach ($transactions as $trans) {
            if ($trans->getId() === $id) {
                return $trans;
            }
        }
        
        return null;
    }
    
    // Get transaction with details (book and user info)
    public function getTransactionWithDetails(Transaction $trans): array {
        $book = $this->bookController->findBookById($trans->getBookId());
        $user = $this->userController->findUserById($trans->getUserId());
        
        return [
            'transaction' => $trans,
            'book' => $book,
            'user' => $user
        ];
    }
    
    // Save transaction
    private function saveTransaction(Transaction $transaction): void {
        $data = json_decode(file_get_contents($this->storageFile), true);
        $data[] = $transaction->toArray();
        file_put_contents($this->storageFile, json_encode($data, JSON_PRETTY_PRINT));
    }
    
    // Update transaction
    private function updateTransaction(Transaction $transaction): void {
        $data = json_decode(file_get_contents($this->storageFile), true);
        
        foreach ($data as $key => $transData) {
            if ($transData['id'] === $transaction->getId()) {
                $data[$key] = $transaction->toArray();
                break;
            }
        }
        
        file_put_contents($this->storageFile, json_encode($data, JSON_PRETTY_PRINT));
    }
}
