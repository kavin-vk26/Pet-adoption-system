<?php require __DIR__ . '/layout/header.php'; ?>
<h2>My Adoptions</h2>
<?php if(empty($adopted)): ?>
  <p>You have not adopted any pets yet.</p>
<?php else: ?>
  <ul>
  <?php foreach($adopted as $p): ?>
    <li>
      <?=htmlspecialchars($p['name'])?> (<?=htmlspecialchars($p['species'])?>) - Adopted at <?=htmlspecialchars($p['adopted_at'])?>
    </li>
  <?php endforeach; ?>
  </ul>
<?php endif; ?>
<?php require __DIR__ . '/layout/footer.php'; ?>
