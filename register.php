<?php
require "conexion.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST["nombre"];
    $correo = $_POST["correo"];
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO usuarios (nombre, correo, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $nombre, $correo, $password);

    if ($stmt->execute()) {
        echo "<!DOCTYPE html><html><head><meta charset='utf-8'></head><body><script>alert('Registro exitoso, ahora inicie sesión'); window.location.href='login.html';</script></body></html>";
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}
$conn->close();
?>
