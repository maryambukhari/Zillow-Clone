<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
 header('Location: login.php');
 exit;
}

$user_id = $_SESSION['user_id'];
try {
 // Fetch saved listings
 $stmt = $pdo->prepare("SELECT p.* FROM properties p JOIN saved_listings s ON p.id = s.property_id WHERE s.user_id = ? AND p.status = 'approved'");
 $stmt->execute([$user_id]);
 $saved_properties = $stmt->fetchAll(PDO::FETCH_ASSOC);

 // Fetch user-owned listings
 $stmt = $pdo->prepare("SELECT * FROM properties WHERE user_id = ?");
 $stmt->execute([$user_id]);
 $my_properties = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
 $error = "Database error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
 <meta charset="UTF-8">
 <meta name="viewport" content="width=device-width, initial-scale=1.0">
 <title>Dashboard</title>
 <style>
 @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap');
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
 .dashboard {
 padding: 20px;
 max-width: 1200px;
 margin: 0 auto;
 }
 .section {
 margin-bottom: 40px;
 }
 .section h2 {
 font-size: 28px;
 color: #0078ff;
 }
 .properties {
 display: grid;
 grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
 gap: 20px;
 }
 .property-card {
 background: white;
 border-radius: 10px;
 overflow: hidden;
 box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
 transition: transform 0.3s ease;
 }
 .property-card:hover {
 transform: scale(1.05);
 }
 .property-card img {
 width: 100%;
 height: 200px;
 object-fit: cover;
 }
 .property-card h3 {
 font-size: 20px;
 margin: 10px;
 }
 .property-card p {
 margin: 0 10px 10px;
 color: #555;
 }
 .btn {
 display: inline-block;
 padding: 10px 20px;
 background: #0078ff;
 color: white;
 text-decoration: none;
 border-radius: 5px;
 margin: 10px;
 }
 .btn:hover {
 background: #005bbb;
 }
 .error {
 color: red;
 text-align: center;
 }
 .no-results {
 text-align: center;
 font-size: 18px;
 color: #555;
 }
 </style>
</head>
<body>
 <div class="navbar">
 <div>
 <a href="index.php">Home</a>
 <a href="search.php">Search</a>
 <a href="list_property.php">List Property</a>
 <a href="logout.php">Logout</a>
 </div>
 </div>
 <div class="dashboard">
 <h1>Welcome, <?php echo htmlspecialchars($_SESSION['user_type']); ?></h1>
 <?php if (isset($error)): ?>
 <p class="error"><?php echo htmlspecialchars($error); ?></p>
 <?php endif; ?>
 <div class="section">
 <h2>Saved Listings</h2>
 <?php if (empty($saved_properties)): ?>
 <p class="no-results">No saved properties found.</p>
 <?php else: ?>
 <div class="properties">
 <?php foreach ($saved_properties as $property): ?>
 <div class="property-card">
 <img src="assets/property_images/<?php echo htmlspecialchars($property['image'] ?? 'default.jpg'); ?>" alt="Property">
 <h3><?php echo htmlspecialchars($property['title']); ?></h3>
 <p><?php echo htmlspecialchars($property['city'] . ', ' . $property['state']); ?></p>
 <p>$<?php echo number_format($property['price'], 2); ?></p>
 <a href="property_details.php?id=<?php echo $property['id']; ?>" class="btn">View Details</a>
 </div>
 <?php endforeach; ?>
 </div>
 <?php endif; ?>
 </div>
 <?php if ($_SESSION['user_type'] === 'agent' || $_SESSION['user_type'] === 'owner'): ?>
 <div class="section">
 <h2>My Listings</h2>
 <?php if (empty($my_properties)): ?>
 <p class="no-results">No properties listed.</p>
 <?php else: ?>
 <div class="properties">
 <?php foreach ($my_properties as $property): ?>
 <div class="property-card">
 <img src="assets/property_images/<?php echo htmlspecialchars($property['image'] ?? 'default.jpg'); ?>" alt="Property">
 <h3><?php echo htmlspecialchars($property['title']); ?></h3>
 <p><?php echo htmlspecialchars($property['city'] . ', ' . $property['state']); ?></p>
 <p>$<?php echo number_format($property['price'], 2); ?></p>
 <a href="property_details.php?id=<?php echo $property['id']; ?>" class="btn">View Details</a>
 </div>
 <?php endforeach; ?>
 </div>
 <?php endif; ?>
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
