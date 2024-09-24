<?php

/*
 * The MIT License
 *
 * Copyright (c) 2024 Toha <tohenk@yahoo.com>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace NTLAB\Object\Test;

use PHPUnit\Framework\TestCase;
use NTLAB\Object\PHP;

class CallbackTest extends TestCase
{
    public function testCallback()
    {
        $arr = PHP::create(['var1' => new PHPVar('myvar'), 'var2' => new MyClass('test')], ['callback' => function($value) {
            if ($value instanceof PHPVar) {
                return sprintf('$%s', $value->var);
            }
        }]);
        $this->assertEquals("[\n    'var1' => \$myvar,\n    'var2' => new MyClass('test')\n]", (string) $arr, 'A callback properly convert object value');
    }
}

class PHPVar
{
    public $var = null;

    public function __construct($var)
    {
        $this->var = $var;
    }
}

class MyClass
{
    public $value = null;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function asString()
    {
        return sprintf("new MyClass('%s')", $this->value);
    }
}

