<?php

namespace AppserverIo\Appserver\ServletEngine\Security\Utils;

use AppserverIo\Appserver\ServletEngine\Security\Utils\Util;
use AppserverIo\Lang\String;

/**
 * Unit test class for Util
 *
 * @author    Alexandros Weigl <a.weigl@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 */
class UtilTest extends \PHPUnit_Framework_TestCase
{
    protected $password;
    protected $name;
    protected $salt;
    protected $hashAlgorithm;
    protected $hashEncoding;
    protected $hashCharset;
    protected $callback;

    /**
     * Setup the test data
     *
     * @return void
     */
    public function setUp()
    {
        $this->name = new String("test");
        $this->password = new String("test");
    }

    /**
     * Test if createPasswordHash hashes md5 correctly without a given salt
     *
     * @return void
     */
    public function testCreatePasswordHashedMd5WithoutSalt()
    {
        $this->hashAlgorithm = HashKeys::MD5;
        $expectedPassword = md5($this->password);
        $password = Util::createPasswordHash(
            $this->hashAlgorithm,
            $this->hashEncoding,
            $this->hashCharset,
            $this->name,
            $this->password,
            $this->callback
        );
        $this->assertEquals($expectedPassword, $password);
    }

    /**
     * Test if createPasswordHash hashes md5 correctly with a salt
     *
     * @return void
     */
    public function testCreatePasswordHashedMd5WithSalt()
    {
        $this->hashAlgorithm = HashKeys::MD5;
        $this->salt = '1234';
        $expectedPassword = md5($this->salt . $this->password);
        $password = Util::createPasswordHash(
            $this->hashAlgorithm,
            $this->hashEncoding,
            $this->hashCharset,
            $this->name,
            $this->password,
            $this->callback,
            $this->salt
        );

        $this->assertEquals($expectedPassword, $password->stringValue());
    }

    /**
     * Test if createPasswordHash hashes SHA1 correctly without a salt
     *
     * @return void
     */
    public function testCreatePasswordHashedSha1WithoutSalt()
    {
        $this->hashAlgorithm = HashKeys::SHA1;
        $expectedPassword = hash(HashKeys::SHA1, $this->password);
        $password = Util::createPasswordHash(
            $this->hashAlgorithm,
            $this->hashEncoding,
            $this->hashCharset,
            $this->name,
            $this->password,
            $this->callback
        );

        $this->assertEquals($expectedPassword, $password);
    }

    /**
     * Test if createPasswordHash hashes SHA1 correctly with a salt
     *
     * @return void
     */
    public function testCreatePasswordHashedSha1WithSalt()
    {
        $this->hashAlgorithm = HashKeys::SHA1;
        $this->salt = '1234';
        $expectedPassword = hash(HashKeys::SHA1, $this->salt . $this->password);
        $password = Util::createPasswordHash(
            $this->hashAlgorithm,
            $this->hashEncoding,
            $this->hashCharset,
            $this->name,
            $this->password,
            $this->callback,
            $this->salt
        );

        $this->assertEquals($expectedPassword, $password->stringValue());
    }

    /**
     * Test if createPasswordHash hashes SHA256 correclty without a salt
     *
     * @return void
     */
    public function testCreatePasswordHashedSha256WithoutSalt()
    {
        $this->hashAlgorithm = HashKeys::SHA256;
        $expectedPassword = hash(HashKeys::SHA256, $this->password);
        $password = Util::createPasswordHash(
            $this->hashAlgorithm,
            $this->hashEncoding,
            $this->hashCharset,
            $this->name,
            $this->password,
            $this->callback
        );

        $this->assertEquals($expectedPassword, $password);
    }

    /**
     * Test if createPasswordHash hashes SHA256 correctly with a salt
     *
     * @return void
     */
    public function testCreatePasswordHashedSha256WithSalt()
    {
        $this->hashAlgorithm = HashKeys::SHA256;
        $this->salt = '1234';
        $expectedPassword = hash(HashKeys::SHA256, $this->salt . $this->password);
        $password = Util::createPasswordHash(
            $this->hashAlgorithm,
            $this->hashEncoding,
            $this->hashCharset,
            $this->name,
            $this->password,
            $this->callback,
            $this->salt
        );

        $this->assertEquals($expectedPassword, $password->stringValue());
    }

    /**
     * Test if createPasswordHash hashes SHA512 correclty without a salt
     *
     * @return void
     */
    public function testCreatePasswordHashedSha512WithoutSalt()
    {
        $this->hashAlgorithm = HashKeys::SHA512;
        $expectedPassword = hash(HashKeys::SHA512, $this->password);
        $password = Util::createPasswordHash(
            $this->hashAlgorithm,
            $this->hashEncoding,
            $this->hashCharset,
            $this->name,
            $this->password,
            $this->callback
        );

        $this->assertEquals($expectedPassword, $password);
    }

    /**
     * Test if createPasswordHash hashes SHA512 correctly with a salt
     *
     * @return void
     */
    public function testCreatePasswordHashedSha512WithSalt()
    {
        $this->hashAlgorithm = HashKeys::SHA512;
        $this->salt = '1234';
        $expectedPassword = hash(HashKeys::SHA512, $this->salt . $this->password);
        $password = Util::createPasswordHash(
            $this->hashAlgorithm,
            $this->hashEncoding,
            $this->hashCharset,
            $this->name,
            $this->password,
            $this->callback,
            $this->salt
        );

        $this->assertEquals($expectedPassword, $password->stringValue());
    }
}
