<?php
@include 'config.php';
session_start();

if (!isset($_SESSION['admin_name'])) {
    header('location: ad_login.php');
    exit();
}

// Handle deletion
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $delete = "DELETE FROM car_parts WHERE id='$id'";
    mysqli_query($conn, $delete);
    
    header('location: dashboard.php');
    exit();
}

// Handle adding new part
if (isset($_POST['submit'])) {
    $part_name = mysqli_real_escape_string($conn, $_POST['part_name']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $image = $_FILES['image']['name'];
    $target = "assets/img/" . basename($image);

    if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
        $insert = "INSERT INTO car_parts (name, image, price, description) VALUES ('$part_name', '$image', '$price', '$description')";
        mysqli_query($conn, $insert);
        header('location: dashboard.php');
        exit();
    }
}

// Fetch car parts
$query = "SELECT * FROM car_parts";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <style>
        body {
            background-image: url('assets/img/dash.jpg'); /* Background image */
            background-size: cover;
            background-position: center;
            margin: 0;
            padding: 20px;
            font-family: Arial, sans-serif;
            color: white;
        }
        .container {
            max-width: 800px;
            margin: auto;
            background: rgba(0, 0, 0, 0.8); /* Semi-transparent black background */
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.5);
        }
        h3 {
            text-align: center;
        }
        input[type="text"], input[type="file"], textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
            border: none;
            background: rgba(255, 255, 255, 0.1); /* Slightly transparent input */
            color: white;
        }
        input[type="submit"] {
            width: 100%;
            padding: 10px;
            background-color: #4CAF50; /* Green background */
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        input[type="submit"]:hover {
            background-color: #45a049; /* Darker green on hover */
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ccc;
        }
        th {
            background-color: #444; /* Dark background for headers */
        }
        tr:hover {
            background-color: rgba(255, 255, 255, 0.1); /* Highlight row on hover */
        }
        .btn {
            color: white; /* White text for buttons */
            background-color: #4CAF50; /* Green background */
            text-decoration: none;
            padding: 5px 10px;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .btn:hover {
            background-color: #45a049; /* Darker green on hover */
        }
        .btn-danger {
            background-color: red; /* Red color for delete button */
        }
        .btn-danger:hover {
            background-color: darkred; /* Darker red on hover */
        }
        a {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: white;
            text-decoration: none;
            background-color: #f44336; /* Logout button color */
            padding: 10px;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h3>Welcome, <?php echo htmlspecialchars($_SESSION['admin_name']); ?></h3>
        
        <h3>Add New Part</h3>
        <form action="" method="post" enctype="multipart/form-data" style="margin-bottom: 20px;">
            <input type="text" name="part_name" required placeholder="Part Name">
            <input type="file" name="image" required accept="image/*">
            <input type="text" name="price" required placeholder="Price">
            <textarea name="description" required placeholder="Description"></textarea>
            <input type="submit" name="submit" value="Add Part">
        </form>
        
        <h3>Existing Parts</h3>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Image</th>
                    <th>Price</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['id']); ?></td>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><img src="assets/img/<?php echo htmlspecialchars($row['image']); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>" style="width: 100px;"></td>
                        <td><?php echo htmlspecialchars($row['price']); ?></td>
                        <td><?php echo htmlspecialchars($row['description']); ?></td>
                        <td>
                            <a href="edit_part.php?id=<?php echo $row['id']; ?>" class="btn">Edit</a>
                            <a href="?delete=<?php echo $row['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this part?');">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <a href="logout.php">Logout</a>
    </div>
</body>
</html>
