<?php
namespace App\Controllers;

use App\Models\EBook;

/**
 * EBookController - handles ebook catalog management
 */
class EBookController {
    private string $storageFile;
    
    public function __construct() {
        $this->storageFile = STORAGE_PATH . '/ebooks.json';
        $this->createSampleBooks();
    }
    
    // Create sample books if storage is empty
    private function createSampleBooks(): void {
        $books = $this->getAllBooks();
        if (empty($books)) {
            $sampleBooks = [
                new EBook(
                    'Clean Code',
                    'Robert C. Martin',
                    '978-0132350884',
                    'Programming',
                    'A handbook of agile software craftsmanship',
                    'https://via.placeholder.com/200x300/4A90E2/fff?text=Clean+Code',
                    5,
                    15000
                ),
                new EBook(
                    'Design Patterns',
                    'Gang of Four',
                    '978-0201633610',
                    'Programming',
                    'Elements of reusable object-oriented software',
                    'https://via.placeholder.com/200x300/7B68EE/fff?text=Design+Patterns',
                    3,
                    20000
                ),
                new EBook(
                    'The Pragmatic Programmer',
                    'Andrew Hunt',
                    '978-0135957059',
                    'Programming',
                    'Your journey to mastery',
                    'https://via.placeholder.com/200x300/FF6B6B/fff?text=Pragmatic',
                    4,
                    18000
                ),
                new EBook(
                    'Introduction to Algorithms',
                    'Thomas H. Cormen',
                    '978-0262033848',
                    'Computer Science',
                    'Comprehensive introduction to algorithms',
                    'https://via.placeholder.com/200x300/4ECDC4/fff?text=Algorithms',
                    2,
                    25000
                ),
                new EBook(
                    'You Don\'t Know JS',
                    'Kyle Simpson',
                    '978-1491904244',
                    'Web Development',
                    'Deep dive into JavaScript mechanics',
                    'https://via.placeholder.com/200x300/FFD93D/333?text=YDKJS',
                    6,
                    12000
                )
            ];
            
            foreach ($sampleBooks as $book) {
                $this->saveBook($book);
            }
        }
    }
    
    // Get all books
    public function getAllBooks(): array {
        $data = json_decode(file_get_contents($this->storageFile), true);
        $books = [];
        
        foreach ($data as $bookData) {
            $books[] = EBook::fromArray($bookData);
        }
        
        return $books;
    }
    
    // Find book by ID
    public function findBookById(string $id): ?EBook {
        $books = $this->getAllBooks();
        
        foreach ($books as $book) {
            if ($book->getId() === $id) {
                return $book;
            }
        }
        
        return null;
    }
    
    // Search books
    public function searchBooks(string $keyword, ?string $category = null): array {
        $books = $this->getAllBooks();
        $results = [];
        
        foreach ($books as $book) {
            $matchKeyword = empty($keyword) || 
                stripos($book->getTitle(), $keyword) !== false ||
                stripos($book->getAuthor(), $keyword) !== false ||
                stripos($book->getIsbn(), $keyword) !== false;
            
            $matchCategory = is_null($category) || $book->getCategory() === $category;
            
            if ($matchKeyword && $matchCategory) {
                $results[] = $book;
            }
        }
        
        return $results;
    }
    
    // Get unique categories
    public function getCategories(): array {
        $books = $this->getAllBooks();
        $categories = [];
        
        foreach ($books as $book) {
            $category = $book->getCategory();
            if (!in_array($category, $categories)) {
                $categories[] = $category;
            }
        }
        
        sort($categories);
        return $categories;
    }
    
    // Add new book (admin only)
    public function addBook(array $data): array {
        try {
            $book = new EBook(
                $data['title'],
                $data['author'],
                $data['isbn'],
                $data['category'],
                $data['description'],
                $data['coverUrl'] ?? 'https://via.placeholder.com/200x300/ccc/333?text=No+Cover',
                (int)$data['totalCopies'],
                (float)$data['rentalPrice']
            );
            
            $this->saveBook($book);
            return ['success' => true, 'message' => 'Buku berhasil ditambahkan'];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Gagal menambahkan buku: ' . $e->getMessage()];
        }
    }
    
    // Update book (admin only)
    public function updateBook(string $id, array $data): array {
        $book = $this->findBookById($id);
        
        if (!$book) {
            return ['success' => false, 'message' => 'Buku tidak ditemukan'];
        }
        
        // Update fields
        if (isset($data['title'])) $book->setTitle($data['title']);
        if (isset($data['author'])) $book->setAuthor($data['author']);
        if (isset($data['description'])) $book->setDescription($data['description']);
        if (isset($data['coverUrl'])) $book->setCoverUrl($data['coverUrl']);
        if (isset($data['rentalPrice'])) $book->setRentalPrice((float)$data['rentalPrice']);
        
        $this->updateBookInStorage($book);
        return ['success' => true, 'message' => 'Buku berhasil diupdate'];
    }
    
    // Delete book (admin only)
    public function deleteBook(string $id): array {
        $data = json_decode(file_get_contents($this->storageFile), true);
        $found = false;
        
        foreach ($data as $key => $bookData) {
            if ($bookData['id'] === $id) {
                unset($data[$key]);
                $found = true;
                break;
            }
        }
        
        if ($found) {
            file_put_contents($this->storageFile, json_encode(array_values($data), JSON_PRETTY_PRINT));
            return ['success' => true, 'message' => 'Buku berhasil dihapus'];
        }
        
        return ['success' => false, 'message' => 'Buku tidak ditemukan'];
    }
    
    // Save book
    private function saveBook(EBook $book): void {
        $data = json_decode(file_get_contents($this->storageFile), true);
        $data[] = $book->toArray();
        file_put_contents($this->storageFile, json_encode($data, JSON_PRETTY_PRINT));
    }
    
    // Update book in storage
    public function updateBookInStorage(EBook $book): void {
        $data = json_decode(file_get_contents($this->storageFile), true);
        
        foreach ($data as $key => $bookData) {
            if ($bookData['id'] === $book->getId()) {
                $data[$key] = $book->toArray();
                break;
            }
        }
        
        file_put_contents($this->storageFile, json_encode($data, JSON_PRETTY_PRINT));
    }
}
