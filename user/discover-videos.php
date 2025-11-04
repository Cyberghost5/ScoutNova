<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>ScoutNova Feed</title>

<link href="https://cdn.jsdelivr.net/npm/@mdi/font/css/materialdesignicons.min.css" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>

<style>
body, html {
  margin: 0;
  padding: 0;
  height: 100%;
  overflow: hidden;
  background-color: #000;
  font-family: 'Poppins', sans-serif;
}
.video-feed {
  height: 100vh;
  scroll-snap-type: y mandatory;
  overflow-y: scroll;
}
.video-card {
  height: 100vh;
  width: 100%;
  scroll-snap-align: start;
  position: relative;
  display: flex;
  justify-content: center;
  align-items: center;
}
video {
  max-width: 100%;
  max-height: 100%;
  /* object-fit: contain; */
  object-fit: contain;
  background-color: #000;
}
.video-overlay {
  position: absolute;
  bottom: 5%;
  left: 5%;
  color: white;
}
.video-overlay h3 {
  font-weight: 600;
}
.video-actions {
  position: absolute;
  right: 5%;
  bottom: 15%;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 20px;
}
.video-actions i {
  font-size: 2rem;
  cursor: pointer;
  transition: transform 0.2s ease;
}
.video-actions i:hover {
  transform: scale(1.2);
}
.video-actions span {
  font-size: 0.9rem;
}
.loader {
  position: fixed;
  bottom: 10px;
  left: 50%;
  transform: translateX(-50%);
  color: white;
  display: none;
}
.comment-modal {
  position: fixed;
  bottom: 0;
  left: 0;
  width: 100%;
  background: rgba(10,10,10,0.95);
  color: white;
  padding: 15px;
  display: none;
  height: 50vh;
  overflow-y: auto;
}
.comment-modal input {
  width: 100%;
  padding: 10px;
  background: #111;
  border: none;
  color: white;
  outline: none;
  margin-top: 10px;
}
</style>
</head>

<body>

<div class="video-feed" id="videoFeed"></div>
<div class="loader" id="loader">Loading videos...</div>

<!-- Comments Modal -->
<div id="commentModal" class="comment-modal">
  <h2 class="text-lg font-bold mb-3">Comments</h2>
  <div id="commentList"></div>
  <input type="text" id="commentInput" placeholder="Write a comment..." onkeydown="if(event.key==='Enter') sendComment()">
</div>

<script>
let page = 1;
let loading = false;
let currentVideoId = null;

document.addEventListener('DOMContentLoaded', async () => {
  await loadVideos();
  handleScroll(); // Trigger autoplay for first video
});

// Load videos asynchronously
async function loadVideos() {
  if (loading) return;
  loading = true;
  document.getElementById('loader').style.display = 'block';

  const res = await fetch('load_videos.php?page=' + page);
  const html = await res.text();

  if (html.trim() !== '') {
    document.getElementById('videoFeed').insertAdjacentHTML('beforeend', html);
    page++;
  }

  document.getElementById('loader').style.display = 'none';
  loading = false;
}

// Autoplay visible video
function handleScroll() {
  const videos = document.querySelectorAll('video');
  let playing = false;

  videos.forEach(v => {
    const rect = v.getBoundingClientRect();
    const inView = rect.top < window.innerHeight * 0.75 && rect.bottom > window.innerHeight * 0.25;

    if (inView && !playing) {
      v.play().catch(() => {});
      playing = true; // Only one video plays at a time
    } else {
      v.pause();
    }
  });

  // Infinite scroll trigger
  if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight - 200) {
    loadVideos();
  }
}


// Toggle mute/unmute
function toggleMute(videoEl) {
  videoEl.muted = !videoEl.muted;
  videoEl.classList.toggle('ring-4');
  videoEl.classList.toggle('ring-white');
}

// Like button handler
async function toggleLike(videoId, el) {
  const res = await fetch('video_actions.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
    body: 'action=like&video_id=' + videoId
  });
  const data = await res.json();
  el.querySelector('span').innerText = data.likes;
  el.querySelector('i').classList.toggle('text-red-500');
}

// Open comments modal
async function openComments(videoId) {
  currentVideoId = videoId;
  const modal = document.getElementById('commentModal');
  modal.style.display = 'block';
  const res = await fetch('video_actions.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
    body: 'action=get_comments&video_id=' + videoId
  });
  const html = await res.text();
  document.getElementById('commentList').innerHTML = html;
}

// Send comment
async function sendComment() {
  const input = document.getElementById('commentInput');
  const text = input.value.trim();
  if (!text) return;

  const res = await fetch('video_actions.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
    body: 'action=add_comment&video_id=' + currentVideoId + '&text=' + encodeURIComponent(text)
  });
  const html = await res.text();
  document.getElementById('commentList').innerHTML = html;
  input.value = '';
}

document.addEventListener('scroll', handleScroll);
document.addEventListener('DOMContentLoaded', () => loadVideos());
</script>

</body>
</html>
