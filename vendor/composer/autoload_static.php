<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit80f7fdcd0d41bade4b01d3882f832546
{
    public static $prefixLengthsPsr4 = array (
        'P' => 
        array (
            'Psr\\Log\\' => 8,
        ),
        'E' => 
        array (
            'ElephantIO\\' => 11,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Psr\\Log\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/log/Psr/Log',
        ),
        'ElephantIO\\' => 
        array (
            0 => __DIR__ . '/..' . '/wisembly/elephant.io/src',
            1 => __DIR__ . '/..' . '/wisembly/elephant.io/test',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit80f7fdcd0d41bade4b01d3882f832546::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit80f7fdcd0d41bade4b01d3882f832546::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
