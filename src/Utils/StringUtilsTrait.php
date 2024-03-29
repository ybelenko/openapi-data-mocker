<?php

/**
 * Openapi Data Mocker
 * PHP version 7.3
 *
 * @package OpenAPIServer\Mock
 * @link    https://github.com/ybelenko/openapi-data-mocker
 * @author  Yuriy Belenko <yura-bely@mail.ru>
 * @license MIT
 */

declare(strict_types=1);

namespace OpenAPIServer\Utils;

/**
 * StringUtilsTrait
 *
 * This class duplicates functionality of ModelUtils.java and AbstractPhpCodegen.java classes from Openapi-Generator.
 */
trait StringUtilsTrait
{
    /**
     * Camelize name (parameter, property, method, etc)
     * This is recreated method of @link modules/openapi-generator/src/main/java/org/openapitools/codegen/utils/StringUtils.java class.
     *
     * @param string    $word                 String to be camelize.
     * @param bool|null $lowercaseFirstLetter Lower case for first letter if set to true.
     *
     * @return string camelized string
     */
    public static function camelize(string $word, ?bool $lowercaseFirstLetter = false): string
    {
        // Replace all slashes with dots (package separator)
        $p = '/\/(.?)/';
        $word = preg_replace($p, '.$1', $word);

        // case out dots
        $parts = explode('.', $word);
        $str = '';
        foreach ($parts as $z) {
            if (strlen($z) > 0) {
                $str .= strtoupper(substr($z, 0, 1)) . substr($z, 1);
            }
        }
        $word = $str;

        // Uppercase the class name.
        $p = '/(\.?)(\w)([^\.]*)$/';
        $word = preg_replace_callback($p, function ($matches) {
            $rep = $matches[1] . strtoupper($matches[2]) . $matches[3];
            $rep = preg_replace('/\$/', '\\\$', $rep);
            return $rep;
        }, $word);

        // Remove all underscores (underscore_case to camelCase)
        $p = '/(_)(.)/';
        while (preg_match($p, $word, $matches) === 1) {
            $original = $matches[2][0];
            $upperCase = strtoupper($original);
            if ($original === $upperCase) {
                $word = preg_replace($p, '$2', $word, 1);
            } else {
                $word = preg_replace($p, $upperCase, $word, 1);
            }
        }

        // Remove all hyphens (hyphen-case to camelCase)
        $p = '/(-)(.)/';
        while (preg_match($p, $word, $matches) === 1) {
            $upperCase = strtoupper($matches[2][0]);
            $word = preg_replace($p, $upperCase, $word, 1);
        }

        if ($lowercaseFirstLetter === true && strlen($word) > 0) {
            $i = 0;
            $charAt = substr($word, $i, 1);
            while (
                $i + 1 < strlen($word)
                && !(
                    ($charAt >= 'a' && $charAt <= 'z')
                    || ($charAt >= 'A' && $charAt <= 'Z')
                )
            ) {
                $i++;
                $charAt = substr($word, $i, 1);
            }
            $i++;
            $word = strtolower(substr($word, 0, $i)) . substr($word, $i);
        }

        // remove all underscore
        $word = str_replace('_', '', $word);

        return $word;
    }

    /**
     * Checks whether string is reserved php keyword.
     * This is recreated method of @link modules/openapi-generator/src/main/java/org/openapitools/codegen/languages/AbstractPhpCodegen.java class.
     *
     * @param string $word Checked string.
     *
     * @return bool
     */
    public static function isReservedWord(string $word): bool
    {
        // __halt_compiler is ommited because class names with underscores not allowed anyway
        return in_array(
            strtolower($word),
            ['abstract', 'and', 'array', 'as', 'break', 'callable', 'case', 'catch', 'class', 'clone', 'const', 'continue', 'declare', 'default', 'die', 'do', 'echo', 'else', 'elseif', 'empty', 'enddeclare', 'endfor', 'endforeach', 'endif', 'endswitch', 'endwhile', 'eval', 'exit', 'extends', 'final', 'for', 'foreach', 'function', 'global', 'goto', 'if', 'implements', 'include', 'include_once', 'instanceof', 'insteadof', 'interface', 'isset', 'list', 'namespace', 'new', 'or', 'print', 'private', 'protected', 'public', 'require', 'require_once', 'return', 'static', 'switch', 'throw', 'trait', 'try', 'unset', 'use', 'var', 'while', 'xor']
        );
    }
}
