
<?php include 'auth.php'; ?>
<?php include '_header.php'; ?>
<h2>Post comments</h2>
<?php
// CSRF token untuk mencegah CSRF
if (empty($_SESSION['csrf_token'])) {
  $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<form method="post">
  <input name="author" placeholder="Name..." required>
  <textarea name="content" placeholder="Comments..." required></textarea>
  <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
  <button>Post</button>
</form>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Validasi CSRF
  if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die('CSRF validation failed'); // Mengatasi CSRF
  }
  // Validasi input sederhana
  $author = trim($_POST['author']);
  $content = trim($_POST['content']);
  // SQL Injection fix: prepared statement
  $stmt = $GLOBALS['PDO']->prepare("INSERT INTO comments(author,content,created_at) VALUES(?,?,datetime('now'))");
  $stmt->execute([$author, $content]);
}
?>
<h3>Comment lists : </h3>
<?php
// Escape output untuk mencegah XSS
foreach ($GLOBALS['PDO']->query("SELECT * FROM comments ORDER BY id DESC") as $row) {
  echo "<p><b>" . htmlspecialchars($row['author']) . "</b>: " . htmlspecialchars($row['content']) . "</p>";
}
?>
<?php include '_footer.php'; ?>
// Perbaikan: SQL Injection (prepared statement), XSS (htmlspecialchars), CSRF (token), validasi input.
