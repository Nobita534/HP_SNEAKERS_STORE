<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;

class ProductSearch
{
    public function products(string $keyword): Builder
    {
        return $this->matching(Product::where('is_active', true), $keyword, ['name', 'brand']);
    }

    public function brands(string $keyword): Builder
    {
        $query = $this->matching(Product::where('is_active', true), $keyword, ['brand'])
            ->select('brand')->groupBy('brand');

        return $this->rank($query, $keyword, ['brand'])->orderBy('brand');
    }

    public function rank(Builder $query, string $keyword, array $columns = ['name', 'brand']): Builder
    {
        if ($keyword === '') {
            return $query->orderByDesc('created_at');
        }

        $this->assertColumns($columns);
        $pattern = $this->pattern($keyword);
        $postgres = $query->getConnection()->getDriverName() === 'pgsql';
        $conditions = array_map(fn ($column) => $this->likeSql($column, $postgres), $columns);

        // Ưu tiên kết quả chứa đúng từ khóa trước các kết quả gần giống.
        $query->orderByRaw('CASE WHEN '.implode(' OR ', $conditions).' THEN 0 ELSE 1 END',
            array_fill(0, count($columns), $postgres ? $pattern : mb_strtolower($pattern)));

        if ($postgres && $this->canMatchApproximately($keyword)) {
            $scores = array_map(fn ($column) => "word_similarity(?, {$column})", $columns);
            $query->orderByRaw('GREATEST('.implode(', ', $scores).') DESC',
                array_fill(0, count($columns), $keyword));
        }

        return $query;
    }

    private function matching(Builder $query, string $keyword, array $columns): Builder
    {
        if ($keyword === '') {
            return $query;
        }

        $postgres = $query->getConnection()->getDriverName() === 'pgsql';
        $pattern = $this->pattern($keyword);

        return $query->where(function (Builder $matches) use ($keyword, $columns, $postgres, $pattern) {
            foreach ($columns as $column) {
                $matches->orWhereRaw($this->likeSql($column, $postgres),
                    [$postgres ? $pattern : mb_strtolower($pattern)]);

                if ($postgres && $this->canMatchApproximately($keyword)) {
                    $matches->orWhereRaw("? <% {$column}", [$keyword]);
                }
            }
        });
    }

    private function likeSql(string $column, bool $postgres): string
    {
        return $postgres ? "{$column} ILIKE ? ESCAPE '!'" : "LOWER({$column}) LIKE ? ESCAPE '!'";
    }

    private function pattern(string $keyword): string
    {
        return '%'.str_replace(['!', '%', '_'], ['!!', '!%', '!_'], $keyword).'%';
    }

    private function canMatchApproximately(string $keyword): bool
    {
        // Ký tự đặc biệt được tìm nguyên văn; từ khóa ngắn chỉ tìm chuỗi con.
        return mb_strlen($keyword) >= 3 && preg_match('/^[\pL\pN\s-]+$/u', $keyword) === 1;
    }

    private function assertColumns(array $columns): void
    {
        if ($columns === [] || array_diff($columns, ['name', 'brand']) !== []) {
            throw new \InvalidArgumentException('Chỉ hỗ trợ tìm theo tên và thương hiệu.');
        }
    }
}
