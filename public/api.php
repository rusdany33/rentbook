<?php
require_once __DIR__ . '/../config.php';

use App\Controllers\UserController;
use App\Controllers\EBookController;
use App\Controllers\TransactionController;

// Set JSON response header
header('Content-Type: application/json');

// Initialize controllers
$userController = new UserController();
$bookController = new EBookController();
$transactionController = new TransactionController();

// Create default admin
$userController->createDefaultAdmin();

// Get action from request
$action = $_REQUEST['action'] ?? 'default';

// Route handlers
switch ($action) {
    // Authentication
    case 'login':
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $result = $userController->login($email, $password);
        echo json_encode($result);
        break;
    
    case 'register':
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $name = $_POST['name'] ?? '';
        $result = $userController->register($email, $password, $name);
        echo json_encode($result);
        break;
    
    case 'logout':
        $userController->logout();
        echo json_encode(['success' => true]);
        break;
    
    case 'check_auth':
        echo json_encode(['authenticated' => isLoggedIn()]);
        break;
    
    case 'get_user':
        if (!isLoggedIn()) {
            echo json_encode(['success' => false, 'message' => 'Not authenticated']);
            exit;
        }
        
        $user = getCurrentUser();
        echo json_encode(['success' => true, 'user' => $user]);
        break;
    
    // Books
    case 'get_books':
        $books = $bookController->getAllBooks();
        $booksArray = array_map(fn($book) => $book->toArray(), $books);
        echo json_encode(['success' => true, 'books' => $booksArray]);
        break;
    
    case 'search_books':
        $keyword = $_GET['keyword'] ?? '';
        $category = $_GET['category'] ?? null;
        $books = $bookController->searchBooks($keyword, $category);
        $booksArray = array_map(fn($book) => $book->toArray(), $books);
        echo json_encode(['success' => true, 'books' => $booksArray]);
        break;
    
    case 'get_categories':
        $categories = $bookController->getCategories();
        echo json_encode(['success' => true, 'categories' => $categories]);
        break;
    
    case 'add_book':
        if (!isAdmin()) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }
        
        $result = $bookController->addBook($_POST);
        echo json_encode($result);
        break;
    
    case 'update_book':
        if (!isAdmin()) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }
        
        $bookId = $_POST['book_id'] ?? '';
        $result = $bookController->updateBook($bookId, $_POST);
        echo json_encode($result);
        break;
    
    case 'delete_book':
        if (!isAdmin()) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }
        
        $bookId = $_POST['book_id'] ?? '';
        $result = $bookController->deleteBook($bookId);
        echo json_encode($result);
        break;
    
    // Transactions
    case 'borrow_book':
        if (!isLoggedIn()) {
            echo json_encode(['success' => false, 'message' => 'Not authenticated']);
            exit;
        }
        
        $userId = $_SESSION['user_id'];
        $bookId = $_POST['book_id'] ?? '';
        $result = $transactionController->borrowBook($userId, $bookId);
        echo json_encode($result);
        break;
    
    case 'return_book':
        if (!isLoggedIn()) {
            echo json_encode(['success' => false, 'message' => 'Not authenticated']);
            exit;
        }
        
        $transactionId = $_POST['transaction_id'] ?? '';
        $result = $transactionController->returnBook($transactionId);
        echo json_encode($result);
        break;
    
    case 'get_transactions':
        if (!isLoggedIn()) {
            echo json_encode(['success' => false, 'message' => 'Not authenticated']);
            exit;
        }
        
        if (isAdmin()) {
            // Admin sees all transactions
            $transactions = $transactionController->getAllTransactions();
        } else {
            // Member sees only their transactions
            $transactions = $transactionController->getUserTransactions($_SESSION['user_id']);
        }
        
        // Get details for each transaction
        $transactionsWithDetails = [];
        foreach ($transactions as $trans) {
            $details = $transactionController->getTransactionWithDetails($trans);
            $transactionsWithDetails[] = [
                'id' => $trans->getId(),
                'userId' => $trans->getUserId(),
                'bookId' => $trans->getBookId(),
                'type' => $trans->getType(),
                'borrowDate' => $trans->getBorrowDate(),
                'dueDate' => $trans->getDueDate(),
                'returnDate' => $trans->getReturnDate(),
                'status' => $trans->getStatus(),
                'amount' => $trans->getAmount(),
                'fine' => $trans->getFine(),
                'book' => $details['book'] ? $details['book']->toArray() : null,
                'user' => $details['user'] ? [
                    'id' => $details['user']->getId(),
                    'name' => $details['user']->getName(),
                    'email' => $details['user']->getEmail(),
                    'role' => $details['user']->getRole()
                ] : null
            ];
        }
        
        echo json_encode(['success' => true, 'transactions' => $transactionsWithDetails]);
        break;
    
    // Default
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}
