<?php

/**
 * AppserverIo\Appserver\Core\Commands\Helper\UnclosedQuotesException
 *
 * The MIT License (MIT)
 *
 * Copyright (c) 2016 Christian Lück
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is furnished
 * to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace AppserverIo\Appserver\Core\Commands\Helper;

/**
 * Exception that is thrown, if command line arguments has not been quoted as expected.
 */
class UnclosedQuotesException extends \InvalidArgumentException
{

    /**
     * The quotes this argument started with.
     *
     * @var string
     */
    private $quotes;

    /**
     * The character position of the quotes within the input.
     *
     * @var integer
     */
    private $position;

    /**
     * Initialize the exception with the passed values.
     *
     * @param string          $quotes   The quotes
     * @param integer         $position The position
     * @param string|null     $message  The message
     * @param integer         $code     The exception code
     * @param \Exception|null $previous The previous exception
     */
    public function __construct($quotes, $position, $message = null, $code = 0, $previous = null)
    {

        // prepare a message, if not passed
        if ($message === null) {
            $message = 'Still in quotes (' . $quotes  . ') from position ' . $position;
        }

        // invoke the parent constructor
        parent::__construct($message, $code, $previous);

        // set quotes and position
        $this->quotes = $quotes;
        $this->position = $position;
    }

    /**
     * Returns the quotes this argument started with.
     *
     * @return string The quotes
     */
    public function getQuotes()
    {
        return $this->quotes;
    }

    /**
     * Returns the character position of the quotes within the input.
     *
     * @return integer The position
     */
    public function getPosition()
    {
        return $this->position;
    }
}
