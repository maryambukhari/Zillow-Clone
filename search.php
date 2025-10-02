<?php
session_start();
require 'db.php';

$filters = [];
$params = [];
$query = "SELECT * FROM properties WHERE status = 'approved'";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
 if (!empty($_POST['city'])) {
 $filters[] = "city LIKE ?";
 $params[] = '%' . $_POST['city'] . '%';
 }
 if (!empty($_POST['state'])) {
 $filters[] = "state LIKE ?";
 $params[] = '%' . $_POST['state'] . '%';
 }
 if (!empty($_POST['min_price'])) {
 $filters[] = "price >= ?";
 $params[] = floatval($_POST['min_price']);
 }
 if (!empty($_POST['max_price'])) {
 $filters[] = "price <= ?";
 $params[] = floatval($_POST['max_price']);
 }
 if (!empty($_POST['property_type'])) {
 $filters[] = "property_type = ?";
 $params[] = $_POST['property_type'];
 }
 if (!empty($_POST['bedrooms'])) {
 $filters[] = "bedrooms >= ?";
 $params[] = intval($_POST['bedrooms']);
 }

 if ($filters) {
 $query .= " AND " . implode(" AND ", $filters);
 }
}

try {
 $stmt = $pdo->prepare($query);
 $stmt->execute($params);
 $properties = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
 $error = "Search error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
 <meta charset="UTF-8">
 <meta name="viewport" content="width=device-width, initial-scale=1.0">
 <title>Search Properties</title>
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
 justify-content: space-between ;
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
 .search-container {
 max-width: 1200px;
 margin: 20px auto;
 padding: 20px;
 background: white;
 border-radius: 10px;
 box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
 }
 .search-form {
 display: grid;
 grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
 gap: 10px;
 margin-bottom: 20px;
 }
 .form-group {
 display: flex;
 flex-direction: column;
 }
 .form-group label {
 font-size: 14px;
 margin-bottom: 5px;
 }
 .form-group input, .form-group select {
 padding: 10px;
 border: 1px solid #ccc;
 border-radius: 5px;
 }
 .btn {
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
 <?php if (isset($_SESSION['user_id'])): ?>
 <a href="dashboard.php">Dashboard</a>
 <a href="list_property.php">List Property</a>
 <a href="logout.php">Logout</a>
 <?php else: ?>
 <a href="signup.php">Sign Up</a>
 <a href="login.php">Login</a>
 <?php endif; ?>
 </div>
 </div>
 <div class="search-container">
 <h2>Search Properties</h2>
 <?php if (isset($error)): ?>
 <p class="error"><?php echo htmlspecialchars($error); ?></p>
 <?php endif; ?>
 <form method="POST" class="search-form">
 <div class="form-group">
 <label for="city">City</label>
 <input type="text" id="city" name="city" value="<?php echo isset($_POST['city']) ? htmlspecialchars($_POST['city']) : ''; ?>">
 </div>
 <div class="form-group">
 <label for="state">State</label>
 <input type="text" id="state" name="state" value="<?php echo isset($_POST['state']) ? htmlspecialchars($_POST['state']) : ''; ?>">
 </div>
 <div class="form-group">
 <label for="min_price">Min Price</label>
 <input type="number" id="min_price" name="min_price" value="<?php echo isset($_POST['min_price']) ? htmlspecialchars($_POST['min_price']) : ''; ?>">
 </div>
 <div class="form-group">
 <label for="max_price">Max Price</label>
 <input type="number" id="max_price" name="max_price" value="<?php echo isset($_POST['max_price']) ? htmlspecialchars($_POST['max_price']) : ''; ?>">
 </div>
 <div class="form-group">
 <label for="property_type">Property Type</label>
 <select id="property_type" name="property_type">
 <option value="">Any</option>
 <option value="house" <?php echo isset($_POST['property_type']) && $_POST['property_type'] === 'house' ? 'selected' : ''; ?>>House</option>
 <option value="apartment" <?php echo isset($_POST['property_type']) && $_POST['property_type'] === 'apartment' ? 'selected' : ''; ?>>Apartment</option>
 <option value="commercial" <?php echo isset($_POST['property_type']) && $_POST['property_type'] === 'commercial' ? 'selected' : ''; ?>>Commercial</option>
 </select>
 </div>
 <div class="form-group">
 <label for="bedrooms">Bedrooms</label>
 <input type="number" id="bedrooms" name="bedrooms" value="<?php echo isset($_POST['bedrooms']) ? htmlspecialchars($_POST['bedrooms']) : ''; ?>">
 </div>
 <button type="submit" class="btn">Search</button>
 </form>
 <div class="properties">
 <?php if (empty($properties)): ?>
 <p class="no-results">No properties found.</p>
 <?php else: ?>
 <?php foreach ($properties as $property): ?>
 <div class="property-card">
 <img src="assets/property_images/<?php echo htmlspecialchars($property['image'] ?? 'default.jpg'); ?>" alt="Property">
 <h3><?php echo htmlspecialchars($property['title']); ?></h3>
 <p><?php echo htmlspecialchars($property['city'] . ', ' . $property['state']); ?></p>
 <p>$<?php echo number_format($property['price'], 2); ?></p>
 <a href="property_details.php?id=<?php echo $property['id']; ?>" class="btn">View Details</a>
 </div>
 <?php endforeach; ?>
 <?php endif; ?>
 </div>
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
