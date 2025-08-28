<?php include 'auth.php'; ?>
<?php include '_header.php'; ?>
<h2>Ping Server</h2>
<form><input name="target"><button>Ping!</button></form>
?>
<?php
if (!isset($_GET['target'])) {
    die("Missing parameter.");
}
    $target = $_GET['target'];
    // Validasi hanya IP/domain
    if (!preg_match('/^[a-zA-Z0-9.\-]+$/', $target)) {
        die("Invalid target.");
    }
    echo "<h3>Ping Result for: " . htmlspecialchars($target) . "</h3>";
    $output = shell_exec("ping -c 2 " . escapeshellarg($target));
    echo "<pre>" . htmlspecialchars($output) . "</pre>";
?>
<?php include '_footer.php'; ?>
