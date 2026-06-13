<?php

namespace App\Services;

use App\Models\Skripsi;
use App\Models\SkripsiSimilarity;
use Sastrawi\Stemmer\StemmerFactory;
use Sastrawi\StopWordRemover\StopWordRemoverFactory;

class SimilarityService
{
    private $stemmer;
    private $stopWordRemover;

    public function __construct()
    {
        $stemmerFactory = new StemmerFactory();
        $this->stemmer = $stemmerFactory->createStemmer();

        $stopWordRemoverFactory = new StopWordRemoverFactory();
        $this->stopWordRemover = $stopWordRemoverFactory->createStopWordRemover();
    }

    /**
     * Preprocess Indonesian text:
     * - lowercase
     * - remove punctuation
     * - remove extra spaces
     * - remove stopwords
     * - stemming
     */
    public function preprocess(string $text): string
    {
        // Lowercase
        $text = mb_strtolower($text);

        // Remove punctuation
        $text = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $text);

        // Remove extra spaces
        $text = preg_replace('/\s+/', ' ', trim($text));

        // Remove stopwords
        $text = $this->stopWordRemover->remove($text);

        // Stem each word
        $words = explode(' ', $text);
        $stemmed = array_map(fn($w) => $this->stemmer->stem($w), $words);
        $stemmed = array_filter($stemmed, fn($w) => !empty(trim($w)));

        return implode(' ', $stemmed);
    }

    /**
     * Tokenize text into word array
     */
    private function tokenize(string $text): array
    {
        return array_filter(explode(' ', trim($text)), fn($w) => !empty($w));
    }

    /**
     * Calculate TF (Term Frequency) for a document
     */
    private function calculateTf(array $words): array
    {
        $tf = [];
        $totalWords = count($words);
        if ($totalWords === 0) return $tf;

        $wordCounts = array_count_values($words);
        foreach ($wordCounts as $word => $count) {
            $tf[$word] = $count / $totalWords;
        }
        return $tf;
    }

    /**
     * Calculate IDF (Inverse Document Frequency) for all documents
     */
    private function calculateIdf(array $allDocumentWords): array
    {
        $idf = [];
        $totalDocs = count($allDocumentWords);
        if ($totalDocs === 0) return $idf;

        // Collect all unique terms
        $allTerms = [];
        foreach ($allDocumentWords as $words) {
            $allTerms = array_merge($allTerms, array_unique($words));
        }
        $allTerms = array_unique($allTerms);

        // Calculate IDF for each term
        foreach ($allTerms as $term) {
            $docCount = 0;
            foreach ($allDocumentWords as $words) {
                if (in_array($term, $words)) {
                    $docCount++;
                }
            }
            $idf[$term] = log(($totalDocs + 1) / ($docCount + 1)) + 1; // smoothed IDF
        }

        return $idf;
    }

    /**
     * Calculate TF-IDF vectors for multiple documents
     */
    public function calculateTfIdf(array $preprocessedTexts): array
    {
        $allDocWords = [];
        foreach ($preprocessedTexts as $text) {
            $allDocWords[] = $this->tokenize($text);
        }

        $idf = $this->calculateIdf($allDocWords);
        $tfidfVectors = [];

        foreach ($allDocWords as $idx => $words) {
            $tf = $this->calculateTf($words);
            $vector = [];
            foreach ($idf as $term => $idfValue) {
                $vector[$term] = ($tf[$term] ?? 0) * $idfValue;
            }
            $tfidfVectors[$idx] = $vector;
        }

        return $tfidfVectors;
    }

    /**
     * Calculate cosine similarity between two vectors
     */
    public function cosineSimilarity(array $vecA, array $vecB): float
    {
        $dotProduct = 0;
        $magnitudeA = 0;
        $magnitudeB = 0;

        $allTerms = array_unique(array_merge(array_keys($vecA), array_keys($vecB)));

        foreach ($allTerms as $term) {
            $a = $vecA[$term] ?? 0;
            $b = $vecB[$term] ?? 0;
            $dotProduct += $a * $b;
            $magnitudeA += $a * $a;
            $magnitudeB += $b * $b;
        }

        $magnitudeA = sqrt($magnitudeA);
        $magnitudeB = sqrt($magnitudeB);

        if ($magnitudeA == 0 || $magnitudeB == 0) {
            return 0.0;
        }

        return $dotProduct / ($magnitudeA * $magnitudeB);
    }

    /**
     * Calculate similarity for a specific skripsi against all others.
     * Called when a skripsi is created or its title is edited.
     */
    public function calculateForSkripsi(Skripsi $skripsi): void
    {
        // Delete old similarity records for this skripsi
        SkripsiSimilarity::where('skripsi_id', $skripsi->id)
            ->orWhere('compared_skripsi_id', $skripsi->id)
            ->delete();

        // Get all other skripsi
        $allSkripsi = Skripsi::where('id', '!=', $skripsi->id)->get();
        if ($allSkripsi->isEmpty()) return;

        // Preprocess all titles
        $preprocessedTarget = $this->preprocess($skripsi->judul);
        $preprocessedOthers = [];
        foreach ($allSkripsi as $other) {
            $preprocessedOthers[$other->id] = $this->preprocess($other->judul);
        }

        // Build document corpus: target + all others
        $allTexts = array_merge([$preprocessedTarget], array_values($preprocessedOthers));
        $otherIds = array_keys($preprocessedOthers);

        // Calculate TF-IDF for all documents
        $tfidfVectors = $this->calculateTfIdf($allTexts);

        $targetVector = $tfidfVectors[0];
        $records = [];
        $now = now();

        foreach ($otherIds as $idx => $otherId) {
            $otherVector = $tfidfVectors[$idx + 1]; // +1 because target is at index 0
            $score = $this->cosineSimilarity($targetVector, $otherVector) * 100;
            $score = round($score, 2);

            // Only store if score >= 30% (keep some buffer below the 70% display threshold)
            if ($score >= 30) {
                $category = SkripsiSimilarity::categorize($score);
                $records[] = [
                    'skripsi_id' => $skripsi->id,
                    'compared_skripsi_id' => $otherId,
                    'similarity_score' => $score,
                    'category' => $category,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
                // Also store reverse pair
                $records[] = [
                    'skripsi_id' => $otherId,
                    'compared_skripsi_id' => $skripsi->id,
                    'similarity_score' => $score,
                    'category' => $category,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        if (!empty($records)) {
            // Insert in chunks to avoid memory issues
            foreach (array_chunk($records, 100) as $chunk) {
                SkripsiSimilarity::insert($chunk);
            }
        }
    }

    /**
     * Recalculate similarity for ALL skripsi.
     * Used for initial data population.
     */
    public function recalculateAll(): int
    {
        // Clear all existing records
        SkripsiSimilarity::truncate();

        $allSkripsi = Skripsi::all();
        if ($allSkripsi->count() < 2) return 0;

        // Preprocess all titles
        $preprocessed = [];
        $skripsiIds = [];
        foreach ($allSkripsi as $s) {
            $preprocessed[] = $this->preprocess($s->judul);
            $skripsiIds[] = $s->id;
        }

        // Calculate TF-IDF for all
        $tfidfVectors = $this->calculateTfIdf($preprocessed);

        $records = [];
        $now = now();
        $count = count($skripsiIds);

        // Compare each pair (i, j) where i < j to avoid duplicates
        for ($i = 0; $i < $count; $i++) {
            for ($j = $i + 1; $j < $count; $j++) {
                $score = $this->cosineSimilarity($tfidfVectors[$i], $tfidfVectors[$j]) * 100;
                $score = round($score, 2);

                if ($score >= 30) {
                    $category = SkripsiSimilarity::categorize($score);
                    $records[] = [
                        'skripsi_id' => $skripsiIds[$i],
                        'compared_skripsi_id' => $skripsiIds[$j],
                        'similarity_score' => $score,
                        'category' => $category,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                    $records[] = [
                        'skripsi_id' => $skripsiIds[$j],
                        'compared_skripsi_id' => $skripsiIds[$i],
                        'similarity_score' => $score,
                        'category' => $category,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }
        }

        if (!empty($records)) {
            foreach (array_chunk($records, 100) as $chunk) {
                SkripsiSimilarity::insert($chunk);
            }
        }

        return count($records) / 2; // Return number of unique pairs
    }
}
