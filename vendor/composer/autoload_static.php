<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit93890b97c589f325273c34da946c1a2c
{
    public static $prefixLengthsPsr4 = array (
        'P' => 
        array (
            'Psr\\Log\\' => 8,
            'PHPMailer\\PHPMailer\\' => 20,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Psr\\Log\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/log/src',
        ),
        'PHPMailer\\PHPMailer\\' => 
        array (
            0 => __DIR__ . '/..' . '/phpmailer/phpmailer/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit93890b97c589f325273c34da946c1a2c::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit93890b97c589f325273c34da946c1a2c::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit93890b97c589f325273c34da946c1a2c::$classMap;

        }, null, ClassLoader::class);
    }
}
