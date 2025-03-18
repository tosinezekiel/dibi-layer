<?php

namespace CircleLinkHealth\ReposModelsGenerator\Models;

use Illuminate\Database\Eloquent\Model as EloquentModel;

/**
 * @template-covariant M
 */
abstract class Model extends EloquentModel
{
    public function load($relations)
    {
        if ($relations) {
            self::throw();
        }

        return $this;
    }

    public static function with($relations)
    {
        if ($relations) {
            self::throw();

            return new static();
        }
    }

    private static function throw()
    {
        throw new \Exception('This method is not available in this codebase. Please make sure you are using repositories.');
    }
}
