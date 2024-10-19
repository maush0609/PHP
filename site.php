<?php

$servername = "localhost";
$username = "root"; 
$password = "";    
$dbname = "user_management";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] == 'add') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $stmt = $conn->prepare("INSERT INTO users (username, email) VALUES (?, ?)");
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $stmt->close();
}

if (isset($_GET['action']) && $_GET['action'] == 'delete') {
    $id = $_GET['id'];
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

$searchQuery = "";
if (isset($_POST['search'])) {
    $searchQuery = $_POST['search'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management System</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h2>User Management</h2>

        <form method="POST" action="">
            <input type="text" name="username" placeholder="Enter Username" required>
            <input type="email" name="email" placeholder="Enter Email" required>
            <button type="submit" name="action" value="add">Add User</button>
        </form>

        <form method="POST" action="">
            <input type="text" name="search" placeholder="Search User by Name" value="<?php echo $searchQuery; ?>">
            <button type="submit">Search</button>
        </form>


        <h3>User List</h3>
        <table>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Action</th>
            </tr>
            <?php
            $sql = "SELECT * FROM users WHERE username LIKE ?";
            $searchParam = "%" . $searchQuery . "%";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $searchParam);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['id'] . "</td>";
                echo "<td>" . $row['username'] . "</td>";
                echo "<td>" . $row['email'] . "</td>";
                echo "<td><a href='?action=delete&id=" . $row['id'] . "' onclick=\"return confirm('Are you sure you want to delete this user?');\">Delete</a></td>";
                echo "</tr>";
            }
            $stmt->close();
            ?>
        </table>
    </div>

</body>
</html>

<?php
$conn->close();
?>
