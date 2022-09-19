<?php

class CStr
{
    /**
     * @param mixed $string     The referenced value to check
     *
     * @return boolean
     *
     * Checks if the referenced value does exists, is of string type
     * and is not an empty string
     */
    public static function isValidString(&$string): bool
    {
        return isset($string) && is_string($string) && !empty($string) && strlen($string) > 0;
    }

    /**
     * @param mixed $string     The referenced value to check
     *
     * @return boolean
     *
     * Checks if the referenced value does exists, is of boolean type
     * and is not an empty string
     */
    public static function isValidBoolean(&$boolean): bool
    {
        return isset($boolean) && is_bool($boolean);
    }

    /**
     * @param mixed $array     The referenced value to check
     *
     * @return boolean
     *
     * Checks if the referenced value does exists, is of array type
     * and is not an empty array
     */
    public static function isValidArray(&$array): bool
    {
        return isset($array) && is_array($array) && !empty($array) && count($array) > 0;
    }

    /**
     * Applies logic to generate a computed CSS class names string based on
     * provided data
     *
     * @param array $class_data     Associative array with keys as class name
     *                              and value as a boolean expression
     *
     * @return string               A computed CSS classes text based on passed
     *                              conditions
     */
    public static function classes(array $class_data): string
    {
        $__classes = [];
        foreach ($class_data as $class => $condition) {
            if (!$condition) continue;
            array_push($__classes, $class);
        }

        return implode(' ', $__classes);
    }

    /**
     * Coverts array of props passed to an element to html attribute/value string
     *
     * @param array $attrs      Attributes being passed to an element
     *
     * @return string           An HTML string that can be embedded into a tag
     */
    public static function attributes(array $attrs): string
    {
        $attributes = [];

        foreach ($attrs as $attr => $val)
            array_push($attributes, sprintf('%s="%s"', $attr, $val));

        return implode(' ', $attributes);
    }
}
