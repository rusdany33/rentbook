# LAPORAN IMPLEMENTASI
## UTS Pemrograman Web - Sistem E-Book Library

### Informasi
- **Mata Kuliah**: Pemrograman Web
- **Semester**: Ganjil 2026/2027
- **Sistem**: Perpustakaan E-Book
- **Teknologi**: PHP 8.x OOP + HTML/CSS/JS

---

## 1. IMPLEMENTASI PHP OOP

### A. Encapsulation ✅
**Lokasi**: `src/Models/User.php`, `EBook.php`, `Transaction.php`

**Implementasi**:
```php
class User {
    protected string $id;           // Protected property
    protected string $email;        // Protected property
    protected string $password;     // Protected property
    private string $name;           // Private property
    
    // Getter (Encapsulation)
    public function getId(): string {
        return $this->id;
    }
    
    // Setter (Encapsulation)
    public function setName(string $name): void {
        $this->name = $name;
    }
}
```

**Penjelasan**:
- Properties dibuat `private`/`protected`
- Akses hanya melalui getter/setter methods
- Password di-hash, tidak bisa diakses langsung

---

### B. Inheritance ✅
**Lokasi**: `src/Models/Admin.php`, `Member.php`

**Implementasi**:
```php
// Base class
abstract class User {
    protected string $email;
    protected string $name;
    
    abstract public function getPermissions(): array;
}

// Child class 1
class Admin extends User {
    public function __construct(string $email, string $password, string $name) {
        parent::__construct($email, $password, $name, 'admin');
    }
    
    public function canManageBooks(): bool {
        return true;  // Admin-specific method
    }
}

// Child class 2
class Member extends User {
    private int $borrowLimit = 3;  // Member-specific property
    
    public function canBorrow(): bool {
        return count($this->borrowedBooks) < $this->borrowLimit;
    }
}
```

**Penjelasan**:
- `Admin` dan `Member` inherit dari `User`
- Child class mendapat properties & methods dari parent
- Child class bisa punya methods sendiri

---

### C. Polymorphism ✅
**Lokasi**: `src/Models/User.php` (abstract method)

**Implementasi**:
```php
abstract class User {
    // Abstract method - wajib di-implement child class
    abstract public function getPermissions(): array;
}

// Admin implementation
class Admin extends User {
    public function getPermissions(): array {
        return [
            'borrow_book',
            'add_book',
            'edit_book',
            'delete_book',
            'manage_users'
        ];
    }
}

// Member implementation
class Member extends User {
    public function getPermissions(): array {
        return [
            'borrow_book',
            'view_catalog'
        ];
    }
}
```

**Penjelasan**:
- Method `getPermissions()` dipanggil di kedua class
- Behavior berbeda tergantung object type
- Admin punya lebih banyak permissions

---

### D. Namespace ✅
**Lokasi**: Semua file di `src/`

**Implementasi**:
```php
// Models
namespace App\Models;

class User { ... }
class EBook { ... }

// Controllers
namespace App\Controllers;

use App\Models\User;
use App\Models\EBook;

class UserController {
    public function login() {
        $user = User::fromArray($data);
    }
}
```

**Penjelasan**:
- Namespace `App\Models` untuk models
- Namespace `App\Controllers` untuk controllers
- `use` statement untuk import classes

---

## 2. FRONT-END IMPLEMENTATION

### A. HTML Semantik ✅
```html
<nav class="navbar">...</nav>
<section class="container">
    <article class="book-card">...</article>
</section>
<form id="login-form">
    <input type="email" required>
</form>
```

### B. CSS Responsive ✅
```css
/* Mobile-first */
.book-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
}

@media (max-width: 768px) {
    .book-grid {
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    }
}
```

### C. JavaScript Validation ✅
```javascript
// Real-time validation
const Validation = {
    validateEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    },
    
    validatePassword(password) {
        return password.length >= 6;
    }
};

// Form submit dengan validation
async function handleLogin(e) {
    e.preventDefault();
    
    if (!Validation.validateEmail(email)) {
        showError('email', 'Format email tidak valid');
        return;
    }
    
    const response = await fetch('index.php', { ... });
}
```

---

## 3. STRUKTUR FILE & KERAPIAN

### Organisasi Folder ✅
```
src/
├── Models/          # OOP Classes
├── Controllers/     # Business Logic
└── Storage/         # Data JSON

public/
├── index.php        # API Router
├── *.html          # Pages
├── css/            # Styles
└── js/             # Scripts
```

### Naming Convention ✅
- Classes: `PascalCase` (UserController, EBook)
- Methods: `camelCase` (getPermissions, borrowBook)
- Files: `PascalCase.php` (User.php, Transaction.php)
- Variables: `camelCase` ($userId, $bookController)

---

## 4. FITUR SISTEM

### User Management
- ✅ Register dengan validation
- ✅ Login dengan session
- ✅ Password hashing (bcrypt)
- ✅ Role-based access (Admin/Member)

### Book Catalog
- ✅ CRUD operations (Admin only)
- ✅ Search & filter kategori
- ✅ Real-time availability
- ✅ Cover images

### Transaction System
- ✅ Borrow book (max 3 per member)
- ✅ Return book
- ✅ Due date tracking (14 hari)
- ✅ Automatic fine calculation (Rp 1.000/hari)
- ✅ Status: active, overdue, returned

---

## 5. TESTING RESULTS

### Backend API ✅
```bash
✅ GET  /index.php?action=check_auth      → {"authenticated":false}
✅ GET  /index.php?action=get_books       → 5 books loaded
✅ GET  /index.php?action=get_categories  → 3 categories
✅ POST /index.php (register)             → {"success":true}
✅ POST /index.php (login)                → {"success":true}
```

### Frontend ✅
- ✅ Login page: validation works
- ✅ Register page: real-time validation
- ✅ Dashboard: book grid responsive
- ✅ Modal: detail buku tampil
- ✅ Transactions: history display

### Cross-browser ✅
- ✅ Chrome
- ✅ Firefox
- ✅ Edge

---

## 6. PEMENUHAN RUBRIK

| Komponen | Indikator | Status | Poin |
|----------|-----------|--------|------|
| **Front-End** | Form validation | ✅ | 10 |
| | Semantic HTML | ✅ | 10 |
| | Responsive layout | ✅ | 5 |
| | JavaScript interaktif | ✅ | 5 |
| **PHP OOP** | Class & Object | ✅ | 10 |
| | Encapsulation | ✅ | 10 |
| | Inheritance | ✅ | 10 |
| | Polymorphism | ✅ | 5 |
| | Namespace | ✅ | 5 |
| **Kerapian** | Struktur folder | ✅ | 10 |
| | Modularitas | ✅ | 10 |
| | Eksekusi tanpa error | ✅ | 10 |
| **TOTAL** | | | **100** |

---

## 7. KREDENSIAL DEFAULT

### Admin
- Email: `admin@ebook.com`
- Password: `admin123`

### Member (auto-created saat register)
- Bebas registrasi sendiri

---

## 8. URL AKSES

- **Login**: http://localhost:8080/login.html
- **Register**: http://localhost:8080/register.html
- **Dashboard**: http://localhost:8080/dashboard.html (setelah login)
- **Transaksi**: http://localhost:8080/transactions.html (setelah login)
- **API**: http://localhost:8080/index.php?action=...

---

## 9. KESIMPULAN

Sistem berhasil diimplementasikan dengan:
✅ PHP OOP Modern (Encapsulation, Inheritance, Polymorphism, Namespace)  
✅ Frontend interaktif (HTML5, CSS3, Vanilla JS)  
✅ Form validation (client & server)  
✅ Responsive design  
✅ Clean architecture (MVC-like)  
✅ Tanpa error eksekusi  

**Status**: READY FOR SUBMISSION ✅
