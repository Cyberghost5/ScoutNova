<?php
include 'include/session.php';

	header('Content-Type: text/csv; charset=utf-8');
	header('Content-Disposition: attachment; filename=Registration.csv');
	$conn = $pdo->open();
	$output = fopen("php://output", "w");
	fputcsv($output, array('S/N', 'Full Name', 'Phone Number', 'Address', 'How were you invited', 'Date Added'));
	$stmt = $conn->prepare("SELECT * FROM registration");
	$stmt->execute();
	$i = 0;
	$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
	foreach ($result as $row) {
		$i++;
		fputcsv($output, array($i,$row['name'],$row['phoneno'],$row['address'],$row['invited'],date('M d, Y', strtotime($row['date']))));
	}
	fclose($output);

?>
