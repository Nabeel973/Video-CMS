/**
 * Mock Movie Data for Video Management System
 * This data simulates the API response structure
 */

export interface Movie {
  id: number;
  name: string;
  genre_id: number;
  category_id: number;
  release: string;
  image: string;
  video_link: string | null;
  video_file: string | null;
  details: string;
  status: 'active' | 'inactive';
  rating: number;
  duration: string;
  tags: Tag[];
  genre: Genre;
  category: Category;
  casts: Cast[];
}

export interface Genre {
  id: number;
  name: string;
  status: 'active' | 'inactive';
}

export interface Category {
  id: number;
  name: string;
  status: 'active' | 'inactive';
}

export interface Tag {
  id: number;
  name: string;
  status: 'active' | 'inactive';
}

export interface Cast {
  id: number;
  name: string;
  role: string;
  image: string;
}

export interface Advertisement {
  id: number;
  name: string;
  type: 'banner' | 'sidebar' | 'popup';
  description: string;
  image: string;
  link: string;
  status: 'active' | 'inactive';
}

// Mock Genres
export const genres: Genre[] = [
  { id: 1, name: 'Action', status: 'active' },
  { id: 2, name: 'Comedy', status: 'active' },
  { id: 3, name: 'Drama', status: 'active' },
  { id: 4, name: 'Horror', status: 'active' },
  { id: 5, name: 'Sci-Fi', status: 'active' },
  { id: 6, name: 'Thriller', status: 'active' },
  { id: 7, name: 'Romance', status: 'active' },
  { id: 8, name: 'Documentary', status: 'active' },
  { id: 9, name: 'Animation', status: 'active' },
  { id: 10, name: 'Fantasy', status: 'active' },
];

// Mock Categories
export const categories: Category[] = [
  { id: 1, name: 'Movies', status: 'active' },
  { id: 2, name: 'TV Series', status: 'active' },
  { id: 3, name: 'Web Series', status: 'active' },
  { id: 4, name: 'Short Films', status: 'active' },
  { id: 5, name: 'Documentaries', status: 'active' },
];

// Mock Tags
export const tags: Tag[] = [
  { id: 1, name: 'Trending', status: 'active' },
  { id: 2, name: 'New Release', status: 'active' },
  { id: 3, name: 'Award Winning', status: 'active' },
  { id: 4, name: 'Must Watch', status: 'active' },
  { id: 5, name: 'Exclusive', status: 'active' },
  { id: 6, name: 'Classic', status: 'active' },
  { id: 7, name: 'Blockbuster', status: 'active' },
  { id: 8, name: 'Indie', status: 'active' },
];

// Mock Advertisements
export const advertisements: Advertisement[] = [
  {
    id: 1,
    name: 'Premium Subscription',
    type: 'banner',
    description: 'Upgrade to Premium for ad-free streaming and exclusive content!',
    image: 'https://images.unsplash.com/photo-1536440136628-849c177e76a1?w=1200&h=300&fit=crop',
    link: '/subscribe',
    status: 'active',
  },
  {
    id: 2,
    name: 'New Releases',
    type: 'banner',
    description: 'Check out the hottest new movies this week!',
    image: 'https://images.unsplash.com/photo-1489599849927-2ee91cede3ba?w=1200&h=300&fit=crop',
    link: '/new-releases',
    status: 'active',
  },
  {
    id: 3,
    name: 'Movie Marathon',
    type: 'sidebar',
    description: 'Join our weekend movie marathon event!',
    image: 'https://images.unsplash.com/photo-1517604931442-7e0c8ed2963c?w=400&h=600&fit=crop',
    link: '/events/marathon',
    status: 'active',
  },
];

// Movie poster images (using Unsplash for placeholders)
const moviePosters = [
  'https://images.unsplash.com/photo-1509347528160-9a9e33742cdb?w=300&h=450&fit=crop',
  'https://images.unsplash.com/photo-1440404653325-ab127d49abc1?w=300&h=450&fit=crop',
  'https://images.unsplash.com/photo-1478720568477-152d9b164e26?w=300&h=450&fit=crop',
  'https://images.unsplash.com/photo-1536440136628-849c177e76a1?w=300&h=450&fit=crop',
  'https://images.unsplash.com/photo-1485846234645-a62644f84728?w=300&h=450&fit=crop',
  'https://images.unsplash.com/photo-1594909122845-11baa439b7bf?w=300&h=450&fit=crop',
  'https://images.unsplash.com/photo-1489599849927-2ee91cede3ba?w=300&h=450&fit=crop',
  'https://images.unsplash.com/photo-1517604931442-7e0c8ed2963c?w=300&h=450&fit=crop',
  'https://images.unsplash.com/photo-1574267432553-4b4628081c31?w=300&h=450&fit=crop',
  'https://images.unsplash.com/photo-1626814026160-2237a95fc5a0?w=300&h=450&fit=crop',
  'https://images.unsplash.com/photo-1535016120720-40c646be5580?w=300&h=450&fit=crop',
  'https://images.unsplash.com/photo-1542204165-65bf26472b9b?w=300&h=450&fit=crop',
];

// Hero background images
export const heroImages = [
  'https://images.unsplash.com/photo-1489599849927-2ee91cede3ba?w=1920&h=1080&fit=crop',
  'https://images.unsplash.com/photo-1536440136628-849c177e76a1?w=1920&h=1080&fit=crop',
  'https://images.unsplash.com/photo-1517604931442-7e0c8ed2963c?w=1920&h=1080&fit=crop',
];

// Mock Movies
export const movies: Movie[] = [
  {
    id: 1,
    name: 'Cyber Odyssey',
    genre_id: 5,
    category_id: 1,
    release: '2025',
    image: moviePosters[0],
    video_link: 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
    video_file: null,
    details: 'In a dystopian future where artificial intelligence controls every aspect of human life, a group of rebels discovers a way to fight back. Their journey takes them through virtual realms and dangerous territories as they seek to restore humanity\'s freedom.',
    status: 'active',
    rating: 4.8,
    duration: '2h 15m',
    tags: [tags[0], tags[1], tags[6]],
    genre: genres[4],
    category: categories[0],
    casts: [
      { id: 1, name: 'Alex Chen', role: 'Lead Protagonist', image: 'https://i.pravatar.cc/150?img=1' },
      { id: 2, name: 'Sarah Miller', role: 'AI Specialist', image: 'https://i.pravatar.cc/150?img=5' },
    ],
  },
  {
    id: 2,
    name: 'The Last Horizon',
    genre_id: 1,
    category_id: 1,
    release: '2025',
    image: moviePosters[1],
    video_file: '/storage/videos/sample.mp4',
    video_link: null,
    details: 'A veteran astronaut must lead one final mission to save Earth from an approaching asteroid. With limited resources and time running out, every decision becomes crucial for the survival of humanity.',
    status: 'active',
    rating: 4.5,
    duration: '2h 30m',
    tags: [tags[0], tags[3], tags[6]],
    genre: genres[0],
    category: categories[0],
    casts: [
      { id: 3, name: 'James Wright', role: 'Commander', image: 'https://i.pravatar.cc/150?img=3' },
      { id: 4, name: 'Maria Santos', role: 'Scientist', image: 'https://i.pravatar.cc/150?img=9' },
    ],
  },
  {
    id: 3,
    name: 'Echoes of Tomorrow',
    genre_id: 3,
    category_id: 1,
    release: '2024',
    image: moviePosters[2],
    video_link: 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
    video_file: null,
    details: 'A touching story about a father reconnecting with his estranged daughter through a series of letters that reveal family secrets spanning three generations.',
    status: 'active',
    rating: 4.7,
    duration: '1h 58m',
    tags: [tags[2], tags[3]],
    genre: genres[2],
    category: categories[0],
    casts: [
      { id: 5, name: 'Robert Taylor', role: 'Father', image: 'https://i.pravatar.cc/150?img=12' },
      { id: 6, name: 'Emma Wilson', role: 'Daughter', image: 'https://i.pravatar.cc/150?img=16' },
    ],
  },
  {
    id: 4,
    name: 'Midnight Terrors',
    genre_id: 4,
    category_id: 1,
    release: '2025',
    image: moviePosters[3],
    video_link: 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
    video_file: null,
    details: 'When a family moves into an old Victorian mansion, they discover that the house has a dark history. Strange occurrences begin to plague them, and they must uncover the truth before it\'s too late.',
    status: 'active',
    rating: 4.2,
    duration: '1h 45m',
    tags: [tags[1], tags[4]],
    genre: genres[3],
    category: categories[0],
    casts: [
      { id: 7, name: 'Lisa Parker', role: 'Mother', image: 'https://i.pravatar.cc/150?img=20' },
      { id: 8, name: 'Tom Henderson', role: 'Father', image: 'https://i.pravatar.cc/150?img=11' },
    ],
  },
  {
    id: 5,
    name: 'Laugh Out Loud',
    genre_id: 2,
    category_id: 1,
    release: '2025',
    image: moviePosters[4],
    video_link: 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
    video_file: null,
    details: 'A hilarious comedy about a group of friends who accidentally become viral internet sensations and must navigate the chaos of sudden fame.',
    status: 'active',
    rating: 4.0,
    duration: '1h 52m',
    tags: [tags[0], tags[1]],
    genre: genres[1],
    category: categories[0],
    casts: [
      { id: 9, name: 'Mike Johnson', role: 'Lead Comic', image: 'https://i.pravatar.cc/150?img=7' },
      { id: 10, name: 'Sophia Brown', role: 'Best Friend', image: 'https://i.pravatar.cc/150?img=23' },
    ],
  },
  {
    id: 6,
    name: 'Quantum Break',
    genre_id: 5,
    category_id: 1,
    release: '2024',
    image: moviePosters[5],
    video_file: '/storage/videos/sample.mp4',
    video_link: null,
    details: 'A physicist discovers a way to manipulate time but accidentally creates a rift that threatens to destroy reality itself. Now he must race against the clock to fix what he\'s broken.',
    status: 'active',
    rating: 4.6,
    duration: '2h 10m',
    tags: [tags[2], tags[6]],
    genre: genres[4],
    category: categories[0],
    casts: [
      { id: 11, name: 'David Kim', role: 'Dr. Marcus', image: 'https://i.pravatar.cc/150?img=33' },
      { id: 12, name: 'Rachel Green', role: 'Lab Assistant', image: 'https://i.pravatar.cc/150?img=44' },
    ],
  },
  {
    id: 7,
    name: 'The Silent Witness',
    genre_id: 6,
    category_id: 1,
    release: '2025',
    image: moviePosters[6],
    video_link: 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
    video_file: null,
    details: 'A deaf woman becomes the only witness to a murder and must use all her skills to survive as the killer hunts her down.',
    status: 'active',
    rating: 4.4,
    duration: '1h 55m',
    tags: [tags[0], tags[4]],
    genre: genres[5],
    category: categories[0],
    casts: [
      { id: 13, name: 'Amy Liu', role: 'Maya', image: 'https://i.pravatar.cc/150?img=25' },
      { id: 14, name: 'Chris Evans', role: 'Detective', image: 'https://i.pravatar.cc/150?img=52' },
    ],
  },
  {
    id: 8,
    name: 'Hearts Entwined',
    genre_id: 7,
    category_id: 1,
    release: '2024',
    image: moviePosters[7],
    video_link: 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
    video_file: null,
    details: 'Two strangers from different worlds meet on a train and share a journey that will change their lives forever. A beautiful story of love, loss, and second chances.',
    status: 'active',
    rating: 4.3,
    duration: '2h 05m',
    tags: [tags[3], tags[5]],
    genre: genres[6],
    category: categories[0],
    casts: [
      { id: 15, name: 'Julia Roberts', role: 'Emily', image: 'https://i.pravatar.cc/150?img=32' },
      { id: 16, name: 'Daniel Craig', role: 'Thomas', image: 'https://i.pravatar.cc/150?img=57' },
    ],
  },
  {
    id: 9,
    name: 'Planet Earth: Mysteries',
    genre_id: 8,
    category_id: 5,
    release: '2025',
    image: moviePosters[8],
    video_file: '/storage/videos/sample.mp4',
    video_link: null,
    details: 'An awe-inspiring documentary exploring the most mysterious and unexplored regions of our planet, from the depths of the ocean to remote mountain peaks.',
    status: 'active',
    rating: 4.9,
    duration: '1h 30m',
    tags: [tags[2], tags[3], tags[4]],
    genre: genres[7],
    category: categories[4],
    casts: [],
  },
  {
    id: 10,
    name: 'Dragon Quest',
    genre_id: 9,
    category_id: 1,
    release: '2025',
    image: moviePosters[9],
    video_link: 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
    video_file: null,
    details: 'A young hero embarks on an epic animated adventure to save a magical kingdom from an ancient evil. Along the way, they make unexpected friends and discover their true destiny.',
    status: 'active',
    rating: 4.5,
    duration: '1h 48m',
    tags: [tags[0], tags[6]],
    genre: genres[8],
    category: categories[0],
    casts: [],
  },
  {
    id: 11,
    name: 'Realm of Shadows',
    genre_id: 10,
    category_id: 1,
    release: '2024',
    image: moviePosters[10],
    video_link: 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
    video_file: null,
    details: 'In a world where magic is real, a young wizard must navigate political intrigue and ancient prophecies to prevent a war that could destroy everything.',
    status: 'active',
    rating: 4.6,
    duration: '2h 25m',
    tags: [tags[0], tags[1], tags[6]],
    genre: genres[9],
    category: categories[0],
    casts: [
      { id: 17, name: 'Ian McKellen', role: 'Elder Wizard', image: 'https://i.pravatar.cc/150?img=60' },
      { id: 18, name: 'Emma Stone', role: 'Princess', image: 'https://i.pravatar.cc/150?img=38' },
    ],
  },
  {
    id: 12,
    name: 'Urban Legends',
    genre_id: 6,
    category_id: 2,
    release: '2025',
    image: moviePosters[11],
    video_link: 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
    video_file: null,
    details: 'A gripping TV series that explores the truth behind famous urban legends. Each episode delves into a different story, blending fact and fiction.',
    status: 'active',
    rating: 4.1,
    duration: '45m/ep',
    tags: [tags[1], tags[4]],
    genre: genres[5],
    category: categories[1],
    casts: [
      { id: 19, name: 'Morgan Freeman', role: 'Narrator', image: 'https://i.pravatar.cc/150?img=65' },
    ],
  },
];

// Featured movie (for hero section)
export const featuredMovie = movies[0];

// Get movies by genre
export const getMoviesByGenre = (genreId: number): Movie[] => {
  return movies.filter(movie => movie.genre_id === genreId);
};

// Get movies by category
export const getMoviesByCategory = (categoryId: number): Movie[] => {
  return movies.filter(movie => movie.category_id === categoryId);
};

// Get trending movies
export const getTrendingMovies = (): Movie[] => {
  return movies.filter(movie => movie.tags.some(tag => tag.name === 'Trending'));
};

// Get new releases
export const getNewReleases = (): Movie[] => {
  return movies.filter(movie => movie.tags.some(tag => tag.name === 'New Release'));
};

// Get related movies (by genre)
export const getRelatedMovies = (movieId: number, limit: number = 6): Movie[] => {
  const movie = movies.find(m => m.id === movieId);
  if (!movie) return [];
  
  return movies
    .filter(m => m.id !== movieId && m.genre_id === movie.genre_id)
    .slice(0, limit);
};

// Search movies
export const searchMovies = (query: string): Movie[] => {
  const lowerQuery = query.toLowerCase();
  return movies.filter(
    movie =>
      movie.name.toLowerCase().includes(lowerQuery) ||
      movie.details.toLowerCase().includes(lowerQuery) ||
      movie.genre.name.toLowerCase().includes(lowerQuery)
  );
};
