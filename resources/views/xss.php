<h2>Hello, <?= htmlspecialchars($userName, ENT_QUOTES, 'UTF-8') ?></h2>
// this demonstrates how to use htmlspecialchars() to prevent XSS attacks
