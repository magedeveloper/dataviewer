<?php
declare(strict_types=1);

namespace Aix\Dataviewer\Utility;

class ArrayUtility
{
    /**
     * Gets an array value by a given path
     *
     * @param array $array Array to search
     * @param string $path Path for array
     * @return mixed
     */
    public static function getArrayValueByPath(array $array, string $path)
    {
        $divided = StringUtility::explodeSeparatedString($path);

        $func = function($arr, $k) {
            return $arr[$k];
        };

        $newArr = $array;
        foreach ($divided as $_key)
        {
            $newArr = $func($newArr, $_key);
        }

        return $newArr;
    }

    /**
     * Recursive finds a key in an array and
     * returns its value
     *
     * @param string $needle
     * @param array $haystack
     * @return mixed|bool
     */
    public static function recursiveFindKey(string $needle, array $haystack)
    {
        $iterator  = new \RecursiveArrayIterator($haystack);
        $recursive = new \RecursiveIteratorIterator($iterator, \RecursiveIteratorIterator::SELF_FIRST);
        $found = [];
        foreach ($recursive as $key => $value) {
            if ($key === $needle) {
                array_push($found, $value);
            }
        }

        return $found;
    }

    /**
     * Parses an xml to an array
     *
     * @param string $xml Input XML String
     * @return array
     */
    public static function xml2array(string $xml): array
    {
        $xml_parser = xml_parser_create();
        xml_parse_into_struct($xml_parser, $xml, $vals);
        xml_parser_free($xml_parser);
        // wyznaczamy tablice z powtarzajacymi sie tagami na tym samym poziomie
        $_tmp='';
        foreach ($vals as $xml_elem)
        {
            $x_tag=$xml_elem['tag'];
            $x_level=$xml_elem['level'];
            $x_type=$xml_elem['type'];
            if ($x_level!=1 && $x_type == 'close')
            {
                if (isset($multi_key[$x_tag][$x_level]))
                    $multi_key[$x_tag][$x_level]=1;
                else
                    $multi_key[$x_tag][$x_level]=0;
            }
            if ($x_level!=1 && $x_type == 'complete')
            {
                if ($_tmp==$x_tag)
                    $multi_key[$x_tag][$x_level]=1;
                $_tmp=$x_tag;
            }
        }

        foreach ($vals as $xml_elem)
        {
            $x_tag=$xml_elem['tag'];
            $x_level=$xml_elem['level'];
            $x_type=$xml_elem['type'];
            if ($x_type == 'open')
                $level[$x_level] = $x_tag;
            $start_level = 1;
            $php_stmt = '$xml_array';
            if ($x_type=='close' && $x_level!=1)
                $multi_key[$x_tag][$x_level]++;
            while ($start_level < $x_level)
            {
                $php_stmt .= '[$level['.$start_level.']]';
                if (isset($multi_key[$level[$start_level]][$start_level]) && $multi_key[$level[$start_level]][$start_level])
                    $php_stmt .= '['.($multi_key[$level[$start_level]][$start_level]-1).']';
                $start_level++;
            }
            $add='';
            if (isset($multi_key[$x_tag][$x_level]) && $multi_key[$x_tag][$x_level] && ($x_type=='open' || $x_type=='complete'))
            {
                if (!isset($multi_key2[$x_tag][$x_level]))
                    $multi_key2[$x_tag][$x_level]=0;
                else
                    $multi_key2[$x_tag][$x_level]++;
                $add='['.$multi_key2[$x_tag][$x_level].']';
            }
            if (isset($xml_elem['value']) && trim($xml_elem['value'])!='' && !array_key_exists('attributes',$xml_elem))
            {
                if ($x_type == 'open')
                    $php_stmt_main=$php_stmt.'[$x_type]'.$add.'[\'content\'] = $xml_elem[\'value\'];';
                else
                    $php_stmt_main=$php_stmt.'[$x_tag]'.$add.' = $xml_elem[\'value\'];';
                eval($php_stmt_main);
            }
            if (array_key_exists('attributes',$xml_elem))
            {
                if (isset($xml_elem['value']))
                {
                    $php_stmt_main=$php_stmt.'[$x_tag]'.$add.'[\'content\'] = $xml_elem[\'value\'];';
                    eval($php_stmt_main);
                }
                foreach ($xml_elem['attributes'] as $key=>$value)
                {
                    $php_stmt_att=$php_stmt.'[$x_tag]'.$add.'[$key] = $value;';
                    eval($php_stmt_att);
                }
            }
        }

        return $xml_array;
    }

    /**
     * UTF-8 Encodes a multidimensional array
     *
     * @param array $array
     * @return array
     */
    public static function utf8encode(array $array): array
    {
        array_walk_recursive($array, function(&$item, $key) {
            if (!mb_detect_encoding($item, 'utf-8', true)) {
                $item = utf8_encode($item);
            }
        });

        return $array;
    }

    /**
     * Checks an array for existence of
     * parameter and value
     *
     * @param array $arrayToCheck
     * @param array $arrayParamValues
     * @return bool
     */
    public static function checkArrayForParamValue(array $arrayToCheck, array $arrayParamValues): bool
    {
        foreach ($arrayParamValues as $_param=>$_values) {
            foreach ($_values as $_value) {
                if (isset($arrayToCheck[$_param]) && $_GET[$_param] == $_value) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Checks if an array is multidimensional
     *
     * @param array $array
     * @return bool
     */
    public static function isMultidimensional(array $array): bool
    {
        return (count($array) != count($array, 1));
    }

    /**
     * Lowercases all keys in an multidimensional
     * array
     *
     * @see http://php.net/manual/de/function.array-change-key-case.php
     * @param array $arr
     * @return array
     */
    public static function lowercaseArrayKeys(array $arr): array
    {
        return array_map(function($item) {
            if (is_array($item))
                $item = self::lowercaseArrayKeys($item);
            return $item;
        },array_change_key_case($arr));
    }

    /**
     * @param array $parents
     * @param array $children
     * @return array
     */
    public static function createBranch(array &$parents, array $children): array
    {
        $tree = [];
        foreach ($children as $child) {
            if (isset($parents[$child['id']])) {
                $child['children'] = self::createBranch($parents, $parents[$child['id']]);
            }
            $tree[$child['id']] = $child;
        }
        return $tree;
    }

    /**
     * @param array $flat
     * @param int $root
     * @return array
     */
    public static function createTree(array $flat, int $root = 0): array
    {
        $parents = [];
        foreach ($flat as $a) {
            $parents[$a['parent']][] = $a;
        }
        return self::createBranch($parents, $parents[$root]);
    }

    /**
     * @param array $input
     * @param array $column_keys
     * @return array
     */
    public static function array_column_multi(array $input, array $column_keys): array
    {
        $result = array();
        $column_keys = array_flip($column_keys);
        foreach($input as $key => $el) {
            $result[$key] = array_intersect_key($el, $column_keys);
        }
        return $result;
    }
}
