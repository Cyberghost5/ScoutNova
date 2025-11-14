<?php 
include 'include/session.php';

// Get player ID from URL parameter, default to top rated player if not specified
$player_id = isset($_GET['player_id']) ? $_GET['player_id'] : null;
$player_id = preg_replace('/\.php$/', '', $player_id);

$stmt = $conn->prepare("SELECT * FROM players WHERE uuid=:userid");
$stmt->execute(['userid'=>$player_id]);
$player = $stmt->fetch();

if (!$player) {
    $_SESSION['error'] = 'Player not found.';
    header('location: ../discover');
    exit();
}

// Fetch player data
if ($player_id) {
    // Fetch specific player
    $stmt = $conn->prepare("
        SELECT u.firstname, u.lastname, u.photo, 
               p.id, p.uuid, p.country, p.positions, p.dob, p.game_type, p.profile_image, p.club,
               ps.average_score, ps.pod_score 
        FROM users u 
        JOIN players p ON u.id = p.user_id 
        LEFT JOIN playerstats ps ON ps.player_id = p.id
        WHERE p.uuid = ?
    ");
    $stmt->execute([$player_id]);
    $player_data = $stmt->fetch(PDO::FETCH_ASSOC);
} else {
    // Fetch top rated player
    $stmt = $conn->prepare("
        SELECT u.firstname, u.lastname, u.photo, 
               p.id, p.uuid, p.country, p.positions, p.dob, p.game_type, p.profile_image, p.club,
               ps.average_score, ps.pod_score 
        FROM users u 
        JOIN players p ON u.id = p.user_id 
        LEFT JOIN playerstats ps ON ps.player_id = p.id
        WHERE ps.pod_score IS NOT NULL
        ORDER BY ps.pod_score DESC
        LIMIT 1
    ");
    $stmt->execute();
    $player_data = $stmt->fetch(PDO::FETCH_ASSOC);
}

// If no player found, use default data
if (!$player_data) {
    $player_data = [
        'id' => 0,
        'firstname' => 'Player',
        'lastname' => 'Not Found',
        'country' => 'Unknown',
        'positions' => 'Unknown',
        'dob' => '2000-01-01',
        'game_type' => 'football',
        'profile_image' => '',
        'photo' => '',
        'club' => 'FC',
        'average_score' => 75,
        'pod_score' => 75
    ];
}

// Calculate player stats and display data
$full_name = trim($player_data['firstname'] . ' ' . $player_data['lastname']);
$age = (new DateTime())->diff(new DateTime($player_data['dob']))->y;
$position = explode(',', $player_data['positions'])[0] ?? 'Player';
$position_short = strtoupper(substr(trim($position), 0, 3)); // First 3 letters
$overall_rating = $player_data['pod_score'] ?: $player_data['average_score'] ?: rand(75, 95);

// Generate stats based on overall rating for more realistic values
$base = $overall_rating - 10;
$pace = min(99, max(50, $base + rand(-5, 15)));
$shooting = min(99, max(50, $base + rand(-8, 12)));
$passing = min(99, max(50, $base + rand(-10, 10)));
$dribbling = min(99, max(50, $base + rand(-7, 13)));

// Get player image
$player_image = '';
if (!empty($player_data['profile_image'])) {
    $player_image = '../images/' . $player_data['profile_image'];
} elseif (!empty($player_data['photo'])) {
    $player_image = '../images/' . $player_data['photo'];
} else {
    $player_image = '../images/profile.jpg';
}

// Get country flag using flag-icons library
$country_codes = [
    'Nigeria' => 'ng',
    'Brazil' => 'br', 
    'England' => 'gb-eng',
    'United Kingdom' => 'gb',
    'Spain' => 'es',
    'Germany' => 'de',
    'France' => 'fr',
    'Ghana' => 'gh',
    'Argentina' => 'ar',
    'Italy' => 'it',
    'Portugal' => 'pt',
    'Netherlands' => 'nl',
    'Belgium' => 'be',
    'Croatia' => 'hr',
    'Poland' => 'pl',
    'Sweden' => 'se',
    'Norway' => 'no',
    'Denmark' => 'dk',
    'Switzerland' => 'ch',
    'Austria' => 'at',
    'Czech Republic' => 'cz',
    'Ukraine' => 'ua',
    'Russia' => 'ru',
    'Turkey' => 'tr',
    'Morocco' => 'ma',
    'Egypt' => 'eg',
    'South Africa' => 'za',
    'Algeria' => 'dz',
    'Tunisia' => 'tn',
    'Senegal' => 'sn',
    'Ivory Coast' => 'ci',
    'Cameroon' => 'cm',
    'Mali' => 'ml',
    'Burkina Faso' => 'bf',
    'Mexico' => 'mx',
    'USA' => 'us',
    'United States' => 'us',
    'Canada' => 'ca',
    'Colombia' => 'co',
    'Peru' => 'pe',
    'Chile' => 'cl',
    'Uruguay' => 'uy',
    'Paraguay' => 'py',
    'Ecuador' => 'ec',
    'Venezuela' => 've',
    'Japan' => 'jp',
    'South Korea' => 'kr',
    'Australia' => 'au',
    'New Zealand' => 'nz',
    'China' => 'cn',
    'India' => 'in',
    'Thailand' => 'th',
    'Indonesia' => 'id',
    'Malaysia' => 'my',
    'Singapore' => 'sg',
    'Philippines' => 'ph'
];

$country_code = $country_codes[$player_data['country']] ?? 'un'; // UN flag as default

// Get club initials
$club_initials = strtoupper(substr($player_data['club'] ?? 'FC', 0, 2));

?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?php echo $full_name; ?> - Player Card | ScoutNova</title>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/lipis/flag-icons@7.0.0/css/flag-icons.min.css" />
  <style>
    :root{
      --bg1:#071124; --bg2:#072733; --accent:#13e0d6; --card:#04213a; --card-edge:#0aa6b3; --muted:#97b3c0;
      --glass: rgba(255,255,255,0.03);
      font-family: 'Montserrat', system-ui, -apple-system, "Segoe UI", Roboto, 'Helvetica Neue', Arial;
    }
    html,body{height:100%;margin:0}
    body{
      display:flex;align-items:center;justify-content:center;
      background:linear-gradient(180deg,var(--bg1) 0%, #021f2a 60%);
      padding:20px;
    }

    .wrap{
      width:360px; max-width:92vw; color:white;
    }

    /* Header */
    .header{ text-align:center; padding:18px 0 10px;}
    .brand{ display:flex; align-items:center; justify-content:center; gap:12px; }
    .brand .logo{ width:34px; height:34px; border-radius:6px; background:linear-gradient(135deg,#fff,#cfe); display:flex;align-items:center;justify-content:center;color:#022; font-weight:800;}
    .title{ margin-top:6px; font-weight:800; letter-spacing:1px; color:var(--accent); font-size:22px }
    .subtitle{ font-size:28px; margin-top:2px; font-weight:800}

    /* Card */
    .card{
      margin-top:8px;
      background:linear-gradient(180deg, rgba(255,255,255,0.02), rgba(255,255,255,0.01));
      border-radius:14px; padding:14px; border:2px solid rgba(255,255,255,0.03);
      box-shadow: 0 8px 30px rgba(2,12,20,0.6);
      position:relative; overflow:hidden;
      border-image: linear-gradient(180deg,var(--card-edge), transparent) 1;
    }

    .card-inner{
      background: linear-gradient(180deg,#07314a 0%, #05223a 100%);
      border-radius:10px; padding:12px; position:relative; overflow:visible; border:3px solid rgba(0,0,0,0.3);
    }

    .rating-row{ display:flex; gap:12px; align-items:center; }
    .rating{ font-size:46px; font-weight:800; width:74px; text-align:center; }
    .pos{ font-size:14px; opacity:0.95 }

    .shield{
      flex:1; display:flex; align-items:center; gap:12px; justify-content:center; position:relative;
    }

    .player-portrait{
      width:170px; height:170px; border-radius:10px; overflow:hidden; display:flex; align-items:center; justify-content:center;
      background: linear-gradient(180deg,#0c84a0,#024c6a);
      box-shadow: 0 10px 30px rgba(0,0,0,0.6);
      transform:translateY(6px);
    }

    .player-portrait img{ width:100%; height:100%; object-fit:cover; display:block }

    /* name */
    .player-name{ text-align:center; font-weight:800; font-size:26px; margin-top:8px }

    /* Stats */
    .stats{ display:flex; gap:8px; justify-content:space-around; align-items:flex-end; margin-top:10px }
    .stat{ text-align:center; min-width:46px }
    .stat .num{ font-size:18px; font-weight:800 }
    .stat .lbl{ font-size:11px; color:var(--muted); margin-top:4px }

    /* footer badges */
    .badges{ display:flex; align-items:center; justify-content:center; gap:14px; margin-top:14px }
    .badge{ display:flex; align-items:center; gap:8px }
    .flag{ width:26px; height:16px; border-radius:2px; overflow:hidden }
    .club{ width:26px; height:26px; border-radius:4px; background:#fff; display:flex;align-items:center;justify-content:center; font-size:10px; font-weight:700; color:#000 }

    /* Big footer text */
    .footer{ text-align:center; margin-top:18px; font-weight:800; color:var(--accent); font-size:22px }
    .footer small{ display:block; color:#fff; opacity:0.9; font-size:12px; margin-top:6px }

    /* Share Controls */
    .share-controls{
      text-align: center; margin-top: 20px;
    }
    .share-btn{
      background: var(--accent); border: none; color: #000; padding: 10px 20px; 
      border-radius: 8px; font-weight: 600; cursor: pointer; margin: 5px;
      text-decoration: none; display: inline-block;
      transition: transform 0.2s;
    }
    .share-btn:hover{
      transform: scale(1.05);
      color: #000;
      text-decoration: none;
    }
    .back-btn{
      background: var(--card-edge); color: #fff;
    }

    /* Decorations */
    .card::before{
      content:''; position:absolute; right:-50px; top:-40px; width:220px; height:220px; 
      background:radial-gradient(circle at 30% 30%, rgba(19,224,214,0.14), transparent 40%);
      transform:rotate(20deg);
    }

    /* Responsive tweaks */
    @media (max-width:420px){
      .rating{ font-size:38px; width:64px }
      .player-portrait{ width:140px; height:140px }
      .player-name{ font-size:22px }
    }
  </style>
</head>
<body>
  <div class="wrap">
    <div class="header">
      <div class="brand">
        <div class="logo">SN</div>
        <div>
          <div class="title">ScoutNova</div>
          <div class="subtitle">Player of the Week</div>
        </div>
      </div>
    </div>

    <div class="card">
      <div class="card-inner">
        <div class="rating-row">
          <div class="rating">
            <div><?php echo $overall_rating; ?></div>
            <div class="pos"><?php echo $position_short; ?></div>
          </div>
          <div class="shield">
            <div class="player-portrait">
              <img src="<?php echo $player_image; ?>" alt="<?php echo $full_name; ?>" onerror="this.src='data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\' width=\'400\' height=\'400\'><rect width=\'100%\' height=\'100%\' fill=\'%230b667d\'/><text x=\'50%\' y=\'50%\' fill=\'white\' font-size=\'28\' font-family=\'Montserrat\' text-anchor=\'middle\' alignment-baseline=\'middle\'>Player Image</text></svg>'"/>
            </div>
          </div>
        </div>

        <div class="player-name"><?php echo $full_name; ?></div>

        <div class="stats" aria-hidden>
          <div class="stat">
            <div class="num"><?php echo $pace; ?></div>
            <div class="lbl">PAC</div>
          </div>
          <div class="stat">
            <div class="num"><?php echo $shooting; ?></div>
            <div class="lbl">SHO</div>
          </div>
          <div class="stat">
            <div class="num"><?php echo $passing; ?></div>
            <div class="lbl">PAS</div>
          </div>
          <div class="stat">
            <div class="num"><?php echo $dribbling; ?></div>
            <div class="lbl">DRI</div>
          </div>
        </div>

        <div class="badges">
          <div class="badge">
            <div class="flag">
              <span class="fi fi-<?php echo $country_code; ?>" style="font-size: 20px; border-radius: 3px;"></span>
            </div>
          </div>

          <div class="badge">
            <div class="club"><?php echo $club_initials; ?></div>
          </div>
        </div>

      </div>
    </div>

    <div class="footer"><?php echo strtoupper(date('F')); ?> <small>@ScoutNova</small></div>
    
    <!-- Share Controls -->
    <div class="share-controls">
      <button onclick="shareCard()" class="share-btn">
        📱 Share Card
      </button>
      <a href="top-players.php" class="share-btn back-btn">
        👑 View Top Players
      </a>
    </div>
  </div>

  <!-- JavaScript for sharing functionality -->
  <script>
    function shareCard() {
      const playerName = '<?php echo addslashes($full_name); ?>';
      const rating = '<?php echo $overall_rating; ?>';
      const url = window.location.href;
      
      if (navigator.share) {
        navigator.share({
          title: `${playerName} - Player Card`,
          text: `Check out ${playerName}'s amazing ${rating} rated player card on ScoutNova!`,
          url: url
        }).catch(console.error);
      } else if (navigator.clipboard) {
        navigator.clipboard.writeText(url).then(() => {
          alert('🔗 Link copied to clipboard! Share it with your friends.');
        });
      } else {
        prompt('Copy this link to share:', url);
      }
    }

    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
      if (e.key === 's' || e.key === 'S') {
        e.preventDefault();
        shareCard();
      }
    });
  </script>
</body>
</html>