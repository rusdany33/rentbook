// Transactions Page
let currentUser = null;
let allTransactions = [];

document.addEventListener('DOMContentLoaded', function() {
    checkAuth();
    loadUserInfo();
    loadTransactions();
});

// Check authentication
function checkAuth() {
    fetch('index.php?action=check_auth')
        .then(res => res.json())
        .then(data => {
            if (!data.authenticated) {
                window.location.href = 'login.html';
            }
        });
}

// Load user info
async function loadUserInfo() {
    const response = await fetch('index.php?action=get_user');
    const data = await response.json();
    
    if (data.success) {
        currentUser = data.user;
        document.getElementById('user-name').textContent = data.user.name;
        
        const roleBadge = document.getElementById('user-role');
        roleBadge.textContent = data.user.role;
        roleBadge.classList.add(data.user.role === 'admin' ? 'badge-admin' : 'badge-member');
        
        // Show all transactions tab for admin
        if (data.user.role === 'admin') {
            document.getElementById('all-tab').style.display = 'block';
        }
    }
}

// Load transactions
async function loadTransactions() {
    const response = await fetch('index.php?action=get_transactions');
    const data = await response.json();
    
    if (data.success) {
        allTransactions = data.transactions;
        displayActiveTransactions();
        displayHistoryTransactions();
        
        if (currentUser && currentUser.role === 'admin') {
            displayAllTransactions();
        }
    }
}

// Display active transactions
function displayActiveTransactions() {
    const container = document.getElementById('active-transactions');
    const active = allTransactions.filter(t => t.status === 'active' || t.status === 'overdue');
    
    if (active.length === 0) {
        container.innerHTML = '<div class="empty-state"><div class="empty-state-icon">📖</div><p>Tidak ada buku yang sedang dipinjam</p></div>';
        return;
    }
    
    container.innerHTML = active.map(t => createTransactionCard(t, true)).join('');
}

// Display history transactions
function displayHistoryTransactions() {
    const container = document.getElementById('history-transactions');
    const history = allTransactions.filter(t => t.status === 'returned');
    
    if (history.length === 0) {
        container.innerHTML = '<div class="empty-state"><div class="empty-state-icon">📚</div><p>Belum ada riwayat transaksi</p></div>';
        return;
    }
    
    container.innerHTML = history.map(t => createTransactionCard(t, false)).join('');
}

// Display all transactions (admin only)
function displayAllTransactions() {
    const container = document.getElementById('all-transactions');
    
    if (allTransactions.length === 0) {
        container.innerHTML = '<div class="empty-state"><div class="empty-state-icon">📋</div><p>Belum ada transaksi</p></div>';
        return;
    }
    
    container.innerHTML = allTransactions.map(t => createTransactionCard(t, t.status !== 'returned', true)).join('');
}

// Create transaction card HTML
function createTransactionCard(transaction, showReturn, showUser = false) {
    const statusClass = transaction.status === 'active' ? 'badge-active' : 
                       transaction.status === 'overdue' ? 'badge-overdue' : 'badge-returned';
    const statusText = transaction.status === 'active' ? 'Aktif' :
                      transaction.status === 'overdue' ? 'Terlambat' : 'Dikembalikan';
    
    return `
        <div class="transaction-card">
            <img src="${transaction.book.coverUrl}" alt="${transaction.book.title}" class="transaction-book-cover" onerror="this.src='https://via.placeholder.com/80x120/ccc/333?text=No+Cover'">
            <div class="transaction-info">
                <div class="transaction-header">
                    <div>
                        <h3 class="transaction-title">${transaction.book.title}</h3>
                        <p class="transaction-meta">oleh ${transaction.book.author}</p>
                        ${showUser ? `<p class="transaction-meta">Peminjam: ${transaction.user.name} (${transaction.user.email})</p>` : ''}
                    </div>
                    <span class="badge ${statusClass}">${statusText}</span>
                </div>
                
                <div class="transaction-dates">
                    <div class="transaction-date">
                        <strong>Tanggal Pinjam</strong>
                        ${formatShortDate(transaction.borrowDate)}
                    </div>
                    ${transaction.dueDate ? `
                    <div class="transaction-date">
                        <strong>Jatuh Tempo</strong>
                        ${formatShortDate(transaction.dueDate)}
                    </div>
                    ` : ''}
                    ${transaction.returnDate ? `
                    <div class="transaction-date">
                        <strong>Tanggal Kembali</strong>
                        ${formatShortDate(transaction.returnDate)}
                    </div>
                    ` : ''}
                    <div class="transaction-date">
                        <strong>Biaya Sewa</strong>
                        ${formatCurrency(transaction.amount)}
                    </div>
                    ${transaction.fine > 0 ? `
                    <div class="transaction-date">
                        <strong>Denda</strong>
                        <span style="color: var(--danger)">${formatCurrency(transaction.fine)}</span>
                    </div>
                    ` : ''}
                </div>
                
                ${showReturn ? `
                <div class="transaction-actions">
                    <button class="btn btn-primary btn-sm" onclick="returnBook('${transaction.id}')">Kembalikan Buku</button>
                </div>
                ` : ''}
            </div>
        </div>
    `;
}

// Return book
async function returnBook(transactionId) {
    if (!confirm('Konfirmasi pengembalian buku?')) return;
    
    const formData = new FormData();
    formData.append('action', 'return_book');
    formData.append('transaction_id', transactionId);
    
    const response = await fetch('index.php', {
        method: 'POST',
        body: formData
    });
    
    const result = await response.json();
    
    if (result.success) {
        alert(result.message);
        loadTransactions();
    } else {
        alert(result.message);
    }
}

// Tab switching
function showTab(tabName) {
    // Update tab buttons
    document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
    event.target.classList.add('active');
    
    // Update tab content
    document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
    
    if (tabName === 'active') {
        document.getElementById('active-tab').classList.add('active');
    } else if (tabName === 'history') {
        document.getElementById('history-tab').classList.add('active');
    } else if (tabName === 'all') {
        document.getElementById('all-tab-content').classList.add('active');
    }
}
