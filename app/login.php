
<?php
// Proteksi session hijacking dan cookie
session_set_cookie_params([
  'secure' => true,
  'httponly' => true,
  'samesite' => 'Strict'
]);
session_start();
require_once __DIR__ . '/init.php';

// CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validasi CSRF
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die('CSRF validation failed');
    }
    // Validasi input sederhana
    $u = trim($_POST['username']);
    $p = trim($_POST['password']);
    if (!preg_match('/^[a-zA-Z0-9_]+$/', $u)) {
        $error = "Username invalid.";
    } else {
        // SQL Injection fix: prepared statement
        $stmt = $GLOBALS['PDO']->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
        $stmt->execute([$u, $p]);
        if ($row = $stmt->fetch()) {
            $_SESSION['user'] = $row['username'];
            $_SESSION['role'] = $row['role'];
            $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
            $_SESSION['ip'] = $_SERVER['REMOTE_ADDR'];
            session_regenerate_id(true); // Proteksi session fixation
            // Simpan profil di session, bukan cookie
            $_SESSION['profile'] = [
                'username' => $row['username'],
                'isAdmin' => $row['role'] === 'admin'
            ];
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Login failed.";
        }
    }
}
?>
<?php include '_header.php'; ?>
<h2>Login</h2>
<?php if (!empty($error)) echo "<p style='color:red'>" . htmlspecialchars($error) . "</p>"; ?>
<form method="post">
  <label>Username <input name="username" required></label>
  <label>Password <input type="password" name="password" required></label>
  <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
  <button type="submit">Login</button>
</form>
<?php include '_footer.php'; ?>
// Semua input dan output sudah divalidasi dan diamankan dari SQL Injection, CSRF, Session Hijacking, dan XSS.
