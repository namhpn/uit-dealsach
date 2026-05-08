<?php

namespace App\Services;

use CodeIgniter\Database\BaseConnection;
use Config\Database;

class MatchingService
{
    private BaseConnection $db;
    private TextNormalizeService $normalizer;

    public function __construct(?BaseConnection $db = null, ?TextNormalizeService $normalizer = null)
    {
        $this->db = $db ?? Database::connect();
        $this->normalizer = $normalizer ?? new TextNormalizeService();
    }

    /**
     * @param array<string, mixed> $record
     *
     * @return array{book_id: int|null, method: string, confidence: float}
     */
    public function match(array $record): array
    {
        $bookId = isset($record['book_id']) ? (int) $record['book_id'] : 0;
        if ($bookId > 0 && $this->bookExists($bookId)) {
            return ['book_id' => $bookId, 'method' => 'book_id', 'confidence' => 1.0];
        }

        $isbn = trim((string) ($record['isbn'] ?? $record['source_isbn'] ?? ''));
        if ($isbn !== '') {
            $book = $this->db->table('books')
                ->select('id')
                ->where('isbn', $isbn)
                ->where('is_active', 1)
                ->where('deleted_at', null)
                ->get()
                ->getRowArray();

            if ($book !== null) {
                return ['book_id' => (int) $book['id'], 'method' => 'isbn', 'confidence' => 1.0];
            }
        }

        $title = (string) ($record['title'] ?? $record['source_title'] ?? '');
        $author = (string) ($record['author'] ?? $record['source_author'] ?? '');
        $best = ['book_id' => null, 'method' => 'unmatched', 'confidence' => 0.0];

        if (trim($title) === '') {
            return $best;
        }

        $books = $this->db->table('books')
            ->select('books.id, books.title, GROUP_CONCAT(authors.name SEPARATOR ", ") AS authors')
            ->join('book_authors', 'book_authors.book_id = books.id', 'left')
            ->join('authors', 'authors.id = book_authors.author_id', 'left')
            ->where('books.is_active', 1)
            ->where('books.deleted_at', null)
            ->groupBy('books.id')
            ->get()
            ->getResultArray();

        foreach ($books as $book) {
            $confidence = $this->scoreTitleAuthor($title, $author, (string) $book['title'], (string) ($book['authors'] ?? ''));
            if ($confidence > $best['confidence']) {
                $best = ['book_id' => (int) $book['id'], 'method' => 'title_author', 'confidence' => $confidence];
            }
        }

        if ($best['confidence'] < 0.7) {
            return ['book_id' => null, 'method' => 'low_confidence', 'confidence' => $best['confidence']];
        }

        return $best;
    }

    public function scoreTitleAuthor(string $sourceTitle, string $sourceAuthor, string $bookTitle, string $bookAuthor): float
    {
        $sourceTitleNorm = $this->normalizer->normalize($sourceTitle);
        $bookTitleNorm = $this->normalizer->normalize($bookTitle);

        if ($sourceTitleNorm === '' || $bookTitleNorm === '') {
            return 0.0;
        }

        similar_text($sourceTitleNorm, $bookTitleNorm, $titlePercent);
        $titleTokenScore = $this->tokenOverlap($sourceTitleNorm, $bookTitleNorm);
        $titleScore = max($titlePercent / 100, $titleTokenScore);

        $sourceAuthorNorm = $this->normalizer->normalize($sourceAuthor);
        $bookAuthorNorm = $this->normalizer->normalize($bookAuthor);
        $authorScore = 0.7;
        if ($sourceAuthorNorm !== '' && $bookAuthorNorm !== '') {
            similar_text($sourceAuthorNorm, $bookAuthorNorm, $authorPercent);
            $authorScore = max($authorPercent / 100, $this->tokenOverlap($sourceAuthorNorm, $bookAuthorNorm));
        }

        return round(($titleScore * 0.78) + ($authorScore * 0.22), 4);
    }

    private function bookExists(int $bookId): bool
    {
        return $this->db->table('books')
            ->where('id', $bookId)
            ->where('is_active', 1)
            ->where('deleted_at', null)
            ->countAllResults() > 0;
    }

    private function tokenOverlap(string $left, string $right): float
    {
        $leftTokens = array_unique(array_filter(explode(' ', $left)));
        $rightTokens = array_unique(array_filter(explode(' ', $right)));

        if ($leftTokens === [] || $rightTokens === []) {
            return 0.0;
        }

        $intersection = array_intersect($leftTokens, $rightTokens);
        $union = array_unique(array_merge($leftTokens, $rightTokens));

        return count($intersection) / count($union);
    }
}
