<?php require __DIR__ . '/layout/header.php'; ?>
<?php if(!$pet){ echo "<p>Pet not found.</p>"; require __DIR__ . '/layout/footer.php'; exit; } ?>
<div class="pet-detail">
  <?php if($pet['image']): ?>
    <img src="/uploads/<?=htmlspecialchars($pet['image'])?>" alt="<?=htmlspecialchars($pet['name'])?>" />
  <?php endif; ?>
  <h2><?=htmlspecialchars($pet['name'])?> <?= $pet['is_adopted'] ? '(Adopted)' : '' ?></h2>
  <p><strong>Species:</strong> <?=htmlspecialchars($pet['species'])?></p>
  <p><strong>Age:</strong> <?=intval($pet['age'])?></p>
  <p><?=nl2br(htmlspecialchars($pet['description']))?></p>

  <?php if(!$pet['is_adopted']): ?>
    <?php if($user): ?>
      <form method="post" action="/?page=adopt_action">
        <input type="hidden" name="pet_id" value="<?=$pet['id']?>">
        <button type="submit">Adopt this pet</button>
      </form>
    <?php else: ?>
      <p><a href="/?page=login">Login</a> to adopt.</p>
    <?php endif; ?>
  <?php else: ?>
    <p>This pet has already been adopted.</p>
  <?php endif; ?>
</div>
<?php require __DIR__ . '/layout/footer.php'; ?>
