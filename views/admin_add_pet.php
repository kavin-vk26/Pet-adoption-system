<?php
// views/admin_add_pet.php
require __DIR__ . '/layout/header.php';
?>

<div class="auth-container">
  <h2>Add New Pet</h2>
  <form method="post" action="/?page=admin_add_action" enctype="multipart/form-data">
    <label for="name">Pet Name:</label>
    <input type="text" name="name" id="name" required>

    <label for="species">Species:</label>
    <input type="text" name="species" id="species" required>

    <label for="age">Age:</label>
    <input type="number" name="age" id="age" required>

    <label for="description">Description:</label>
    <textarea name="description" id="description" rows="4" style="width: 100%;"></textarea>

    <label for="image">Image:</label>
    <input type="file" name="image" id="image" accept="image/*">

    <br><br>
    <button type="submit">Add Pet</button>
  </form>
</div>

<?php require __DIR__ . '/layout/footer.php'; ?>