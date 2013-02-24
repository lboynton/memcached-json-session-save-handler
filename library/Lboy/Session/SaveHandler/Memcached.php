<?php

namespace Lboy\Session\SaveHandler;

/**
 * Memcached JSON-formatted session save handler
 *
 * The default memcache session save handler stores sessions encoded with
 * session_encode, but the encoded session is not simple to parse in other
 * languages. Therefore, this class encodes the session in JSON to make reading
 * the session in other languages simple.
 *
 * Note: This class uses the newer php-memcached extension, not php-memcache!
 * @see http://php.net/manual/en/book.memcached.php
 *
 * @author Lee Boynton <lee@lboynton.com>
 */
class Memcached
{
    /**
     * @var \Memcached
     */
    protected $memcached;

    /**
     * Create new memcached session save handler
     * @param \Memcached $memcached
     */
    public function __construct(\Memcached $memcached)
    {
        $this->memcached = $memcached;
    }

    /**
     * Close session
     *
     * @return boolean
     */
    public function close()
    {
        return true;
    }

    /**
     * Destroy session
     *
     * @param string $id
     * @return boolean
     */
    public function destroy($id)
    {
        return $this->memcached->delete("sessions/{$id}");
    }

    /**
     * Garbage collect. Memcache handles this with expiration times.
     *
     * @param int $maxlifetime
     * @return boolean Always true
     */
    public function gc($maxlifetime)
    {
        // let memcached handle this with expiration time
        return true;
    }

    /**
     * Open session
     *
     * @param string $savePath
     * @param string $name
     * @return boolean
     */
    public function open($savePath, $name)
    {
        // Note: session save path is not used
        $this->sessionName = $name;
        $this->lifetime = ini_get('session.gc_maxlifetime');
        return true;
    }

    /**
     * Read session data
     *
     * @param string $id
     * @return string
     */
    public function read($id)
    {
        $_SESSION = json_decode($this->memcached->get("sessions/{$id}"), true);

        if (isset($_SESSION) && !empty($_SESSION) && $_SESSION != null)
        {
            return session_encode();
        }

        return '';
    }

    /**
     * Write session data
     *
     * @param string $id
     * @param string $data
     * @return boolean
     */
    public function write($id, $data)
    {
        // note: $data is not used as it has already been serialised by PHP,
        // so we use $_SESSION which is an unserialised version of $data.
        return $this->memcached->set("sessions/{$id}", json_encode($_SESSION),
            $this->lifetime);
    }
}
