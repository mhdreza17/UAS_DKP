
<?php include 'auth.php'; ?>
<?php include '_header.php'; ?>
<h2>Wiki Search</h2>
<form method="get"><input name="q"><button>Search</button></form>
<?php
if (isset($_GET['q'])) {
    $q = trim($_GET['q']);
    // SQL Injection fix: prepared statement
    $stmt = $GLOBALS['PDO']->prepare("SELECT * FROM articles WHERE title LIKE ?");
    // Validasi input agar tidak ada karakter berbahaya
    if (!preg_match('/^[a-zA-Z0-9 \-]*$/', $q)) {
        die('Invalid search query.'); // Mengatasi path traversal
    }
    $stmt->execute(['%' . $q . '%']);
    foreach ($stmt as $row) {
        // Escape output untuk mencegah XSS
        echo "<li>" . htmlspecialchars($row['title']) . ": " . htmlspecialchars($row['body']) . "</li>";
    }
}
?>
<?php include '_footer.php'; ?>
// Perbaikan: SQL Injection (prepared statement), XSS (htmlspecialchars).
