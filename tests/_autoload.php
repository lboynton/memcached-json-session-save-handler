<?php

/**
 * Setup autoloading
 */
function LboyTest_Autoloader($class)
{
    $class = ltrim($class, '\\');

    if (!preg_match('#^(Lboy(Test)?|Zend|PHPUnit)(\\\\|_)#', $class)) {
        return false;
    }

    // $segments = explode('\\', $class); // preg_split('#\\\\|_#', $class);//
    $segments = preg_split('#[\\\\_]#', $class); // preg_split('#\\\\|_#', $class);//
    $ns       = array_shift($segments);

    switch ($ns) {
        case 'Lboy':
            $file = dirname(__DIR__) . '/library/Lboy/';
            break;
        case 'LboyTest':
            $file = __DIR__ . '/LboyTest/';
            break;
        default:
            $file = false;
            break;
    }

    if ($file) {
        $file .= implode('/', $segments) . '.php';
        if (file_exists($file)) {
            return include_once $file;
        }
    }

    $segments = explode('_', $class);
    $ns       = array_shift($segments);

    switch ($ns) {
        case 'Lboy':
            $file = dirname(__DIR__) . '/library/Lboy/';
            break;
        case 'Zend':
            $file = 'Zend/';
            break;
        default:
            return false;
    }
    $file .= implode('/', $segments) . '.php';

//    if (file_exists($file)) {
        return include_once $file;
//    }

    return false;
}

spl_autoload_register('LboyTest_Autoloader', true, true);
