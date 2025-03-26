<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class TmdbDownloader extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tmdb:download {year=2024}
                           {--output=storage/app/tmdb-data}
                           {--download-images}
                           {--poster-size=w500}
                           {--backdrop-size=w780}
                           {--memory-limit=512M}
                           {--stream-images}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Download all movies data from TMDB for a specific year';

    /**
     * TMDB API configuration
     */
    protected $apiKey = 'e7956087ccc04e8e4bd5659b55ed08ec';
    protected $baseUrl = 'https://api.themoviedb.org/3';
    protected $imageBaseUrl = 'https://image.tmdb.org/t/p/';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $year = $this->argument('year');
        $outputPath = $this->option('output');
        $downloadImages = $this->option('download-images');
        $posterSize = $this->option('poster-size');
        $backdropSize = $this->option('backdrop-size');
        $memoryLimit = $this->option('memory-limit');
        $streamImages = $this->option('stream-images');

        // Set memory limit if specified
        if ($memoryLimit) {
            ini_set('memory_limit', $memoryLimit);
            $this->info("Memory limit set to: $memoryLimit");
        }

        if (!file_exists($outputPath)) {
            mkdir($outputPath, 0755, true);
        }

        $this->info("Starting download of ALL TMDB movies for $year");

        // Create directories
        $detailsDir = "$outputPath/movie_details";
        $imagesDir = "$outputPath/images";

        if (!file_exists($detailsDir)) {
            mkdir($detailsDir, 0755, true);
        }

        if ($downloadImages && !file_exists($imagesDir)) {
            mkdir($imagesDir, 0755, true);
            mkdir("$imagesDir/posters", 0755, true);
            mkdir("$imagesDir/backdrops", 0755, true);
        }

        // Check if we already have the basic data file
        $basicDataFile = "$outputPath/tmdb_movies_{$year}_basic.json";
        $allMovies = [];

        if (file_exists($basicDataFile)) {
            $this->info("Found existing basic data file. Loading movie data...");
            $allMovies = json_decode(file_get_contents($basicDataFile), true);
        } else {
            // Step 1: Fetch basic movie data from all pages
            $page = 1;
            $totalPages = null;

            $this->info("Fetching ALL movies released in $year...");

            while (true) {
                try {
                    $response = Http::get("$this->baseUrl/discover/movie", [
                        'api_key' => $this->apiKey,
                        'primary_release_year' => $year,
                        'page' => $page,
                        'include_adult' => true,
                        'sort_by' => 'primary_release_date.asc',
                    ]);

                    if ($response->failed()) {
                        throw new \Exception("HTTP error: " . $response->status());
                    }

                    $data = $response->json();

                    // Get total pages on first request
                    if ($totalPages === null) {
                        $totalPages = $data['total_pages'] ?? 1;
                        $this->info("Found {$data['total_results']} movies across $totalPages pages");
                        $bar = $this->output->createProgressBar($totalPages);
                        $bar->start();
                    }

                    $results = $data['results'] ?? [];
                    $allMovies = array_merge($allMovies, $results);

                    $bar->advance();

                    // Break if we've processed all pages
                    if ($page >= $totalPages) {
                        break;
                    }

                    $page++;

                    // Sleep to avoid rate limits
                    usleep(250000);
                } catch (\Exception $e) {
                    $this->error("Error fetching page $page: " . $e->getMessage());
                    break;
                }
            }

            $bar->finish();
            $this->newLine();

            // Save basic movie data
            file_put_contents($basicDataFile, json_encode($allMovies, JSON_PRETTY_PRINT));
            $this->info("Saved basic movie data to $basicDataFile");
        }

        $totalMovies = count($allMovies);
        $this->info("Found $totalMovies movies for $year");

        // Process all movies
        $this->info("Processing all $totalMovies movies");
        $bar = $this->output->createProgressBar($totalMovies);
        $bar->start();

        $successCount = 0;
        $movieIdList = [];
        $runtimeList = [];
        $imageDownloadCount = 0;

        foreach ($allMovies as $basicMovie) {
            try {
                // Fetch movie details
                $movieId = $basicMovie['id'];

                // Check if we already have this movie's details
                $detailFile = "$detailsDir/$movieId.json";
                $movieDetails = null;

                if (file_exists($detailFile)) {
                    $movieDetails = json_decode(file_get_contents($detailFile), true);
                } else {
                    $response = Http::get("$this->baseUrl/movie/$movieId", [
                        'api_key' => $this->apiKey,
                        'append_to_response' => 'credits,videos'
                    ]);

                    if ($response->failed()) {
                        throw new \Exception("Failed to fetch details for movie ID $movieId");
                    }

                    $movieDetails = $response->json();

                    // Save movie details to file
                    file_put_contents($detailFile, json_encode($movieDetails, JSON_PRETTY_PRINT));
                }

                // Track movie ID and runtime
                $movieIdList[] = $movieId;
                if (isset($movieDetails['runtime']) && $movieDetails['runtime'] > 0) {
                    $runtimeList[] = [
                        'id' => $movieId,
                        'title' => $movieDetails['title'],
                        'runtime' => $movieDetails['runtime']
                    ];
                }

                // Download images if requested
                if ($downloadImages) {
                    // Helper to check if image already exists
                    $imageExists = function($path) {
                        return file_exists($path) && filesize($path) > 0;
                    };

                    // Download poster
                    if (!empty($movieDetails['poster_path'])) {
                        $posterFile = "$imagesDir/posters/{$movieId}_poster_{$posterSize}.jpg";

                        if (!$imageExists($posterFile)) {
                            $posterUrl = $this->imageBaseUrl . $posterSize . $movieDetails['poster_path'];

                            if ($streamImages) {
                                // Stream image directly to file to save memory
                                if ($this->streamImageToFile($posterUrl, $posterFile)) {
                                    $imageDownloadCount++;
                                }
                            } else {
                                // Regular download
                                if ($this->downloadImage($posterUrl, $posterFile)) {
                                    $imageDownloadCount++;
                                }
                            }
                        }
                    }

                    // Download backdrop
                    if (!empty($movieDetails['backdrop_path'])) {
                        $backdropFile = "$imagesDir/backdrops/{$movieId}_backdrop_{$backdropSize}.jpg";

                        if (!$imageExists($backdropFile)) {
                            $backdropUrl = $this->imageBaseUrl . $backdropSize . $movieDetails['backdrop_path'];

                            if ($streamImages) {
                                // Stream image directly to file to save memory
                                if ($this->streamImageToFile($backdropUrl, $backdropFile)) {
                                    $imageDownloadCount++;
                                }
                            } else {
                                // Regular download
                                if ($this->downloadImage($backdropUrl, $backdropFile)) {
                                    $imageDownloadCount++;
                                }
                            }
                        }
                    }
                }

                $successCount++;

                // Force garbage collection to free memory
                if (function_exists('gc_collect_cycles')) {
                    gc_collect_cycles();
                }

                // Sleep to avoid rate limits
                usleep(250000);
            } catch (\Exception $e) {
                $this->error("\nError processing movie ID {$basicMovie['id']}: " . $e->getMessage());
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();

        // Save ID list file
        $idListFile = "$outputPath/tmdb_movies_{$year}_id_list.json";
        file_put_contents($idListFile, json_encode($movieIdList, JSON_PRETTY_PRINT));

        // Save runtime list file
        $runtimeFile = "$outputPath/tmdb_movies_{$year}_runtime_list.json";
        file_put_contents($runtimeFile, json_encode($runtimeList, JSON_PRETTY_PRINT));

        $this->info("Download completed:");
        $this->info("- Successfully processed $successCount out of $totalMovies movies");
        $this->info("- Movie details saved in $detailsDir");
        $this->info("- ID list saved in $idListFile");
        $this->info("- Runtime list saved in $runtimeFile");

        if ($downloadImages) {
            $this->info("- Downloaded $imageDownloadCount new images");
            $this->info("- Poster size: $posterSize");
            $this->info("- Backdrop size: $backdropSize");
        }

        return 0;
    }

    /**
     * Download an image from URL to local path
     */
    protected function downloadImage($url, $path)
    {
        try {
            $response = Http::get($url);
            if ($response->successful()) {
                file_put_contents($path, $response->body());
                return true;
            }
        } catch (\Exception $e) {
            $this->warn("Failed to download image: $url - " . $e->getMessage());
        }
        return false;
    }

    /**
     * Stream an image directly to a file to save memory
     */
    protected function streamImageToFile($url, $path)
    {
        try {
            $fp = fopen($path, 'w+');

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_FILE, $fp);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);

            $success = curl_exec($ch);
            curl_close($ch);
            fclose($fp);

            return $success;
        } catch (\Exception $e) {
            $this->warn("Failed to stream image: $url - " . $e->getMessage());
            return false;
        }
    }
}
