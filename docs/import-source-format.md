# DealSach Import Source Format

P5 uses an import-first pipeline. Retailer data is stored as JSON arrays under `writable/import/` and processed by:

```bash
php spark dealsach:crawl all
php spark dealsach:crawl fahasa
php spark dealsach:crawl nhasachphuongnam
php spark dealsach:crawl tiki
php spark dealsach:crawl shopee
```

## Files

| Retailer | File |
|---|---|
| Fahasa | `writable/import/fahasa.json` |
| Nhà sách Phương Nam | `writable/import/nhasachphuongnam.json` |
| Tiki | `writable/import/tiki.json` |
| Shopee | `writable/import/shopee.json` |

## Record Schema

```json
{
  "book_id": 1,
  "title": "Đắc Nhân Tâm",
  "author": "Dale Carnegie",
  "isbn": "9786045876412",
  "original_id": "FHS-001",
  "url": "https://fahasa.com/fhs-001",
  "listed_price": 128000,
  "discounted_price": 99000,
  "in_stock": true
}
```

Required fields: `original_id`, `url`, and either a valid `book_id`, matching `isbn`, or enough `title`/`author` text for matching.

Price rule: `effective_price = discounted_price` when present and positive, otherwise `listed_price`.

Matching rule: `book_id` is accepted for deterministic demo imports, then ISBN exact match, then normalized title and author match. Low-confidence records are logged as row errors and do not create active retailer items.
