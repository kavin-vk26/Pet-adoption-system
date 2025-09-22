<?php require __DIR__ . '/layout/header.php'; ?>

<div class="auth-container">
  <h2>Register</h2>
  <form method="post" action="/?page=register_action">
    <label for="name">Name:</label>
    <input type="text" name="name" id="name" required>

    <label for="email">Email:</label>
    <input type="email" name="email" id="email" required>

    <label for="password">Password:</label>
    <input type="password" name="password" id="password" required>

    <button type="submit">Register</button>
  </form>
</div>

<?php require __DIR__ . '/layout/footer.php'; ?>