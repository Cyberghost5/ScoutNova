<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ScoutNova - Video Feed</title>
    
    <!-- External Dependencies -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Custom Styles -->
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background: #000;
            overflow: hidden;
            height: 100vh;
        }

        .feed-container {
            height: 100vh;
            overflow: hidden; /* Disable scroll, we'll use swipe */
            scroll-snap-type: y mandatory;
            scroll-behavior: smooth;
            -webkit-overflow-scrolling: touch;
            position: relative;
        }

        .video-item {
            height: 100vh;
            width: 100%;
            position: absolute;
            top: 0;
            left: 0;
            scroll-snap-align: start;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #000;
            transform: translateY(100%);
            transition: transform 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        }

        .video-item.active {
            transform: translateY(0);
        }

        .video-item.prev {
            transform: translateY(-100%);
        }

        .video-item.next {
            transform: translateY(100%);
        }

        .video-player {
            width: 100%;
            height: 100%;
            object-fit: contain; /* Changed from cover to contain for better aspect ratio handling */
            cursor: pointer;
            background: #000;
        }

        /* Portrait videos - make them fill more space */
        .video-player.portrait {
            object-fit: cover;
            width: 100%;
            height: 100%;
        }

        /* Landscape videos - maintain aspect ratio with letterboxing */
        .video-player.landscape {
            object-fit: contain;
            width: 100%;
            max-height: 100vh;
        }

        /* Square videos */
        .video-player.square {
            object-fit: cover;
            width: 100%;
            height: 100%;
        }

        /* Swipe visual feedback */
        .video-item.swiping {
            transition: transform 0.1s ease-out;
        }

        /* Loading animation for aspect ratio detection */
        .video-player:not([data-aspect-detected]) {
            opacity: 0.8;
        }

        .video-player[data-aspect-detected] {
            opacity: 1;
            transition: opacity 0.3s ease;
        }

        /* Better mobile touch handling */
        .feed-container {
            touch-action: pan-y;
            user-select: none;
        }

        /* Prevent context menu on long press */
        .video-player {
            -webkit-touch-callout: none;
            -webkit-user-select: none;
            -khtml-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }

        .video-controls {
            position: absolute;
            bottom: 80px;
            left: 20px;
            right: 80px;
            color: white;
            z-index: 10;
        }

        .video-info h3 {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 8px;
            text-shadow: 0 1px 3px rgba(0,0,0,0.8);
        }

        .video-info p {
            font-size: 14px;
            line-height: 1.4;
            opacity: 0.9;
            text-shadow: 0 1px 3px rgba(0,0,0,0.8);
        }

        .action-sidebar {
            position: absolute;
            right: 15px;
            bottom: 120px;
            display: flex;
            flex-direction: column;
            gap: 25px;
            z-index: 10;
        }

        .action-btn {
            display: flex;
            flex-direction: column;
            align-items: center;
            color: white;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .action-btn:hover {
            transform: scale(1.1);
        }

        .action-btn i {
            font-size: 28px;
            margin-bottom: 5px;
            filter: drop-shadow(0 1px 3px rgba(0,0,0,0.8));
        }

        .action-btn span {
            font-size: 12px;
            font-weight: 500;
            text-shadow: 0 1px 3px rgba(0,0,0,0.8);
        }

        .profile-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            border: 3px solid white;
            object-fit: cover;
            cursor: pointer;
            filter: drop-shadow(0 2px 8px rgba(0,0,0,0.3));
        }

        .liked {
            color: #ff2d4a !important;
        }

        .loading-indicator {
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(10px);
            padding: 12px 24px;
            border-radius: 25px;
            color: white;
            font-size: 14px;
            display: none;
            z-index: 100;
        }

        .comments-overlay {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 60vh;
            background: linear-gradient(to top, rgba(0,0,0,0.95), rgba(0,0,0,0.8));
            backdrop-filter: blur(20px);
            color: white;
            transform: translateY(100%);
            transition: transform 0.3s ease-out;
            z-index: 1000;
            border-radius: 20px 20px 0 0;
        }

        .comments-overlay.show {
            transform: translateY(0);
        }

        .comments-header {
            padding: 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            display: flex;
            justify-content: between;
            align-items: center;
        }

        .comments-list {
            flex: 1;
            overflow-y: auto;
            padding: 20px;
        }

        .comment-item {
            display: flex;
            gap: 12px;
            margin-bottom: 20px;
        }

        .comment-avatar {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            object-fit: cover;
        }

        .comment-content h4 {
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 4px;
        }

        .comment-content p {
            font-size: 14px;
            line-height: 1.4;
            opacity: 0.9;
        }

        .comment-time {
            font-size: 12px;
            opacity: 0.6;
            margin-top: 4px;
        }

        .comment-input-area {
            padding: 20px;
            border-top: 1px solid rgba(255,255,255,0.1);
            display: flex;
            gap: 12px;
            align-items: center;
        }

        .comment-input {
            flex: 1;
            background: rgba(255,255,255,0.1);
            border: none;
            padding: 12px 16px;
            border-radius: 25px;
            color: white;
            font-size: 14px;
            outline: none;
        }

        .comment-input::placeholder {
            color: rgba(255,255,255,0.6);
        }

        .send-btn {
            background: #ff2d4a;
            border: none;
            padding: 12px;
            border-radius: 50%;
            color: white;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .send-btn:hover {
            background: #e02440;
            transform: scale(1.05);
        }

        .close-comments {
            position: absolute;
            top: 15px;
            right: 20px;
            background: none;
            border: none;
            color: white;
            font-size: 24px;
            cursor: pointer;
            padding: 5px;
        }

        .mute-indicator {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(0,0,0,0.6);
            color: white;
            padding: 15px;
            border-radius: 50%;
            font-size: 24px;
            opacity: 0;
            transition: opacity 0.3s ease;
            pointer-events: none;
        }

        .mute-indicator.show {
            opacity: 1;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .video-controls {
                bottom: 60px;
                left: 15px;
                right: 70px;
            }
            
            .action-sidebar {
                right: 10px;
                bottom: 100px;
                gap: 20px;
            }
            
            .action-btn i {
                font-size: 24px;
            }
            
            .profile-avatar {
                width: 45px;
                height: 45px;
            }
        }
    </style>
</head>

<body>
    <!-- Main Feed Container -->
    <div class="feed-container" id="feedContainer">
        <!-- Videos will be loaded here dynamically -->
    </div>

    <!-- Loading Indicator -->
    <div class="loading-indicator" id="loadingIndicator">
        <i class="bi bi-arrow-clockwise me-2"></i>
        Loading videos...
    </div>

    <!-- Comments Overlay -->
    <div class="comments-overlay" id="commentsOverlay">
        <button class="close-comments" onclick="closeComments()">
            <i class="bi bi-x"></i>
        </button>
        
        <div class="comments-header">
            <h3>Comments</h3>
        </div>
        
        <div class="comments-list" id="commentsList">
            <!-- Comments will be loaded here -->
        </div>
        
        <div class="comment-input-area">
            <input type="text" class="comment-input" id="commentInput" 
                   placeholder="Add a comment..." maxlength="500">
            <button class="send-btn" onclick="sendComment()">
                <i class="bi bi-send-fill"></i>
            </button>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        class VideoFeed {
            constructor() {
                this.currentPage = 1;
                this.isLoading = false;
                this.currentVideoId = null;
                this.videos = [];
                this.currentIndex = 0;
                this.touchStartY = 0;
                this.touchEndY = 0;
                this.isDragging = false;
                this.videoElements = [];
                
                this.init();
            }

            async init() {
                await this.loadVideos();
                this.setupEventListeners();
                this.showCurrentVideo(); // Show first video
            }

            setupEventListeners() {
                const container = document.getElementById('feedContainer');
                
                // Touch events for swipe
                container.addEventListener('touchstart', (e) => {
                    this.touchStartY = e.touches[0].clientY;
                    this.isDragging = true;
                });

                container.addEventListener('touchmove', (e) => {
                    if (!this.isDragging) return;
                    e.preventDefault(); // Prevent scrolling
                    this.touchEndY = e.touches[0].clientY;
                });

                container.addEventListener('touchend', () => {
                    if (!this.isDragging) return;
                    this.handleSwipe();
                    this.isDragging = false;
                });

                // Mouse events for desktop swipe simulation
                container.addEventListener('mousedown', (e) => {
                    this.touchStartY = e.clientY;
                    this.isDragging = true;
                });

                container.addEventListener('mousemove', (e) => {
                    if (!this.isDragging) return;
                    e.preventDefault();
                    this.touchEndY = e.clientY;
                });

                container.addEventListener('mouseup', () => {
                    if (!this.isDragging) return;
                    this.handleSwipe();
                    this.isDragging = false;
                });

                // Keyboard navigation
                document.addEventListener('keydown', (e) => {
                    if (e.key === 'ArrowUp') {
                        this.previousVideo();
                    } else if (e.key === 'ArrowDown') {
                        this.nextVideo();
                    }
                });

                // Comment input enter key
                document.getElementById('commentInput').addEventListener('keypress', (e) => {
                    if (e.key === 'Enter') {
                        this.sendComment();
                    }
                });

                // Close comments on overlay click
                document.getElementById('commentsOverlay').addEventListener('click', (e) => {
                    if (e.target.id === 'commentsOverlay') {
                        this.closeComments();
                    }
                });
            }

            handleSwipe() {
                const swipeThreshold = 50; // Minimum swipe distance
                const swipeDistance = this.touchStartY - this.touchEndY;

                if (Math.abs(swipeDistance) < swipeThreshold) return;

                if (swipeDistance > 0) {
                    // Swipe up - next video
                    this.nextVideo();
                } else {
                    // Swipe down - previous video
                    this.previousVideo();
                }
            }

            nextVideo() {
                if (this.currentIndex < this.videoElements.length - 1) {
                    this.currentIndex++;
                    this.showCurrentVideo();
                } else {
                    // Load more videos if at the end
                    this.loadVideos().then(() => {
                        if (this.videoElements.length > this.currentIndex + 1) {
                            this.currentIndex++;
                            this.showCurrentVideo();
                        }
                    });
                }
            }

            previousVideo() {
                if (this.currentIndex > 0) {
                    this.currentIndex--;
                    this.showCurrentVideo();
                }
            }

            showCurrentVideo() {
                this.videoElements.forEach((element, index) => {
                    element.classList.remove('active', 'prev', 'next');
                    
                    if (index === this.currentIndex) {
                        element.classList.add('active');
                        const video = element.querySelector('video');
                        if (video) {
                            video.play().catch(() => {});
                        }
                    } else if (index < this.currentIndex) {
                        element.classList.add('prev');
                        const video = element.querySelector('video');
                        if (video) video.pause();
                    } else {
                        element.classList.add('next');
                        const video = element.querySelector('video');
                        if (video) video.pause();
                    }
                });
            }

            async loadVideos() {
                if (this.isLoading) return;
                
                this.isLoading = true;
                this.showLoading(true);

                try {
                    const response = await fetch(`load-videos.php?page=${this.currentPage}&limit=5`);
                    const data = await response.json();

                    if (data.success && data.videos.length > 0) {
                        this.renderVideos(data.videos);
                        this.currentPage++;
                    }
                } catch (error) {
                    console.error('Error loading videos:', error);
                } finally {
                    this.isLoading = false;
                    this.showLoading(false);
                }
            }

            renderVideos(videos) {
                const container = document.getElementById('feedContainer');
                
                videos.forEach((video, index) => {
                    const videoElement = this.createVideoElement(video);
                    container.appendChild(videoElement);
                    this.videos.push(video);
                    this.videoElements.push(videoElement);
                });

                // If this is the first batch of videos, show the first one
                if (this.videoElements.length === videos.length) {
                    this.showCurrentVideo();
                }
            }

            createVideoElement(video) {
                const div = document.createElement('div');
                div.className = 'video-item';
                
                const videoElement = document.createElement('video');
                videoElement.className = 'video-player';
                videoElement.preload = 'metadata';
                videoElement.loop = true;
                videoElement.muted = true;
                videoElement.playsInline = true;
                videoElement.poster = video.thumbnail_url || '/assets/images/video-placeholder.jpg';
                
                // Add aspect ratio detection
                videoElement.addEventListener('loadedmetadata', () => {
                    this.detectAndApplyAspectRatio(videoElement);
                });
                
                videoElement.onclick = () => this.toggleMute(videoElement);
                
                const source = document.createElement('source');
                source.src = video.file_url;
                source.type = 'video/mp4';
                videoElement.appendChild(source);
                
                div.innerHTML = `
                    <div class="mute-indicator" id="muteIndicator-${video.id}">
                        <i class="bi bi-volume-mute-fill"></i>
                    </div>

                    <div class="video-controls">
                        <div class="video-info">
                            <h3>@${video.username}</h3>
                            <p>${video.description}</p>
                        </div>
                    </div>

                    <div class="action-sidebar">
                        <div class="action-btn profile-btn">
                            <img src="${video.avatar || '/assets/images/default-avatar.png'}" 
                                 alt="Profile" class="profile-avatar">
                        </div>

                        <div class="action-btn like-btn" onclick="videoFeed.toggleLike(${video.id}, this)">
                            <i class="bi ${video.user_liked ? 'bi-heart-fill liked' : 'bi-heart'}"></i>
                            <span>${video.likes_count}</span>
                        </div>

                        <div class="action-btn comment-btn" onclick="videoFeed.openComments(${video.id})">
                            <i class="bi bi-chat"></i>
                            <span>${video.comments_count}</span>
                        </div>

                        <div class="action-btn share-btn" onclick="videoFeed.shareVideo(${video.id})">
                            <i class="bi bi-share"></i>
                            <span>Share</span>
                        </div>
                    </div>
                `;
                
                // Insert video element at the beginning
                div.insertBefore(videoElement, div.firstChild);
                
                return div;
            }

            detectAndApplyAspectRatio(videoElement) {
                const aspectRatio = videoElement.videoWidth / videoElement.videoHeight;
                
                // Remove existing aspect ratio classes
                videoElement.classList.remove('portrait', 'landscape', 'square');
                
                if (aspectRatio < 0.8) {
                    // Portrait video (height > width)
                    videoElement.classList.add('portrait');
                } else if (aspectRatio > 1.2) {
                    // Landscape video (width > height)
                    videoElement.classList.add('landscape');
                } else {
                    // Square-ish video
                    videoElement.classList.add('square');
                }
                
                // Mark as detected for CSS transition
                videoElement.setAttribute('data-aspect-detected', 'true');
            }

            handleScroll() {
                const container = document.getElementById('feedContainer');
                const videos = container.querySelectorAll('video');
                
                let playingVideo = null;
                
                videos.forEach((video, index) => {
                    const rect = video.getBoundingClientRect();
                    const isVisible = rect.top < window.innerHeight * 0.5 && 
                                    rect.bottom > window.innerHeight * 0.5;
                    
                    if (isVisible && !playingVideo) {
                        video.play().catch(() => {});
                        playingVideo = video;
                        this.currentIndex = index;
                    } else {
                        video.pause();
                    }
                });

                // Infinite scroll trigger
                const scrollHeight = container.scrollHeight;
                const scrollTop = container.scrollTop;
                const clientHeight = container.clientHeight;
                
                if (scrollTop + clientHeight >= scrollHeight - 1000) {
                    this.loadVideos();
                }
            }

            toggleMute(videoElement) {
                const isMuted = videoElement.muted;
                videoElement.muted = !isMuted;
                
                // Show mute indicator
                const videoId = this.videos[this.currentIndex]?.id;
                if (videoId) {
                    const indicator = document.getElementById(`muteIndicator-${videoId}`);
                    if (indicator) {
                        indicator.querySelector('i').className = isMuted ? 
                            'bi bi-volume-up-fill' : 'bi bi-volume-mute-fill';
                        indicator.classList.add('show');
                        setTimeout(() => indicator.classList.remove('show'), 1000);
                    }
                }
            }

            async toggleLike(videoId, element) {
                try {
                    const response = await fetch('api/video-actions.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            action: 'toggle_like',
                            video_id: videoId
                        })
                    });

                    const data = await response.json();
                    
                    if (data.success) {
                        const icon = element.querySelector('i');
                        const count = element.querySelector('span');
                        
                        if (data.liked) {
                            icon.className = 'bi bi-heart-fill liked';
                        } else {
                            icon.className = 'bi bi-heart';
                        }
                        
                        count.textContent = data.likes_count;
                    }
                } catch (error) {
                    console.error('Error toggling like:', error);
                }
            }

            async openComments(videoId) {
                this.currentVideoId = videoId;
                const overlay = document.getElementById('commentsOverlay');
                
                try {
                    const response = await fetch('api/video-actions.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            action: 'get_comments',
                            video_id: videoId
                        })
                    });

                    const data = await response.json();
                    
                    if (data.success) {
                        this.renderComments(data.comments);
                        overlay.classList.add('show');
                    }
                } catch (error) {
                    console.error('Error loading comments:', error);
                }
            }

            renderComments(comments) {
                const container = document.getElementById('commentsList');
                
                if (comments.length === 0) {
                    container.innerHTML = '<p class="text-center opacity-60">No comments yet. Be the first to comment!</p>';
                    return;
                }
                
                container.innerHTML = comments.map(comment => `
                    <div class="comment-item">
                        <img src="${comment.avatar || '/assets/images/default-avatar.png'}" 
                             alt="User" class="comment-avatar">
                        <div class="comment-content">
                            <h4>${comment.username}</h4>
                            <p>${comment.text}</p>
                            <div class="comment-time">${comment.time_ago}</div>
                        </div>
                    </div>
                `).join('');
            }

            async sendComment() {
                const input = document.getElementById('commentInput');
                const text = input.value.trim();
                
                if (!text || !this.currentVideoId) return;
                
                try {
                    const response = await fetch('api/video-actions.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            action: 'add_comment',
                            video_id: this.currentVideoId,
                            text: text
                        })
                    });

                    const data = await response.json();
                    
                    if (data.success) {
                        this.renderComments(data.comments);
                        input.value = '';
                    }
                } catch (error) {
                    console.error('Error sending comment:', error);
                }
            }

            closeComments() {
                document.getElementById('commentsOverlay').classList.remove('show');
                this.currentVideoId = null;
            }

            shareVideo(videoId) {
                if (navigator.share) {
                    navigator.share({
                        title: 'Check out this video on ScoutNova',
                        url: `${window.location.origin}/video/${videoId}`
                    });
                } else {
                    // Fallback - copy to clipboard
                    navigator.clipboard.writeText(`${window.location.origin}/video/${videoId}`)
                        .then(() => alert('Link copied to clipboard!'));
                }
            }

            showLoading(show) {
                const indicator = document.getElementById('loadingIndicator');
                indicator.style.display = show ? 'block' : 'none';
            }

            debounce(func, wait) {
                let timeout;
                return function executedFunction(...args) {
                    const later = () => {
                        clearTimeout(timeout);
                        func(...args);
                    };
                    clearTimeout(timeout);
                    timeout = setTimeout(later, wait);
                };
            }
        }

        // Global functions for onclick handlers
        window.videoFeed = new VideoFeed();
        
        window.closeComments = () => videoFeed.closeComments();
        window.sendComment = () => videoFeed.sendComment();
    </script>
</body>
</html>