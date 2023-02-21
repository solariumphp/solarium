<?php

namespace Solarium\Tests\QueryType\Luke\ResponseParser;

/**
 * Default info data used in multiple ResponseParser tests.
 */
trait InfoDataTrait
{
    public function getInfoData(): array
    {
        // all the actual flag abbrevations are needed for parsing 'schema', 'doc', or 'fields'
        return [
            'key' => [
                'I' => 'Flag I',
                'T' => 'Flag T',
                'S' => 'Flag S',
                'D' => 'Flag D',
                'U' => 'Flag U',
                'M' => 'Flag M',
                'V' => 'Flag V',
                'o' => 'Flag o',
                'p' => 'Flag p',
                'y' => 'Flag y',
                'O' => 'Flag O',
                'F' => 'Flag F',
                'P' => 'Flag P',
                'H' => 'Flag H',
                'L' => 'Flag L',
                'B' => 'Flag B',
                'f' => 'Flag f',
                'l' => 'Flag l',
            ],
            'NOTE' => 'This is a note.',
        ];
    }
}
