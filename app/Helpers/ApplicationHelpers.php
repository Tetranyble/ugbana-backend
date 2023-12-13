<?php

use Illuminate\Support\Facades\File;

if (! function_exists('getModels')) {
    function getModels($namespace = 'Models')
    {
        $appNamespace = Illuminate\Container\Container::getInstance()->getNamespace();

        return collect(File::allFiles(app_path($namespace)))->map(function ($item) use ($appNamespace, $namespace) {
            $rel = $item->getRelativePathName();
            $class = sprintf('\%s%s%s', $appNamespace, $namespace ? $namespace.'\\' : '',
                implode('\\', explode('/', substr($rel, 0, strrpos($rel, '.')))));

            return class_exists($class) ? $class : null;
        })->filter();
    }
}
