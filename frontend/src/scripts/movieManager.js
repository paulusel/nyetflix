const MovieManager = {
    async fetchMovies(category) {
        try {
            const token = localStorage.getItem('token');
            if (!token) {
                throw new Error('No authentication token found');
            }

            const response = await fetch('/api/home.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`
                }
            });


            if (!response.ok) {
                throw new Error('Failed to fetch movies');
            }

            const data = await response.json();
            if (!data.ok) {
                throw new Error(data.message || 'Failed to fetch movies');
            }

            if (category) {
                return data.categories.find(cat => cat.title === category)?.movies || [];
            }
            return data.categories.flatMap(cat => cat.movies);
        } catch (error) {
            console.error('Error fetching movies:', error);
            return [];
        }
    },

    createMovieElement(movie) {
        const content = document.createElement('div');
        content.className = 'content';
        
        // Create front card
        const frontCard = document.createElement('div');
        frontCard.className = 'wrapper__front';
        const frontImg = document.createElement('img');
        frontImg.src = movie.thumbnail;
        frontImg.alt = movie.title;
        frontCard.appendChild(frontImg);
        
        // Create back card with details
        const backCard = document.createElement('div');
        backCard.className = 'wrapper__back';
        
        // Back card header with image
        const backHeader = document.createElement('div');
        backHeader.className = 'card__header';
        const backImg = document.createElement('img');
        backImg.src = movie.thumbnail;
        backImg.alt = movie.title;
        backHeader.appendChild(backImg);
        
        // Back card body
        const cardBody = document.createElement('div');
        cardBody.className = 'card__body';
        
        // Buttons container
        const buttonsContainer = document.createElement('div');
        buttonsContainer.className = 'flex justify-between items-center';
        
        const leftButtons = document.createElement('div');
        
        // Play button
        const playButton = document.createElement('button');
        playButton.className = 'btn btn--transparent btn--circle';
        playButton.innerHTML = `
            <svg class="card__icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                <path d="M21.44 10.72L5.96 2.98A1.38 1.38 0 004 4.213v15.474a1.373 1.373 0 002 1.233l15.44-7.74a1.38 1.38 0 000-2.467v.007z" />
            </svg>
        `;
        
        // Add to list button
        const addButton = document.createElement('button');
        addButton.className = 'btn btn--transparent btn--circle';
        addButton.innerHTML = `
            <svg class="card__icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                <path d="M12 0a1.5 1.5 0 011.5 1.5v9h9a1.5 1.5 0 110 3h-9v9a1.5 1.5 0 11-3 0v-9h-9a1.5 1.5 0 110-3h9v-9A1.5 1.5 0 0112 0z" />
            </svg>
        `;
        
        // Like button
        const likeButton = document.createElement('button');
        likeButton.className = 'btn btn--transparent btn--circle';
        likeButton.innerHTML = `
            <svg xmlns="http://www.w3.org/2000/svg" class="card__icon" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                <path d="M4.875 10.5h-3.75C.504 10.5 0 11.004 0 11.625v11.25C0 23.496.504 24 1.125 24h3.75C5.496 24 6 23.496 6 22.875v-11.25c0-.621-.504-1.125-1.125-1.125zM3 22.125a1.125 1.125 0 110-2.25 1.125 1.125 0 010 2.25zM18 3.818c0 1.988-1.217 3.104-1.56 4.432h4.768c1.566 0 2.785 1.3 2.792 2.723.004.841-.354 1.746-.911 2.306l-.005.006c.46 1.094.386 2.626-.437 3.725.407 1.213-.003 2.705-.768 3.504.202.825.106 1.527-.288 2.092C20.634 23.981 18.263 24 16.258 24h-.133c-2.264 0-4.116-.825-5.605-1.487-.748-.333-1.726-.745-2.468-.759a.563.563 0 01-.552-.562v-10.02c0-.15.06-.294.167-.4 1.857-1.835 2.655-3.777 4.177-5.302.694-.695.947-1.745 1.19-2.76.209-.868.645-2.71 1.591-2.71C15.75 0 18 .375 18 3.818z" />
            </svg>
        `;
        
        // Dislike button
        const dislikeButton = document.createElement('button');
        dislikeButton.className = 'btn btn--transparent btn--circle';
        dislikeButton.innerHTML = `
            <svg xmlns="http://www.w3.org/2000/svg" class="card__icon" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                <path d="M21.856 10.561a4.353 4.353 0 00-.421-2.952 4.52 4.52 0 00-.813-3.14C20.578 1.848 18.943 0 15.328 0h-1.041C9.43 0 7.922 1.875 6 1.875h-.508A1.494 1.494 0 004.5 1.5h-3A1.5 1.5 0 000 3v11.25a1.5 1.5 0 001.5 1.5h3c.555 0 1.04-.302 1.299-.75h.33c.898.795 2.157 2.843 3.223 3.91.641.64.476 5.09 3.364 5.09 2.7 0 4.466-1.497 4.466-4.91 0-.862-.184-1.58-.415-2.18h1.71c2.278 0 4.023-1.95 4.023-4.012 0-.898-.233-1.64-.644-2.337zM3 13.875a1.125 1.125 0 110-2.25 1.125 1.125 0 010 2.25zm15.477.784h-4.874c0 1.773 1.329 2.596 1.329 4.432 0 1.113 0 2.659-2.216 2.659-.886-.886-.443-3.102-1.773-4.432-1.245-1.245-3.102-4.568-4.431-4.568H6V4.023c2.513 0 4.688-1.773 8.046-1.773h1.772c1.665 0 2.851.803 2.49 3.09.713.382 1.243 1.708.654 2.698 1.012.956.876 2.394.244 3.076.443 0 1.048.886 1.044 1.772-.004.887-.783 1.773-1.773 1.773z" />
            </svg>
        `;
        
        leftButtons.appendChild(playButton);
        leftButtons.appendChild(addButton);
        leftButtons.appendChild(likeButton);
        leftButtons.appendChild(dislikeButton);
        
        // More info button
        const moreInfoButton = document.createElement('button');
        moreInfoButton.className = 'btn btn--transparent btn--circle';
        moreInfoButton.innerHTML = `
            <svg xmlns="http://www.w3.org/2000/svg" class="card__icon" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                <path fill-rule="evenodd" d="M2.469 6.969a.75.75 0 011.062 0L12 15.439l8.469-8.47a.75.75 0 111.062 1.062l-9 9a.75.75 0 01-1.062 0l-9-9a.75.75 0 010-1.062z" clip-rule="evenodd" />
            </svg>
        `;
        
        buttonsContainer.appendChild(leftButtons);
        buttonsContainer.appendChild(moreInfoButton);
        
        // Title and progress section
        const titleElement = document.createElement('p');
        titleElement.className = 'card__title text';
        titleElement.innerHTML = `
            <span class="text--bold">${movie.type === 'series' ? 'S1:E1' : ''}</span>
            ${movie.title}
        `;
        
        // Progress bar
        const progressContainer = document.createElement('div');
        progressContainer.className = 'card__progress flex justify-between items-center';
        progressContainer.innerHTML = `
            <div class="progressbar">
                <div class="progressbar__status"></div>
            </div>
            <span class="text text--bold text--muted">51 of 52m</span>
        `;
        
        cardBody.appendChild(buttonsContainer);
        cardBody.appendChild(titleElement);
        cardBody.appendChild(progressContainer);
        
        backCard.appendChild(backHeader);
        backCard.appendChild(cardBody);
        
        content.appendChild(frontCard);
        content.appendChild(backCard);
        
        content.addEventListener('click', () => this.handleMovieClick(movie.movie_id));
        
        return content;
    },

    async handleMovieClick(movieId) {
        try {
            const token = localStorage.getItem('token');
            if (!token) {
                throw new Error('No authentication token found');
            }

            const response = await fetch('/api/movie.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`
                },
                body: JSON.stringify(movieId)
            });

            if (!response.ok) {
                throw new Error('Failed to fetch movie details');
            }

            const data = await response.json();
            if (!data.ok) {
                throw new Error(data.message || 'Failed to fetch movie details');
            }

            // TODO: Handle movie details display
            console.log('Movie details:', data.movie);
        } catch (error) {
            console.error('Error fetching movie details:', error);
        }
    },

    async loadMoviesIntoRow(rowElement, category) {
        const slider = rowElement.querySelector('.slider');
        if (!slider) return;

        slider.innerHTML = '';
        
        const loadingIndicator = document.createElement('div');
        loadingIndicator.className = 'loading-indicator';
        loadingIndicator.textContent = 'Loading...';
        slider.appendChild(loadingIndicator);

        try {
            const movies = await this.fetchMovies(category);
            
            slider.removeChild(loadingIndicator);
            if (movies.length === 0) {
                slider.innerHTML = '<div class="error-message">No movies found</div>';
                return;
            }
            
            movies.forEach(movie => {
                const movieElement = this.createMovieElement(movie);
                slider.appendChild(movieElement);
            });
        } catch (error) {
            console.error('Error loading movies:', error);
            slider.innerHTML = '<div class="error-message">Failed to load movies</div>';
        }
    },

    init() {
        const token = localStorage.getItem('token');
        if (!token) {
            console.error('No authentication token found');
            return;
        }
        document.querySelectorAll('.content-row').forEach(async (row) => {
            const title = row.querySelector('.title').textContent;
            await this.loadMoviesIntoRow(row, title);
        });
    }
};

export default MovieManager; 