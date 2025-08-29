<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Film;
use App\Models\FilmCategory;
use App\Models\Category;
use Illuminate\Http\UploadedFile;
use App\Http\Controllers\UploadController;
use Exception;

class ImportTmdbToFilm extends Command
{
    protected $signature = 'tmdb:upload-incremental
                            {data-path : Path to the TMDB data directory}
                            {--type=movies : Type of content to upload (movies)}
                            {--batch-size=500 : Number of items to process per batch}
                            {--delay=0 : Delay in seconds between batches}
                            {--start-from=0 : Start from specific item index}
                            {--max-items=0 : Maximum items to process (0 = all)}
                            {--resume : Resume from last checkpoint}
                            {--dry-run : Show what would be uploaded without actually uploading}
                            {--skip-existing : Skip items that already exist in database}
                            {--update-existing : Update existing items with new data}
                            {--year=2024 : Year of data to process}
                            {--film-type=7 : Film type (7=movie, 8=series)}
                            {--default-runtime=90 : Default runtime for movies without runtime}
                            {--skip-duplicates : Skip duplicate checking for speed}
                            {--bulk-insert : Use bulk database operations}
                            {--skip-images : Skip all image uploads for speed}
                            {--fast-with-images : Fast mode but keep image uploads}
                            {--parallel-images : Upload images in parallel}
                            {--image-batch=50 : Batch size for image uploads}
                            {--chunk-size=1000 : Chunk size for bulk operations}
                            {--memory=2G : PHP memory limit}
                            {--super-fast : Enable all speed optimizations}';

    protected $description = 'Upload TMDB downloaded data to Film model incrementally with OSS image upload and category mapping';

    /**
     * Categories from the database
     */
    protected $categories = [];
    protected $categoryNameToIdMap = [];

    /**
     * Countries from the database
     */
    protected $countries = [];
    protected $countryNameToIdMap = [];
    protected $countryCodeToIdMap = [];

    private $stats = [
        'movies_processed' => 0,
        'movies_skipped' => 0,
        'movies_failed' => 0,
        'movies_updated' => 0,
        'categories_assigned' => 0,
        'start_time' => 0
    ];

    public function handle()
    {
        $this->stats['start_time'] = microtime(true);

        // Set memory limit
        $memoryLimit = $this->option('memory');
        ini_set('memory_limit', $memoryLimit);
        $this->info("Memory limit set to $memoryLimit");

        // Test database connection first
        if (!$this->testDatabaseConnection()) {
            return 1;
        }

        $dataPath = rtrim($this->argument('data-path'), '/');
        $type = $this->option('type');
        $batchSize = (int) $this->option('batch-size');
        $delay = (int) $this->option('delay');
        $startFrom = (int) $this->option('start-from');
        $maxItems = (int) $this->option('max-items');
        $resume = $this->option('resume');
        $dryRun = $this->option('dry-run');
        $skipExisting = $this->option('skip-existing');
        $updateExisting = $this->option('update-existing');
        $year = $this->option('year');
        $filmType = $this->option('film-type');
        $defaultRuntime = $this->option('default-runtime');
        $skipImages = $this->option('skip-images');
        $skipDuplicates = $this->option('skip-duplicates');
        $bulkInsert = $this->option('bulk-insert');
        $superFast = $this->option('super-fast');
        $fastWithImages = $this->option('fast-with-images');
        $parallelImages = $this->option('parallel-images');
        $imageBatch = (int) $this->option('image-batch');
        $chunkSize = (int) $this->option('chunk-size');

        // Super fast mode enables all optimizations
        if ($superFast) {
            $skipDuplicates = true;
            $bulkInsert = true;
            $skipImages = true;
            $batchSize = max($batchSize, 1000);
            $delay = 0;
            $this->info("🚀 SUPER FAST MODE ENABLED - All speed optimizations active!");
        }

        // Fast with images mode - optimize DB but keep images
        if ($fastWithImages) {
            $skipDuplicates = true;
            $bulkInsert = true;
            $skipImages = false; // Keep images
            $parallelImages = true;
            $batchSize = max($batchSize, 300);
            $delay = 0;
            $this->info("🏃‍♂️ FAST WITH IMAGES MODE - DB optimized, images enabled!");
        }

        if (!is_dir($dataPath)) {
            $this->error("Data path does not exist: $dataPath");
            return 1;
        }

        $this->info("════════════════════════════════════════════════");
        $this->info("  TMDB INCREMENTAL UPLOADER");
        $this->info("════════════════════════════════════════════════");
        $this->info("Settings:");
        $this->info("  • Data path: $dataPath");
        $this->info("  • Content type: $type");
        $this->info("  • Year: $year");
        $this->info("  • Film type: $filmType");
        $this->info("  • Batch size: $batchSize");
        $this->info("  • Chunk size: $chunkSize");
        $this->info("  • Delay between batches: {$delay}s");
        $this->info("  • Start from: $startFrom");
        $this->info("  • Max items: " . ($maxItems > 0 ? $maxItems : 'All'));
        $this->info("  • Skip existing: " . ($skipExisting ? 'Yes' : 'No'));
        $this->info("  • Update existing: " . ($updateExisting ? 'Yes' : 'No'));
        $this->info("  • Fast with images: " . ($fastWithImages ? 'Yes' : 'No'));
        $this->info("  • Parallel images: " . ($parallelImages ? 'Yes' : 'No'));
        $this->info("  • Skip duplicates check: " . ($skipDuplicates ? 'Yes' : 'No'));
        $this->info("  • Bulk operations: " . ($bulkInsert ? 'Yes' : 'No'));
        $this->info("  • Skip images: " . ($skipImages ? 'Yes' : 'No'));
        $this->info("  • Dry run: " . ($dryRun ? 'Yes' : 'No'));
        $this->info("════════════════════════════════════════════════\n");

        // Load data from database
        if (!$dryRun) {
            $this->loadCategories();
            $this->loadCountries();
        } else {
            $this->info("Skipping database data loading in dry-run mode");
        }

        // Only process movies for now
        if ($type === 'movies') {
            $this->processMoviesIncremental(
                $dataPath, $year, $filmType, $defaultRuntime, $skipImages,
                $batchSize, $delay, $startFrom, $maxItems, $resume, $dryRun,
                $skipExisting, $updateExisting, $skipDuplicates, $bulkInsert,
                $parallelImages, $imageBatch, $chunkSize
            );
        } else {
            $this->error("Only 'movies' type is supported in this version");
            return 1;
        }

        $this->displayFinalStats();
        return 0;
    }

    protected function testDatabaseConnection()
    {
        $this->info("Testing database connection...");

        try {
            DB::connection()->getPdo();
            $this->info("✓ Database connection successful - " . DB::connection()->getDatabaseName());
            return true;
        } catch (\Exception $e) {
            $this->error("✗ Database connection failed!");
            $this->error("Error: " . $e->getMessage());
            $this->newLine();
            $this->error("Please check your .env file and run: php artisan config:clear");
            return false;
        }
    }

    protected function processMoviesIncremental($dataPath, $year, $filmType, $defaultRuntime, $skipUpload, $batchSize, $delay, $startFrom, $maxItems, $resume, $dryRun, $skipExisting, $updateExisting, $skipDuplicates, $bulkInsert, $parallelImages, $imageBatch, $chunkSize)
    {
        $this->info("\n╔════════════════════════════════════════════════╗");
        $this->info("║  Processing MOVIES incrementally for $year");
        $this->info("╚════════════════════════════════════════════════╝\n");

        // Look for your exact file structure
        $detailsDir = "$dataPath/movie/details";
        $imagesDir = "$dataPath/movie/images";
        $postersDir = "$imagesDir/posters";
        $backdropsDir = "$imagesDir/backdrops";
        $checkpointFile = "$dataPath/movie/upload_checkpoint.json";

        if (!is_dir($detailsDir)) {
            $this->error("Details directory not found: $detailsDir");
            $this->error("Expected structure: {data-path}/movie/details/*.json");
            return;
        }

        // Check image directories if not skipping uploads
        if (!$skipUpload && !$dryRun) {
            if (!is_dir($postersDir)) {
                $this->warn("Posters directory not found: $postersDir - will use defaults");
            }
        }

        // Load checkpoint if resuming
        $checkpoint = null;
        if ($resume && file_exists($checkpointFile)) {
            $checkpoint = json_decode(file_get_contents($checkpointFile), true);
            $startFrom = max($startFrom, $checkpoint['last_index'] ?? 0);
            $this->info("Resuming from checkpoint at index: $startFrom");
        }

        // Get all detail files (sorted for consistency)
        $files = glob("$detailsDir/*.json");
        sort($files);
        $totalFiles = count($files);

        if ($totalFiles === 0) {
            $this->warn("No detail files found in $detailsDir");
            return;
        }

        // Apply limits
        $endAt = $maxItems > 0 ? min($startFrom + $maxItems, $totalFiles) : $totalFiles;
        $filesToProcess = array_slice($files, $startFrom, $endAt - $startFrom);
        $actualCount = count($filesToProcess);

        $this->info("Found $totalFiles total files");
        $this->info("Will process $actualCount files (from $startFrom to " . ($startFrom + $actualCount - 1) . ")");

        if ($dryRun) {
            $this->info("\n🔍 DRY RUN MODE - No data will be uploaded");
        }

        // Pre-load existing films for duplicate checking (if not skipping)
        $existingFilmsCache = [];
        if (!$skipDuplicates && !$dryRun) {
            $this->info("Loading existing films cache...");
            $startTime = microtime(true);
            $existingFilms = DB::table('films')
                ->select('id', 'title', 'release_date')
                ->get();

            foreach ($existingFilms as $film) {
                $key = $this->generateFilmKey($film->title, $film->release_date);
                $existingFilmsCache[$key] = $film->id;
            }
            $loadTime = round(microtime(true) - $startTime, 2);
            $this->info("✓ Loaded " . count($existingFilmsCache) . " existing films in {$loadTime}s");
        }

        // Create upload controller only if not skipping uploads
        $uploadController = $skipUpload || $dryRun ? null : new UploadController();

        // Use optimized bulk processing if enabled
        if ($bulkInsert && $chunkSize > $batchSize) {
            $this->processInChunks($filesToProcess, $chunkSize, $filmType, $defaultRuntime, $skipUpload, $uploadController, $postersDir, $backdropsDir, $dryRun, $existingFilmsCache, $checkpointFile, $startFrom);
        } elseif ($bulkInsert) {
            $this->processBulkWithImages($filesToProcess, $filmType, $defaultRuntime, $skipUpload, $uploadController, $postersDir, $backdropsDir, $dryRun, $existingFilmsCache, $checkpointFile, $startFrom, $batchSize, $parallelImages, $imageBatch);
        } else {
            $this->processInBatches($filesToProcess, $batchSize, $delay, $filmType, $defaultRuntime, $skipUpload, $uploadController, $postersDir, $backdropsDir, $dryRun, $skipExisting, $updateExisting, $skipDuplicates, $existingFilmsCache, $bulkInsert, $checkpointFile, $startFrom);
        }

        // Remove checkpoint file when complete
        if (!$dryRun && file_exists($checkpointFile)) {
            unlink($checkpointFile);
            $this->info("✓ Checkpoint file removed - upload complete");
        }

        $this->info("✓ Completed MOVIES processing");
        $this->info("  - New items: {$this->stats['movies_processed']}");
        $this->info("  - Updated: {$this->stats['movies_updated']}");
        $this->info("  - Skipped: {$this->stats['movies_skipped']}");
        $this->info("  - Failed: {$this->stats['movies_failed']}");
        $this->info("  - Categories assigned: {$this->stats['categories_assigned']}\n");
    }

    protected function processInChunks($files, $chunkSize, $filmType, $defaultRuntime, $skipUpload, $uploadController, $postersDir, $backdropsDir, $dryRun, $existingFilmsCache, $checkpointFile, $startFrom)
    {
        $this->info("🚀 CHUNK MODE: Processing {$chunkSize} items at once");

        $chunks = array_chunk($files, $chunkSize);
        $totalChunks = count($chunks);
        $currentIndex = $startFrom;

        $progressBar = $this->output->createProgressBar(count($files));
        $progressBar->start();

        foreach ($chunks as $chunkIndex => $chunk) {
            $this->newLine();
            $this->info("Processing chunk " . ($chunkIndex + 1) . "/$totalChunks ({$chunkSize} items)");

            $startTime = microtime(true);

            // Process entire chunk in one go
            $result = $this->processBulkChunk($chunk, $filmType, $defaultRuntime, $skipUpload, $uploadController, $postersDir, $backdropsDir, $existingFilmsCache);

            $chunkTime = round(microtime(true) - $startTime, 2);

            $this->stats['movies_processed'] += $result['processed'];
            $this->stats['movies_skipped'] += $result['skipped'];
            $this->stats['movies_failed'] += $result['failed'];
            $this->stats['categories_assigned'] += $result['categories_assigned'];

            $this->info("✓ Chunk completed in {$chunkTime}s: {$result['processed']} processed, {$result['skipped']} skipped, {$result['failed']} failed");

            $progressBar->advance(count($chunk));
            $currentIndex += count($chunk);

            // Save checkpoint
            if (!$dryRun) {
                file_put_contents($checkpointFile, json_encode([
                    'last_index' => $currentIndex,
                    'timestamp' => date('Y-m-d H:i:s'),
                    'chunk' => $chunkIndex + 1,
                    'total_chunks' => $totalChunks,
                    'stats' => [
                        'processed' => $this->stats['movies_processed'],
                        'skipped' => $this->stats['movies_skipped'],
                        'failed' => $this->stats['movies_failed'],
                        'categories_assigned' => $this->stats['categories_assigned']
                    ]
                ], JSON_PRETTY_PRINT));
            }

            // Memory cleanup
            gc_collect_cycles();
        }

        $progressBar->finish();
        $this->newLine();
    }

    protected function processBulkChunk($files, $filmType, $defaultRuntime, $skipUpload, $uploadController, $postersDir, $backdropsDir, $existingFilmsCache)
    {
        $filmsToInsert = [];
        $categoriesToInsert = [];
        $processed = 0;
        $skipped = 0;
        $failed = 0;
        $categoriesAssigned = 0;

        // Process all files in memory first
        foreach ($files as $file) {
            try {
                $movieData = json_decode(file_get_contents($file), true);
                if (!$movieData || !isset($movieData['id'])) {
                    $failed++;
                    continue;
                }

                $title = $movieData['title'] ?? 'Unknown';
                $releaseDate = $this->formatReleaseDate($movieData['release_date'] ?? null);
                $filmKey = $this->generateFilmKey($title, $releaseDate);

                // Skip if exists
                if (isset($existingFilmsCache[$filmKey])) {
                    $skipped++;
                    continue;
                }

                // Prepare film data for bulk insert
                $filmData = [
                    'title' => $title,
                    'overview' => mb_substr($movieData['overview'] ?? '', 0, 1000),
                    'release_date' => $releaseDate,
                    'view' => 0,
                    'rating' => '0',
                    'type' => $filmType,
                    'running_time' => $movieData['runtime'] ?? $defaultRuntime,
                    'language' => $this->findCountryIdForMovie($movieData),
                    'category' => '',
                    'tag' => $this->extractTags($movieData),
                    'director' => $this->extractDirector($movieData),
                    'cast' => '',
                    'genre_id' => null,
                    'distributor_id' => null,
                    'poster' => '3442',
                    'cover' => '3442',
                    'created_at' => now(),
                    'updated_at' => now()
                ];

                // Only add trailer if it exists
                $trailer = $this->extractTrailer($movieData);
                if ($trailer !== null) {
                    $filmData['trailer'] = $trailer;
                }

                // Handle images if not skipping
                if (!$skipUpload && $uploadController) {
                    $this->handleImageUploadsFast($filmData, $movieData, $uploadController, $postersDir, $backdropsDir);
                }

                $filmsToInsert[] = [
                    'film_data' => $filmData,
                    'movie_data' => $movieData
                ];

                $processed++;

            } catch (\Exception $e) {
                $failed++;
            }
        }

        if (empty($filmsToInsert)) {
            return ['processed' => $processed, 'skipped' => $skipped, 'failed' => $failed, 'categories_assigned' => 0];
        }

        // Bulk insert all films at once
        try {
            DB::beginTransaction();

            // Insert films in chunks to avoid query size limits
            $filmInsertChunks = array_chunk($filmsToInsert, 200);

            foreach ($filmInsertChunks as $chunk) {
                $filmDataToInsert = array_map(function($item) {
                    return $item['film_data'];
                }, $chunk);

                // Bulk insert films
                DB::table('films')->insert($filmDataToInsert);

                // Get the inserted IDs
                $titles = array_map(function($item) {
                    return $item['film_data']['title'];
                }, $chunk);

                $insertedFilms = DB::table('films')
                    ->whereIn('title', $titles)
                    ->where('created_at', '>=', now()->subMinutes(1))
                    ->get(['id', 'title', 'release_date']);

                // Map back to our data for category assignment
                foreach ($chunk as $index => $item) {
                    $filmData = $item['film_data'];
                    $movieData = $item['movie_data'];

                    // Find the matching inserted film
                    $insertedFilm = $insertedFilms->first(function($film) use ($filmData) {
                        return $film->title === $filmData['title'] && $film->release_date === $filmData['release_date'];
                    });

                    if ($insertedFilm) {
                        // Prepare categories for this film
                        $filmCategories = $this->prepareCategories($insertedFilm->id, $movieData);
                        $categoriesToInsert = array_merge($categoriesToInsert, $filmCategories);
                        $categoriesAssigned += count($filmCategories);
                    }
                }
            }

            // Bulk insert all categories at once
            if (!empty($categoriesToInsert)) {
                $categoryChunks = array_chunk($categoriesToInsert, 500);
                foreach ($categoryChunks as $categoryChunk) {
                    DB::table('film_categories')->insert($categoryChunk);
                }
            }

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Bulk insert failed: " . $e->getMessage());
            $failed = count($filmsToInsert);
            $processed = 0;
        }

        return [
            'processed' => $processed,
            'skipped' => $skipped,
            'failed' => $failed,
            'categories_assigned' => $categoriesAssigned
        ];
    }

    protected function processBulkWithImages($files, $filmType, $defaultRuntime, $skipUpload, $uploadController, $postersDir, $backdropsDir, $dryRun, $existingFilmsCache, $checkpointFile, $startFrom, $batchSize, $parallelImages, $imageBatch)
    {
        $this->info("🏃‍♂️ BULK MODE WITH IMAGES: Processing in optimized batches");

        $batches = array_chunk($files, $batchSize);
        $totalBatches = count($batches);
        $currentIndex = $startFrom;

        $progressBar = $this->output->createProgressBar(count($files));
        $progressBar->start();

        foreach ($batches as $batchIndex => $batch) {
            $this->newLine();
            $this->info("Processing batch " . ($batchIndex + 1) . "/$totalBatches ({$batchSize} items)");

            $startTime = microtime(true);

            // STEP 1: Process all data and create films quickly
            $result = $this->processBulkBatch($batch, $filmType, $defaultRuntime, $existingFilmsCache);

            $this->stats['movies_processed'] += $result['processed'];
            $this->stats['movies_skipped'] += $result['skipped'];
            $this->stats['movies_failed'] += $result['failed'];

            // STEP 2: Handle images separately if enabled
            if (!$skipUpload && !empty($result['films_for_images'])) {
                $imageTime = microtime(true);

                if ($parallelImages) {
                    $imageResult = $this->processImagesParallel($result['films_for_images'], $uploadController, $postersDir, $backdropsDir, $imageBatch);
                } else {
                    $imageResult = $this->processImagesSequential($result['films_for_images'], $uploadController, $postersDir, $backdropsDir);
                }

                $imageElapsed = round(microtime(true) - $imageTime, 2);
                $this->info("  Images processed in {$imageElapsed}s: {$imageResult['uploaded']} uploaded, {$imageResult['failed']} failed");
            }

            $batchTime = round(microtime(true) - $startTime, 2);
            $this->info("✓ Batch completed in {$batchTime}s: {$result['processed']} processed, {$result['skipped']} skipped, {$result['failed']} failed");

            $progressBar->advance(count($batch));
            $currentIndex += count($batch);

            // Save checkpoint
            if (!$dryRun) {
                file_put_contents($checkpointFile, json_encode([
                    'last_index' => $currentIndex,
                    'timestamp' => date('Y-m-d H:i:s'),
                    'batch' => $batchIndex + 1,
                    'total_batches' => $totalBatches,
                    'stats' => [
                        'processed' => $this->stats['movies_processed'],
                        'skipped' => $this->stats['movies_skipped'],
                        'failed' => $this->stats['movies_failed'],
                        'categories_assigned' => $this->stats['categories_assigned']
                    ]
                ], JSON_PRETTY_PRINT));
            }

            // Memory cleanup
            gc_collect_cycles();
        }

        $progressBar->finish();
        $this->newLine();
    }

    protected function processBulkBatch($files, $filmType, $defaultRuntime, $existingFilmsCache)
    {
        $filmsToInsert = [];
        $filmsForImages = [];
        $processed = 0;
        $skipped = 0;
        $failed = 0;

        // STEP 1: Prepare all data in memory
        foreach ($files as $file) {
            try {
                $movieData = json_decode(file_get_contents($file), true);
                if (!$movieData || !isset($movieData['id'])) {
                    $failed++;
                    continue;
                }

                $title = $movieData['title'] ?? 'Unknown';
                $releaseDate = $this->formatReleaseDate($movieData['release_date'] ?? null);
                $filmKey = $this->generateFilmKey($title, $releaseDate);

                // Skip if exists
                if (isset($existingFilmsCache[$filmKey])) {
                    $skipped++;
                    continue;
                }

                // Prepare film data (without images for now)
                $filmData = [
                    'title' => $title,
                    'overview' => mb_substr($movieData['overview'] ?? '', 0, 1000),
                    'release_date' => $releaseDate,
                    'view' => 0,
                    'rating' => '0',
                    'type' => $filmType,
                    'running_time' => $movieData['runtime'] ?? $defaultRuntime,
                    'language' => $this->findCountryIdForMovie($movieData),
                    'category' => '',
                    'tag' => $this->extractTags($movieData),
                    'director' => $this->extractDirector($movieData),
                    'cast' => '',
                    'genre_id' => null,
                    'distributor_id' => null,
                    'poster' => '3442',
                    'cover' => '3442',
                    'created_at' => now(),
                    'updated_at' => now()
                ];

                // Only add trailer if it exists
                $trailer = $this->extractTrailer($movieData);
                if ($trailer !== null) {
                    $filmData['trailer'] = $trailer;
                }

                $filmsToInsert[] = $filmData;

                // Save movie data for image processing later
                $filmsForImages[] = [
                    'tmdb_id' => $movieData['id'],
                    'title' => $title,
                    'release_date' => $releaseDate,
                    'poster_path' => $movieData['poster_path'] ?? null,
                    'backdrop_path' => $movieData['backdrop_path'] ?? null,
                    'genres' => $movieData['genres'] ?? [],
                    'genre_ids' => $movieData['genre_ids'] ?? []
                ];

                $processed++;

            } catch (\Exception $e) {
                $failed++;
            }
        }

        if (empty($filmsToInsert)) {
            return [
                'processed' => 0,
                'skipped' => $skipped,
                'failed' => $failed,
                'films_for_images' => []
            ];
        }

        // STEP 2: Bulk insert all films
        try {
            DB::beginTransaction();

            // Insert films in chunks
            $filmChunks = array_chunk($filmsToInsert, 200);
            $insertedFilms = [];

            foreach ($filmChunks as $chunk) {
                DB::table('films')->insert($chunk);

                // Get inserted film IDs by matching titles and dates
                $titles = array_column($chunk, 'title');
                $releaseDates = array_column($chunk, 'release_date');

                $newFilms = DB::table('films')
                    ->select('id', 'title', 'release_date')
                    ->whereIn('title', $titles)
                    ->whereIn('release_date', $releaseDates)
                    ->where('created_at', '>=', now()->subMinutes(2))
                    ->get();

                $insertedFilms = array_merge($insertedFilms, $newFilms->toArray());
            }

            // STEP 3: Prepare and bulk insert categories
            $categoriesToInsert = [];

            foreach ($filmsForImages as $index => $filmImageData) {
                // Find the matching inserted film
                $insertedFilm = collect($insertedFilms)->first(function($film) use ($filmImageData) {
                    return $film->title === $filmImageData['title'] &&
                        $film->release_date === $filmImageData['release_date'];
                });

                if ($insertedFilm) {
                    // Prepare categories
                    $filmCategories = $this->prepareCategories($insertedFilm->id, $filmImageData);
                    $categoriesToInsert = array_merge($categoriesToInsert, $filmCategories);

                    // Update films_for_images with actual film ID
                    $filmsForImages[$index]['film_id'] = $insertedFilm->id;
                }
            }

            // Bulk insert categories
            if (!empty($categoriesToInsert)) {
                $categoryChunks = array_chunk($categoriesToInsert, 500);
                foreach ($categoryChunks as $categoryChunk) {
                    DB::table('film_categories')->insert($categoryChunk);
                }
                $this->stats['categories_assigned'] += count($categoriesToInsert);
            }

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Bulk insert failed: " . $e->getMessage());
            return [
                'processed' => 0,
                'skipped' => $skipped,
                'failed' => $processed,
                'films_for_images' => []
            ];
        }

        return [
            'processed' => $processed,
            'skipped' => $skipped,
            'failed' => $failed,
            'films_for_images' => $filmsForImages
        ];
    }

    protected function processInBatches($files, $batchSize, $delay, $filmType, $defaultRuntime, $skipUpload, $uploadController, $postersDir, $backdropsDir, $dryRun, $skipExisting, $updateExisting, $skipDuplicates, $existingFilmsCache, $bulkInsert, $checkpointFile, $startFrom)
    {
        $batches = array_chunk($files, $batchSize);
        $totalBatches = count($batches);
        $currentIndex = $startFrom;

        $progressBar = $this->output->createProgressBar(count($files));
        $progressBar->start();

        foreach ($batches as $batchIndex => $batch) {
            $this->newLine(2);
            $this->info("Processing batch " . ($batchIndex + 1) . "/$totalBatches");

            $batchResults = [
                'processed' => 0,
                'skipped' => 0,
                'failed' => 0,
                'updated' => 0
            ];

            foreach ($batch as $file) {
                try {
                    $result = $this->processMovieFile($file, $filmType, $defaultRuntime, $skipUpload, $uploadController, $postersDir, $backdropsDir, $dryRun, $skipExisting, $updateExisting, $skipDuplicates, $existingFilmsCache, $bulkInsert);

                    switch ($result['status']) {
                        case 'processed':
                            $batchResults['processed']++;
                            $this->stats['movies_processed']++;
                            break;
                        case 'updated':
                            $batchResults['updated']++;
                            $this->stats['movies_updated']++;
                            break;
                        case 'skipped':
                            $batchResults['skipped']++;
                            $this->stats['movies_skipped']++;
                            break;
                        case 'failed':
                            $batchResults['failed']++;
                            $this->stats['movies_failed']++;
                            break;
                    }

                    if (isset($result['categories_assigned'])) {
                        $this->stats['categories_assigned'] += $result['categories_assigned'];
                    }

                } catch (\Exception $e) {
                    $batchResults['failed']++;
                    $this->stats['movies_failed']++;
                    $this->error("  Exception " . basename($file, '.json') . ": " . $e->getMessage());
                }

                $progressBar->advance();
                $currentIndex++;
            }

            // Show batch results
            $this->info("  Results: {$batchResults['processed']} new, {$batchResults['updated']} updated, {$batchResults['skipped']} skipped, {$batchResults['failed']} failed");

            // Save checkpoint
            if (!$dryRun) {
                file_put_contents($checkpointFile, json_encode([
                    'last_index' => $currentIndex,
                    'timestamp' => date('Y-m-d H:i:s'),
                    'batch' => $batchIndex + 1,
                    'total_batches' => $totalBatches,
                    'stats' => $this->stats
                ], JSON_PRETTY_PRINT));
            }

            // Delay between batches
            if ($batchIndex < $totalBatches - 1 && $delay > 0) {
                $this->info("  Waiting {$delay}s before next batch...");
                sleep($delay);
            }

            // Memory cleanup
            if ($batchIndex % 10 === 0) {
                gc_collect_cycles();
            }
        }

        $progressBar->finish();
        $this->newLine();
    }

    protected function processMovieFile($file, $filmType, $defaultRuntime, $skipUpload, $uploadController, $postersDir, $backdropsDir, $dryRun, $skipExisting, $updateExisting, $skipDuplicates, $existingFilmsCache, $bulkInsert)
    {
        if (!file_exists($file) || filesize($file) < 50) {
            return ['status' => 'failed', 'error' => 'File not found or empty'];
        }

        $movieData = json_decode(file_get_contents($file), true);
        if (!$movieData || !isset($movieData['id'])) {
            return ['status' => 'failed', 'error' => 'Invalid JSON data or missing ID'];
        }

        $title = $movieData['title'] ?? 'Unknown';
        $releaseDate = $this->formatReleaseDate($movieData['release_date'] ?? null);

        // Fast duplicate check using cache
        $existingFilm = null;
        if (!$skipDuplicates) {
            $filmKey = $this->generateFilmKey($title, $releaseDate);
            if (isset($existingFilmsCache[$filmKey])) {
                if ($skipExisting) {
                    return ['status' => 'skipped', 'reason' => 'Already exists (cached)'];
                } elseif (!$updateExisting) {
                    return ['status' => 'skipped', 'reason' => 'Already exists (cached)'];
                }
                // For updates, we'd need to fetch the full record
                $existingFilm = Film::find($existingFilmsCache[$filmKey]);
            }
        }

        if ($dryRun) {
            $action = $existingFilm ? 'would_update' : 'would_create';
            return ['status' => 'processed', 'action' => $action];
        }

        // Process the movie
        try {
            if ($bulkInsert && !$existingFilm) {
                // Use faster bulk insert method
                $result = $this->createMovieFast($movieData, $filmType, $defaultRuntime, $skipUpload, $uploadController, $postersDir, $backdropsDir);
            } else {
                // Use safe transaction for updates or when bulk disabled
                $result = $this->safeTransaction(function() use ($movieData, $filmType, $defaultRuntime, $skipUpload, $uploadController, $postersDir, $backdropsDir, $existingFilm) {
                    return $this->createOrUpdateMovie($movieData, $filmType, $defaultRuntime, $skipUpload, $uploadController, $postersDir, $backdropsDir, $existingFilm);
                });
            }

            $status = $existingFilm ? 'updated' : 'processed';
            return [
                'status' => $status,
                'film_id' => $result['film_id'],
                'categories_assigned' => $result['categories_assigned']
            ];

        } catch (\Exception $e) {
            return ['status' => 'failed', 'error' => $e->getMessage()];
        }
    }

    protected function processImagesParallel($filmsForImages, $uploadController, $postersDir, $backdropsDir, $imageBatch)
    {
        $this->info("  📸 Uploading images in parallel batches...");

        $batches = array_chunk($filmsForImages, $imageBatch);
        $uploaded = 0;
        $failed = 0;

        foreach ($batches as $batch) {
            $imageUpdates = [];

            foreach ($batch as $filmData) {
                if (!isset($filmData['film_id'])) continue;

                $filmId = $filmData['film_id'];
                $tmdbId = $filmData['tmdb_id'];
                $posterUploaded = false;
                $backdropUploaded = false;

                // Try poster upload
                if (!empty($filmData['poster_path'])) {
                    $posterFiles = glob($postersDir . "/{$tmdbId}_poster.jpg");
                    if (!empty($posterFiles) && file_exists($posterFiles[0])) {
                        try {
                            $uploadedFile = new UploadedFile(
                                $posterFiles[0],
                                basename($posterFiles[0]),
                                'image/jpeg',
                                null,
                                true
                            );
                            $posterId = $uploadController->uploadFile($uploadedFile, 'film');
                            $imageUpdates[$filmId]['poster'] = $posterId;
                            $posterUploaded = true;
                        } catch (\Exception $e) {
                            // Continue with default
                        }
                    }
                }

                // Try backdrop upload
                if (!empty($filmData['backdrop_path'])) {
                    $backdropFiles = glob($backdropsDir . "/{$tmdbId}_backdrop.jpg");
                    if (!empty($backdropFiles) && file_exists($backdropFiles[0])) {
                        try {
                            $uploadedFile = new UploadedFile(
                                $backdropFiles[0],
                                basename($backdropFiles[0]),
                                'image/jpeg',
                                null,
                                true
                            );
                            $coverId = $uploadController->uploadFile($uploadedFile, 'film');
                            $imageUpdates[$filmId]['cover'] = $coverId;
                            $backdropUploaded = true;
                        } catch (\Exception $e) {
                            // Continue with default
                        }
                    }
                }

                if ($posterUploaded || $backdropUploaded) {
                    $uploaded++;
                } else {
                    $failed++;
                }
            }

            // Bulk update film records with new image IDs
            if (!empty($imageUpdates)) {
                foreach ($imageUpdates as $filmId => $updates) {
                    DB::table('films')->where('id', $filmId)->update($updates);
                }
            }
        }

        return ['uploaded' => $uploaded, 'failed' => $failed];
    }

    protected function processImagesSequential($filmsForImages, $uploadController, $postersDir, $backdropsDir)
    {
        $uploaded = 0;
        $failed = 0;

        foreach ($filmsForImages as $filmData) {
            if (!isset($filmData['film_id'])) continue;

            $filmId = $filmData['film_id'];
            $tmdbId = $filmData['tmdb_id'];
            $updates = [];

            // Process poster
            if (!empty($filmData['poster_path'])) {
                $posterFiles = glob($postersDir . "/{$tmdbId}_poster.jpg");
                if (!empty($posterFiles) && file_exists($posterFiles[0])) {
                    try {
                        $uploadedFile = new UploadedFile(
                            $posterFiles[0],
                            basename($posterFiles[0]),
                            'image/jpeg',
                            null,
                            true
                        );
                        $updates['poster'] = $uploadController->uploadFile($uploadedFile, 'film');
                    } catch (\Exception $e) {
                        // Continue
                    }
                }
            }

            // Process backdrop
            if (!empty($filmData['backdrop_path'])) {
                $backdropFiles = glob($backdropsDir . "/{$tmdbId}_backdrop.jpg");
                if (!empty($backdropFiles) && file_exists($backdropFiles[0])) {
                    try {
                        $uploadedFile = new UploadedFile(
                            $backdropFiles[0],
                            basename($backdropFiles[0]),
                            'image/jpeg',
                            null,
                            true
                        );
                        $updates['cover'] = $uploadController->uploadFile($uploadedFile, 'film');
                    } catch (\Exception $e) {
                        // Continue
                    }
                }
            }

            // Update film record if we have new images
            if (!empty($updates)) {
                DB::table('films')->where('id', $filmId)->update($updates);
                $uploaded++;
            } else {
                $failed++;
            }
        }

        return ['uploaded' => $uploaded, 'failed' => $failed];
    }

    protected function prepareCategories($filmId, $movieData)
    {
        $genreMap = $this->getGenreMap();
        $categories = [];

        if (!empty($movieData['genres'])) {
            foreach ($movieData['genres'] as $genre) {
                if (!empty($genre['name'])) {
                    $categoryId = $this->findCategoryIdByName($genre['name']);
                    if ($categoryId) {
                        $categories[] = [
                            'film_id' => $filmId,
                            'category_id' => $categoryId,
                            'created_at' => now(),
                            'updated_at' => now()
                        ];
                    }
                }
            }
        } elseif (!empty($movieData['genre_ids'])) {
            foreach ($movieData['genre_ids'] as $genreId) {
                if (isset($genreMap[$genreId])) {
                    $categoryId = $this->findCategoryIdByName($genreMap[$genreId]);
                    if ($categoryId) {
                        $categories[] = [
                            'film_id' => $filmId,
                            'category_id' => $categoryId,
                            'created_at' => now(),
                            'updated_at' => now()
                        ];
                    }
                }
            }
        }

        return $categories;
    }

    protected function generateFilmKey($title, $releaseDate)
    {
        return md5(strtolower($title) . '_' . $releaseDate);
    }

    protected function createMovieFast($movieData, $filmType, $defaultRuntime, $skipUpload, $uploadController, $postersDir, $backdropsDir)
    {
        $title = $movieData['title'] ?? 'Unknown';

        // Prepare film data for direct insert
        $filmData = [
            'title' => $title,
            'overview' => mb_substr($movieData['overview'] ?? '', 0, 1000),
            'release_date' => $this->formatReleaseDate($movieData['release_date'] ?? null),
            'view' => 0,
            'rating' => '0',
            'type' => $filmType,
            'running_time' => $movieData['runtime'] ?? $defaultRuntime,
            'language' => $this->findCountryIdForMovie($movieData),
            'category' => '',
            'tag' => $this->extractTags($movieData),
            'director' => $this->extractDirector($movieData),
            'cast' => '',
            'genre_id' => null,
            'distributor_id' => null,
            'poster' => '3442',
            'cover' => '3442',
            'created_at' => now(),
            'updated_at' => now()
        ];

        // Only add trailer if it exists
        $trailer = $this->extractTrailer($movieData);
        if ($trailer !== null) {
            $filmData['trailer'] = $trailer;
        }

        // Handle image uploads only if not skipping
        if (!$skipUpload && $uploadController) {
            $this->handleImageUploadsFast($filmData, $movieData, $uploadController, $postersDir, $backdropsDir);
        }

        // Insert film directly
        $filmId = DB::table('films')->insertGetId($filmData);

        // Assign categories in bulk
        $categoriesAssigned = $this->assignCategoriesToFilmFast($filmId, $movieData);

        return [
            'film_id' => $filmId,
            'categories_assigned' => $categoriesAssigned
        ];
    }

    protected function createOrUpdateMovie($movieData, $filmType, $defaultRuntime, $skipUpload, $uploadController, $postersDir, $backdropsDir, $existingFilm = null)
    {
        $title = $movieData['title'] ?? 'Unknown';

        // Create or update film
        if ($existingFilm) {
            $film = $existingFilm;
            // Clear existing categories for update
            FilmCategory::where('film_id', $film->id)->delete();
        } else {
            $film = new Film();
        }

        // Set all the fields
        $film->title = $title;
        $film->overview = mb_substr($movieData['overview'] ?? '', 0, 1000);
        $film->release_date = $this->formatReleaseDate($movieData['release_date'] ?? null);
        $film->view = 0;
        $film->rating = '0';
        $film->type = $filmType;
        $film->running_time = $movieData['runtime'] ?? $defaultRuntime;
        $film->language = $this->findCountryIdForMovie($movieData);
        $film->category = '';
        $film->tag = $this->extractTags($movieData);
        $film->director = $this->extractDirector($movieData);
        $film->cast = '';
        $film->genre_id = null;
        $film->distributor_id = null;

        // Only set trailer if it exists
        $trailer = $this->extractTrailer($movieData);
        if ($trailer !== null) {
            $film->trailer = $trailer;
        }

        // Handle image uploads
        $this->handleImageUploads($film, $movieData, $skipUpload, $uploadController, $postersDir, $backdropsDir);

        // Save the film
        $film->save();

        // Assign categories
        $categoriesAssigned = $this->assignCategoriesToFilm($film->id, $movieData);

        return [
            'film_id' => $film->id,
            'categories_assigned' => $categoriesAssigned
        ];
    }

    protected function extractTags($movieData)
    {
        if (empty($movieData['keywords']['keywords'])) {
            return '';
        }

        $tags = [];
        $count = 0;
        foreach ($movieData['keywords']['keywords'] as $keyword) {
            if (isset($keyword['name'])) {
                $tags[] = $keyword['name'];
                $count++;
                if ($count >= 5) break;
            }
        }
        return implode(', ', $tags);
    }

    protected function extractTrailer($movieData)
    {
        if (empty($movieData['videos']['results'])) {
            return null;
        }

        foreach ($movieData['videos']['results'] as $video) {
            if (($video['type'] ?? '') === 'Trailer' && ($video['site'] ?? '') === 'YouTube') {
                return 'https://www.youtube.com/watch?v=' . $video['key'];
            }
        }
        return null;
    }

    protected function extractDirector($movieData)
    {
        if (empty($movieData['credits']['crew'])) {
            return '';
        }

        foreach ($movieData['credits']['crew'] as $person) {
            if (($person['job'] ?? '') === 'Director') {
                return $person['name'];
            }
        }
        return '';
    }

    protected function handleImageUploadsFast(&$filmData, $movieData, $uploadController, $postersDir, $backdropsDir)
    {
        // Try poster upload first
        if (!empty($movieData['poster_path'])) {
            $posterPattern = $postersDir . "/{$movieData['id']}_poster.jpg";
            $posterFiles = glob($posterPattern);

            if (!empty($posterFiles) && file_exists($posterFiles[0])) {
                try {
                    $uploadedFile = new UploadedFile(
                        $posterFiles[0],
                        basename($posterFiles[0]),
                        'image/jpeg',
                        null,
                        true
                    );
                    $filmData['poster'] = $uploadController->uploadFile($uploadedFile, 'film');
                } catch (\Exception $e) {
                    // Keep default poster on failure
                }
            }
        }

        // Try backdrop upload
        if (!empty($movieData['backdrop_path'])) {
            $backdropPattern = $backdropsDir . "/{$movieData['id']}_backdrop.jpg";
            $backdropFiles = glob($backdropPattern);

            if (!empty($backdropFiles) && file_exists($backdropFiles[0])) {
                try {
                    $uploadedFile = new UploadedFile(
                        $backdropFiles[0],
                        basename($backdropFiles[0]),
                        'image/jpeg',
                        null,
                        true
                    );
                    $filmData['cover'] = $uploadController->uploadFile($uploadedFile, 'film');
                } catch (\Exception $e) {
                    // Keep default cover on failure
                }
            }
        }
    }

    protected function handleImageUploads($film, $movieData, $skipUpload, $uploadController, $postersDir, $backdropsDir)
    {
        if ($skipUpload) {
            $film->poster = '3442';
            $film->cover = '3442';
            return;
        }

        $posterUploaded = false;

        // Handle poster upload
        if (!empty($movieData['poster_path'])) {
            try {
                $posterPattern = $postersDir . "/{$movieData['id']}_poster.jpg";
                $posterFiles = glob($posterPattern);

                if (!empty($posterFiles)) {
                    $posterFile = $posterFiles[0];
                    $uploadedFile = new UploadedFile(
                        $posterFile,
                        basename($posterFile),
                        mime_content_type($posterFile),
                        null,
                        true
                    );

                    $film->poster = $uploadController->uploadFile($uploadedFile, 'film');
                    $posterUploaded = true;
                }
            } catch (\Exception $e) {
                // Continue with default poster
            }
        }

        // Default poster if upload failed
        if (!$posterUploaded) {
            $film->poster = '3442';
        }

        // Handle backdrop/cover upload
        if (!empty($movieData['backdrop_path']) && file_exists($backdropsDir)) {
            try {
                $backdropPattern = $backdropsDir . "/{$movieData['id']}_backdrop.jpg";
                $backdropFiles = glob($backdropPattern);

                if (!empty($backdropFiles)) {
                    $backdropFile = $backdropFiles[0];
                    $uploadedFile = new UploadedFile(
                        $backdropFile,
                        basename($backdropFile),
                        mime_content_type($backdropFile),
                        null,
                        true
                    );
                    $film->cover = $uploadController->uploadFile($uploadedFile, 'film');
                } else {
                    $film->cover = '3442';
                }
            } catch (\Exception $e) {
                $film->cover = '3442';
            }
        } else {
            $film->cover = '3442';
        }
    }

    protected function assignCategoriesToFilmFast($filmId, $movieData)
    {
        $genreMap = $this->getGenreMap();
        $categoriesToInsert = [];

        if (!empty($movieData['genres'])) {
            foreach ($movieData['genres'] as $genre) {
                if (!empty($genre['name'])) {
                    $categoryId = $this->findCategoryIdByName($genre['name']);
                    if ($categoryId) {
                        $categoriesToInsert[] = [
                            'film_id' => $filmId,
                            'category_id' => $categoryId,
                            'created_at' => now(),
                            'updated_at' => now()
                        ];
                    }
                }
            }
        } elseif (!empty($movieData['genre_ids'])) {
            foreach ($movieData['genre_ids'] as $genreId) {
                if (isset($genreMap[$genreId])) {
                    $categoryId = $this->findCategoryIdByName($genreMap[$genreId]);
                    if ($categoryId) {
                        $categoriesToInsert[] = [
                            'film_id' => $filmId,
                            'category_id' => $categoryId,
                            'created_at' => now(),
                            'updated_at' => now()
                        ];
                    }
                }
            }
        }

        // Bulk insert categories
        if (!empty($categoriesToInsert)) {
            DB::table('film_categories')->insert($categoriesToInsert);
        }

        return count($categoriesToInsert);
    }

    protected function assignCategoriesToFilm($filmId, $movieData)
    {
        $genreMap = $this->getGenreMap();
        $assignedCount = 0;

        if (!empty($movieData['genres'])) {
            foreach ($movieData['genres'] as $genre) {
                if (!empty($genre['name'])) {
                    $categoryId = $this->findCategoryIdByName($genre['name']);
                    if ($categoryId) {
                        $this->createFilmCategory($filmId, $categoryId);
                        $assignedCount++;
                    }
                }
            }
        } elseif (!empty($movieData['genre_ids'])) {
            foreach ($movieData['genre_ids'] as $genreId) {
                if (isset($genreMap[$genreId])) {
                    $categoryId = $this->findCategoryIdByName($genreMap[$genreId]);
                    if ($categoryId) {
                        $this->createFilmCategory($filmId, $categoryId);
                        $assignedCount++;
                    }
                }
            }
        }

        return $assignedCount;
    }

    protected function createFilmCategory($filmId, $categoryId)
    {
        $this->reconnectIfMissing();

        DB::table('film_categories')->insert([
            'film_id' => $filmId,
            'category_id' => $categoryId,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    protected function loadCategories()
    {
        $this->reconnectIfMissing();

        $this->categories = DB::table('categories')
            ->select('id', 'name')
            ->where('status', '1')
            ->get()
            ->toArray();

        $this->info("Loaded " . count($this->categories) . " categories from database");

        foreach ($this->categories as $category) {
            $this->categoryNameToIdMap[strtolower($category->name)] = $category->id;
            $this->categoryNameToIdMap[$category->name] = $category->id;
        }
    }

    protected function loadCountries()
    {
        $this->reconnectIfMissing();

        $this->countries = DB::table('countries')
            ->select('id', 'name', 'code')
            ->where('status', '1')
            ->get()
            ->toArray();

        $this->info("Loaded " . count($this->countries) . " countries from database");

        foreach ($this->countries as $country) {
            $this->countryNameToIdMap[strtolower($country->name)] = $country->id;
            $this->countryNameToIdMap[$country->name] = $country->id;

            if (!empty($country->code)) {
                $this->countryCodeToIdMap[strtolower($country->code)] = $country->id;
                $this->countryCodeToIdMap[$country->code] = $country->id;
            }
        }
    }

    protected function formatReleaseDate($date)
    {
        if (empty($date)) {
            return date('d/m/Y');
        }

        if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $date)) {
            return $date;
        }

        try {
            $dateObj = new \DateTime($date);
            return $dateObj->format('d/m/Y');
        } catch (\Exception $e) {
            return date('d/m/Y');
        }
    }

    protected function findCountryIdForMovie($movieData)
    {
        $defaultCountryId = 185; // United States

        if (!empty($movieData['production_countries'])) {
            $country = reset($movieData['production_countries']);

            if (!empty($country['iso_3166_1'])) {
                $countryCode = $country['iso_3166_1'];
                if (isset($this->countryCodeToIdMap[$countryCode])) {
                    return $this->countryCodeToIdMap[$countryCode];
                }
            }

            if (!empty($country['name'])) {
                $countryName = $country['name'];
                if (isset($this->countryNameToIdMap[$countryName])) {
                    return $this->countryNameToIdMap[$countryName];
                }

                $lowerCountryName = strtolower($countryName);
                if (isset($this->countryNameToIdMap[$lowerCountryName])) {
                    return $this->countryNameToIdMap[$lowerCountryName];
                }
            }
        }

        // Language fallback mapping
        if (!empty($movieData['original_language'])) {
            $languageCountryMap = [
                'en' => 185, 'es' => 161, 'fr' => 59, 'de' => 63, 'it' => 81,
                'ja' => 83, 'ko' => 89, 'zh' => 36, 'hi' => 75, 'ru' => 141,
                'pt' => 136, 'ar' => 159, 'bn' => 11, 'pa' => 75, 'jv' => 77,
                'ms' => 102, 'te' => 75, 'vi' => 191, 'ta' => 75, 'ur' => 131,
                'fa' => 78, 'tr' => 178, 'pl' => 135, 'uk' => 183, 'ro' => 140,
                'nl' => 118, 'hu' => 74, 'el' => 66, 'cs' => 49, 'sv' => 165,
                'he' => 79, 'no' => 125, 'fi' => 58, 'da' => 50, 'id' => 77,
                'th' => 173, 'sk' => 158, 'bg' => 24, 'hr' => 45, 'sr' => 155,
                'sl' => 159, 'lt' => 97, 'lv' => 94, 'et' => 55, 'mk' => 100,
                'sq' => 2, 'is' => 76, 'mt' => 103, 'ga' => 78, 'cy' => 185,
                'eu' => 161, 'gl' => 161, 'ca' => 161, 'af' => 160, 'sw' => 86,
                'am' => 56, 'km' => 31, 'lo' => 93, 'my' => 117, 'ne' => 122,
                'si' => 163, 'tl' => 134, 'ml' => 75, 'kn' => 75, 'gu' => 75,
                'or' => 75, 'mr' => 75, 'ps' => 131, 'hy' => 8
            ];

            if (isset($languageCountryMap[$movieData['original_language']])) {
                return $languageCountryMap[$movieData['original_language']];
            }
        }

        return $defaultCountryId;
    }

    protected function findCategoryIdByName($genreName)
    {
        if (isset($this->categoryNameToIdMap[$genreName])) {
            return $this->categoryNameToIdMap[$genreName];
        }

        $lowerGenreName = strtolower($genreName);
        if (isset($this->categoryNameToIdMap[$lowerGenreName])) {
            return $this->categoryNameToIdMap[$lowerGenreName];
        }

        $specialMappings = [
            'science fiction' => 'Sci-Fi',
            'tv movie' => 'Drama',
            'music' => 'Musical',
        ];

        if (isset($specialMappings[$lowerGenreName])) {
            $mappedName = $specialMappings[$lowerGenreName];
            if (isset($this->categoryNameToIdMap[$mappedName])) {
                return $this->categoryNameToIdMap[$mappedName];
            }
        }

        return null;
    }

    protected function getGenreMap()
    {
        return [
            28 => 'Action', 12 => 'Adventure', 16 => 'Animation', 35 => 'Comedy',
            80 => 'Crime', 99 => 'Documentary', 18 => 'Drama', 10751 => 'Family',
            14 => 'Fantasy', 36 => 'History', 27 => 'Horror', 10402 => 'Musical',
            9648 => 'Mystery', 10749 => 'Romance', 878 => 'Sci-Fi', 10770 => 'Drama',
            53 => 'Thriller', 10752 => 'War', 37 => 'Western'
        ];
    }

    protected function reconnectIfMissing()
    {
        try {
            DB::select('SELECT 1');
        } catch (\Exception $e) {
            $this->warn("\nDatabase connection lost. Reconnecting...");
            DB::disconnect();
            sleep(1);
            DB::reconnect();

            try {
                DB::select('SELECT 1');
                $this->info("\nDatabase connection re-established");
            } catch (\Exception $e) {
                $this->error("\nFailed to reconnect to database: " . $e->getMessage());
            }
        }
    }

    protected function safeTransaction(callable $callback)
    {
        $attempts = 0;
        $maxAttempts = 3;

        while ($attempts < $maxAttempts) {
            try {
                $this->reconnectIfMissing();
                DB::beginTransaction();
                $result = $callback();
                DB::commit();
                return $result;
            } catch (\PDOException $e) {
                if (DB::transactionLevel() > 0) {
                    try {
                        DB::rollBack();
                    } catch (\Exception $rollbackException) {
                        DB::disconnect();
                    }
                }

                if (strpos($e->getMessage(), 'server has gone away') !== false ||
                    strpos($e->getMessage(), 'Lost connection') !== false) {

                    $attempts++;
                    if ($attempts < $maxAttempts) {
                        $this->info("\nRetrying operation (attempt $attempts of $maxAttempts)...");
                        sleep(2);
                        continue;
                    }
                }
                throw $e;
            } catch (\Exception $e) {
                if (DB::transactionLevel() > 0) {
                    try {
                        DB::rollBack();
                    } catch (\Exception $rollbackException) {
                        // Ignore rollback exceptions
                    }
                }
                throw $e;
            }
        }
    }

    protected function displayFinalStats()
    {
        $elapsed = microtime(true) - $this->stats['start_time'];
        $minutes = floor($elapsed / 60);
        $seconds = round($elapsed % 60);

        $this->info("\n════════════════════════════════════════════════");
        $this->info("  UPLOAD COMPLETE!");
        $this->info("════════════════════════════════════════════════");

        $this->table(
            ['Metric', 'Count'],
            [
                ['New Movies', number_format($this->stats['movies_processed'])],
                ['Updated Movies', number_format($this->stats['movies_updated'])],
                ['Skipped', number_format($this->stats['movies_skipped'])],
                ['Failed', number_format($this->stats['movies_failed'])],
                ['Categories Assigned', number_format($this->stats['categories_assigned'])],
            ]
        );

        $this->info("\nTotal Time: {$minutes}m {$seconds}s");

        $totalProcessed = $this->stats['movies_processed'] + $this->stats['movies_updated'];
        if ($elapsed > 0) {
            $rate = round($totalProcessed / ($elapsed / 60), 1);
            $this->info("Average Rate: $rate movies/min");

            // Performance analysis
            if ($rate < 50) {
                $this->warn("\n⚠️  SLOW PERFORMANCE DETECTED");
                $this->warn("Try these optimizations:");
                $this->warn("  • Use --super-fast for maximum speed");
                $this->warn("  • Use --skip-images to avoid upload delays");
                $this->warn("  • Use --skip-duplicates to avoid DB queries");
                $this->warn("  • Increase --batch-size to 1000+");
            } elseif ($rate < 100) {
                $this->info("\n💡 Good performance. For even more speed, try --super-fast");
            } else {
                $this->info("\n🚀 Excellent performance!");
            }
        }
    }
}
