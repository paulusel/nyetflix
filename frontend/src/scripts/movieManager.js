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
        
        const img = document.createElement('img');
        img.className = 'item';
        img.src = movie.thumbnail;
        img.alt = movie.title;
        
        content.addEventListener('click', () => this.handleMovieClick(movie.movie_id));
        
        content.appendChild(img);
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