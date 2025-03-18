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
                           {--year=2024}
                           {--limit=}
                           {--type=1}
                           {--default-runtime=90}
                           {--skip-upload}';

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
        $sourceDir = $this->argument('source_dir');
        $year = $this->option('year');
        $limit = $this->option('limit');
        $filmType = $this->option('type');
        $defaultRuntime = $this->option('default-runtime');
        $skipUpload = $this->option('skip-upload');

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

        // Try to load movie data from available files
        if (file_exists($detailedDataFile)) {
            $this->info("Using complete details file");
            $movies = json_decode(file_get_contents($detailedDataFile), true);
        }
        elseif (file_exists($idListFile) && is_dir($detailsDir)) {
            $this->info("Using individual movie files");
            $movieIds = json_decode(file_get_contents($idListFile), true);
            $movies = [];

            foreach ($movieIds as $id) {
                $movieFile = "$detailsDir/$id.json";
                if (file_exists($movieFile)) {
                    $movies[] = json_decode(file_get_contents($movieFile), true);
                }
            }
        }
        elseif (file_exists($basicDataFile)) {
            $this->info("Using basic movie data");
            $movies = json_decode(file_get_contents($basicDataFile), true);
        }
        else {
            $this->error("No TMDB movie data found. Run the download command first.");
            return 1;
        }

        $totalMovies = count($movies);
        $this->info("Found $totalMovies movies to process");

        if ($limit && is_numeric($limit) && $limit < $totalMovies) {
            $movies = array_slice($movies, 0, $limit);
            $this->info("Limiting import to the first $limit movies");
        }

        $this->info("Starting import of " . count($movies) . " movies to Film model");
        if ($skipUpload) {
            $this->info("Image uploads will be skipped");
        }

        $bar = $this->output->createProgressBar(count($movies));
        $bar->start();

        $importCount = 0;
        $errorCount = 0;
        $skippedCount = 0;
        $categoryCount = 0;

        // Create upload controller only if not skipping uploads
        $uploadController = $skipUpload ? null : new UploadController();

        foreach ($movies as $movieData) {
            try {
                // Check if the film already exists to avoid duplicates
                $existingFilm = Film::where('title', $movieData['title'])
                    ->where('release_date', $movieData['release_date'] ?? null)
                    ->first();

                if ($existingFilm) {
                    $skippedCount++;
                    $bar->advance();
                    continue;
                }

                // Begin transaction for film and categories
                DB::beginTransaction();

                // Create new film object
                $film = new Film();
                $film->title = $movieData['title'];
                $film->overview = $movieData['overview'] ?? '';
                $film->release_date = $movieData['release_date'] ?? date('d/m/Y');
                $film->view = 0;
                $film->rating = '0';

                // Set film type and running time
                $film->type = $filmType;
                $film->running_time = $movieData['runtime'] ?? $defaultRuntime;

                // Get original language from TMDB data

                $film->language = $this->findCountryIdForMovie($movieData);

                // Set empty category field instead of comma-separated values
                // We'll use the FilmCategory table to store relations instead
                $film->category = '';

                // Map country data from TMDB to our country ID
                $film->country_id = $this->findCountryIdForMovie($movieData);

                // Map keywords to tags
                if (!empty($movieData['keywords']['keywords'])) {
                    $keywords = array_column($movieData['keywords']['keywords'], 'name');
                    $film->tag = implode(', ', array_slice($keywords, 0, 5));
                }

                // Map trailer
                if (!empty($movieData['videos']['results'])) {
                    $trailers = array_filter($movieData['videos']['results'], function($video) {
                        return $video['type'] === 'Trailer' && $video['site'] === 'YouTube';
                    });

                    if (!empty($trailers)) {
                        $film->trailer = 'https://www.youtube.com/watch?v=' . reset($trailers)['key'];
                    }
                }

                // Map directors
                if (!empty($movieData['credits']['crew'])) {
                    $directors = array_filter($movieData['credits']['crew'], function($person) {
                        return $person['job'] === 'Director';
                    });

                    if (!empty($directors)) {
                        $directorNames = array_column($directors, 'name');
                        $film->director = implode(', ', $directorNames);
                    }
                }

                // Handle image uploads if not skipping
                if (!$skipUpload) {
                    try {
                        // Find and upload poster (required)
                        if (!empty($movieData['poster_path'])) {
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
                                try {
                                    $film->poster = $uploadController->uploadFile($uploadedFile, 'film');
                                    $this->info("\nPoster uploaded for '{$film->title}' - ID: {$film->poster}");
                                } catch (Exception $e) {
                                    $this->warn("\nCouldn't upload poster: " . $e->getMessage());
                                    throw new Exception("OSS upload failed: " . $e->getMessage());
                                }
                            } else {
                                $this->warn("\nNo poster file found for '{$film->title}'");
                                throw new Exception("No poster file found");
                            }
                        } else {
                            $this->warn("\nNo poster path in data for '{$film->title}'");
                            throw new Exception("No poster path in data");
                        }

                        // Find and upload cover (optional)
                        if (!empty($movieData['backdrop_path']) && file_exists($backdropsDir)) {
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

                                // Upload to OSS and get the ID directly - this is optional so catch errors
                                try {
                                    $film->cover = $uploadController->uploadFile($uploadedFile, 'film');
                                    $this->info("\nCover uploaded for '{$film->title}' - ID: {$film->cover}");
                                } catch (Exception $e) {
                                    $this->warn("\nCouldn't upload cover (continuing anyway): " . $e->getMessage());
                                }
                            }
                        }
                    } catch (Exception $e) {
                        // If there's an upload error, skip this movie
                        $this->error("\nSkipping movie due to upload error: " . $e->getMessage());
                        $errorCount++;
                        $bar->advance();
                        continue;
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

                // Commit the transaction
                DB::commit();

                $importCount++;
                $countryName = $film->country_id ? $this->getCountryNameById($film->country_id) : 'Unknown';
                $this->info("\nImported: {$film->title} with $filmCategoryCount categories, Country: $countryName");

            } catch (Exception $e) {
                // Rollback transaction if there was an error
                DB::rollBack();

                $this->newLine();
                $this->error("Error importing movie ': " . $e->getMessage());
                $errorCount++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("Import completed:");
        $this->info("- Successfully imported: $importCount films");
        $this->info("- Total categories assigned: $categoryCount");
        $this->info("- Skipped: $skippedCount films");
        $this->info("- Errors: $errorCount films");

        return 0;
    }

    /**
     * Load categories from the database
     */
    protected function loadCategories()
    {
        // Load categories from database
        $this->categories = Category::where('status', '1')->get()->toArray();
        $this->info("Loaded " . count($this->categories) . " categories from database");

        // Create name to ID mapping
        foreach ($this->categories as $category) {
            // Map both lowercase name and original name to handle case sensitivity
            $this->categoryNameToIdMap[strtolower($category['name'])] = $category['id'];
            $this->categoryNameToIdMap[$category['name']] = $category['id'];
        }
    }

    /**
     * Load countries from the database
     */
    protected function loadCountries()
    {
        // Load countries from database - assuming a Country model or similar
        $this->countries = DB::table('countries')->where('status', '1')->get()->toArray();
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
     * Find a country ID for a movie based on TMDB data
     *
     * @param array $movieData
     * @return int|null
     */
    protected function findCountryIdForMovie($movieData)
    {
        // Default to United States (ID: 185) if no country info available
        $defaultCountryId = 185;

        // Check for production countries in the movie data
        if (!empty($movieData['production_countries'])) {
            // Try to match the first production country
            foreach ($movieData['production_countries'] as $country) {
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
        }

        // If no country found, check the original language as a fallback
        if (!empty($movieData['original_language'])) {

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
     * Get a country name by ID
     *
     * @param int $countryId
     * @return string
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
     * Assign categories to a film
     *
     * @param int $filmId
     * @param array $movieData
     * @return int Number of categories assigned
     */
    protected function assignCategoriesToFilm($filmId, $movieData)
    {
        $genreMap = $this->getGenreMap();
        $assignedCount = 0;
        $genreNames = [];

        // Get genre names from the movie data
        if (!empty($movieData['genres'])) {
            $genreNames = array_column($movieData['genres'], 'name');
        } elseif (!empty($movieData['genre_ids'])) {
            // For basic data that only has genre IDs, use the genre map
            foreach ($movieData['genre_ids'] as $genreId) {
                if (isset($genreMap[$genreId])) {
                    $genreNames[] = $genreMap[$genreId];
                }
            }
        }

        // Process each genre and try to find a matching category
        foreach ($genreNames as $genreName) {
            // Try to find a matching category in our database
            $categoryId = $this->findCategoryIdByName($genreName);

            if ($categoryId) {
                // Create film_category relationship
                FilmCategory::create([
                    'film_id' => $filmId,
                    'category_id' => $categoryId
                ]);

                $assignedCount++;
            } else {
                $this->warn("\nCouldn't find category match for genre: $genreName");
            }
        }

        return $assignedCount;
    }

    /**
     * Find a category ID by matching the name
     *
     * @param string $genreName
     * @return int|null
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
     * Get a mapping of genre IDs to names
     *
     * @return array
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
            10402 => 'Musical', // Changed from 'Music' to match our database
            9648 => 'Mystery',
            10749 => 'Romance',
            878 => 'Sci-Fi',   // Changed from 'Science Fiction' to match our database
            10770 => 'Drama',  // Changed from 'TV Movie' to a reasonable fallback
            53 => 'Thriller',
            10752 => 'War',
            37 => 'Western'
        ];
    }
}
