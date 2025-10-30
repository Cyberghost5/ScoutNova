<?php
include 'include/session.php';

$conn = $pdo->open();

$chat_id = $_GET['chat_id'] ?? 0;

$query = "
  SELECT m.*, u.firstname, u.lastname, u.photo, u.role
  FROM messages m
  JOIN users u ON m.user_id = u.id
  WHERE m.chat_id = ?
  ORDER BY m.id ASC
";

$result = $conn->prepare($query);
$result->execute([$chat_id]);
$messages = [];

while ($row = $result->fetch()) {
    $messages[] = $row;
}

echo json_encode($messages);

// include 'db.php';

// $query = "
//   SELECT m.id, m.user_id, m.message, m.timestamp, u.firstname, u.lastname
//   FROM messages m
//   JOIN users u ON m.user_id = u.id
//   ORDER BY m.id ASC
// ";
// $result = $conn->query($query);
// $messages = [];

// while ($row = $result->fetch_assoc()) {
//   $messages[] = $row;
// }

// echo json_encode($messages);

