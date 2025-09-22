<?php
// public/index.php
require_once __DIR__ . '/../app/config.php';
require_once __DIR__ . '/../app/db.php';
require_once __DIR__ . '/../app/auth.php';
require_once __DIR__ . '/../app/functions.php';

$page = $_GET['page'] ?? 'home';
$pdo = getPDO();
$user = currentUser();

// Simple routing
switch ($page) {
    case 'register':
        require __DIR__ . '/../views/register.php';
        break;
    case 'login':
        require __DIR__ . '/../views/login.php';
        break;
    case 'logout':
        session_destroy();
        redirect('/');
        break;
    case 'register_action':
        // handle registration
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        if (!$name || !$email || !$password) {
            $_SESSION['flash'] = 'All fields required.';
            redirect('/?page=register');
        }
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = :email');
        $stmt->execute(['email' => $email]);
        if ($stmt->fetch()) {
            $_SESSION['flash'] = 'Email already registered.';
            redirect('/?page=register');
        }
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare('INSERT INTO users (name, email, password) VALUES (:name, :email, :password)');
        $stmt->execute(['name'=>$name, 'email'=>$email, 'password'=>$hash]);
        $_SESSION['flash'] = 'Registration successful. Please login.';
        redirect('/?page=login');
        break;
    case 'login_action':
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        if (!$email || !$password) {
            $_SESSION['flash'] = 'All fields required.';
            redirect('/?page=login');
        }
        $stmt = $pdo->prepare('SELECT id, password FROM users WHERE email = :email');
        $stmt->execute(['email'=>$email]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row && password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['flash'] = 'Login successful.';
            redirect('/');
        } else {
            $_SESSION['flash'] = 'Invalid credentials.';
            redirect('/?page=login');
        }
        break;
    case 'pet':
        $pet_id = intval($_GET['id'] ?? 0);
        $stmt = $pdo->prepare('SELECT * FROM pets WHERE id = :id');
        $stmt->execute(['id'=>$pet_id]);
        $pet = $stmt->fetch(PDO::FETCH_ASSOC);
        require __DIR__ . '/../views/pet_details.php';
        break;
    case 'adopt_action':
        // adopt a pet
        requireLogin();
        $pet_id = intval($_POST['pet_id'] ?? 0);
        // check pet exists and not already adopted
        $stmt = $pdo->prepare('SELECT is_adopted FROM pets WHERE id = :id');
        $stmt->execute(['id'=>$pet_id]);
        $pet = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$pet) {
            $_SESSION['flash'] = 'Pet not found.';
            redirect('/');
        }
        if ($pet['is_adopted']) {
            $_SESSION['flash'] = 'Pet already adopted.';
            redirect('/?page=pet&id=' . $pet_id);
        }
        // insert into adoptions, set pet adopted
        try {
            $pdo->beginTransaction();
            $stmt = $pdo->prepare('INSERT INTO adoptions (user_id, pet_id) VALUES (:user_id, :pet_id)');
            $stmt->execute(['user_id'=>$_SESSION['user_id'], 'pet_id'=>$pet_id]);
            $stmt = $pdo->prepare('UPDATE pets SET is_adopted = TRUE WHERE id = :id');
            $stmt->execute(['id'=>$pet_id]);
            $pdo->commit();
            $_SESSION['flash'] = 'Adoption successful. Thank you!';
        } catch (PDOException $e) {
            $pdo->rollBack();
            $_SESSION['flash'] = 'Failed to adopt: ' . $e->getMessage();
        }
        redirect('/');
        break;
    case 'admin_add':
        requireLogin();
        if (!$user || !$user['is_admin']) {
            $_SESSION['flash'] = 'Access denied.';
            redirect('/');
        }
        require __DIR__ . '/../views/admin_add_pet.php';
        break;
    case 'admin_add_action':
        requireLogin();
        if (!$user || !$user['is_admin']) {
            $_SESSION['flash'] = 'Access denied.';
            redirect('/');
        }
        $name = trim($_POST['name'] ?? '');
        $species = trim($_POST['species'] ?? '');
        $age = intval($_POST['age'] ?? 0);
        $description = trim($_POST['description'] ?? '');
        $image = uploadImage($_FILES['image'] ?? null);
        $stmt = $pdo->prepare('INSERT INTO pets (name, species, age, description, image) VALUES (:name,:species,:age,:desc,:img)');
        $stmt->execute(['name'=>$name,'species'=>$species,'age'=>$age,'desc'=>$description,'img'=>$image]);
        $_SESSION['flash'] = 'Pet added.';
        redirect('/?page=admin_add');
        break;
    case 'profile':
        requireLogin();
        // fetch user's adoptions
        $stmt = $pdo->prepare('SELECT p.* , a.adopted_at FROM pets p JOIN adoptions a ON p.id = a.pet_id WHERE a.user_id = :uid');
        $stmt->execute(['uid'=>$_SESSION['user_id']]);
        $adopted = $stmt->fetchAll(PDO::FETCH_ASSOC);
        require __DIR__ . '/../views/profile.php';
        break;
    default:
        // home: list pets
        $stmt = $pdo->query('SELECT * FROM pets ORDER BY created_at DESC');
        $pets = $stmt->fetchAll(PDO::FETCH_ASSOC);
        require __DIR__ . '/../views/home.php';
        break;
}
