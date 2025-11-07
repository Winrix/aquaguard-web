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

$ts = null;
try {
  $sql = "SELECT MAX(detected_at) AS ts FROM anomalias";
  $res = $conn->query($sql);
  if ($res && $row = $res->fetch_assoc()) {
    $ts = $row['ts'];
  }
} catch (Throwable $e) {
  http_response_code(500);
  echo json_encode(['error' => 'DB error']);
  exit;
}

if (!$ts) {
  // Fallback: si no hay anomalías, usar 24h atrás
  $ts = date('Y-m-d H:i:s', time() - 24 * 3600);
}

echo json_encode([
  'timestamp' => date(DATE_ATOM, strtotime($ts))
], JSON_UNESCAPED_UNICODE);