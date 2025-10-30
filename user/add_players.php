<?php
require_once __DIR__ . '/../vendor/autoload.php'; // adjust path if needed
include 'include/session.php';

$conn = $pdo->open();

$faker = Faker\Factory::create();

// Number of users to create
$totalUsers = 50;

$roles = ['user', 'agent'];
$types = ['0']; // optional — customize if needed

for ($i = 0; $i < $totalUsers; $i++) {
    $email = $faker->unique()->safeEmail;
    $username = $faker->unique()->userName;
    $password = password_hash('password123', PASSWORD_DEFAULT); // hashed password
    $type = $faker->randomElement($types);
    $role = $faker->randomElement($roles);
    $profile_set = $faker->numberBetween(0, 1); // 0 = incomplete, 1 = complete
    $firstname = $faker->firstName;
    $lastname = $faker->lastName;
    $address = $faker->address;
    $contact_info = $faker->phoneNumber;

    $stmt = $conn->prepare("
        INSERT INTO users (email, username, password, type, role, profile_set, firstname, lastname, address, contact_info)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->execute([
        $email,
        $username,
        $password,
        $type,
        $role,
        $profile_set,
        $firstname,
        $lastname,
        $address,
        $contact_info
    ]);
}

echo "✅ Successfully inserted $totalUsers fake users!";

$faker = Faker\Factory::create();

// Number of fake players to create
$totalPlayers = 50;

// Example static values or arrays for random selection
$gameTypes = ['Football', 'Futsal', 'Beach Soccer'];
$countries = ['Nigeria', 'England', 'Spain', 'Brazil', 'Germany', 'France'];
$genders = ['Male', 'Female'];
$clubs = ['Real Madrid', 'Chelsea', 'Arsenal', 'Barcelona', 'PSG', 'Manchester City', 'Juventus'];
$positions = [
    'Goalkeeper',
    'Right Back',
    'Left Back',
    'Center Back',
    'Defensive Midfielder',
    'Central Midfielder',
    'Attacking Midfielder',
    'Right Winger',
    'Left Winger',
    'Striker'
];
$footednessOptions = ['Right', 'Left', 'Both'];
$academyStatuses = ['Active', 'Inactive', 'Graduate'];

for ($i = 0; $i < $totalPlayers; $i++) {
    // Example: link players to random users (1–10)
    $userid = $i + 1;// $faker->numberBetween(1, 10);

    $game_type = $faker->randomElement($gameTypes);
    $country = $faker->randomElement($countries);
    $gender = $faker->randomElement($genders);
    $club = $faker->randomElement($clubs);

    // Choose 1–3 random positions
    $positionsPicked = $faker->randomElements($positions, $faker->numberBetween(1, 3));
    $positionsStr = implode(', ', $positionsPicked);

    $weight = $faker->numberBetween(60, 90); // kg
    $height = $faker->numberBetween(160, 200); // cm
    $footedness = $faker->randomElement($footednessOptions);
    $academy_status = $faker->randomElement($academyStatuses);
    $academy_name = $faker->company . " Academy";
    $description = $faker->sentence(12);
    $dob = $faker->date('Y-m-d', '2008-12-31'); // players under 17 maybe
    $newFileName = 'default_profile.jpg'; // placeholder image

    // Insert record
    $stmt = $conn->prepare("INSERT INTO players 
        (user_id, game_type, country, gender, club, positions, weight, description, height, footedness, academy_status, academy_name, profile_image, dob) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
    );

    $stmt->execute([
        $userid,
        $game_type,
        $country,
        $gender,
        $club,
        $positionsStr,
        $weight,
        $description,
        $height,
        $footedness,
        $academy_status,
        $academy_name,
        $newFileName,
        $dob
    ]);
}

echo "✅ Successfully inserted $totalPlayers dummy players!";
