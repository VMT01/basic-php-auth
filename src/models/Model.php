<?php

namespace app\models;

use app\core\Validator;

class Model extends Validator
{
    /**
     * @param array<string, string> $data 
     */
    public function loadData($data)
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
    }

    /**
     * @param string $value
     */
    protected function normalize(string $value): string
    {
        $value = html_entity_decode(
            htmlspecialchars_decode($value),
            ENT_QUOTES
        );
        $value = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value);

        return $value;
    }
}
