<?php

namespace Saxulum\Crud\Util;

class Helper
{
    /**
     * @param string $input
     *
     * @return string
     */
    public static function camelCaseToUnderscore($input)
    {
        $output = '';
        $outputParts = preg_split('/(?=[\p{Lu}])/', $input);
        foreach ($outputParts as $outputPart) {
            if ($outputPart) {
                $output .= rtrim($outputPart, '_').'_';
            }
        }

        $output = mb_strtolower(rtrim($output, '_'));

        return $output;
    }
}
