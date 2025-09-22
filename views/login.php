<?php require __DIR__ . '/layout/header.php'; ?>

<div class="auth-container">
  <h2>Login</h2>
  <form method="post" action="/?page=login_action">
    <label for="email">Email:</label>
    <input type="email" name="email" id="email" required>

    <label for="password">Password:</label>
    <input type="password" name="password" id="password" required>

    <button type="submit">Login</button>
  </form>
</div>

<?php require __DIR__ . '/layout/footer.php'; ?>