<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');
session_start();
if (!isset($_SESSION['usuario'])) {
  http_response_code(401);
  echo json_encode(['error' => 'No autorizado']);
  exit;
}

require_once __DIR__ . '/../conexion.php';

$since = $_GET['since'] ?? '';
$sinceDt = false;
if ($since !== '') {
  $sinceDt = date_create($since);
}
if ($since === '' || $sinceDt === false) {
  // Fallback por seguridad: últimos 2 días si el parámetro no llega o es inválido
  $sinceSql = date('Y-m-d H:i:s', time() - 2 * 24 * 3600);
} else {
  $sinceSql = date('Y-m-d H:i:s', $sinceDt->getTimestamp());
}

$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 500;
if ($limit < 1 || $limit > 5000) $limit = 500;

try {
  $stmt = $conn->prepare(
    "SELECT tstamp, cloro, ph, humedad, temperatura
     FROM lecturas
     WHERE tstamp >= ?
     ORDER BY tstamp ASC
     LIMIT ?"
  );
  $stmt->bind_param("si", $sinceSql, $limit);
  $stmt->execute();
  $res = $stmt->get_result();

  $out = [];
  while ($row = $res->fetch_assoc()) {
    $out[] = [
      't' => date(DATE_ATOM, strtotime($row['tstamp'])),
      'cloro' => isset($row['cloro']) ? (float)$row['cloro'] : null,
      'ph' => isset($row['ph']) ? (float)$row['ph'] : null,
      'humedad' => isset($row['humedad']) ? (int)$row['humedad'] : null,
      'temperatura' => isset($row['temperatura']) ? (float)$row['temperatura'] : null
    ];
  }
  echo json_encode($out, JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
  http_response_code(500);
  echo json_encode(['error' => 'DB error']);
}