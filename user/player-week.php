<?php 
include 'include/session.php';

// Fetch top 5 players with highest POD scores
$stmt = $conn->prepare("
    SELECT u.*, 
           p.*,
           ps.*
    FROM users u 
    JOIN players p ON u.id = p.user_id 
    LEFT JOIN playerstats ps ON ps.player_id = p.id
    WHERE ps.pod_score IS NOT NULL
    ORDER BY ps.pod_score DESC
    LIMIT 5
");
$stmt->execute();
$top_players = $stmt->fetchAll(PDO::FETCH_ASSOC);

// If no players found, use default data
if (empty($top_players)) {
    $top_players = [[
        'id' => 0,
        'firstname' => 'No',
        'lastname' => 'Players Found',
        'country' => 'Unknown',
        'positions' => 'Unknown',
        'dob' => '2000-01-01',
        'game_type' => 'football',
        'profile_image' => '',
        'photo' => '',
        'club' => 'FC',
        'average_score' => 75,
        'pod_score' => 75
    ]];
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>FUT-style Player Card (HTML/CSS)</title>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/lipis/flag-icons@7.0.0/css/flag-icons.min.css" />
  <style>
    :root{
      --bg1:#071124; --bg2:#072733; --accent:#13e0d6; --card:#04213a; --card-edge:#0aa6b3; --muted:#97b3c0;
      --glass: rgba(255,255,255,0.03);
      --gold: #FFD700;
      --silver: #C0C0C0;
      --bronze: #CD7F32;
      --neon-blue: #00FFFF;
      --neon-pink: #FF1493;
      --gradient-primary: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      --gradient-secondary: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
      --shadow-glow: 0 0 20px rgba(19, 224, 214, 0.3);
      font-family: 'Montserrat', system-ui, -apple-system, "Segoe UI", Roboto, 'Helvetica Neue', Arial;
    }
    
    @keyframes float {
      0%, 100% { transform: translateY(0px); }
      50% { transform: translateY(-10px); }
    }
    
    @keyframes glow {
      0%, 100% { box-shadow: 0 0 20px rgba(19, 224, 214, 0.3); }
      50% { box-shadow: 0 0 30px rgba(19, 224, 214, 0.6); }
    }
    
    @keyframes slideInUp {
      from {
        opacity: 0;
        transform: translateY(30px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
    
    @keyframes pulse {
      0% { transform: scale(1); }
      50% { transform: scale(1.05); }
      100% { transform: scale(1); }
    }
    
    @keyframes shimmer {
      0% { background-position: -200% 0; }
      100% { background-position: 200% 0; }
    }
    
    html,body{height:100%;margin:0}
    body{
      background: linear-gradient(135deg, #0c1327 0%, #1a1a2e 25%, #16213e 50%, #0f2027 75%, #203a43 100%);
      background-size: 400% 400%;
      animation: gradient 15s ease infinite;
      padding:20px;
      color: white;
      overflow-x: hidden;
      position: relative;
    }
    
    @keyframes gradient {
      0% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
      100% { background-position: 0% 50%; }
    }
    
    body::before {
      content: '';
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: radial-gradient(circle at 20% 20%, rgba(19, 224, 214, 0.1) 0%, transparent 50%),
                  radial-gradient(circle at 80% 80%, rgba(255, 20, 147, 0.1) 0%, transparent 50%),
                  radial-gradient(circle at 40% 60%, rgba(0, 255, 255, 0.05) 0%, transparent 50%);
      pointer-events: none;
      z-index: -1;
    }

    .container{
      max-width: 1200px;
      margin: 0 auto;
    }

    /* Header */
    .header{ 
      text-align:center; 
      padding:20px 0 30px;
      animation: slideInUp 1s ease-out;
    }
    .brand{ 
      display:flex; 
      align-items:center; 
      justify-content:center; 
      gap:12px; 
      margin-bottom: 20px;
      animation: float 3s ease-in-out infinite;
    }
    .brand .logo{ 
      width:50px; 
      height:50px; 
      border-radius:12px; 
      background: linear-gradient(135deg, var(--accent) 0%, var(--neon-blue) 100%); 
      display:flex;
      align-items:center;
      justify-content:center;
      color:#000; 
      font-weight:800;
      font-size: 20px;
      box-shadow: 0 8px 25px rgba(19, 224, 214, 0.4);
      animation: pulse 2s ease-in-out infinite;
      position: relative;
      overflow: hidden;
    }
    .brand .logo::before {
      content: '';
      position: absolute;
      top: -50%;
      left: -50%;
      width: 200%;
      height: 200%;
      background: linear-gradient(45deg, transparent 30%, rgba(255,255,255,0.3) 50%, transparent 70%);
      transform: rotate(45deg);
      animation: shimmer 3s infinite;
    }
    .title{ 
      font-weight:800; 
      letter-spacing:2px; 
      background: linear-gradient(45deg, var(--accent), var(--neon-pink), var(--neon-blue));
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
      font-size:28px;
      margin-bottom: 5px;
      text-shadow: 0 0 10px rgba(19, 224, 214, 0.5);
    }
    .subtitle{ 
      font-size:36px; 
      font-weight:800;
      margin-bottom: 10px;
      background: linear-gradient(45deg, #fff, var(--accent));
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
      text-shadow: 0 0 20px rgba(255, 255, 255, 0.3);
    }
    .description{
      color: var(--muted);
      font-size: 16px;
      margin-bottom: 20px;
    }

    /* Players Grid */
    .players-grid{
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
      gap: 30px;
      margin-top: 40px;
      perspective: 1000px;
    }
    
    .players-grid .player-card:nth-child(1) { animation: slideInUp 0.8s ease-out 0.1s both; }
    .players-grid .player-card:nth-child(2) { animation: slideInUp 0.8s ease-out 0.2s both; }
    .players-grid .player-card:nth-child(3) { animation: slideInUp 0.8s ease-out 0.3s both; }
    .players-grid .player-card:nth-child(4) { animation: slideInUp 0.8s ease-out 0.4s both; }
    .players-grid .player-card:nth-child(5) { animation: slideInUp 0.8s ease-out 0.5s both; }

    /* Card Styles */
    .player-card{
      background: linear-gradient(145deg, rgba(255,255,255,0.05), rgba(255,255,255,0.02));
      border-radius:20px; 
      padding:20px; 
      border:1px solid rgba(255,255,255,0.1);
      box-shadow: 0 15px 35px rgba(0,0,0,0.3);
      position:relative; 
      overflow:hidden;
      transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
      backdrop-filter: blur(20px);
      cursor: pointer;
    }
    
    .player-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: linear-gradient(135deg, 
        rgba(19, 224, 214, 0.1) 0%, 
        rgba(255, 20, 147, 0.05) 50%, 
        rgba(0, 255, 255, 0.1) 100%);
      opacity: 0;
      transition: opacity 0.3s ease;
      border-radius: 20px;
      pointer-events: none;
    }

    .player-card:hover{
      transform: translateY(-15px) rotateX(5deg);
      box-shadow: 0 25px 50px rgba(0,0,0,0.4), 
                  0 0 30px rgba(19, 224, 214, 0.3);
    }
    
    .player-card:hover::before {
      opacity: 1;
    }
    
    /* Rank-based special effects */
    .player-card:nth-child(1) {
      border: 2px solid var(--gold);
      box-shadow: 0 15px 35px rgba(255, 215, 0, 0.2);
    }
    .player-card:nth-child(1):hover {
      box-shadow: 0 25px 50px rgba(255, 215, 0, 0.3), 0 0 40px var(--gold);
      animation: glow 2s ease-in-out infinite;
    }
    
    .player-card:nth-child(2) {
      border: 2px solid var(--silver);
      box-shadow: 0 15px 35px rgba(192, 192, 192, 0.2);
    }
    .player-card:nth-child(2):hover {
      box-shadow: 0 25px 50px rgba(192, 192, 192, 0.3), 0 0 30px var(--silver);
    }
    
    .player-card:nth-child(3) {
      border: 2px solid var(--bronze);
      box-shadow: 0 15px 35px rgba(205, 127, 50, 0.2);
    }
    .player-card:nth-child(3):hover {
      box-shadow: 0 25px 50px rgba(205, 127, 50, 0.3), 0 0 30px var(--bronze);
    }

    .card-inner{
      background: linear-gradient(145deg, rgba(7, 49, 74, 0.8), rgba(5, 34, 58, 0.9));
      border-radius:16px; 
      padding:18px; 
      position:relative; 
      border:2px solid rgba(19, 224, 214, 0.2);
      backdrop-filter: blur(10px);
      transition: all 0.3s ease;
      overflow: hidden;
    }
    
    .card-inner::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: linear-gradient(45deg, 
        rgba(19, 224, 214, 0.05) 0%, 
        transparent 50%, 
        rgba(0, 255, 255, 0.05) 100%);
      opacity: 0;
      transition: opacity 0.3s ease;
      pointer-events: none;
    }
    
    .player-card:hover .card-inner {
      border-color: rgba(19, 224, 214, 0.4);
      box-shadow: inset 0 0 20px rgba(19, 224, 214, 0.1);
    }
    
    .player-card:hover .card-inner::before {
      opacity: 1;
    }

    .rank-badge{
      position: absolute;
      top: -10px;
      right: -10px;
      width: 45px;
      height: 45px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 900;
      font-size: 18px;
      z-index: 10;
      border: 3px solid var(--bg1);
      box-shadow: 0 5px 15px rgba(0,0,0,0.4);
      transition: all 0.3s ease;
      animation: pulse 3s ease-in-out infinite;
    }
    
    .player-card:nth-child(1) .rank-badge {
      background: linear-gradient(135deg, var(--gold), #FFA500);
      color: #000;
      box-shadow: 0 5px 20px rgba(255, 215, 0, 0.5);
    }
    
    .player-card:nth-child(2) .rank-badge {
      background: linear-gradient(135deg, var(--silver), #A8A8A8);
      color: #000;
      box-shadow: 0 5px 20px rgba(192, 192, 192, 0.5);
    }
    
    .player-card:nth-child(3) .rank-badge {
      background: linear-gradient(135deg, var(--bronze), #B87333);
      color: #fff;
      box-shadow: 0 5px 20px rgba(205, 127, 50, 0.5);
    }
    
    .player-card:nth-child(4) .rank-badge,
    .player-card:nth-child(5) .rank-badge {
      background: linear-gradient(135deg, var(--accent), var(--neon-blue));
      color: #000;
      box-shadow: 0 5px 20px rgba(19, 224, 214, 0.5);
    }
    
    .rank-badge:hover {
      transform: scale(1.1) rotate(10deg);
    }

    .rating-section{ 
      display:flex; 
      align-items:center; 
      gap:18px;
      margin-bottom: 18px;
    }
    .rating{ 
      font-size:48px; 
      font-weight:900; 
      text-align:center; 
      min-width: 80px;
      transition: all 0.3s ease;
      position: relative;
    }
    .rating .score{
      background: linear-gradient(45deg, var(--accent), var(--neon-blue));
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
      text-shadow: 0 0 20px rgba(19, 224, 214, 0.5);
      transition: all 0.3s ease;
    }
    .player-card:hover .rating {
      transform: scale(1.1);
    }
    .player-card:nth-child(1) .rating .score {
      background: linear-gradient(45deg, var(--gold), #FFA500);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }
    .rating .pos{ 
      font-size:14px; 
      opacity:0.9;
      margin-top: -8px;
      color: var(--muted);
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 1px;
    }

    .player-info{
      flex: 1;
    }

    .player-portrait{
      width:90px; 
      height:90px; 
      border-radius:12px; 
      overflow:hidden; 
      background: linear-gradient(135deg, rgba(12, 132, 160, 0.8), rgba(2, 76, 106, 0.9));
      box-shadow: 0 8px 25px rgba(0,0,0,0.4), inset 0 0 20px rgba(19, 224, 214, 0.2);
      margin-left: auto;
      transition: all 0.4s ease;
      position: relative;
      border: 2px solid rgba(19, 224, 214, 0.3);
    }
    
    .player-portrait::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: linear-gradient(45deg, transparent 40%, rgba(19, 224, 214, 0.2) 50%, transparent 60%);
      opacity: 0;
      transition: opacity 0.3s ease;
    }
    
    .player-card:hover .player-portrait {
      transform: scale(1.05) rotate(2deg);
      box-shadow: 0 15px 35px rgba(0,0,0,0.6), 0 0 30px rgba(19, 224, 214, 0.4);
    }
    
    .player-card:hover .player-portrait::before {
      opacity: 1;
    }

    .player-portrait img{ 
      width:100%; 
      height:100%; 
      object-fit:cover;
      transition: all 0.4s ease;
    }
    
    .player-card:hover .player-portrait img {
      transform: scale(1.1);
    }

    .player-name{ 
      font-weight:900; 
      font-size:22px; 
      margin-bottom:8px;
      background: linear-gradient(45deg, #fff, var(--accent));
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
      transition: all 0.3s ease;
      text-shadow: 0 2px 10px rgba(255, 255, 255, 0.1);
    }
    
    .player-card:hover .player-name {
      transform: translateX(5px);
      letter-spacing: 1px;
    }

    .player-details{
      color: var(--muted);
      font-size: 14px;
      margin-bottom: 12px;
      transition: all 0.3s ease;
    }
    
    .player-card:hover .player-details {
      color: var(--accent);
    }

    /* Stats */
    .stats{ 
      display:flex; 
      gap:10px; 
      justify-content:space-between; 
      margin:18px 0;
      transition: all 0.3s ease;
    }
    .stat{ 
      text-align:center; 
      flex: 1;
      transition: all 0.3s ease;
      padding: 8px 4px;
      border-radius: 8px;
      background: rgba(255,255,255,0.02);
      border: 1px solid rgba(255,255,255,0.05);
    }
    .stat:hover {
      background: rgba(19, 224, 214, 0.1);
      border-color: rgba(19, 224, 214, 0.3);
      transform: translateY(-2px);
    }
    .stat .num{ 
      font-size:18px; 
      font-weight:900;
      color: white;
      transition: all 0.3s ease;
    }
    .stat:hover .num {
      color: var(--accent);
      transform: scale(1.1);
    }
    .stat .lbl{ 
      font-size:11px; 
      color:var(--muted);
      margin-top:3px;
      font-weight: 600;
      letter-spacing: 0.5px;
    }

    /* Badges */
    .badges{ 
      display:flex; 
      align-items:center; 
      justify-content:space-between;
      margin-top:18px;
    }
    .badge{ 
      display:flex; 
      align-items:center; 
      gap:10px;
      transition: all 0.3s ease;
    }
    .badge:hover {
      transform: scale(1.05);
    }
    .flag{ 
      font-size: 20px;
      filter: drop-shadow(0 2px 5px rgba(0,0,0,0.3));
      transition: all 0.3s ease;
    }
    .badge:hover .flag {
      transform: scale(1.1);
    }
    .club{ 
      background: linear-gradient(135deg, #fff, #f0f0f0); 
      color: #000;
      padding: 6px 12px;
      border-radius:6px; 
      font-size:13px; 
      font-weight:800;
      min-width: 35px;
      text-align: center;
      box-shadow: 0 3px 10px rgba(0,0,0,0.2);
      transition: all 0.3s ease;
    }
    .badge:hover .club {
      background: linear-gradient(135deg, var(--accent), var(--neon-blue));
      transform: scale(1.05);
    }
    
    /* Footer */
    .footer {
      text-align: center;
      margin-top: 40px;
      color: var(--muted);
      font-weight: 600;
      font-size: 16px;
      letter-spacing: 1px;
      animation: slideInUp 1s ease-out 0.7s both;
    }

    /* Navigation */
    .navigation{
      text-align: center;
      margin-top: 50px;
      animation: slideInUp 1s ease-out 0.6s both;
    }
    .nav-btn{
      background: linear-gradient(135deg, var(--accent), var(--neon-blue)); 
      border: none; 
      color: #000; 
      padding: 15px 30px; 
      border-radius: 12px; 
      font-weight: 700; 
      cursor: pointer; 
      margin: 10px;
      transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
      text-decoration: none;
      display: inline-block;
      font-size: 14px;
      letter-spacing: 0.5px;
      text-transform: uppercase;
      position: relative;
      overflow: hidden;
      box-shadow: 0 8px 25px rgba(19, 224, 214, 0.3);
    }
    
    .nav-btn::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
      transition: left 0.5s;
    }
    
    .nav-btn:hover::before {
      left: 100%;
    }
    
    .nav-btn:hover{
      transform: translateY(-3px) scale(1.05);
      box-shadow: 0 15px 35px rgba(19,224,214,0.5);
    }
    .nav-btn.secondary{
      background: linear-gradient(135deg, var(--card-edge), var(--neon-pink)); 
      color: #fff;
      box-shadow: 0 8px 25px rgba(10, 166, 179, 0.3);
    }
    .nav-btn.secondary:hover {
      box-shadow: 0 15px 35px rgba(10, 166, 179, 0.5);
    }

    /* Responsive */
    @media (max-width:768px){
      .players-grid{
        grid-template-columns: 1fr;
      }
      .rating{ 
        font-size:36px;
      }
      .player-name{ 
        font-size:18px;
      }
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
          <div class="subtitle">Top 5 Players</div>
        </div>
      </div>
    </div>

    <div class="players-grid">
      <?php $rank = 1; foreach($top_players as $player): 
      
      // Calculate player stats and display data
      $full_name = trim($player['firstname'] . ' ' . $player['lastname']);
      $age = (new DateTime())->diff(new DateTime($player['dob']))->y;
      $position = explode(',', $player['positions'])[0] ?? 'Player';
      $position_short = strtoupper(substr(trim($position), 0, 3)); // First 3 letters
      $overall_rating = $player['pod_score'] ?: $player['average_score'] ?: rand(75, 95);

      // Generate stats based on overall rating for more realistic values
      $base = $overall_rating - 10;
      $pace = min(99, max(50, $base + rand(-5, 15)));
      $shooting = min(99, max(50, $base + rand(-8, 12)));
      $passing = min(99, max(50, $base + rand(-10, 10)));
      $dribbling = min(99, max(50, $base + rand(-7, 13)));

      // Get player image
      $player_image = '';
      if (!empty($player['profile_image'])) {
          $player_image = 'images/' . $player['profile_image'];
      } elseif (!empty($player['photo'])) {
          $player_image = 'images/' . $player['photo'];
      } else {
          $player_image = 'images/profile.jpg';
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

      $country_code = $country_codes[$player['country']] ?? 'un'; // UN flag as default

      // Get club initials
      $club_initials = strtoupper(substr($player['club'] ?? 'FC', 0, 2));
        
      ?>
      <div class="player-card">
        <div class="rank-badge"><?php echo $rank; ?></div>
        <div class="card-inner">
          <div class="rating-row">
            <div class="rating">
              <div><?php echo $overall_rating; ?></div>
              <div class="pos"><?php echo $position; ?></div>
            </div>
            <div class="shield">
              <div class="player-portrait">
                <img src="<?php echo $player_image; ?>" alt="<?php echo $player['username']; ?>" 
                     onerror="this.src='data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\' width=\'400\' height=\'400\'><rect width=\'100%\' height=\'100%\' fill=\'%230b667d\'/><text x=\'50%\' y=\'50%\' fill=\'white\' font-size=\'28\' font-family=\'Montserrat\' text-anchor=\'middle\' alignment-baseline=\'middle\'>Player Image</text></svg>'"/>
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
      <?php $rank++; endforeach; ?>
    </div>

    <div class="footer"><?php echo strtoupper(date('F')); ?> <small>@ScoutNova</small></div>
    
    <!-- Navigation Controls -->
    <div style="text-align: center; margin-top: 20px;">
      <button onclick="window.location.href='top-players.php'" style="background: var(--accent); border: none; color: #000; padding: 8px 16px; border-radius: 6px; font-weight: 600; cursor: pointer; margin: 5px;">
        📊 Full Rankings
      </button>
      <button onclick="window.location.href='player-card.php'" style="background: var(--card-edge); border: none; color: #fff; padding: 8px 16px; border-radius: 6px; font-weight: 600; cursor: pointer; margin: 5px;">
        🎯 Player Cards
      </button>
    </div>
  </div>

  <!-- JavaScript for enhanced functionality -->
  <script>
    // Add click handlers to player cards for navigation
    document.addEventListener('DOMContentLoaded', function() {
      const playerCards = document.querySelectorAll('.player-card');
      playerCards.forEach((card, index) => {
        card.style.cursor = 'pointer';
        card.addEventListener('click', function() {
          // Navigate to individual player card page
          const playerName = card.querySelector('.player-name').textContent;
          console.log(`Clicked on player: ${playerName}`);
          // You can add navigation logic here if needed
        });
      });
    });

    // Function for console debugging - show player stats
    function showPlayerStats(rank) {
      const cards = document.querySelectorAll('.player-card');
      if (cards[rank - 1]) {
        const card = cards[rank - 1];
        const name = card.querySelector('.player-name').textContent;
        const rating = card.querySelector('.rating div').textContent;
        console.log(`Player #${rank}: ${name} (Rating: ${rating})`);
      }
    }
    window.showPlayerStats = showPlayerStats;

    // Refresh page function
    function refreshTopPlayers() {
      window.location.reload();
    }
    window.refreshTopPlayers = refreshTopPlayers;
  </script>
</body>
</html>
