<?php

namespace App\Support;

class ListQuery
{
    /**
     * @param  list<string>  $keys
     * @return array<string, string>
     */
    public static function pull(array $keys): array
    {
        $query = [];

        foreach ($keys as $key) {
            $value = request()->query($key);

            if (! is_scalar($value)) {
                continue;
            }

            $value = trim((string) $value);

            if ($value === '') {
                continue;
            }

            if ($key === 'page' && ! ctype_digit($value)) {
                continue;
            }

            $query[$key] = $value;
        }

        return $query;
    }
}
