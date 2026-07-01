<?php
require 'includes/config.php';
require 'includes/header.php';

// Sanitize inputs
$name = $conn->real_escape_string($_GET['name'] ?? '');
$species = $conn->real_escape_string($_GET['species'] ?? '');
$min_age = intval($_GET['min_age'] ?? 0);
$max_age = intval($_GET['max_age'] ?? 99);

$where = [];
if ($name !== '') $where[] = "name LIKE '%$name%'";
if ($species !== '') $where[] = "species='$species'";
if ($min_age > 0) $where[] = "age >= $min_age";
if ($max_age < 99) $where[] = "age <= $max_age";

$sql = "SELECT * FROM pets WHERE is_adopted=0";
if($where) $sql .= " AND ".implode(" AND ", $where);

$pets = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Pet Adoption - Search Pets</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    /* Background */
    body {
        background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
        font-family: "Poppins", sans-serif;
        color: #333;
        min-height: 100vh;
        padding: 30px;
    }

    /* Headings */
    h1 {
        color: #00796b;
        text-shadow: 1px 1px #fff;
    }

    /* Search filter card */
    .auth-card {
        background: rgba(255,255,255,0.95);
        padding: 25px;
        border-radius: 15px;
        box-shadow: 0 6px 12px rgba(0,0,0,0.1);
        margin-bottom: 40px;
    }

    /* Inputs */
    .form-control, .form-select {
        border-radius: 10px;
        border: 2px solid #ddd;
        transition: all 0.3s ease;
    }
    .form-control:focus, .form-select:focus {
        border-color: #00bfa6;
        box-shadow: 0 0 8px rgba(0,191,166,0.4);
    }

    /* Buttons */
    .btn-primary {
        background: #00bfa6;
        border: none;
        border-radius: 10px;
        transition: 0.3s;
        font-weight: 500;
    }
    .btn-primary:hover {
        background: #009e89;
    }

    /* Pet cards */
    .card {
        border: none;
        border-radius: 15px;
        overflow: hidden;
        transition: transform 0.2s, box-shadow 0.2s;
        background: #fff;
    }
    .card:hover {
        transform: translateY(-6px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.15);
    }

    /* Badges */
    .badge {
        background: #00bfa6 !important;
    }
  </style>
</head>
<body>

  <div class="container">
    <h1 class="text-center mb-4 display-4 fw-bold">Find Your New Best Friend 🐾</h1>

    <!-- Search Filters -->
    <div class="auth-card">
      <h4 class="mb-3 text-center">Search & Filter</h4>
      <form class="row g-3" method="get">
        <div class="col-md-6">
          <label for="name" class="form-label">Name</label>
          <input type="text" class="form-control" name="name" id="name" placeholder="Search by name" value="<?= htmlspecialchars($_GET['name'] ?? '') ?>">
        </div>
        <div class="col-md-6">
          <label for="species" class="form-label">Species</label>
          <select class="form-select" name="species" id="species">
              <option value="">All Species</option>
              <option value="Dog" <?= ($_GET['species'] ?? '') == 'Dog' ? 'selected' : '' ?>>Dog</option>
              <option value="Cat" <?= ($_GET['species'] ?? '') == 'Cat' ? 'selected' : '' ?>>Cat</option>
              <option value="Bird" <?= ($_GET['species'] ?? '') == 'Bird' ? 'selected' : '' ?>>Bird</option>
          </select>
        </div>
        <div class="col-md-6">
          <label for="min_age" class="form-label">Min Age (Years)</label>
          <input type="number" class="form-control" name="min_age" id="min_age" placeholder="Min Age" value="<?= htmlspecialchars($_GET['min_age'] ?? '') ?>">
        </div>
        <div class="col-md-6">
          <label for="max_age" class="form-label">Max Age (Years)</label>
          <input type="number" class="form-control" name="max_age" id="max_age" placeholder="Max Age" value="<?= htmlspecialchars($_GET['max_age'] ?? '') ?>">
        </div>
        <div class="col-12 mt-4">
          <button class="btn btn-primary w-100 py-2">Apply Filters</button>
        </div>
      </form>
    </div>

    <!-- Pets Grid -->
    <div class="row">
    <?php 
    if ($pets && $pets->num_rows > 0) {
        while($pet = $pets->fetch_assoc()){ 
            $img = $conn->query("SELECT image FROM pet_images WHERE pet_id=".$pet['id']." LIMIT 1")->fetch_assoc();
            $img_src = $img ? 'uploads/'.$img['image'] : 'assets/images/default_pet.jpg';
    ?>
      <div class="col-lg-4 col-md-6 mb-4">
        <div class="card h-100 shadow-sm">
          <img src="<?= $img_src ?>" class="card-img-top" alt="<?= htmlspecialchars($pet['name']) ?>" style="height:280px; object-fit:cover;">
          <div class="card-body d-flex flex-column">
            <h5 class="card-title fw-bold text-primary"><?= htmlspecialchars($pet['name']) ?></h5>
            <p class="card-text text-muted">
                <span class="badge me-2"><?= htmlspecialchars($pet['species']) ?></span> 
                Age: <?= htmlspecialchars($pet['age']) ?> years
            </p>
            <p class="mb-3"><?= htmlspecialchars(substr($pet['description'],0,100)) ?>...</p>
            <a href="pet_detail.php?id=<?= $pet['id'] ?>" class="btn btn-primary mt-auto">View & Adopt</a>
          </div>
        </div>
      </div>
    <?php 
        }
    } else {
        echo "<div class='col-12'><p class='alert alert-info text-center py-4'>🐾 No pets matching your search criteria are currently available. Try broadening your search!</p></div>";
    }
    ?>
    </div>
  </div>

<?php require 'includes/footer.php'; ?>
</body>
</html>
