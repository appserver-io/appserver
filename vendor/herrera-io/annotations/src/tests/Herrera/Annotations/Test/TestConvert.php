<?php

namespace Herrera\Annotations\Test;

use Herrera\Annotations\Convert\AbstractConvert;
use Herrera\Annotations\Tokens;

/**
 * A simple test converter that increments a counter.
 *
 * @author Kevin Herrera <kevin@herrera.io>
 */
class TestConvert extends AbstractConvert
{
    /**
     * @override
     */
    public $tokens;

    /**
     * Set the counter to 100.
     */
    public function __construct()
    {
        $this->result = 100;
    }

    /**
     * @override
     */
    protected function handle()
    {
        $this->result++;
    }

    /**
     * @override
     */
    protected function reset(Tokens $tokens)
    {
        $this->result = 0;
        $this->tokens = $tokens;
    }
}
