<?php
$host = "rds-primary.cwhwvbizzssn.us-east-1.rds.amazonaws.com";
$db   = "myDB";
$user = "admin";
$pass = "12345678";

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$db;charset=utf8",
        $user,
        $pass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

$id = $_GET['id'] ?? null;

/* Tổng dân số */
$totalStmt = $pdo->query("SELECT SUM(Population) AS total FROM city");
$totalPopulation = $totalStmt->fetch()['total'];

if ($id) {
    // Chi tiết thành phố
    $stmt = $pdo->prepare("SELECT * FROM city WHERE ID = ?");
    $stmt->execute([$id]);
    $city = $stmt->fetch();

    if (!$city) {
        die("City not found");
    }
} else {
    // Danh sách thành phố
    $stmt = $pdo->query("
        SELECT ID, Name, CountryCode, District, Population
        FROM city 
        ORDER BY Population DESC
    ");
    $cities = $stmt->fetchAll();
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>City Information</title>
    <style>
        body { font-family: Arial; margin: 40px; }
        table { border-collapse: collapse; width: 75%; }
        th, td { border: 1px solid #ccc; padding: 8px; }
        th { background: #f2f2f2; }
        a { text-decoration: none; color: blue; }
    </style>
</head>
<body>

<h1>🌍 City Database</h1>
<h2>👥 Tổng dân số: <?php echo number_format($totalPopulation); ?></h2>

<?php if ($id): ?>

    <!-- CHI TIẾT THÀNH PHỐ -->
    <h3>🏙️ <?php echo htmlspecialchars($city['Name']); ?></h3>
    <ul>
        <li><b>Country Code:</b> <?php echo $city['CountryCode']; ?></li>
        <li><b>District:</b> <?php echo $city['District']; ?></li>
        <li><b>Population:</b> <?php echo number_format($city['Population']); ?></li>
    </ul>

    <p><a href="index.php">⬅ Quay lại danh sách</a></p>

<?php else: ?>

    <!-- DANH SÁCH THÀNH PHỐ -->
    <table>
        <tr>
            <th>City</th>
            <th>Country</th>
            <th>Population</th>
            <th>Detail</th>
        </tr>
        <?php foreach ($cities as $c): ?>
        <tr>
            <td><?php echo htmlspecialchars($c['Name']); ?></td>
            <td><?php echo $c['CountryCode']; ?></td>
            <td><?php echo number_format($c['Population']); ?></td>
            <td>
                <a href="?id=<?php echo $c['ID']; ?>">View</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>
</body>
</html>
