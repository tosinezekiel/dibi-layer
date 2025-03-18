<?php

if ( ! function_exists('readRepo')) {
    /**
     * @template ReadRepo
     * @param  class-string<ReadRepo> $fqcn
     * @return ReadRepo
     */
    function readRepo(string $fqcn)
    {
        return app($fqcn);
    }
}

if ( ! function_exists('writeRepo')) {
    /**
     * @template WriteRepo
     * @param  class-string<WriteRepo> $fqcn
     * @return WriteRepo
     */
    function writeRepo(string $fqcn)
    {
        return app($fqcn);
    }
}
