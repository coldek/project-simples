<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitf1c76bcd2bb66d584fb9aa9143be8ef4
{
    public static $prefixLengthsPsr4 = array (
        'A' => 
        array (
            'App\\' => 4,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'App\\' => 
        array (
            0 => __DIR__ . '/../..' . '/lib',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitf1c76bcd2bb66d584fb9aa9143be8ef4::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitf1c76bcd2bb66d584fb9aa9143be8ef4::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}