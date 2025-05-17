import api from './api.js';
import ui from './ui.js';

class Home {
    constructor() {
        this.loadingOverlay = document.getElementById('loading-overlay');
        this.heroBanner = document.getElementById('hero-banner');
        this.contentRows = document.getElementById('content-rows');
    }

    async loadHeroBanner() {
        try {
            const recent = await api.getRecents();
            if (recent && recent.length > 0) {
                const featured = recent[0];
                this.heroBanner.innerHTML = `
                    <img src="${featured.image}" alt="background-image" class="bg-image" />
                    <div class="dark-left"></div>
                    <div class="dark-bottom"></div>
                    <div class="billboard">
                        <img src="${featured.image}" alt="bg-img" />
                        <div class="title">${featured.title}</div>
                        <div class="description">${featured.description}</div>
                        <div class="buttons">
                            <button class="play-btn">
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

    async init() {
        try {
            // Load hero banner
            await this.loadHeroBanner();

            // Load content rows
            await ui.loadContent();

            // Hide loading overlay with fade effect
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

// Initialize Feather icons
feather.replace();

// Initialize home page
const home = new Home();
document.addEventListener('DOMContentLoaded', () => {
    home.init();
});
