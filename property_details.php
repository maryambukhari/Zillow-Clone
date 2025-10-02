<?php
session_start();
require 'db.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
 header('Location: index.php?error=invalid_id');
 exit;
}

$property_id = (int)$_GET['id'];
try {
 $stmt = $pdo->prepare("SELECT p.*, u.username FROM properties p JOIN users u ON p.user_id = u.id WHERE p.id = ? AND p.status = 'approved'");
 $stmt->execute([$property_id]);
 $property = $stmt->fetch(PDO::FETCH_ASSOC);

 if (!$property) {
 header('Location: index.php?error=property_not_found');
 exit;
 }
} catch (PDOException $e) {
 header('Location: index.php?error=database_error');
 exit;
}

$success = '';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
 $user_id = $_SESSION['user_id'];
 try {
 if (isset($_POST['save'])) {
 $stmt = $pdo->prepare("INSERT INTO saved_listings (user_id, property_id) VALUES (?, ?)");
 if ($stmt->execute([$user_id, $property_id])) {
 $success = "Property saved successfully!";
 } else {
 $error = "Failed to save property.";
 }
 } elseif (isset($_POST['inquiry'])) {
 $message = $_POST['message'];
 $stmt = $pdo->prepare("INSERT INTO inquiries (property_id, user_id, message) VALUES (?, ?, ?)");
 $stmt->execute([$property_id, $user_id, $message]);
 $success = "Inquiry sent successfully!";
 }
 } catch (PDOException $e) {
 $error = "Error: " . $e->getMessage();
 }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
 <meta charset="UTF-8">
 <meta name="viewport" content="width=device-width, initial-scale=1.0">
 <title>Property Details</title>
 <style>
 @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;800&display=swap');
 body {
 font-family: 'Poppins', sans-serif;
 background: linear-gradient(135deg, #f5f7fa, #c3cfe2);
 margin: 0;
 }
 .navbar {
 background: #0078ff;
 padding: 15px;
 display: flex;
 justify-content: space-between;
 }
 .navbar a {
 color: white;
 text-decoration: none;
 font-size: 18px;
 margin: 0 15px;
 }
 .navbar a:hover {
 color: #ffeb3b;
 }
 .property-details {
 max-width: 800px;
 margin: 20px auto;
 background: white;
 padding: 20px;
 border-radius: 10px;
 box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
 }
 .property-details img {
 width: 100%;
 height: 400px;
 object-fit: cover;
 border-radius: 10px;
 }
 .property-details h1 {
 font-size: 32px;
 color: #0078ff;
 }
 .property-details p {
 font-size: 16px;
 color: #555;
 }
 .btn {
 display: inline-block;
 padding: 10px 20px;
 background: #0078ff;
 color: white;
 border: none;
 border-radius: 5px;
 cursor: pointer;
 }
 .btn:hover {
 background: #005bbb;
 }
 .inquiry-form {
 margin-top: 20px;
 }
 .inquiry-form textarea {
 width: 100%;
 padding: 10px;
 border: 1px solid #ccc;
 border-radius: 5px;
 }
 .success {
 color: green;
 text-align: center;
 }
 .error {
 color: red;
 text-align: center;
 }
 </style>
</head>
<body>
 <div class="navbar">
 <div>
 <a href="index.php">Home</a>
 <a href="search.php">Search</a>
 <?php if (isset($_SESSION['user_id'])): ?>
 <a href="dashboard.php ">Dashboard</a>
 <a href="list_property.php">List Property</a>
 <a href="logout.php">Logout</a>
 <?php else: ?>
 <a href="signup.php">Sign Up</a>
 <a href="login.php">Login</a>
 <?php endif; ?>
 </div>
 </div>
 <div class="property-details">
 <?php if (isset($success)): ?>
 <p class="success"><?php echo htmlspecialchars($success); ?></p>
 <?php endif; ?>
 <?php if (isset($error)): ?>
 <p class="error"><?php echo htmlspecialchars($error); ?></p>
 <?php endif; ?>
 <img src="assets/property_images/<?php echo htmlspecialchars($property['image'] ?? 'default.jpg'); ?>" alt="Property">
 <h1><?php echo htmlspecialchars($property['title']); ?></h1>
 <p><?php echo htmlspecialchars($property['city'] . ', ' . $property['state']); ?></p>
 <p>$<?php echo number_format($property['price'], 2); ?></p>
 <p><?php echo htmlspecialchars($property['description']); ?></p>
 <p>Bedrooms: <?php echo htmlspecialchars($property['bedrooms']); ?></p>
 <p>Bathrooms: <?php echo htmlspecialchars($property['bathrooms']); ?></p>
 <p>Amenities: <?php echo htmlspecialchars($property['amenities'] ?? 'None'); ?></p>
 <p>Contact: <?php echo htmlspecialchars($property['username']); ?></p>
 <?php if (isset($_SESSION['user_id'])): ?>
 <form method="POST">
 <button type="submit" name="save" class="btn">Save Property</button>
 </form>
 <div class="inquiry-form">
 <h2>Send Inquiry</h2>
 <form method="POST">
 <textarea name="message" required placeholder="Your message..."></textarea>
 <button type="submit" name="inquiry" class="btn">Send Inquiry</button>
 </form>
 </div>
 <?php endif; ?>
 </div>
 <script>
 document.querySelectorAll('.btn, .navbar a').forEach(link => {
 link.addEventListener('click', function(e) {
 if (!this.closest('form')) {
 e.preventDefault();
 window.location.href = this.getAttribute('href');
 }
 });
 });
 </script>
</body>
</html>
