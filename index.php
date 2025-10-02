<?php
session_start();
require 'db.php';
$stmt = $pdo->query("SELECT * FROM properties WHERE status = 'approved' LIMIT 6");
$properties = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zillow Clone - Home</title>
    <style>
        /* Stunning CSS with animations */
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;800&display=swap');
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            background: linear-gradient(135deg, #f5f7fa, #c3cfe2);
            color: #333;
        }
        .navbar {
            background: #0078ff;
            padding: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .navbar a {
            color: white;
            text-decoration: none;
            font-size: 18px;
            margin: 0 15px;
            transition: color 0.3s ease;
        }
        .navbar a:hover {
            color: #ffeb3b;
        }
        .hero {
            text-align: center;
            padding: 50px 20px;
            background: url('assets/hero-bg.jpg') no-repeat center/cover;
            color: white;
            animation: fadeIn 2s ease-in;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        .hero h1 {
            font-size: 48px;
            font-weight: 800;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }
        .properties {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            padding: 20px;
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
            transition: background 0.3s ease;
        }
        .btn:hover {
            background: #005bbb;
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
    <div class="hero">
        <h1>Find Your Dream Home</h1>
        <p>Explore thousands of properties for sale and rent.</p>
        <a href="search.php" class="btn">Start Searching</a>
    </div>
    <div class="properties">
        <?php foreach ($properties as $property): ?>
            <div class="property-card">
                <img src="assets/property_images/<?php echo htmlspecialchars($property['image']); ?>" alt="Property">
                <h3><?php echo htmlspecialchars($property['title']); ?></h3>
                <p><?php echo htmlspecialchars($property['city'] . ', ' . $property['state']); ?></p>
                <p>$<?php echo number_format($property['price'], 2); ?></p>
                <a href="property_details.php?id=<?php echo $property['id']; ?>" class="btn">View Details</a>
            </div>
        <?php endforeach; ?>
    </div>
    <script>
        // JavaScript for redirection
        document.querySelectorAll('.btn').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                window.location.href = this.getAttribute('href');
            });
        });
    </script>
</body>
</html>
