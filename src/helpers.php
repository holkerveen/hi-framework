<?php
// src/helpers.php

if (!function_exists('dd')) {
    /**
     * Dump and die - outputs variable contents wrapped in <pre> tags and exits
     *
     * @param mixed ...$vars Variables to dump
     * @return never
     */
    function dd(mixed ...$vars): never
    {
        echo '<pre>';
        foreach ($vars as $var) {
            var_dump($var);
        }
        echo '</pre>';
        exit(1);
    }
}
