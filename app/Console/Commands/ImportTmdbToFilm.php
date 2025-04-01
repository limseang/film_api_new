<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Film;
use App\Models\FilmCategory;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\UploadedFile;
use App\Http\Controllers\UploadController;
use Exception;

class ImportTmdbToFilm extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:tmdb-to-film {source_dir=storage/app/tmdb-data}
                           {--year=2022}
                           {--limit=}
                           {--type=1}
                           {--default-runtime=90}
                           {--skip-upload}
                           {--memory=512M}
                           {--batch-size=10}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import TMDB movie data to Film model with OSS image upload, FilmCategory mapping, and country mapping';

    /**
     * Categories from the database
     *
     * @var array
     */
    protected $categories = [];

    /**
     * Category name to ID mapping
     *
     * @var array
     */
    protected $categoryNameToIdMap = [];

    /**
     * Countries from the database
     *
     * @var array
     */
    protected $countries = [];

    /**
     * Country name to ID mapping
     *
     * @var array
     */
    protected $countryNameToIdMap = [];

    /**
     * Country code to ID mapping
     *
     * @var array
     */
    protected $countryCodeToIdMap = [];

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Set memory limit from command parameter
        $memoryLimit = $this->option('memory');
        ini_set('memory_limit', $memoryLimit);
        $this->info("Memory limit set to $memoryLimit");

        // Batch size for processing
        $batchSize = (int) $this->option('batch-size');

        $sourceDir = $this->argument('source_dir');
        $year = $this->option('year');
        $limit = $this->option('limit');
        $filmType = $this->option('type');
        $defaultRuntime = $this->option('default-runtime');
        $skipUpload = $this->option('skip-upload');

        // Test database connection first
        try {
            DB::connection()->getPdo();
            $this->info("Connected to database: " . DB::connection()->getDatabaseName());
        } catch (\Exception $e) {
            $this->error("Database connection failed: " . $e->getMessage());
            return 1;
        }

        // Load categories from database
        $this->loadCategories();

        // Load countries from database
        $this->loadCountries();

        if (!file_exists($sourceDir)) {
            $this->error("Source directory not found: $sourceDir");
            return 1;
        }

        // Check if images directory exists (only if not skipping uploads)
        $imagesDir = "$sourceDir/images";
        $postersDir = "$imagesDir/posters";
        $backdropsDir = "$imagesDir/backdrops";

        if (!$skipUpload && (!file_exists($imagesDir) || !file_exists($postersDir))) {
            $this->error("Images directory structure not found. Run the download command first.");
            return 1;
        }

        // Look for movie data
        $basicDataFile = "$sourceDir/tmdb_movies_{$year}_basic.json";
        $detailedDataFile = "$sourceDir/tmdb_movies_{$year}_details_complete.json";
        $idListFile = "$sourceDir/tmdb_movies_{$year}_id_list.json";
        $detailsDir = "$sourceDir/movie_details";

        // Initialize counters
        $importCount = 0;
        $errorCount = 0;
        $skippedCount = 0;
        $categoryCount = 0;

        // Create upload controller only if not skipping uploads
        $uploadController = $skipUpload ? null : new UploadController();

        // Process data based on available files
        if (file_exists($detailedDataFile)) {
            $this->info("Using line-by-line processing for detailed file");
            $this->processJsonFileByLine(
                $detailedDataFile,
                $limit,
                $filmType,
                $defaultRuntime,
                $skipUpload,
                $uploadController,
                $postersDir,
                $backdropsDir,
                $importCount,
                $errorCount,
                $skippedCount,
                $categoryCount,
                $batchSize
            );
        }
        elseif (file_exists($idListFile) && is_dir($detailsDir)) {
            $this->info("Using individual movie files");
            $this->processIndividualFiles(
                $idListFile,
                $detailsDir,
                $limit,
                $filmType,
                $defaultRuntime,
                $skipUpload,
                $uploadController,
                $postersDir,
                $backdropsDir,
                $importCount,
                $errorCount,
                $skippedCount,
                $categoryCount,
                $batchSize
            );
        }
        elseif (file_exists($basicDataFile)) {
            $this->info("Using line-by-line processing for basic file");
            $this->processJsonFileByLine(
                $basicDataFile,
                $limit,
                $filmType,
                $defaultRuntime,
                $skipUpload,
                $uploadController,
                $postersDir,
                $backdropsDir,
                $importCount,
                $errorCount,
                $skippedCount,
                $categoryCount,
                $batchSize
            );
        }
        else {
            $this->error("No TMDB movie data found. Run the download command first.");
            return 1;
        }

        $this->newLine(2);
        $this->info("Import completed:");
        $this->info("- Successfully imported: $importCount films");
        $this->info("- Total categories assigned: $categoryCount");
        $this->info("- Skipped: $skippedCount films");
        $this->info("- Errors: $errorCount films");

        return 0;
    }

    /**
     * Ultra memory-efficient method to process JSON file line by line
     */
    protected function processJsonFileByLine($filePath, $limit, $filmType, $defaultRuntime, $skipUpload, $uploadController, $postersDir, $backdropsDir, &$importCount, &$errorCount, &$skippedCount, &$categoryCount, $batchSize)
    {
        $this->info("Reading file: $filePath");

        // Get file size for progress reporting
        $fileSize = filesize($filePath);
        $this->info("File size: " . round($fileSize / 1024 / 1024, 2) . " MB");

        // Open the file for reading
        $handle = @fopen($filePath, "r");
        if (!$handle) {
            $this->error("Could not open file: $filePath");
            return;
        }

        // Create a progress bar
        $bar = $this->output->createProgressBar(100); // We'll update in percentage
        $bar->start();

        // Read the first line to check if it starts with [ (JSON array)
        $firstChar = fread($handle, 1);
        if ($firstChar !== '[') {
            $this->error("File does not start with '[', not a JSON array");
            fclose($handle);
            return;
        }

        // Variables for tracking
        $movieCount = 0;
        $buffer = '';
        $inString = false;
        $escapeNext = false;
        $braceDepth = 0;
        $foundStart = false;
        $lastPercentage = 0;

        // Process the file character by character
        while (!feof($handle)) {
            $char = fread($handle, 1);

            // Skip whitespace outside strings to save memory
            if (!$inString && ($char === ' ' || $char === "\n" || $char === "\r" || $char === "\t")) {
                continue;
            }

            // Handle string escaping
            if ($escapeNext) {
                $buffer .= $char;
                $escapeNext = false;
                continue;
            }

            // Handle quote escaping
            if ($char === '\\' && $inString) {
                $buffer .= $char;
                $escapeNext = true;
                continue;
            }

            // Handle string boundaries
            if ($char === '"' && !$escapeNext) {
                $inString = !$inString;
            }

            // Track JSON object depth
            if (!$inString) {
                if ($char === '{') {
                    if (!$foundStart && $braceDepth === 0) {
                        $foundStart = true;
                    }
                    $braceDepth++;
                } else if ($char === '}') {
                    $braceDepth--;

                    // When braceDepth returns to 0, we've found a complete object
                    if ($foundStart && $braceDepth === 0) {
                        $buffer .= $char;

                        // Process the movie object
                        $movieData = json_decode($buffer, true);
                        if ($movieData) {
                            // Process in batches with explicit memory cleanup
                            if ($movieCount % $batchSize === 0 && $movieCount > 0) {
                                $this->info("\nProcessing batch " . floor($movieCount / $batchSize));
                                // Force garbage collection
                                gc_collect_cycles();
                            }

                            $this->processMovie(
                                $movieData,
                                $filmType,
                                $defaultRuntime,
                                $skipUpload,
                                $uploadController,
                                $postersDir,
                                $backdropsDir,
                                $importCount,
                                $errorCount,
                                $skippedCount,
                                $categoryCount
                            );

                            $movieCount++;

                            // Update progress
                            $currentPosition = ftell($handle);
                            $percentage = min(100, floor(($currentPosition / $fileSize) * 100));
                            if ($percentage > $lastPercentage) {
                                $bar->setProgress($percentage);
                                $lastPercentage = $percentage;
                            }

                            // Free memory
                            unset($movieData);

                            // Check limit
                            if ($limit && $movieCount >= $limit) {
                                break;
                            }
                        } else {
                            $this->error("\nFailed to parse JSON object: " . substr($buffer, 0, 50) . "...");
                        }

                        // Reset buffer and state for next movie
                        $buffer = '';
                        $foundStart = false;
                        continue;
                    }
                }
            }

            // Only append to buffer if we've found the start of an object
            if ($foundStart) {
                $buffer .= $char;
            }

            // Skip commas between objects at root level
            if (!$inString && $braceDepth === 0 && $char === ',') {
                // Do nothing, just skip the comma
                continue;
            }

            // Hard memory limit safety check - stop if buffer gets too large
            if (strlen($buffer) > 10 * 1024 * 1024) { // 10MB
                $this->error("\nBuffer size exceeded safety limit. Stopping import.");
                break;
            }
        }

        fclose($handle);
        $bar->finish();
        $this->newLine();
        $this->info("Processed $movieCount movies from file");
    }

    /**
     * Process individual movie files with ultra-low memory usage
     */
    protected function processIndividualFiles($idListFile, $detailsDir, $limit, $filmType, $defaultRuntime, $skipUpload, $uploadController, $postersDir, $backdropsDir, &$importCount, &$errorCount, &$skippedCount, &$categoryCount, $batchSize)
    {
        // Read ID list file in chunks to avoid loading everything at once
        $this->info("Reading ID list: $idListFile");
        $idsContent = file_get_contents($idListFile);
        $movieIds = json_decode($idsContent, true);
        unset($idsContent); // Free memory

        if (!is_array($movieIds)) {
            $this->error("Failed to parse ID list as JSON array");
            return;
        }

        $totalIds = count($movieIds);
        $this->info("Found $totalIds movie IDs");

        if ($limit && is_numeric($limit) && $limit < $totalIds) {
            $movieIds = array_slice($movieIds, 0, $limit);
            $this->info("Limiting import to the first $limit movies");
        }

        $this->info("Processing " . count($movieIds) . " individual movie files");
        $bar = $this->output->createProgressBar(count($movieIds));
        $bar->start();

        $movieCount = 0;

        foreach ($movieIds as $index => $id) {
            // Process in batches
            if ($movieCount % $batchSize === 0 && $movieCount > 0) {
                $this->info("\nProcessing batch " . floor($movieCount / $batchSize));
                gc_collect_cycles(); // Force garbage collection
            }

            $movieFile = "$detailsDir/$id.json";
            if (file_exists($movieFile)) {
                // Read and process one file at a time
                $movieJson = file_get_contents($movieFile);
                $movieData = json_decode($movieJson, true);
                unset($movieJson); // Free memory immediately

                if ($movieData) {
                    $this->processMovie(
                        $movieData,
                        $filmType,
                        $defaultRuntime,
                        $skipUpload,
                        $uploadController,
                        $postersDir,
                        $backdropsDir,
                        $importCount,
                        $errorCount,
                        $skippedCount,
                        $categoryCount
                    );

                    $movieCount++;

                    // Free memory
                    unset($movieData);
                }
            }

            // Free memory by removing processed ID
            unset($movieIds[$index]);

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Processed $movieCount individual movie files");
    }

    /**
     * Process an individual movie with minimal memory footprint
     */
    protected function processMovie($movieData, $filmType, $defaultRuntime, $skipUpload, $uploadController, $postersDir, $backdropsDir, &$importCount, &$errorCount, &$skippedCount, &$categoryCount)
    {
        $title = $movieData['title'] ?? 'Unknown';

        try {
            // Check if the film already exists to avoid duplicates
            $this->reconnectIfMissing(); // Check connection before query
            $existingFilm = Film::where('title', $title)
                ->where('release_date', $this->formatReleaseDate($movieData['release_date'] ?? null))
                ->first();

            if ($existingFilm) {
                $skippedCount++;
                return;
            }

            // Use the safe transaction wrapper
            $this->safeTransaction(function() use ($movieData, $filmType, $defaultRuntime, $skipUpload, $uploadController, $postersDir, $backdropsDir, &$importCount, &$categoryCount, $title) {
                // Create new film object with minimal data extraction
                $film = new Film();
                $film->title = $title;
                $film->overview = mb_substr($movieData['overview'] ?? '', 0, 1000); // Limit overview length
                $film->release_date = $this->formatReleaseDate($movieData['release_date'] ?? null);
                $film->view = 0;
                $film->rating = '0';
                $film->type = $filmType;
                $film->running_time = $movieData['runtime'] ?? $defaultRuntime;
                $film->language = $this->findCountryIdForMovie($movieData);
                $film->category = '';
                $film->country_id = $this->findCountryIdForMovie($movieData);

                // Extract minimal required data, avoiding deep array operations

                // Map keywords to tags - only if they exist
                if (!empty($movieData['keywords']['keywords'])) {
                    // Extract just 5 keywords max, with minimal memory operations
                    $tagArray = [];
                    $count = 0;
                    foreach ($movieData['keywords']['keywords'] as $keyword) {
                        if (isset($keyword['name'])) {
                            $tagArray[] = $keyword['name'];
                            $count++;
                            if ($count >= 5) break;
                        }
                    }
                    $film->tag = implode(', ', $tagArray);
                    unset($tagArray); // Free memory
                }

                // Map trailer - only extract first YouTube trailer
                if (!empty($movieData['videos']['results'])) {
                    foreach ($movieData['videos']['results'] as $video) {
                        if (($video['type'] ?? '') === 'Trailer' && ($video['site'] ?? '') === 'YouTube') {
                            $film->trailer = 'https://www.youtube.com/watch?v=' . $video['key'];
                            break; // Just get the first one
                        }
                    }
                }

                // Map directors - extract first director only to save memory
                if (!empty($movieData['credits']['crew'])) {
                    foreach ($movieData['credits']['crew'] as $person) {
                        if (($person['job'] ?? '') === 'Director') {
                            $film->director = $person['name'];
                            break; // Just get the first one
                        }
                    }
                }

                // Handle image uploads if not skipping
                if (!$skipUpload) {
                    $posterUploaded = false;

                    // Find and upload poster (attempt to use TMDB poster first)
                    if (!empty($movieData['poster_path'])) {
                        try {
                            // Look for poster file in the posters directory
                            $posterPattern = $postersDir . "/{$movieData['id']}_poster_*.jpg";
                            $posterFiles = glob($posterPattern);

                            if (!empty($posterFiles)) {
                                // Use the first found poster file
                                $posterFile = $posterFiles[0];

                                // Create uploadable file
                                $uploadedFile = new UploadedFile(
                                    $posterFile,
                                    basename($posterFile),
                                    mime_content_type($posterFile),
                                    null,
                                    true
                                );

                                // Upload to OSS and get the ID directly
                                $film->poster = $uploadController->uploadFile($uploadedFile, 'film');
                                $posterUploaded = true;
                            }
                        } catch (\Exception $e) {
                            // Log exception but continue with default poster
                        }
                    }

                    // If poster upload failed or no poster available, use default poster from assets
                    if (!$posterUploaded) {
                        try {
                            // Path to your default poster in assets
                            $defaultPosterPath = public_path('assets/images/no_poster.png');

                            if (file_exists($defaultPosterPath)) {
                                // Create uploadable file from default poster
                                $uploadedFile = new UploadedFile(
                                    $defaultPosterPath,
                                    'no_poster.png',
                                    mime_content_type($defaultPosterPath),
                                    null,
                                    true
                                );

                                // Upload default poster
                                $film->poster = $uploadController->uploadFile($uploadedFile, 'film');
                            } else {
                                // If even the default poster is missing, use a hardcoded ID
                                $film->poster = '3442'; // Fallback to hardcoded poster ID
                            }
                        } catch (\Exception $e) {
                            // Last resort - use hardcoded poster ID
                            $film->poster = '3442'; // Fallback to hardcoded poster ID
                        }
                    }

                    // Find and upload cover (optional) - only if memory allows
                    if (!empty($movieData['backdrop_path']) && file_exists($backdropsDir)) {
                        try {
                            // Look for backdrop file in the backdrops directory
                            $backdropPattern = $backdropsDir . "/{$movieData['id']}_backdrop_*.jpg";
                            $backdropFiles = glob($backdropPattern);

                            if (!empty($backdropFiles)) {
                                // Use the first found backdrop file
                                $backdropFile = $backdropFiles[0];

                                // Create uploadable file
                                $uploadedFile = new UploadedFile(
                                    $backdropFile,
                                    basename($backdropFile),
                                    mime_content_type($backdropFile),
                                    null,
                                    true
                                );

                                // Try to upload - but don't fail if it doesn't work
                                $film->cover = $uploadController->uploadFile($uploadedFile, 'film');
                            }
                        } catch (\Exception $e) {
                            // Just ignore cover upload failures
                        }
                    }
                } else {
                    // If skipping uploads, set default values for the required fields
                    $film->poster = '3442'; // Use a default poster ID
                }

                // Save the film with all its properties
                $film->save();

                // Now that we have the film ID, we can assign categories
                $filmCategoryCount = $this->assignCategoriesToFilm($film->id, $movieData);
                $categoryCount += $filmCategoryCount;

                $importCount++;

                // Only output occasionally to save memory from console buffer
                if ($importCount <= 5 || $importCount % 50 === 0) {
                    $this->info("\nImported: $title (Film ID: {$film->id})");
                }

                return true;
            });

        } catch (Exception $e) {
            $errorCount++;

            // Only output occasionally to save memory from console buffer
            if ($errorCount <= 5 || $errorCount % 50 === 0) {
                $this->error("\nError importing '$title': " . $e->getMessage());
            }
        }
    }


    protected function reconnectIfMissing()
    {
        try {
            // Test the connection with a simple query
            DB::select('SELECT 1');
        } catch (\Exception $e) {
            $this->warn("\nDatabase connection lost. Reconnecting...");

            // Close the existing connection
            DB::disconnect();

            // Wait a moment before reconnecting
            sleep(1);

            // Reconnect
            DB::reconnect();

            // Test the new connection
            try {
                DB::select('SELECT 1');
                $this->info("\nDatabase connection re-established");
            } catch (\Exception $e) {
                $this->error("\nFailed to reconnect to database: " . $e->getMessage());
                // You could throw an exception here if you want to stop the import
            }
        }
    }

    /**
     * Execute database operations with reconnect capability
     */
    protected function safeTransaction(callable $callback)
    {
        $attempts = 0;
        $maxAttempts = 3;

        while ($attempts < $maxAttempts) {
            try {
                // Check connection before starting transaction
                $this->reconnectIfMissing();

                // Start transaction
                DB::beginTransaction();

                // Execute the operations
                $result = $callback();

                // Commit transaction
                DB::commit();

                // Success - return the result
                return $result;
            } catch (\PDOException $e) {
                // If transaction is active, try to roll it back
                if (DB::transactionLevel() > 0) {
                    try {
                        DB::rollBack();
                    } catch (\Exception $rollbackException) {
                        // Rollback failed, likely due to lost connection
                        $this->warn("\nRollback failed: " . $rollbackException->getMessage());
                        DB::disconnect();
                    }
                }

                // MySQL server gone away or similar connection errors
                if (strpos($e->getMessage(), 'server has gone away') !== false ||
                    strpos($e->getMessage(), 'Lost connection') !== false) {

                    $attempts++;
                    $this->warn("\nDatabase connection error: " . $e->getMessage());

                    if ($attempts < $maxAttempts) {
                        $this->info("\nRetrying operation (attempt $attempts of $maxAttempts)...");
                        sleep(2); // Wait before retrying
                        continue;
                    }
                }

                // Either not a connection error or max attempts reached
                throw $e;
            } catch (\Exception $e) {
                // For any other exception, roll back and re-throw
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

    /**
     * Load categories from the database
     */

    protected function formatReleaseDate($date)
    {
        if (empty($date)) {
            // If no date provided, use current date in the correct format
            return date('d/m/Y');
        }

        // Check if the date is already in the correct format (dd/mm/yyyy)
        if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $date)) {
            return $date; // Already in the correct format
        }

        // Try to parse the date if it's in a standard format (like YYYY-MM-DD from TMDB)
        try {
            $dateObj = new \DateTime($date);
            return $dateObj->format('d/m/Y'); // Format as dd/mm/yyyy
        } catch (\Exception $e) {
            // If parsing fails, return current date as fallback
            return date('d/m/Y');
        }
    }
    protected function loadCategories()
    {
        $this->reconnectIfMissing();

        // Load categories from database - query directly to minimize memory
        $this->categories = DB::table('categories')
            ->select('id', 'name')
            ->where('status', '1')
            ->get()
            ->toArray();

        $this->info("Loaded " . count($this->categories) . " categories from database");

        // Create name to ID mapping
        foreach ($this->categories as $category) {
            // Map both lowercase name and original name to handle case sensitivity
            $this->categoryNameToIdMap[strtolower($category->name)] = $category->id;
            $this->categoryNameToIdMap[$category->name] = $category->id;
        }
    }

    /**
     * Load countries from the database
     */
    protected function loadCountries()
    {
        $this->reconnectIfMissing();

        // Load countries from database - directly from query to minimize memory
        $this->countries = DB::table('countries')
            ->select('id', 'name', 'code')
            ->where('status', '1')
            ->get()
            ->toArray();

        $this->info("Loaded " . count($this->countries) . " countries from database");

        // Create name and code to ID mappings
        foreach ($this->countries as $country) {
            // Map both lowercase name and original name to handle case sensitivity
            $this->countryNameToIdMap[strtolower($country->name)] = $country->id;
            $this->countryNameToIdMap[$country->name] = $country->id;

            // Map country code to ID
            if (!empty($country->code)) {
                $this->countryCodeToIdMap[strtolower($country->code)] = $country->id;
                $this->countryCodeToIdMap[$country->code] = $country->id;
            }
        }
    }

    /**
     * Find a country ID for a movie based on TMDB data - simplified for memory
     */
    protected function findCountryIdForMovie($movieData)
    {
        // Default to United States (ID: 185) if no country info available
        $defaultCountryId = 185;

        // Check for production countries in the movie data
        if (!empty($movieData['production_countries'])) {
            // Try to match the first production country only
            $country = reset($movieData['production_countries']);

            // Try to match by ISO code (e.g., US, GB, etc.)
            if (!empty($country['iso_3166_1'])) {
                $countryCode = $country['iso_3166_1'];
                if (isset($this->countryCodeToIdMap[$countryCode])) {
                    return $this->countryCodeToIdMap[$countryCode];
                }
            }

            // If no match by code, try to match by name
            if (!empty($country['name'])) {
                $countryName = $country['name'];
                // Direct match
                if (isset($this->countryNameToIdMap[$countryName])) {
                    return $this->countryNameToIdMap[$countryName];
                }

                // Case-insensitive match
                $lowerCountryName = strtolower($countryName);
                if (isset($this->countryNameToIdMap[$lowerCountryName])) {
                    return $this->countryNameToIdMap[$lowerCountryName];
                }

                // Special cases for country names that might not match directly
                $specialMappings = [
                    'united states of america' => 'United States of America',
                    'usa' => 'United States of America',
                    'united states' => 'United States of America',
                    'uk' => 'United Kingdom',
                    'great britain' => 'United Kingdom',
                    'south korea' => 'Korea, South',
                    'north korea' => 'Korea, North',
                    'republic of korea' => 'Korea, South',
                ];

                if (isset($specialMappings[$lowerCountryName])) {
                    $mappedName = $specialMappings[$lowerCountryName];
                    if (isset($this->countryNameToIdMap[$mappedName])) {
                        return $this->countryNameToIdMap[$mappedName];
                    }
                }
            }
        }

        // If no country found, check the original language as a fallback
        if (!empty($movieData['original_language'])) {
            // Simple mapping for common languages to countries
            $languageCountryMap = [
                // Existing mappings
                'en' => 185, // English -> United States of America
                'es' => 161, // Spanish -> Spain
                'fr' => 59,  // French -> France
                'de' => 63,  // German -> Germany
                'it' => 81,  // Italian -> Italy
                'ja' => 83,  // Japanese -> Japan
                'ko' => 89,  // Korean -> South Korea
                'zh' => 36,  // Chinese -> China
                'hi' => 75,  // Hindi -> India
                'ru' => 141, // Russian -> Russia
                'ar' => 51,  // Arabic -> Egypt (as a general fallback)
                'pt' => 24,  // Portuguese -> Brazil

                // Additional countries and languages
                'km' => 30,  // Khmer -> Cambodia
                'dv' => 105, // Dhivehi -> Maldives
                'kh' => 30,  // Alternative code for Khmer -> Cambodia
                'lo' => 92,  // Lao -> Laos
                'mn' => 115, // Mongolian -> Mongolia
                'my' => 119, // Myanmar/Burmese -> Myanmar (Burma)
                'ne' => 122, // Nepali -> Nepal
                'ps' => 1,   // Pashto -> Afghanistan
                'si' => 162, // Sinhala -> Sri Lanka
                'tw' => 169, // Taiwanese -> Taiwan
                'uz' => 187, // Uzbek -> Uzbekistan
                'vi' => 191, // Vietnamese -> Vietnam
                'am' => 56,  // Amharic -> Ethiopia
                'az' => 11,  // Azerbaijani -> Azerbaijan
                'be' => 16,  // Belarusian -> Belarus
                'bg' => 26,  // Bulgarian -> Bulgaria
                'bs' => 22,  // Bosnian -> Bosnia and Herzegovina
                'ca' => 4,   // Catalan -> Andorra
                'cs' => 45,  // Czech -> Czechia
                'da' => 46,  // Danish -> Denmark
                'et' => 55,  // Estonian -> Estonia
                'fa' => 77,  // Farsi/Persian -> Iran
                'fi' => 58,  // Finnish -> Finland
                'el' => 65,  // Greek -> Greece
                'he' => 80,  // Hebrew -> Israel
                'hr' => 42,  // Croatian -> Croatia
                'hu' => 73,  // Hungarian -> Hungary
                'hy' => 8,   // Armenian -> Armenia
                'id' => 76,  // Indonesian -> Indonesia
                'is' => 74,  // Icelandic -> Iceland
                'ka' => 62,  // Georgian -> Georgia
                'kk' => 85,  // Kazakh -> Kazakhstan
                'ky' => 91,  // Kyrgyz -> Kyrgyzstan
                'lt' => 99,  // Lithuanian -> Lithuania
                'lv' => 93,  // Latvian -> Latvia
                'mk' => 101, // Macedonian -> North Macedonia
                'ms' => 104, // Malay -> Malaysia
                'mt' => 107, // Maltese -> Malta
                'nl' => 123, // Dutch -> Netherlands
                'no' => 128, // Norwegian -> Norway
                'pl' => 137, // Polish -> Poland
                'ro' => 140, // Romanian -> Romania
                'sk' => 155, // Slovak -> Slovakia
                'sl' => 156, // Slovenian -> Slovenia
                'sq' => 2,   // Albanian -> Albania
                'sr' => 151, // Serbian -> Serbia
                'sv' => 166, // Swedish -> Sweden
                'sw' => 171, // Swahili -> Tanzania
                'th' => 172, // Thai -> Thailand
                'tk' => 179, // Turkmen -> Turkmenistan
                'tr' => 178, // Turkish -> Turkey
                'uk' => 182, // Ukrainian -> Ukraine
                'ur' => 130, // Urdu -> Pakistan
                'af' => 159, // Afrikaans -> South Africa
                'bn' => 14,  // Bengali -> Bangladesh
                'rw' => 142, // Kinyarwanda -> Rwanda
                'lb' => 100, // Luxembourgish -> Luxembourg
                'mg' => 102, // Malagasy -> Madagascar
                'sm' => 146, // Samoan -> Samoa
                'sn' => 163, // Arabic -> Sudan
                'so' => 158, // Somali -> Somalia
                'tg' => 170, // Tajik -> Tajikistan
                'to' => 175, // Tongan -> Tonga
                'tt' => 176, // Trinidadian English -> Trinidad and Tobago
                'wo' => 150, // Wolof -> Senegal
                'dz' => 20,  // Dzongkha -> Bhutan
                'la' => 189, // Latin -> Vatican City
                'ga' => 79,  // Irish -> Ireland
                'cy' => 184, // Welsh -> United Kingdom
                'gd' => 184, // Scottish Gaelic -> United Kingdom
                'eu' => 161, // Basque -> Spain
                'gl' => 161, // Galician -> Spain
                'bi' => 188, // Bislama -> Vanuatu
                'mh' => 108, // Marshallese -> Marshall Islands
                'na' => 121, // Nauruan -> Nauru
                'ny' => 103, // Chichewa/Nyanja -> Malawi
                'sg' => 33,  // Sango -> Central African Republic
                'st' => 95,  // Sesotho -> Lesotho
                'ss' => 165, // Swati -> Swaziland
                'tn' => 23,  // Tswana -> Botswana
                'ty' => 57,  // Fijian -> Fiji
                'zu' => 159, // Zulu -> South Africa
                'ak' => 64,  // Akan -> Ghana
                'ee' => 64,  // Ewe -> Ghana
                'ha' => 127, // Hausa -> Nigeria
                'ig' => 127, // Igbo -> Nigeria
                'yo' => 127, // Yoruba -> Nigeria
                'ki' => 86,  // Kikuyu -> Kenya
                'ku' => 78,  // Kurdish -> Iraq
                'pa' => 75,  // Punjabi -> India
                'ta' => 75,  // Tamil -> India
                'te' => 75,  // Telugu -> India
                'ml' => 75,  // Malayalam -> India
                'kn' => 75,  // Kannada -> India
                'mr' => 75,  // Marathi -> India
                'gu' => 75,  // Gujarati -> India
                'or' => 75,  // Odia -> India
                'xh' => 159  // Xhosa -> South Africa
            ];

            if (isset($languageCountryMap[$movieData['original_language']])) {
                return $languageCountryMap[$movieData['original_language']];
            }
        }

        // Return default country ID if nothing else matched
        return $defaultCountryId;
    }

    /**
     * Get a country name by ID - simplified
     */
    protected function getCountryNameById($countryId)
    {
        foreach ($this->countries as $country) {
            if ($country->id == $countryId) {
                return $country->name;
            }
        }

        return 'Unknown';
    }

    /**
     * Assign categories to a film - memory-optimized
     */
    protected function assignCategoriesToFilm($filmId, $movieData)
    {
        $genreMap = $this->getGenreMap();
        $assignedCount = 0;

        // Process genres directly without creating intermediate arrays
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

    protected function checkForDuplicates($movieData)
    {
        $this->reconnectIfMissing();

        $title = $movieData['title'] ?? 'Unknown';
        $releaseDate = $this->formatReleaseDate($movieData['release_date'] ?? null);
        $tmdbId = $movieData['id'] ?? null;
        $runtime = $movieData['runtime'] ?? null;

        $query = Film::query();

        // Start with base query - title match is required
        $query->where('title', $title);

        // Add release date check if available
        if (!empty($releaseDate)) {
            $query->where('release_date', $releaseDate);
        }

        // Check for any direct TMDB ID stored in your database (if you have such field)
        // Uncomment and modify if you have a tmdb_id field
        // if (!empty($tmdbId)) {
        //     $query->orWhere('tmdb_id', $tmdbId);
        // }

        // If we have runtime, use it as additional identifier
        if (!empty($runtime)) {
            $query->where(function($q) use ($runtime) {
                // Allow slight runtime difference (±2 minutes)
                $q->whereBetween('running_time', [$runtime-2, $runtime+2]);
            });
        }

        // Try to find the film
        $existingFilm = $query->first();

        if ($existingFilm) {
            // Log more detailed information about the duplicate
            $this->logDuplicateInfo($movieData, $existingFilm);
        }

        return $existingFilm;
    }

    protected function logDuplicateInfo($movieData, $existingFilm)
    {
        $tmdbTitle = $movieData['title'] ?? 'Unknown';
        $tmdbDate = $movieData['release_date'] ?? 'Unknown';
        $tmdbRuntime = $movieData['runtime'] ?? 'Unknown';

        // Create a log record of the duplicate
        $duplicateLog = "Duplicate found:\n";
        $duplicateLog .= "- TMDB: '$tmdbTitle' ($tmdbDate) - Runtime: $tmdbRuntime min\n";
        $duplicateLog .= "- Existing: '{$existingFilm->title}' ({$existingFilm->release_date}) - Runtime: {$existingFilm->running_time} min\n";
        $duplicateLog .= "- DB ID: {$existingFilm->id}";

        // Log to console
        $this->info($duplicateLog);

        // Optionally, log to file for tracking all duplicates
        $logFile = storage_path('logs/film_import_duplicates.log');
        file_put_contents(
            $logFile,
            date('[Y-m-d H:i:s] ') . $duplicateLog . "\n\n",
            FILE_APPEND
        );
    }

    protected $importedFilmChecksums = [];
    protected function generateMovieChecksum($movieData)
    {
        $title = strtolower($movieData['title'] ?? '');
        $year = substr($movieData['release_date'] ?? '', 0, 4);

        return md5($title . '_' . $year);
    }

    /**
     * Create a film-category relationship directly
     */
    protected function createFilmCategory($filmId, $categoryId)
    {
        $this->reconnectIfMissing();

        // Use direct DB insertion instead of Eloquent to save memory
        DB::table('film_categories')->insert([
            'film_id' => $filmId,
            'category_id' => $categoryId,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    /**
     * Find a category ID by matching the name - simplified
     */
    protected function findCategoryIdByName($genreName)
    {
        // Direct match
        if (isset($this->categoryNameToIdMap[$genreName])) {
            return $this->categoryNameToIdMap[$genreName];
        }

        // Case-insensitive match
        $lowerGenreName = strtolower($genreName);
        if (isset($this->categoryNameToIdMap[$lowerGenreName])) {
            return $this->categoryNameToIdMap[$lowerGenreName];
        }

        // Special case mappings for TMDB genre names that don't match our categories exactly
        $specialMappings = [
            'science fiction' => 'Sci-Fi',
            'tv movie' => 'Drama', // Map TV Movie to Drama as fallback
            'music' => 'Musical',  // Map Music to Musical
        ];

        if (isset($specialMappings[$lowerGenreName])) {
            $mappedName = $specialMappings[$lowerGenreName];
            if (isset($this->categoryNameToIdMap[$mappedName])) {
                return $this->categoryNameToIdMap[$mappedName];
            }
        }

        return null;
    }

    /**
     * Get a mapping of genre IDs to names - simplified static map
     */
    protected function getGenreMap()
    {
        return [
            28 => 'Action',
            12 => 'Adventure',
            16 => 'Animation',
            35 => 'Comedy',
            80 => 'Crime',
            99 => 'Documentary',
            18 => 'Drama',
            10751 => 'Family',
            14 => 'Fantasy',
            36 => 'History',
            27 => 'Horror',
            10402 => 'Musical',
            9648 => 'Mystery',
            10749 => 'Romance',
            878 => 'Sci-Fi',
            10770 => 'Drama',
            53 => 'Thriller',
            10752 => 'War',
            37 => 'Western'
        ];
    }
}
