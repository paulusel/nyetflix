import api from './api.js';
import ui from './ui.js';
import { getStreamer } from './loader.js';

class Home {
    constructor() {
        this.loadingOverlay = document.getElementById('loading-overlay');
        this.heroBanner = document.getElementById('hero-banner');
        this.contentRows = document.getElementById('content-rows');
        this.videoPlayer = document.getElementById('video-player');
        this.video = document.getElementById('video');
        this.closeBtn = document.querySelector('.close-btn');
        this.hls = null;

        // event handlers
        this.closeBtn.addEventListener('click', () => this.closeVideo());
        this.video.addEventListener('ended', () => this.closeVideo());
        document.addEventListener('playVideo', (e) => this.playVideo(e.detail.movieId));
    }

    async loadHeroBanner() {
        try {
            const recentRes = await api.getRecents();
            if (recentRes.movies && recentRes.movies.length > 0) {
                const featured = recentRes.movies[0];
                this.heroBanner.innerHTML = `
                    <img src="${featured.thumbnail}" alt="background-image" class="bg-image" />
                    <div class="dark-left"></div>
                    <div class="dark-bottom"></div>
                    <div class="billboard">
                        <img src="${featured.thumbnail}" alt="bg-img" />
                        <div class="title">${featured.title}</div>
                        <div class="description">${featured.description}</div>
                        <div class="buttons">
                            <button class="play-btn" data-id="${featured.movie_id}">
                                <i class="fa fa-play"></i>
                                <div>Play</div>
                            </button>
                            <button class="info-btn">
                                <i data-feather="info"></i>
                                <div>More Info</div>
                            </button>
                        </div>
                    </div>
                `;
                feather.replace();

                // Add event listener for hero banner play button
                const playBtn = this.heroBanner.querySelector('.play-btn');
                if (playBtn) {
                    playBtn.addEventListener('click', () => this.playVideo(featured.movie_id));
                }
            }
        } catch (error) {
            console.error('Failed to load hero banner:', error);
            this.heroBanner.innerHTML = `
                <div class="error-message">
                    Failed to load featured content. Please try again later.
                </div>
            `;
        }
    }

    async playVideo(movie_id) {
        try {
            this.videoPlayer.style.display = 'block';
            document.body.style.overflow = 'hidden';

            if (this.hls) {
                this.hls.destroy();
            }

            const response = await api.watchMovie(movie_id);
            this.hls = getStreamer('/nyetflix/api/playMovie.php', response.movie.movie_id, response.movie.position);
            this.hls.attachMedia(this.video);
            this.hls.loadSource(`dummy://${movie_id}.m3u8`);
            this.hls.on(Hls.Events.MANIFEST_PARSED, () => {
                this.video.play();
            });
            this.hls.on(Hls.Events.ERROR, (event, data) => {
                console.error('Error occurred in streamer:', data);
            });
        } catch (error) {
            console.error('Failed to play video:', error);
            this.closeVideo();
        }
    }

    closeVideo() {
        this.video.pause();
        this.video.src = '';
        this.videoPlayer.style.display = 'none';
        document.body.style.overflow = '';
        if (this.hls) {
            this.hls.destroy();
            this.hls = null;
        }
    }

    async init() {
        try {
            await this.loadHeroBanner();

            await ui.loadContent();

            this.loadingOverlay.style.opacity = '0';
            setTimeout(() => {
                this.loadingOverlay.style.display = 'none';
            }, 500);
        } catch (error) {
            console.error('Initialization failed:', error);
            this.loadingOverlay.innerHTML = `
                <div class="error-message">
                    Failed to load content. Please try again later.
                </div>
            `;
        }
    }
}

feather.replace();

const home = new Home();
document.addEventListener('DOMContentLoaded', () => {
    home.init();
});
