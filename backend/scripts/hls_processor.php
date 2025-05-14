<?php

// Check command line arguments
if ($argc < 2) {
    die("Usage: php hls_processor.php <input_directory> [output_directory]\n");
}

$inputDir = rtrim($argv[1], '/');
$outputDir = $argv[2] ? rtrim($argv[2], '/') : '.';

new DirectoryProcessor()->process($inputDir, $outputDir);

class DirectoryProcessor {
    private PDO $db;
    private $videoExtensions = ['mp4', 'mov', 'mkv', 'avi', 'webm', 'flv']; // Supported video extensions
    private $thumbnailExtensions = ['jpg', 'jpeg', 'png', 'gif']; // Supported thumbnail extensions

    private string $thumbnailDir = '';
    private string $outputDir = '';

    public function __construct() {
       $this->db = new PDO('mysql:host=localhost;dbname=nyetflix', 'nyetflix', 'nyetflix');
       $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function process(string $inputDir, string $outputDir) : void {
        if (!is_dir($inputDir)) {
            die("Error: Input directory does not exist or is not accessible\n");
        }

        $videosOutputDir = $outputDir . '/movies';
        $thumbnailsOutputDir = $outputDir . '/thumbnails';

        if (file_exists($videosOutputDir) && !is_dir($videosOutputDir)) {
            die("Error: movies output path exists but is not a directory\n");
        }

        if (file_exists($thumbnailsOutputDir) && !is_dir($thumbnailsOutputDir)) {
            die("Error: thumbnails output path exists but is not a directory\n");
        }

        if (!mkdir($videosOutputDir, 0755, true) || !mkdir($thumbnailsOutputDir, 0755, true)) {
            die("Error: Failed to create output directories\n");
        }

        $this->thumbnailDir = $thumbnailsOutputDir;
        $this->outputDir = $videosOutputDir;

        $success = false;
        $files = $this->getInterestingFiles($inputDir);
        foreach($files['dirs'] as $item) {
            $itemPath = $inputDir. '/' . $item;
            $success = $success || $this->processDirectory($itemPath);
        }
        if(!$success) {
            $this->cleanupDirectory($videosOutputDir);
            $this->cleanupDirectory($thumbnailsOutputDir);
        }
    }

    private function processDirectory(string $input_path) : bool {
        $files = $this->getInterestingFiles($input_path);

        if(isset($files['video']) && isset($files['thumbnail'])) {
            return $this->processMovie($input_path, '', $files);
        }
        else if(!empty($files['dirs']) && isset($files['thumbnail'])) {
            return $this->processSeries($input_path, $files);
        }
        return false;
    }

    private function sanitizeInput(string $input) : string {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }

    private function validateSeasonNumber(string $input) : ?int {
        $number = filter_var($input, FILTER_VALIDATE_INT);
        return ($number !== false && $number > 0) ? $number : null;
    }

    private function validateEpisodeNumber(string $input) : ?int {
        $number = filter_var($input, FILTER_VALIDATE_INT);
        return ($number !== false && $number > 0) ? $number : null;
    }

    private function processSeries(string $input_path, array $files) : bool {
        echo "Processing series: $input_path\n";

        echo "Title: ";
        $title = $this->sanitizeInput(fgets(STDIN));
        echo "Description: ";
        $description = $this->sanitizeInput(fgets(STDIN));

        if (empty($title) || empty($description)) {
            return $this->handleError("Title and description cannot be empty");
        }

        try {
            $stmnt = $this->db->prepare("INSERT INTO movies (title, description, type) values (?, ?, 2)");
            $stmnt->execute([$title, $description]);
            $movie_id = $this->db->lastInsertId();
        } catch (PDOException $e) {
            return $this->handleError("Database error: " . $e->getMessage());
        }

        $series_path = "$this->outputDir/$movie_id";
        if (!mkdir($series_path, 0755)) {
            return $this->handleError("Failed to create series directory");
        }

        $success = false;
        foreach($files['dirs'] as $dir) {
            $success = $success || $this->processSeason($movie_id, "$input_path/$dir", "$movie_id");
        }
        if(!$success) {
            $this->cleanupDirectory($series_path);
            return false;
        }

        $thumbnail = "$input_path" . '/' . $files['thumbnail'];
        $thumbnail_dir = "$this->thumbnailDir/$movie_id";
        if (!mkdir($thumbnail_dir, 0755, true)) {
            return $this->handleError("Failed to create thumbnail directory", $series_path);
        }

        $ext = strtolower(pathinfo($thumbnail, PATHINFO_EXTENSION));
        $thumbnail_out = "$thumbnail_dir/thumbnail_$movie_id.$ext";
        if (!copy($thumbnail, $thumbnail_out)) {
            return $this->handleError("Failed to copy thumbnail", $series_path);
        }

        return true;
    }

    private function processSeason(int $movie_id, string $input_path, string $output_path) : bool {
        echo "Processing season: $input_path\n";
        echo "Season No: ";
        $season_no = $this->validateSeasonNumber(trim(fgets(STDIN)));
        
        if ($season_no === null) {
            return $this->handleError("Invalid season number");
        }

        try {
            $stmnt = $this->db->prepare('INSERT INTO seasons (movie_id, season_no) values (?, ?)');
            $stmnt->execute([$movie_id, $season_no]);
            $season_id = $this->db->lastInsertId();
        } catch (PDOException $e) {
            return $this->handleError("Database error: " . $e->getMessage());
        }

        $season_path = $output_path . '/' . $season_id;
        if (!mkdir($this->outputDir . '/' . $season_path, 0755)) {
            return $this->handleError("Failed to create season directory");
        }

        $files = $this->getInterestingFiles($input_path);
        $success = false;
        foreach($files['dirs'] as $dir) {
            $success = $success || $this->processEpisode($season_id, "$input_path/$dir", $season_path);
        }
        if(!$success) {
            $this->cleanupDirectory($this->outputDir . '/' . $season_path);
            return false;
        }
        return $success;
    }

    private function processEpisode(int $season_id, string $input_path, string $output_path) : bool {
        echo "Processing episode: $input_path\n";
        echo "Episode No: ";
        $episode_no = $this->validateEpisodeNumber(trim(fgets(STDIN)));
        
        if ($episode_no === null) {
            return $this->handleError("Invalid episode number");
        }

        $movie_id = $this->processMovie($input_path, $output_path);
        if(!$movie_id) return false;

        try {
            $stmnt = $this->db->prepare('INSERT INTO episodes (season_id, episode_no, movie_id) values (?, ?, ?)');
            $stmnt->execute([$season_id, $episode_no, $movie_id]);
            return true;
        } catch (PDOException $e) {
            return $this->handleError("Database error: " . $e->getMessage());
        }
    }

    private function processMovie(string $input_path, string $output_path, array|null $files = null) : int|false {
        echo "Processing movie: $output_path\n";

        echo "Title: ";
        $title = trim(fgets(STDIN));
        echo "Description: ";
        $description = trim(fgets(STDIN));

        try {
            $stmnt = $this->db->prepare('INSERT INTO movies (title, description, type) values (?, ?, 1)');
            $stmnt->execute([$title, $description]);
            $movie_id = $this->db->lastInsertId();
        } catch (PDOException $e) {
            echo "Database error: " . $e->getMessage() . "\n";
            return false;
        }

        if(is_null($files)) {
            $files = $this->getInterestingFiles($output_path);
        }
        if(empty($files)) return false;

        $out_path = $output_path . '/' . $movie_id;
        if (!mkdir($out_path, 0755, true)) {
            return false;
        }

        $success = $this->processVideo($input_path . '/' . $files['video'], $out_path);
        if(!$success) {
            // delete $movie_path
            return false;
        }

        $thumbnail = "$input_path" . '/' . $files['thumbnail'];
        $thumbnail_dir = "$this->thumbnailDir/$movie_id";
        mkdir($thumbnail_dir, 0755, true);

        $ext = strtolower(pathinfo($thumbnail, PATHINFO_EXTENSION));
        $thumbnail_out = "$thumbnail_dir/$movie_id/thumbnail_$movie_id.$ext";
        copy($thumbnail, $thumbnail_out);


        $thumbnailDir = $this->thumbnailDir . '/' . $output_path . '/' . $movie_id;
        $thumbnail_dir = "$this->thumbnailDir/$movie_id";
        mkdir($thumbnailDir, 0755, true);

        $thumbnail =  $input_path . '/' . $files['thumbnail'];
        $ext = strtolower(pathinfo($thumbnail, PATHINFO_EXTENSION));

        copy($thumbnail, $thumbnailDir . '/' . $movie_id . $ext);
        return $movie_id;
    }

    private function processVideo(string $videoPath, string $outputDir) : bool {
        $ffmpegPath = getenv('FFMPEG_PATH') ?: 'ffmpeg';

        if (!file_exists($videoPath)) {
            return $this->handleError("Video file not found: $videoPath");
        }

        // Single FFmpeg command for 720p stream
        $cmd = sprintf(
            '%s -i "%s" ' .
            '-c:v libx264 -crf 23 -preset veryfast -profile:v main -level 3.1 -vf "scale=-2:720" ' .
            '-c:a aac -b:a 128k ' .
            '-g 60 -keyint_min 60 -sc_threshold 0 ' .
            '-f hls -hls_time 6 -hls_list_size 0 -hls_playlist_type vod ' .
            '-hls_segment_filename "%s/segment_%%03d.ts" "%s/playlist.m3u8" 2>&1',
            $ffmpegPath,
            $videoPath,
            $outputDir,
            $outputDir
        );

        exec($cmd, $output, $returnCode);

        if ($returnCode !== 0) {
            return $this->handleError("FFmpeg error: " . implode("\n", $output));
        }

        // Verify that the output files were created
        if (!file_exists("$outputDir/playlist.m3u8")) {
            return $this->handleError("Failed to create HLS playlist");
        }

        return true;
    }

    private function getInterestingFiles(string $path) : array {
        $items = scandir($path);
        $output = ['dirs' => [], 'video' => null, 'thumbnail' => null];

        foreach($items as $item) {
            $full_path = $path . '/' . $item;
            if($item === '.' || $item === '..') continue;
            if(is_dir($full_path)) $output['dirs'][] = $item;
            if(is_null($output['video']) || is_null($output['thumbnail'])) {
                // process files as well
                $ext = strtolower(pathinfo($full_path, PATHINFO_EXTENSION));
                if(in_array($ext, $this->videoExtensions)) {
                    $output['video'] = $item;
                }
                else if(in_array($ext, $this->thumbnailExtensions)) {
                    $output['thumbnail'] = $item;
                }
            }
        }

        return $output;
    }

    private function cleanupDirectory(string $path) : void {
        if (is_dir($path)) {
            $files = array_diff(scandir($path), ['.', '..']);
            foreach ($files as $file) {
                $filePath = $path . '/' . $file;
                if (is_dir($filePath)) {
                    $this->cleanupDirectory($filePath);
                } else {
                    unlink($filePath);
                }
            }
            rmdir($path);
        }
    }

    private function handleError(string $message, ?string $cleanupPath = null) : bool {
        echo "Error: $message\n";
        if ($cleanupPath) {
            $this->cleanupDirectory($cleanupPath);
        }
        return false;
    }
}

