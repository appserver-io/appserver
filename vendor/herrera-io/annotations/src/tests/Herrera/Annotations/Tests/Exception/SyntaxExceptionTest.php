<?php

namespace Herrera\Annotations\Tests;

use Doctrine\Common\Annotations\DocLexer;
use Herrera\Annotations\Exception\SyntaxException;
use Herrera\PHPUnit\TestCase;

class SyntaxExceptionTest extends TestCase
{
    public function testExpectedToken()
    {
        $exception = SyntaxException::expectedToken('test');

        $this->assertEquals(
            'Expected test, received end of string.',
            $exception->getMessage()
        );
    }

    public function testExpectedTokenWithToken()
    {
        $exception = SyntaxException::expectedToken(
            'test',
            array(
                'position' => 123,
                'value' => 'abc',
            )
        );

        $this->assertEquals(
            'Expected test, received \'abc\' at position 123.',
            $exception->getMessage()
        );
    }

    public function testExpectedTokenWithLookahead()
    {
        $lexer = new DocLexer();
        $lexer->lookahead = array(
            'position' => 123,
            'value' => 'abc',
        );

        $exception = SyntaxException::expectedToken('test', null, $lexer);

        $this->assertEquals(
            'Expected test, received \'abc\' at position 123.',
            $exception->getMessage()
        );
    }
}
