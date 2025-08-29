<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;
use GuzzleHttp\Pool;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Exception\RequestException;

class TmdbDownloader extends Command
{
    protected $signature = 'tmdb:download-all
                            {year=2024 : The year to download data for}
                            {--type=both : Type of content to download (movies, tv, both)}
                            {--output=storage/app/tmdb-data : Output directory}
                            {--download-images : Whether to download images}
                            {--poster-size=w500 : Poster image size}
                            {--backdrop-size=w780 : Backdrop image size}
                            {--memory-limit=2G : PHP memory limit}
                            {--concurrent=25 : Number of concurrent requests}
                            {--batch-size=100 : Batch size for processing}
                            {--resume : Resume from last checkpoint}
                            {--verify : Verify completeness after download}
                            {--skip-detailed : Skip generating the large detailed JSON file}';

    protected $description = 'Download ALL movies and TV series from TMDB for a specific year - FAST & COMPLETE';

    protected $apiKey = 'e7956087ccc04e8e4bd5659b55ed08ec';
    protected $baseUrl = 'https://api.themoviedb.org/3';
    protected $imageBaseUrl = 'https://image.tmdb.org/t/p/';

    private $client;
    private $concurrency;
    private $batchSize;
    private $stats = [
        'api_calls' => 0,
        'items_processed' => 0,
        'images_downloaded' => 0,
        'errors' => 0,
        'start_time' => 0
    ];

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $year = $this->argument('year');
        $type = $this->option('type');
        $outputPath = $this->option('output');
        $downloadImages = $this->option('download-images');
        $posterSize = $this->option('poster-size');
        $backdropSize = $this->option('backdrop-size');
        $memoryLimit = $this->option('memory-limit');
        $this->concurrency = (int) $this->option('concurrent');
        $this->batchSize = (int) $this->option('batch-size');
        $resume = $this->option('resume');
        $verify = $this->option('verify');
        $skipDetailed = $this->option('skip-detailed');

        // Initialize
        $this->stats['start_time'] = microtime(true);
        ini_set('memory_limit', $memoryLimit);

        $this->client = new Client([
            'timeout' => 30,
            'connect_timeout' => 10,
            'http_errors' => false,
        ]);

        if (!file_exists($outputPath)) {
            mkdir($outputPath, 0755, true);
        }

        $this->info("════════════════════════════════════════════════");
        $this->info("  TMDB COMPLETE DOWNLOADER FOR $year");
        $this->info("════════════════════════════════════════════════");
        $this->info("Settings:");
        $this->info("  • Concurrent connections: {$this->concurrency}");
        $this->info("  • Batch size: {$this->batchSize}");
        $this->info("  • Memory limit: $memoryLimit");
        $this->info("  • Download images: " . ($downloadImages ? 'Yes' : 'No'));
        $this->info("  • Skip detailed file: " . ($skipDetailed ? 'Yes' : 'No'));
        $this->info("════════════════════════════════════════════════\n");

        // Process content types
        if ($type === 'movies' || $type === 'both') {
            $this->processContentType('movie', $year, $outputPath, $downloadImages, $posterSize, $backdropSize, $resume, $skipDetailed);
        }

        if ($type === 'tv' || $type === 'both') {
            $this->processContentType('tv', $year, $outputPath, $downloadImages, $posterSize, $backdropSize, $resume, $skipDetailed);
        }

        // Final verification if requested
        if ($verify) {
            $this->verifyCompleteness($year, $type, $outputPath);
        }

        $this->displayFinalStats();
        return 0;
    }

    protected function processContentType($contentType, $year, $outputPath, $downloadImages, $posterSize, $backdropSize, $resume, $skipDetailed = false)
    {
        $contentTypeName = $contentType === 'movie' ? 'MOVIES' : 'TV SERIES';

        $this->info("\n╔════════════════════════════════════════════════╗");
        $this->info("║  Downloading ALL $contentTypeName for $year");
        $this->info("╚════════════════════════════════════════════════╝\n");

        // Setup directories
        $contentOutputPath = "$outputPath/$contentType";
        $detailsDir = "$contentOutputPath/details";
        $imagesDir = "$contentOutputPath/images";
        $checkpointFile = "$contentOutputPath/checkpoint.json";

        $this->createDirectories($contentOutputPath, $detailsDir, $imagesDir, $downloadImages);

        // Load checkpoint if resuming
        $checkpoint = $resume && file_exists($checkpointFile)
            ? json_decode(file_get_contents($checkpointFile), true)
            : null;

        // STEP 1: Fetch ALL items using multiple methods to ensure completeness
        $allItems = $this->fetchAllItemsComprehensive($contentType, $year, $contentOutputPath, $checkpoint);

        if (empty($allItems)) {
            $this->error("No items found for $year");
            return;
        }

        $totalItems = count($allItems);
        $this->info("✓ Found $totalItems unique $contentTypeName for $year\n");

        // STEP 2: Download all details
        $this->downloadAllDetails($allItems, $contentType, $detailsDir, $checkpointFile);

        // STEP 3: Download images if requested
        if ($downloadImages) {
            $this->downloadAllImages($allItems, $detailsDir, $imagesDir, $posterSize, $backdropSize);
        }

        // STEP 4: Generate summary files (memory optimized)
        $this->generateSummaryFilesOptimized($allItems, $detailsDir, $contentType, $contentOutputPath, $year, $skipDetailed);

        $this->info("✓ Completed $contentTypeName for $year\n");
    }

    protected function generateSummaryFilesOptimized($items, $detailsDir, $contentType, $outputPath, $year, $skipDetailed = false)
    {
        $this->info("Generating summary files (memory optimized)...");

        $summary = [
            'year' => $year,
            'type' => $contentType,
            'total_items' => count($items),
            'timestamp' => date('Y-m-d H:i:s'),
            'items' => []
        ];

        $genreStats = [];
        $languageStats = [];
        $processedCount = 0;

        // Open detailed file for streaming write (if not skipping)
        $detailedHandle = null;
        if (!$skipDetailed) {
            $detailedFile = "$outputPath/tmdb_{$contentType}_{$year}_detailed.json";
            $detailedHandle = fopen($detailedFile, 'w');
            fwrite($detailedHandle, "[\n");
        }

        $firstItem = true;
        $totalItems = count($items);

        // Process items in chunks to manage memory
        $chunks = array_chunk($items, 100); // Process 100 items at a time

        foreach ($chunks as $chunkIndex => $chunk) {
            $this->info("Processing summary chunk " . ($chunkIndex + 1) . "/" . count($chunks) . "...");

            foreach ($chunk as $item) {
                $itemId = $item['id'];
                $detailFile = "$detailsDir/$itemId.json";

                if (file_exists($detailFile)) {
                    // Read and process one file at a time
                    $detailContent = file_get_contents($detailFile);
                    $details = json_decode($detailContent, true);

                    if ($details) {
                        // Write to detailed file (streaming) if not skipping
                        if (!$skipDetailed && $detailedHandle) {
                            if (!$firstItem) {
                                fwrite($detailedHandle, ",\n");
                            }
                            fwrite($detailedHandle, $detailContent);
                            $firstItem = false;
                        }

                        // Create summary item (lightweight)
                        $summaryItem = [
                            'id' => $itemId,
                            'title' => $details[$contentType === 'movie' ? 'title' : 'name'] ?? 'Unknown',
                            'release_date' => $details[$contentType === 'movie' ? 'release_date' : 'first_air_date'] ?? null,
                            'popularity' => $details['popularity'] ?? 0,
                            'vote_average' => $details['vote_average'] ?? 0,
                            'vote_count' => $details['vote_count'] ?? 0,
                        ];

                        if ($contentType === 'movie') {
                            $summaryItem['runtime'] = $details['runtime'] ?? 0;
                            $summaryItem['budget'] = $details['budget'] ?? 0;
                            $summaryItem['revenue'] = $details['revenue'] ?? 0;
                        } else {
                            $summaryItem['seasons'] = $details['number_of_seasons'] ?? 0;
                            $summaryItem['episodes'] = $details['number_of_episodes'] ?? 0;
                            $summaryItem['status'] = $details['status'] ?? 'Unknown';
                        }

                        $summary['items'][] = $summaryItem;

                        // Collect stats
                        foreach ($details['genres'] ?? [] as $genre) {
                            $genreStats[$genre['name']] = ($genreStats[$genre['name']] ?? 0) + 1;
                        }

                        $lang = $details['original_language'] ?? 'unknown';
                        $languageStats[$lang] = ($languageStats[$lang] ?? 0) + 1;

                        $processedCount++;

                        // Show progress every 1000 items
                        if ($processedCount % 1000 == 0) {
                            $this->info("  Processed $processedCount/$totalItems items...");
                        }
                    }

                    // Free memory immediately
                    unset($details);
                    unset($detailContent);
                }
            }

            // Force garbage collection after each chunk
            gc_collect_cycles();
        }

        // Close detailed JSON file if it was created
        if (!$skipDetailed && $detailedHandle) {
            fwrite($detailedHandle, "\n]");
            fclose($detailedHandle);
        }

        // Sort items by popularity
        usort($summary['items'], function($a, $b) {
            return $b['popularity'] <=> $a['popularity'];
        });

        // Add statistics
        $summary['statistics'] = [
            'genres' => $genreStats,
            'languages' => $languageStats,
            'total_processed' => $processedCount,
            'top_rated' => array_slice(array_filter($summary['items'], function($item) {
                return $item['vote_count'] > 10;
            }), 0, 20),
            'most_popular' => array_slice($summary['items'], 0, 20)
        ];

        // Save summary file
        file_put_contents("$outputPath/tmdb_{$contentType}_{$year}_summary.json", json_encode($summary, JSON_PRETTY_PRINT));

        // Create CSV for easy viewing
        $this->createCSV($summary['items'], "$outputPath/tmdb_{$contentType}_{$year}.csv", $contentType);

        $this->info("✓ Generated summary files");
        $this->info("  - Summary: tmdb_{$contentType}_{$year}_summary.json");
        if (!$skipDetailed) {
            $this->info("  - Detailed: tmdb_{$contentType}_{$year}_detailed.json");
        } else {
            $this->info("  - Detailed file skipped (--skip-detailed used)");
        }
        $this->info("  - CSV: tmdb_{$contentType}_{$year}.csv");
        $this->info("  - Processed: $processedCount items\n");
    }

    protected function fetchAllItemsComprehensive($contentType, $year, $outputPath, $checkpoint)
    {
        $basicDataFile = "$outputPath/tmdb_{$contentType}_{$year}_complete.json";

        // Check if we have cached complete data
        if (!$checkpoint && file_exists($basicDataFile)) {
            $this->info("Loading existing complete dataset...");
            $items = json_decode(file_get_contents($basicDataFile), true);
            $this->info("Loaded " . count($items) . " items from cache");
            return $items;
        }

        $this->info("Fetching ALL $contentType items for $year...");
        $this->info("This will use multiple API endpoints to ensure completeness\n");

        $allItems = [];
        $uniqueIds = [];

        // Method 1: Primary discover endpoint with date sorting
        $this->info("Method 1: Fetching by release date...");
        $items1 = $this->fetchByDiscoverMethod($contentType, $year, 'primary_release_date.asc');
        foreach ($items1 as $item) {
            if (!isset($uniqueIds[$item['id']])) {
                $uniqueIds[$item['id']] = true;
                $allItems[] = $item;
            }
        }
        $this->info("  → Found " . count($items1) . " items (Total unique: " . count($allItems) . ")");

        // Method 2: Fetch with popularity sorting (catches different items)
        $this->info("Method 2: Fetching by popularity...");
        $items2 = $this->fetchByDiscoverMethod($contentType, $year, 'popularity.desc');
        $added = 0;
        foreach ($items2 as $item) {
            if (!isset($uniqueIds[$item['id']])) {
                $uniqueIds[$item['id']] = true;
                $allItems[] = $item;
                $added++;
            }
        }
        $this->info("  → Found " . count($items2) . " items (Added $added new)");

        // Method 3: Fetch by vote count (catches more items)
        $this->info("Method 3: Fetching by vote count...");
        $items3 = $this->fetchByDiscoverMethod($contentType, $year, 'vote_count.desc');
        $added = 0;
        foreach ($items3 as $item) {
            if (!isset($uniqueIds[$item['id']])) {
                $uniqueIds[$item['id']] = true;
                $allItems[] = $item;
                $added++;
            }
        }
        $this->info("  → Found " . count($items3) . " items (Added $added new)");

        // Method 4: Month-by-month search for movies (catches regional releases)
        if ($contentType === 'movie') {
            $this->info("Method 4: Fetching month by month...");
            $monthItems = $this->fetchByMonthlySearch($year);
            $added = 0;
            foreach ($monthItems as $item) {
                if (!isset($uniqueIds[$item['id']])) {
                    $uniqueIds[$item['id']] = true;
                    $allItems[] = $item;
                    $added++;
                }
            }
            $this->info("  → Added $added new items from monthly search");
        }

        // Save complete dataset
        file_put_contents($basicDataFile, json_encode($allItems, JSON_PRETTY_PRINT));
        $this->info("\n✓ Saved complete dataset: " . count($allItems) . " unique items");

        return $allItems;
    }

    protected function fetchByDiscoverMethod($contentType, $year, $sortBy)
    {
        $allResults = [];
        $page = 1;
        $maxPages = 500; // TMDB limit

        // Base parameters
        $params = [
            'api_key' => $this->apiKey,
            'sort_by' => $sortBy,
            'include_adult' => 'true',
            'include_video' => 'true',
            'page' => $page
        ];

        // Year parameters differ for movies vs TV
        if ($contentType === 'movie') {
            $params['primary_release_year'] = $year;
            // Also search by year range for better coverage
            $params['primary_release_date.gte'] = "$year-01-01";
            $params['primary_release_date.lte'] = "$year-12-31";
        } else {
            $params['first_air_date_year'] = $year;
            $params['first_air_date.gte'] = "$year-01-01";
            $params['first_air_date.lte'] = "$year-12-31";
        }

        // Get first page to determine total
        $response = Http::timeout(30)->get("$this->baseUrl/discover/$contentType", $params);

        if ($response->failed()) {
            $this->error("Failed to fetch discover data");
            return [];
        }

        $data = $response->json();
        $totalPages = min($data['total_pages'] ?? 1, $maxPages);
        $totalResults = $data['total_results'] ?? 0;

        if ($totalResults === 0) {
            return [];
        }

        // Prepare all page requests
        $requests = [];
        for ($p = 1; $p <= $totalPages; $p++) {
            $pageParams = $params;
            $pageParams['page'] = $p;
            $url = "$this->baseUrl/discover/$contentType?" . http_build_query($pageParams);
            $requests[] = new Request('GET', $url);
        }

        // Fetch all pages concurrently
        $bar = $this->output->createProgressBar($totalPages);
        $bar->start();

        $pool = new Pool($this->client, $requests, [
            'concurrency' => min($this->concurrency, 10),
            'fulfilled' => function ($response, $index) use (&$allResults, $bar) {
                $data = json_decode($response->getBody(), true);
                if (isset($data['results'])) {
                    $allResults = array_merge($allResults, $data['results']);
                }
                $this->stats['api_calls']++;
                $bar->advance();
            },
            'rejected' => function ($reason, $index) use ($bar) {
                $this->stats['errors']++;
                $bar->advance();
            },
        ]);

        $promise = $pool->promise();
        $promise->wait();

        $bar->finish();
        $this->newLine();

        return $allResults;
    }

    protected function fetchByMonthlySearch($year)
    {
        $allResults = [];
        $requests = [];

        // Search month by month for better coverage
        for ($month = 1; $month <= 12; $month++) {
            $startDate = sprintf("%d-%02d-01", $year, $month);
            $endDate = sprintf("%d-%02d-%02d", $year, $month, cal_days_in_month(CAL_GREGORIAN, $month, $year));

            $params = [
                'api_key' => $this->apiKey,
                'primary_release_date.gte' => $startDate,
                'primary_release_date.lte' => $endDate,
                'sort_by' => 'primary_release_date.asc',
                'page' => 1
            ];

            $url = "$this->baseUrl/discover/movie?" . http_build_query($params);
            $requests[] = new Request('GET', $url);
        }

        $bar = $this->output->createProgressBar(12);
        $bar->start();

        $pool = new Pool($this->client, $requests, [
            'concurrency' => 6,
            'fulfilled' => function ($response, $index) use (&$allResults, $bar) {
                $data = json_decode($response->getBody(), true);
                if (isset($data['results'])) {
                    $allResults = array_merge($allResults, $data['results']);
                }
                $bar->advance();
            },
            'rejected' => function ($reason, $index) use ($bar) {
                $bar->advance();
            },
        ]);

        $promise = $pool->promise();
        $promise->wait();

        $bar->finish();
        $this->newLine();

        return $allResults;
    }

    protected function downloadAllDetails($items, $contentType, $detailsDir, $checkpointFile)
    {
        $this->info("Downloading detailed information for " . count($items) . " items...");

        $chunks = array_chunk($items, $this->batchSize);
        $totalChunks = count($chunks);
        $processedCount = 0;
        $skippedCount = 0;

        $bar = $this->output->createProgressBar(count($items));
        $bar->start();

        foreach ($chunks as $chunkIndex => $chunk) {
            $requests = [];
            $itemMap = [];

            foreach ($chunk as $item) {
                $itemId = $item['id'];
                $detailFile = "$detailsDir/$itemId.json";

                // Skip if already exists
                if (file_exists($detailFile) && filesize($detailFile) > 100) {
                    $skippedCount++;
                    $bar->advance();
                    continue;
                }

                // Prepare request with additional data
                $appendData = $contentType === 'movie'
                    ? 'credits,videos,keywords,recommendations,similar,reviews,release_dates'
                    : 'credits,videos,keywords,recommendations,similar,reviews,content_ratings,seasons';

                $url = "$this->baseUrl/$contentType/$itemId?api_key={$this->apiKey}&append_to_response=$appendData";
                $requests[] = new Request('GET', $url);
                $itemMap[] = $itemId;
            }

            if (empty($requests)) {
                continue;
            }

            // Process batch
            $pool = new Pool($this->client, $requests, [
                'concurrency' => $this->concurrency,
                'fulfilled' => function ($response, $index) use ($itemMap, $detailsDir, &$processedCount, $bar) {
                    $itemId = $itemMap[$index];
                    $data = json_decode($response->getBody(), true);

                    if (!empty($data['id'])) {
                        file_put_contents("$detailsDir/$itemId.json", json_encode($data, JSON_PRETTY_PRINT));
                        $processedCount++;
                        $this->stats['items_processed']++;
                    }
                    $this->stats['api_calls']++;
                    $bar->advance();
                },
                'rejected' => function ($reason, $index) use ($itemMap, $bar) {
                    $this->stats['errors']++;
                    $bar->advance();
                },
            ]);

            $promise = $pool->promise();
            $promise->wait();

            // Save checkpoint
            if ($chunkIndex % 5 === 0) {
                file_put_contents($checkpointFile, json_encode([
                    'last_chunk' => $chunkIndex,
                    'processed' => $processedCount,
                    'timestamp' => time()
                ]));
            }

            // Brief pause between chunks to respect rate limits
            if ($chunkIndex < $totalChunks - 1) {
                usleep(100000); // 0.1 second
            }
        }

        $bar->finish();
        $this->newLine();

        $this->info("✓ Downloaded $processedCount new items (Skipped $skippedCount existing)\n");
    }

    protected function downloadAllImages($items, $detailsDir, $imagesDir, $posterSize, $backdropSize)
    {
        $this->info("Preparing image downloads...");

        $imageRequests = [];
        $imageMap = [];
        $totalPossible = count($items) * 2; // poster + backdrop per item

        foreach ($items as $item) {
            $itemId = $item['id'];
            $detailFile = "$detailsDir/$itemId.json";

            if (!file_exists($detailFile)) {
                continue;
            }

            $details = json_decode(file_get_contents($detailFile), true);

            // Queue poster
            if (!empty($details['poster_path'])) {
                $posterFile = "$imagesDir/posters/{$itemId}_poster.jpg";
                if (!file_exists($posterFile) || filesize($posterFile) < 1000) {
                    $url = $this->imageBaseUrl . $posterSize . $details['poster_path'];
                    $imageRequests[] = new Request('GET', $url);
                    $imageMap[] = ['file' => $posterFile, 'type' => 'poster', 'id' => $itemId];
                }
            }

            // Queue backdrop
            if (!empty($details['backdrop_path'])) {
                $backdropFile = "$imagesDir/backdrops/{$itemId}_backdrop.jpg";
                if (!file_exists($backdropFile) || filesize($backdropFile) < 1000) {
                    $url = $this->imageBaseUrl . $backdropSize . $details['backdrop_path'];
                    $imageRequests[] = new Request('GET', $url);
                    $imageMap[] = ['file' => $backdropFile, 'type' => 'backdrop', 'id' => $itemId];
                }
            }
        }

        if (empty($imageRequests)) {
            $this->info("✓ All images already downloaded\n");
            return;
        }

        $totalImages = count($imageRequests);
        $this->info("Downloading $totalImages images...");

        $bar = $this->output->createProgressBar($totalImages);
        $bar->start();

        $downloadedCount = 0;
        $chunks = array_chunk($imageRequests, $this->batchSize * 2);
        $mapChunks = array_chunk($imageMap, $this->batchSize * 2);

        foreach ($chunks as $index => $chunk) {
            $currentMap = $mapChunks[$index];

            $pool = new Pool($this->client, $chunk, [
                'concurrency' => $this->concurrency * 2, // More concurrency for images
                'fulfilled' => function ($response, $idx) use ($currentMap, &$downloadedCount, $bar) {
                    $info = $currentMap[$idx];
                    $content = $response->getBody()->getContents();

                    if (strlen($content) > 1000) { // Verify it's a real image
                        file_put_contents($info['file'], $content);
                        $downloadedCount++;
                        $this->stats['images_downloaded']++;
                    }
                    $bar->advance();
                },
                'rejected' => function ($reason, $idx) use ($bar) {
                    $bar->advance();
                },
            ]);

            $promise = $pool->promise();
            $promise->wait();
        }

        $bar->finish();
        $this->newLine();

        $this->info("✓ Downloaded $downloadedCount images\n");
    }

    protected function createCSV($items, $filename, $contentType)
    {
        $fp = fopen($filename, 'w');

        // Headers
        if ($contentType === 'movie') {
            fputcsv($fp, ['ID', 'Title', 'Release Date', 'Popularity', 'Rating', 'Votes', 'Runtime', 'Budget', 'Revenue']);
        } else {
            fputcsv($fp, ['ID', 'Name', 'First Air Date', 'Popularity', 'Rating', 'Votes', 'Seasons', 'Episodes', 'Status']);
        }

        // Data
        foreach ($items as $item) {
            if ($contentType === 'movie') {
                fputcsv($fp, [
                    $item['id'],
                    $item['title'],
                    $item['release_date'],
                    $item['popularity'],
                    $item['vote_average'],
                    $item['vote_count'],
                    $item['runtime'],
                    $item['budget'],
                    $item['revenue']
                ]);
            } else {
                fputcsv($fp, [
                    $item['id'],
                    $item['title'],
                    $item['release_date'],
                    $item['popularity'],
                    $item['vote_average'],
                    $item['vote_count'],
                    $item['seasons'],
                    $item['episodes'],
                    $item['status'] ?? 'Unknown'
                ]);
            }
        }

        fclose($fp);
    }

    protected function verifyCompleteness($year, $type, $outputPath)
    {
        $this->info("\n════════════════════════════════════════════════");
        $this->info("  VERIFYING COMPLETENESS");
        $this->info("════════════════════════════════════════════════\n");

        if ($type === 'movies' || $type === 'both') {
            $this->verifyContentType('movie', $year, $outputPath);
        }

        if ($type === 'tv' || $type === 'both') {
            $this->verifyContentType('tv', $year, $outputPath);
        }
    }

    protected function verifyContentType($contentType, $year, $outputPath)
    {
        $contentPath = "$outputPath/$contentType";
        $basicFile = "$contentPath/tmdb_{$contentType}_{$year}_complete.json";
        $detailsDir = "$contentPath/details";

        if (!file_exists($basicFile)) {
            $this->warn("No basic data file found for $contentType");
            return;
        }

        $items = json_decode(file_get_contents($basicFile), true);
        $totalItems = count($items);
        $detailsCount = 0;
        $missingDetails = [];

        foreach ($items as $item) {
            $detailFile = "$detailsDir/{$item['id']}.json";
            if (file_exists($detailFile) && filesize($detailFile) > 100) {
                $detailsCount++;
            } else {
                $missingDetails[] = $item['id'];
            }
        }

        $completeness = round(($detailsCount / $totalItems) * 100, 2);

        $this->table(
            ['Metric', 'Value'],
            [
                ['Content Type', strtoupper($contentType)],
                ['Total Items', number_format($totalItems)],
                ['Details Downloaded', number_format($detailsCount)],
                ['Missing Details', number_format(count($missingDetails))],
                ['Completeness', "$completeness%"],
            ]
        );

        if (!empty($missingDetails)) {
            file_put_contents("$contentPath/missing_details.txt", implode("\n", $missingDetails));
            $this->warn("Missing detail IDs saved to missing_details.txt");
        }
    }

    protected function displayFinalStats()
    {
        $elapsed = microtime(true) - $this->stats['start_time'];
        $minutes = floor($elapsed / 60);
        $seconds = round($elapsed % 60);

        $this->info("\n════════════════════════════════════════════════");
        $this->info("  DOWNLOAD COMPLETE!");
        $this->info("════════════════════════════════════════════════");

        $this->table(
            ['Metric', 'Value'],
            [
                ['Total Time', "{$minutes}m {$seconds}s"],
                ['API Calls', number_format($this->stats['api_calls'])],
                ['Items Processed', number_format($this->stats['items_processed'])],
                ['Images Downloaded', number_format($this->stats['images_downloaded'])],
                ['Errors', number_format($this->stats['errors'])],
                ['Avg Speed', round($this->stats['items_processed'] / ($elapsed / 60), 1) . ' items/min'],
            ]
        );
    }

    protected function createDirectories($contentOutputPath, $detailsDir, $imagesDir, $downloadImages)
    {
        foreach ([$contentOutputPath, $detailsDir] as $dir) {
            if (!file_exists($dir)) {
                mkdir($dir, 0755, true);
            }
        }

        if ($downloadImages) {
            foreach ([$imagesDir, "$imagesDir/posters", "$imagesDir/backdrops"] as $dir) {
                if (!file_exists($dir)) {
                    mkdir($dir, 0755, true);
                }
            }
        }
    }
}
