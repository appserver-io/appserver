<?php

namespace Herrera\Annotations\Tests;

use Herrera\Annotations\Exception\Exception;
use Herrera\PHPUnit\TestCase;

class ExceptionTest extends TestCase
{
    public function testCreate()
    {
        $this->assertEquals(
            'This is the message.',
            Exception::create('This is %s message.', 'the')->getMessage()
        );
    }

    public function testLastError()
    {
        @$wat;

        $this->assertEquals(
            'Undefined variable: wat',
            Exception::lastError()->getMessage()
        );
    }
}
