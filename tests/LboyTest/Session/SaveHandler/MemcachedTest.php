<?php

namespace LboyTest\Session\SaveHandler;

use Lboy\Session\SaveHandler\Memcached;

/**
 * Tests for memcached session save handler
 *
 * @author Lee Boynton <lee@lboynton.com>
 */
class MemcachedTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Memcache
     */
    protected $memcached;

    /**
     * @var string
     */
    protected $originalSessionSavePath;

    public function setUp()
    {
        // fix permission denied warnings by setting to a path we should have
        // write access to
        $this->originalSessionSavePath = session_save_path();
        session_save_path('/tmp');

        $this->memcached = new \Memcached();
        $this->memcached->addServer(TESTS_MEMCACHE_HOST,
            TESTS_MEMCACHE_PORT);
    }

    /**
     * @runInSeparateProcess
     */
    public function testReadWrite()
    {
        session_start();
        $saveHandler = new Memcached($this->memcached);
        $this->assertTrue($saveHandler->open('savepath', 'sessionname'));

        $id = session_id();
        $_SESSION = array('foo' => 'bar', 'bar' => array('foo' => 'bar'));

        $this->assertTrue($saveHandler->write($id, session_encode()));
        $this->assertEquals($_SESSION,
            json_decode($this->memcached->get("sessions/{$id}"), true));
        $serializedSession = $saveHandler->read($id);
        $this->assertTrue(!empty($serializedSession));

        $_SESSION = array('foo' => array(1, 2, 3));

        $this->assertTrue($saveHandler->write($id, serialize($_SESSION)));
        $this->assertEquals($_SESSION,
            json_decode($this->memcached->get("sessions/{$id}"), true));
        $serializedSession2 = $saveHandler->read($id);
        $this->assertTrue(!empty($serializedSession2));
    }

    /**
     * @runInSeparateProcess
     */
    public function testDestroy()
    {
        session_start();
        $saveHandler = new Memcached($this->memcached);
        $saveHandler->open('savepath', 'sessionname');

        $id = session_id();
        $_SESSION = array('foo' => 'bar', 'bar' => array('foo' => 'bar'));

        $saveHandler->write($id, serialize($_SESSION));
        $this->assertEquals($_SESSION,
            json_decode($this->memcached->get("sessions/{$id}"), true));

        $saveHandler->destroy($id);
        $this->assertEquals('', $saveHandler->read($id));
        $this->assertFalse($this->memcached->get("sessions/{$id}"));
    }

    public function testGarbageCollection()
    {
        $saveHandler = new Memcached($this->memcached);
        // should always return true
        $this->assertTrue($saveHandler->gc(-1));
    }

    public function testClose()
    {
        $saveHandler = new Memcached($this->memcached);
        // should always return true
        $this->assertTrue($saveHandler->close());
    }

    public function tearDown()
    {
        $this->memcached->flush();

        // reset session save path back to default
        session_save_path($this->originalSessionSavePath);
    }
}
