<?php

namespace App\Support;

class SearchHighlight
{
    /**
     * @param  list<string>  $terms
     */
    public static function html(?string $text, array $terms): string
    {
        $text = $text ?? '';
        $terms = array_values(array_unique(array_filter(array_map('trim', $terms))));

        if ($terms === []) {
            return e($text);
        }

        $escaped = e($text);
        foreach ($terms as $term) {
            if ($term === '') {
                continue;
            }
            $needle = e($term);
            if ($needle === '') {
                continue;
            }
            $escaped = str_ireplace($needle, '<mark class="search-highlight">'.$needle.'</mark>', $escaped);
        }

        return $escaped;
    }
}
