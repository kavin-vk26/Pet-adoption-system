<?php require __DIR__ . '/layout/header.php'; ?>

<h2>Available Pets</h2>
<div class="pet-list">
<?php foreach($pets as $pet): ?>
  <div class="pet-card">
    <?php if($pet['image']): ?>
      <img src="<?= UPLOAD_URL . htmlspecialchars($pet['image']) ?>" alt="<?= htmlspecialchars($pet['name']) ?>" />
    <?php else: ?>
      <img src="https://placehold.co/220x140?text=No+Image" alt="No image available" />
    <?php endif; ?>
    <h3><?= htmlspecialchars($pet['name']) ?> <?= $pet['is_adopted'] ? '<span style="color:red;">(Adopted)</span>' : '' ?></h3>
    <p><?= htmlspecialchars($pet['species']) ?> — Age: <?= intval($pet['age']) ?></p>
    <p><?= nl2br(htmlspecialchars(substr($pet['description'], 0, 80))) ?>...</p>
    <a href="/?page=pet&id=<?= $pet['id'] ?>">View Details</a>
  </div>
<?php endforeach; ?>
</div>

<?php require __DIR__ . '/layout/footer.php'; ?>