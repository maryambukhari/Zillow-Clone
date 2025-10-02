<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_type'], ['agent', 'owner'])) {
    header('Location: login.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $price = floatval($_POST['price']);
    $city = $_POST['city'];
    $state = $_POST['state'];
    $neighborhood = $_POST['neighborhood'] ?? '';
    $property_type = $_POST['property_type'];
    $bedrooms = intval($_POST['bedrooms']);
    $bathrooms = intval($_POST['bathrooms']);
    $amenities = $_POST['amenities'] ?? '';
    $user_id = $_SESSION['user_id'];

    // Handle image upload
    $image = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        if (in_array($_FILES['image']['type'], $allowed_types)) {
            $image = time() . '_' . basename($_FILES['image']['name']);
            $target = 'assets/property_images/' . $image;
            if (!move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
                $error = "Failed to upload image.";
            }
        } else {
            $error = "Invalid image type. Only JPEG, PNG, and GIF are allowed.";
        }
    } else {
        $error = "Image upload failed.";
    }

    if (!$error) {
        try {
            $stmt = $pdo->prepare("INSERT INTO properties (user_id, title, description, price, city, state, neighborhood, property_type, bedrooms, bathrooms, amenities, image) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$user_id, $title, $description, $price, $city, $state, $neighborhood, $property_type, $bedrooms, $bathrooms, $amenities, $image]);
            header('Location: dashboard.php');
            exit;
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>List Property</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap');
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f5f7fa, #c3cfe2);
            margin: 0;
            padding: 20px;
        }
        .form-container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            animation: slideIn 0.5s ease;
        }
        @keyframes slideIn {
            from { transform: translateY(-50px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        .form-container h2 {
            text-align: center;
            color: #0078ff;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            font-size: 14px;
            margin-bottom: 5px;
        }
        .form-group input, .form-group select, .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }
        .btn {
            width: 100%;
            padding: 10px;
            background: #0078ff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .btn:hover {
            background: #005bbb;
        }
        .error {
            color: red;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>List a Property</h2>
        <?php if ($error): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" id="title" name="title" required>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" required></textarea>
            </div>
            <div class="form-group">
                <label for="price">Price ($)</label>
                <input type="number" id="price" name="price" step="0.01" required>
            </div>
            <div class="form-group">
                <label for="city">City</label>
                <input type="text" id="city" name="city" required>
            </div>
            <div class="form-group">
                <label for="state">State</label>
                <input type="text" id="state" name="state" required>
            </div>
            <div class="form-group">
                <label for="neighborhood">Neighborhood</label>
                <input type="text" id="neighborhood" name="neighborhood">
            </div>
            <div class="form-group">
                <label for="property_type">Property Type</label>
                <select id="property_type" name="property_type" required>
                    <option value="house">House</option>
                    <option value="apartment">Apartment</option>
                    <option value="commercial">Commercial</option>
                </select>
            </div>
            <div class="form-group">
                <label for="bedrooms">Bedrooms</label>
                <input type="number" id="bedrooms" name="bedrooms" required>
            </div>
            <div class="form-group">
                <label for="bathrooms">Bathrooms</label>
                <input type="number" id="bathrooms" name="bathrooms" required>
            </div>
            <div class="form-group">
                <label for="amenities">Amenities</label>
                <textarea id="amenities" name="amenities"></textarea>
            </div>
            <div class="form-group">
                <label for="image">Property Image</label>
                <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/gif" required>
            </div>
            <button type="submit" class="btn">List Property</button>
        </form>
    </div>
    <script>
        document.querySelector('.btn').addEventListener('click', function(e) {
            if (!this.closest('form').checkValidity()) {
                e.preventDefault();
            }
        });
    </script>
</body>
</html>
