<?php
namespace App\Models;

/**
 * EBook class - demonstrates Encapsulation
 */
class EBook {
    private string $id;
    private string $title;
    private string $author;
    private string $isbn;
    private string $category;
    private string $description;
    private string $coverUrl;
    private int $totalCopies;
    private int $availableCopies;
    private float $rentalPrice;
    private string $createdAt;
    
    public function __construct(
        string $title,
        string $author,
        string $isbn,
        string $category,
        string $description,
        string $coverUrl,
        int $totalCopies,
        float $rentalPrice
    ) {
        $this->id = $this->generateId();
        $this->title = $title;
        $this->author = $author;
        $this->isbn = $isbn;
        $this->category = $category;
        $this->description = $description;
        $this->coverUrl = $coverUrl;
        $this->totalCopies = $totalCopies;
        $this->availableCopies = $totalCopies;
        $this->rentalPrice = $rentalPrice;
        $this->createdAt = date('Y-m-d H:i:s');
    }
    
    // Getters
    public function getId(): string {
        return $this->id;
    }
    
    public function getTitle(): string {
        return $this->title;
    }
    
    public function getAuthor(): string {
        return $this->author;
    }
    
    public function getIsbn(): string {
        return $this->isbn;
    }
    
    public function getCategory(): string {
        return $this->category;
    }
    
    public function getDescription(): string {
        return $this->description;
    }
    
    public function getCoverUrl(): string {
        return $this->coverUrl;
    }
    
    public function getTotalCopies(): int {
        return $this->totalCopies;
    }
    
    public function getAvailableCopies(): int {
        return $this->availableCopies;
    }
    
    public function getRentalPrice(): float {
        return $this->rentalPrice;
    }
    
    public function getCreatedAt(): string {
        return $this->createdAt;
    }
    
    // Setters
    public function setTitle(string $title): void {
        $this->title = $title;
    }
    
    public function setAuthor(string $author): void {
        $this->author = $author;
    }
    
    public function setDescription(string $description): void {
        $this->description = $description;
    }
    
    public function setCoverUrl(string $coverUrl): void {
        $this->coverUrl = $coverUrl;
    }
    
    public function setRentalPrice(float $price): void {
        $this->rentalPrice = $price;
    }
    
    // Business logic
    public function isAvailable(): bool {
        return $this->availableCopies > 0;
    }
    
    public function borrow(): bool {
        if ($this->availableCopies > 0) {
            $this->availableCopies--;
            return true;
        }
        return false;
    }
    
    public function returnBook(): void {
        if ($this->availableCopies < $this->totalCopies) {
            $this->availableCopies++;
        }
    }
    
    public function toArray(): array {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'author' => $this->author,
            'isbn' => $this->isbn,
            'category' => $this->category,
            'description' => $this->description,
            'coverUrl' => $this->coverUrl,
            'totalCopies' => $this->totalCopies,
            'availableCopies' => $this->availableCopies,
            'rentalPrice' => $this->rentalPrice,
            'createdAt' => $this->createdAt
        ];
    }
    
    private function generateId(): string {
        return uniqid('book_', true);
    }
    
    public static function fromArray(array $data): EBook {
        $book = new self(
            $data['title'],
            $data['author'],
            $data['isbn'],
            $data['category'],
            $data['description'],
            $data['coverUrl'],
            $data['totalCopies'],
            $data['rentalPrice']
        );
        
        $book->id = $data['id'];
        $book->availableCopies = $data['availableCopies'];
        $book->createdAt = $data['createdAt'];
        
        return $book;
    }
}
