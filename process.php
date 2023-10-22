<?php
$host = getenv('RDS_HOSTNAME');
$username = getenv('RDS_USERNAME');
$password = getenv('RDS_PASSWORD');
$database = getenv('RDS_DB_NAME');

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create the employees table if it doesn't exist
$createTableSQL = "CREATE TABLE IF NOT EXISTS employees (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    dob DATE NOT NULL,
    empid VARCHAR(50) NOT NULL
)";

if ($conn->query($createTableSQL) !== TRUE) {
    die("Error creating table: " . $conn->error);
}

function sanitize_input($data) {
    global $conn;
    return mysqli_real_escape_string($conn, htmlspecialchars($data));
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["name"]) && isset($_POST["email"]) && isset($_POST["dob"]) && isset($_POST["empid"])) {
    $name = sanitize_input($_POST["name"]);
    $email = sanitize_input($_POST["email"]);
    $dob = sanitize_input($_POST["dob"]);
    $empid = sanitize_input($_POST["empid"]);

    $insertSQL = "INSERT INTO employees (name, email, dob, empid) VALUES ('$name', '$email', '$dob', '$empid')";

    if ($conn->query($insertSQL) !== TRUE) {
        echo json_encode(["error" => "Error: " . $insertSQL . "<br>" . $conn->error]);
    } else {
        echo json_encode(["success" => "Employee data submitted successfully!"]);
    }
}

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["empid"]) && isset($_GET["fetchData"])) {
    $empid = sanitize_input($_GET["empid"]);
    $fetchData = json_decode($_GET["fetchData"]);

    $fields = implode(", ", array_map(function ($field) {
        global $conn;
        return mysqli_real_escape_string($conn, $field);
    }, $fetchData));

    $selectSQL = "SELECT $fields FROM employees WHERE empid = '$empid'";
    $result = $conn->query($selectSQL);

    if ($result) {
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $output = [];
            foreach ($fetchData as $field) {
                $output[$field] = $row[$field];
            }
            echo json_encode($output);
        } else {
            echo json_encode(["error" => "Employee not found with the given Employee ID."]);
        }
    } else {
        echo json_encode(["error" => "Error: " . $conn->error]);
    }

    $result->free_result();
}

$conn->close();
?>
