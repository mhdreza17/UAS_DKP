
<?php
include 'auth.php';

// Ambil profile dari session, bukan cookie (mengatasi insecure deserialization)
if (!isset($_SESSION['profile'])) {
    die("Profile tidak ditemukan. Silakan login ulang.");
}
$profile = $_SESSION['profile'];

// CSRF token untuk mencegah CSRF
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// jika admin, boleh hapus user lain
if ($profile['isAdmin'] && isset($_POST['delete_user'])) {
    // Validasi CSRF
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die('CSRF validation failed');
    }
    $target = $_POST['delete_user'];
    // SQL Injection fix: prepared statement
    $stmt = $GLOBALS['PDO']->prepare("DELETE FROM users WHERE username = ?");
    $stmt->execute([$target]);
    $msg = "<p style='color:green'>User <b>" . htmlspecialchars($target) . "</b> berhasil dihapus!</p>";
}

include '_header.php';
?>
<h2>Profile Page</h2>
<p><?php echo "User: " . htmlspecialchars($profile['username']) . ", Role: " . ($profile['isAdmin'] ? "Admin" : "User"); ?></p>

<?php if ($profile['isAdmin']): ?>
  <h3>Admin Panel</h3>
  <form method="post">
    <label>Delete user:
      <select name="delete_user">
        <?php
        $users = $GLOBALS['PDO']->query("SELECT username FROM users");
        foreach ($users as $u) {
            if ($u['username'] !== $profile['username']) {
                echo "<option value='" . htmlspecialchars($u['username']) . "'>" . htmlspecialchars($u['username']) . "</option>";
            }
        }
        ?>
      </select>
    </label>
    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
    <button type="submit">Delete</button>
  </form>
  <?php if (!empty($msg)) echo $msg; ?>
<?php else: ?>
  <p style="color:red">You are a regular user. You do not have admin panel access.</p>
<?php endif; ?>

<?php include '_footer.php'; ?>
// Perbaikan: insecure deserialization (pakai session), SQL Injection (prepared statement), XSS (htmlspecialchars), CSRF (token).
