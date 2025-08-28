
<?php include 'auth.php'; ?>
<?php include '_header.php'; ?>
<h2>Crash Test</h2>
<?php
$factor = isset($_GET['factor']) ? $_GET['factor'] : 1;
// Validasi input agar tidak division by zero dan tidak disclosure
if (!is_numeric($factor) || $factor == 0) {
	echo "Invalid factor."; // Mengatasi information disclosure dan division by zero
} else {
	$result = 100 / $factor;
	echo "100 / " . htmlspecialchars($factor) . " = " . htmlspecialchars($result);
}
?>
<?php include '_footer.php'; ?>
// Perbaikan: validasi input, error handling, escape output.
