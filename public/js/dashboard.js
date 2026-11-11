// Dashboard - Catalog Page
let currentUser = null;
let allBooks = [];
let currentBookId = null;

document.addEventListener('DOMContentLoaded', function() {
    checkAuth();
    loadUserInfo();
    loadCategories();
    loadBooks();
    
    // Book form submit
    const bookForm = document.getElementById('book-form');
    if (bookForm) {
        bookForm.addEventListener('submit', handleBookFormSubmit);
    }
    
    // Search on enter key
    document.getElementById('search-input').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            searchBooks();
        }
    });
});

// Check authentication
function checkAuth() {
    fetch('api.php?action=check_auth')
        .then(res => res.json())
        .then(data => {
            if (!data.authenticated) {
                window.location.href = 'login.html';
            }
        })
        .catch(() => {
            window.location.href = 'login.html';
        });
}

// Load user info
async function loadUserInfo() {
    const response = await fetch('api.php?action=get_user');
    const data = await response.json();
    
    if (data.success) {
        currentUser = data.user;
        document.getElementById('user-name').textContent = data.user.name;
        
        const roleBadge = document.getElementById('user-role');
        roleBadge.textContent = data.user.role;
        roleBadge.classList.add(data.user.role === 'admin' ? 'badge-admin' : 'badge-member');
        
        // Show admin button
        if (data.user.role === 'admin') {
            document.getElementById('admin-add-btn').style.display = 'block';
        }
    }
}

// Load categories
async function loadCategories() {
    const response = await fetch('api.php?action=get_categories');
    const data = await response.json();
    
    if (data.success) {
        const select = document.getElementById('category-filter');
        data.categories.forEach(category => {
            const option = document.createElement('option');
            option.value = category;
            option.textContent = category;
            select.appendChild(option);
        });
    }
}

// Load books
async function loadBooks() {
    const response = await fetch('api.php?action=get_books');
    const data = await response.json();
    
    if (data.success) {
        allBooks = data.books;
        displayBooks(allBooks);
    }
}

// Search books
async function searchBooks() {
    const keyword = document.getElementById('search-input').value.trim();
    const category = document.getElementById('category-filter').value;
    
    const params = new URLSearchParams();
    params.append('action', 'search_books');
    if (keyword) params.append('keyword', keyword);
    if (category) params.append('category', category);
    
    const response = await fetch('api.php?' + params.toString());
    const data = await response.json();
    
    if (data.success) {
        displayBooks(data.books);
    }
}

// Display books
function displayBooks(books) {
    const grid = document.getElementById('book-grid');
    
    if (books.length === 0) {
        grid.innerHTML = '<div class="empty-state"><div class="empty-state-icon">📚</div><p>Tidak ada buku ditemukan</p></div>';
        return;
    }
    
    grid.innerHTML = books.map(book => `
        <div class="book-card" onclick="showBookDetail('${book.id}')">
            <img src="${book.coverUrl}" alt="${book.title}" class="book-cover" onerror="this.src='https://via.placeholder.com/200x300/ccc/333?text=No+Cover'">
            <div class="book-info">
                <h3 class="book-title">${book.title}</h3>
                <p class="book-author">oleh ${book.author}</p>
                <div class="book-meta">
                    <span class="book-price">${formatCurrency(book.rentalPrice)}</span>
                    <span class="book-availability ${book.availableCopies > 0 ? 'available' : 'unavailable'}">
                        ${book.availableCopies > 0 ? `${book.availableCopies} tersedia` : 'Tidak tersedia'}
                    </span>
                </div>
                ${currentUser && currentUser.role === 'admin' ? `
                <div class="book-actions">
                    <button class="btn btn-sm btn-secondary" onclick="event.stopPropagation(); editBook('${book.id}')">Edit</button>
                    <button class="btn btn-sm btn-danger" onclick="event.stopPropagation(); deleteBook('${book.id}')">Hapus</button>
                </div>
                ` : ''}
            </div>
        </div>
    `).join('');
}

// Show book detail
function showBookDetail(bookId) {
    const book = allBooks.find(b => b.id === bookId);
    if (!book) return;
    
    currentBookId = bookId;
    
    const modal = document.getElementById('borrow-modal');
    const details = document.getElementById('modal-book-details');
    
    details.innerHTML = `
        <div class="modal-book-detail">
            <img src="${book.coverUrl}" alt="${book.title}" class="modal-book-cover" onerror="this.src='https://via.placeholder.com/200x300/ccc/333?text=No+Cover'">
            <div class="modal-book-info">
                <h3>${book.title}</h3>
                <p><strong>Penulis:</strong> ${book.author}</p>
                <p><strong>ISBN:</strong> ${book.isbn}</p>
                <p><strong>Kategori:</strong> ${book.category}</p>
                <p><strong>Harga Sewa:</strong> ${formatCurrency(book.rentalPrice)}</p>
                <p><strong>Ketersediaan:</strong> ${book.availableCopies} dari ${book.totalCopies} salinan</p>
                <p><strong>Deskripsi:</strong><br>${book.description}</p>
            </div>
        </div>
    `;
    
    const borrowBtn = document.getElementById('borrow-confirm-btn');
    
    if (currentUser && currentUser.role === 'member') {
        if (book.availableCopies > 0) {
            borrowBtn.style.display = 'inline-block';
            borrowBtn.onclick = () => borrowBook(bookId);
        } else {
            borrowBtn.style.display = 'none';
        }
    } else {
        borrowBtn.style.display = 'none';
    }
    
    modal.classList.add('active');
}

// Close borrow modal
function closeBorrowModal() {
    document.getElementById('borrow-modal').classList.remove('active');
}

// Borrow book
async function borrowBook(bookId) {
    const formData = new FormData();
    formData.append('action', 'borrow_book');
    formData.append('book_id', bookId);
    
    const response = await fetch('api.php', {
        method: 'POST',
        body: formData
    });
    
    const result = await response.json();
    
    if (result.success) {
        alert(result.message);
        closeBorrowModal();
        loadBooks(); // Reload to update availability
    } else {
        alert(result.message);
    }
}

// Show add book modal (admin)
function showAddBookModal() {
    document.getElementById('book-form-title').textContent = 'Tambah Buku';
    document.getElementById('book-form').reset();
    document.getElementById('book-id').value = '';
    document.getElementById('book-form-modal').classList.add('active');
}

// Edit book (admin)
function editBook(bookId) {
    const book = allBooks.find(b => b.id === bookId);
    if (!book) return;
    
    document.getElementById('book-form-title').textContent = 'Edit Buku';
    document.getElementById('book-id').value = book.id;
    document.getElementById('book-title').value = book.title;
    document.getElementById('book-author').value = book.author;
    document.getElementById('book-isbn').value = book.isbn;
    document.getElementById('book-category').value = book.category;
    document.getElementById('book-copies').value = book.totalCopies;
    document.getElementById('book-price').value = book.rentalPrice;
    document.getElementById('book-cover').value = book.coverUrl;
    document.getElementById('book-description').value = book.description;
    
    document.getElementById('book-form-modal').classList.add('active');
}

// Delete book (admin)
async function deleteBook(bookId) {
    if (!confirm('Yakin ingin menghapus buku ini?')) return;
    
    const formData = new FormData();
    formData.append('action', 'delete_book');
    formData.append('book_id', bookId);
    
    const response = await fetch('api.php', {
        method: 'POST',
        body: formData
    });
    
    const result = await response.json();
    
    if (result.success) {
        alert(result.message);
        loadBooks();
    } else {
        alert(result.message);
    }
}

// Close book form modal
function closeBookFormModal() {
    document.getElementById('book-form-modal').classList.remove('active');
}

// Handle book form submit
async function handleBookFormSubmit(e) {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    const bookId = document.getElementById('book-id').value;
    
    if (bookId) {
        formData.append('action', 'update_book');
        formData.append('book_id', bookId);
    } else {
        formData.append('action', 'add_book');
    }
    
    const response = await fetch('api.php', {
        method: 'POST',
        body: formData
    });
    
    const result = await response.json();
    
    if (result.success) {
        alert(result.message);
        closeBookFormModal();
        loadBooks();
        loadCategories(); // Reload in case new category added
    } else {
        alert(result.message);
    }
}

// Close modal on outside click
window.addEventListener('click', function(e) {
    const borrowModal = document.getElementById('borrow-modal');
    const bookFormModal = document.getElementById('book-form-modal');
    
    if (e.target === borrowModal) {
        closeBorrowModal();
    }
    if (e.target === bookFormModal) {
        closeBookFormModal();
    }
});
