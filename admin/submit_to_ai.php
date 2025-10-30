<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Process the form submission to submit data to AI
    $data = $_POST['data'] ?? '';

    if (!empty($data)) {
        // Here you would add your logic to submit $data to the AI system
        // For example, making an API call to the AI service

        // Assuming submission is successful
        $_SESSION['success'] = 'Data successfully submitted to AI.';
    } else {
        $_SESSION['error'] = 'No data provided for submission.';
    }

    header('Location: submit_to_ai.php');
    exit();
} else {
    // Display the submission form
    ?>
    <form method="POST" action="submit_to_ai.php">
        <label for="data">Data to submit to AI:</label><br>
        <textarea id="data" name="data" rows="10" cols="50"></textarea><br>
        <input type="submit" value="Submit to AI">
    </form>
    <?php
}

?>