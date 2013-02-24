Memcached JSON Session Save Handler
==========

[![Build Status](https://travis-ci.org/lboynton/memcached-json-session-save-handler.png?branch=master)](https://travis-ci.org/lboynton/memcached-json-session-save-handler)

A JSON-formatted memcached session save handler. By default, when saving 
sessions in memcached using the php-memcached extension, serialisation is
performed by either php, php_igbinary or WDDX. This custom session save
handler serialises the session as JSON and stores it in memcached.

Usage
----------

    // create connection to memcached
    $memcached = new Memcached();
    $memcached->addServer('localhost', 11211);

    // register handler (PHP 5.3 compatible)
    $handler = new Lboy\Session\SaveHandler($memcached);

    session_set_save_handler(
        array($saveHandler, 'open'),
        array($saveHandler, 'close'),
        array($saveHandler, 'read'),
        array($saveHandler, 'write'),
        array($saveHandler, 'destroy'),
        array($saveHandler, 'gc')
    );

    register_shutdown_function('session_write_close');
    session_start();

    // start using the session
    $_SESSION['serialiser'] = 'this should be in json';
