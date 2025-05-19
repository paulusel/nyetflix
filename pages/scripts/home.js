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
        this.currentContent = 'home';

        // event handlers
        this.closeBtn.addEventListener('click', () => { this.closeVideo(); });
        this.video.addEventListener('ended', () => { this.movieEnded(); });
        this.video.addEventListener('timeupdate', () => { this.reportProgress(); });
        document.addEventListener('playVideo', (e) => this.playVideo(e.detail.movieId));

        // setup navigation
        this.setupNavigation();
    }

    setupNavigation() {
        document.querySelectorAll('.nav-items[data-content]').forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const contentType = e.currentTarget.dataset.content;
                this.loadContent(contentType);
            });
        });
    }

    async loadContent(contentType) {
        this.currentContent = contentType;
        this.showLoading();

        try {
            let content = [];
            let title = '';

            switch (contentType) {
                case 'home':
                    const [recentRes, filmsRes, seriesRes, myListRes] = await Promise.all([
                        api.getRecents(),
                        api.getFilms(),
                        api.getSeries(),
                        api.getMyList()
                    ]);
                    content = [
                        { title: 'Get In on the Action', items: recentRes.movies },
                        { title: 'New on Netflix', items: filmsRes.films },
                        { title: 'Golden Globe Award-winning TV Comedies', items: seriesRes.series },
                        { title: 'My List', items: myListRes.items }
                    ];
                    break;

                case 'series':
                    const seriesData = await api.getSeries();
                    content = [{ title: 'TV Shows', items: seriesData.series }];
                    break;

                case 'films':
                    const filmsData = await api.getFilms();
                    content = [{ title: 'Movies', items: filmsData.films }];
                    break;

                case 'mylist':
                    const myListData = await api.getMyList();
                    content = [{ title: 'My List', items: myListData.items }];
                    break;

                case 'latest':
                    const latestRes = await api.getRecents();
                    content = [{ title: 'Latest', items: latestRes.movies }];
                    break;
            }

            this.contentRows.innerHTML = '';
            this.heroBanner.style.display = contentType === 'mylist' ? 'none' : 'block';

            // hero banner is not loaded in mylist page
            if (contentType !== 'mylist' && content[0]?.items?.length > 0) {
                await this.loadHeroBanner(content[0].items);
            }

            content.forEach(section => {
                if (section.items && section.items.length > 0) {
                    this.contentRows.appendChild(ui.createContentRow(section.title, section.items));
                }
            });

            // setup event listeners for new content
            ui.setupMovieCardListeners();

        } catch (error) {
            console.error('Failed to load content:', error);
            this.showError('Failed to load content. Please try again later.');
        } finally {
            this.hideLoading();
        }
    }

    async loadHeroBanner(items) {
        try {
            // randomly pick one movie for banner
            const featured = items[Math.floor(Math.random() * items.length)];
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
                            <i data-feather="play"></i>
                            <div>Play</div>
                        </button>
                        <button class="info-btn">
                            <i data-feather="info"></i>
                            <div>More Info</div>
                        </button>
                    </div>
                </div>
            `;
            window.feather.replace();

            const playBtn = this.heroBanner.querySelector('.play-btn');
            if (playBtn) {
                playBtn.addEventListener('click', () => this.playVideo(featured.movie_id));
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

    showLoading() {
        this.loadingOverlay.style.display = 'flex';
        this.loadingOverlay.style.opacity = '1';
    }

    hideLoading() {
        this.loadingOverlay.style.opacity = '0';
        setTimeout(() => {
            this.loadingOverlay.style.display = 'none';
        }, 500);
    }

    showError(message) {
        this.contentRows.innerHTML = `
            <div class="error-message">
                ${message}
            </div>
        `;
    }

    async movieEnded() {
        const data = this.video._customData;
        const response = await api.getNextPlay(data.movie_id);
        this.playVideo(response.movie.movie_id);
    }

    async reportProgress() {
        const data = this.video._customData;
        if(!data) return;

        const currentPosition = this.video.currentTime;
        const positionDelta = Math.abs(currentPosition - data.lastReportedPosition);

        if (positionDelta >= 5) {
            await api.reportProgress(data.movie_id, currentPosition);
            data.lastReportedPosition = currentPosition;
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

            this.video._customData = {
                movie_id : response.movie.movie_id,
                lastReportedPosition : 0
            };
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
        this.video._customData = null;
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
            await this.loadContent('home');
        } catch (error) {
            console.error('Initialization failed:', error);
            this.showError('Failed to load content. Please try again later.');
        }
    }
}

const home = new Home();
document.addEventListener('DOMContentLoaded', () => {
    home.init();
});
