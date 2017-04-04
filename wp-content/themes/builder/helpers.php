<?php

function asset($path = '') {
    return get_bloginfo('template_directory') . "/" . $path;
}

function normalizeTemplatePath($path) {
    $path = str_replace("/", DIRECTORY_SEPARATOR, $path);
    $path = str_replace("\\", DIRECTORY_SEPARATOR, $path);
    # Remove Trail
    $path = trim(str_replace(__DIR__, "", $path), "\\/");
    $path = preg_replace("/((.blade)?.php)|(.scss)/", "", $path);
    $path = str_replace(DIRECTORY_SEPARATOR, ".", $path);
    return $path;
}

/**
 * Determine if a given string starts with a given substring.
 *
 * @param  string $haystack
 * @param  string|array $needles
 * @return bool
 */
function startsWith($haystack, $needles)
{
    foreach ((array)$needles as $needle) {
        if ($needle != '' && substr($haystack, 0, strlen($needle)) === (string)$needle) {
            return true;
        }
    }

    return false;
}


/**
 * Recursive Glob
 * @param $pattern
 * @param int $flags
 * @return array
 */
function rglob($pattern, $flags = 0) {
    $files = glob($pattern, $flags);
    foreach (glob(dirname($pattern).'/*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir) {
        $files = array_merge($files, rglob($dir.'/'.basename($pattern), $flags));
    }
    return $files;
}

function start_section($name)
{
    global $attribute;
    $attribute->start_section($name);
}

function stop_section()
{
    global $attribute;
    $attribute->stop_section();
}

function attr($key, $type = Attribute::KING_TEXT, $label = '')
{
    global $attribute;
    return $attribute->get($key, $type, $label);
}