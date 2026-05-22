
<?php
require_once '../config.php'; 
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  echo json_encode(['status'=>'error','message'=>'Method not allowed']); exit;
}

define('DB_HOST','localhost'); define('DB_USER','root');
define('DB_PASS','');          define('DB_NAME','bottleback');

$bottle_count  = isset($_POST['bottle_count'])  ? intval($_POST['bottle_count'])  : 1;
$reward_amount = isset($_POST['reward_amount']) ? intval($_POST['reward_amount']) : 1;
$status        = isset($_POST['status'])        ? trim($_POST['status'])          : 'Accepted';
$bin_level     = isset($_POST['bin_level'])     ? intval($_POST['bin_level'])     : null;
$node_id       = isset($_POST['node_id'])       ? trim($_POST['node_id'])         : 'node_001';

$allowed_status = ['Accepted','Rejected'];
if (!in_array($status, $allowed_status)) $status = 'Accepted';

try {
  $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
  if ($conn->connect_error) throw new Exception('DB error: '.$conn->connect_error);

  // Insert transaction
  $stmt = $conn->prepare("INSERT INTO transactions (bottle_count, reward_amount, status, node_id) VALUES (?,?,?,?)");
  $stmt->bind_param('iiss', $bottle_count, $reward_amount, $status, $node_id);
  $stmt->execute();
  $tx_id = $stmt->insert_id;
  $stmt->close();

  // Update bin level if provided
  if ($bin_level !== null) {
    $bin_level = max(0, min(100, $bin_level));
    $stmt2 = $conn->prepare("UPDATE machine_status SET bin_level=?, is_online=1, updated_at=NOW() WHERE node_id=?");
    $stmt2->bind_param('is', $bin_level, $node_id);
    $stmt2->execute();
    $stmt2->close();
  }

  $conn->close();
  echo json_encode(['status'=>'success','transaction_id'=>$tx_id,'timestamp'=>date('Y-m-d H:i:s')]);

} catch (Exception $e) {
  http_response_code(500);
  echo json_encode(['status'=>'error','message'=>$e->getMessage()]);
}
