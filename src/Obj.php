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

namespace NTLAB\Object;

use stdClass;

/**
 * A base class to represent PHP object.
 *
 * @author Toha <tohenk@yahoo.com>
 */
abstract class Obj
{
    public const EOL = "\n";
    public const SINGLE_QUOTE = '\'';
    public const DOUBLE_QUOTE = '"';
    public const WRAP_SKIP_FIRST = 1;
    public const WRAP_SKIP_LAST = 2;

    /**
     * @var mixed
     */
    protected $value = null;

    /**
     * @var array
     */
    protected $options = [
        'raw' => false,
        'inline' => false,
        'skip_null' => false,
        'skip_keys' => [],
        'stdclass_as_array' => false,
        'wrapper' => '%s',
        'level' => null,
        'indentation' => '    ',
    ];

    /**
     * Constructor.
     *
     * @param mixed $value
     * @param array $options
     */
    public function __construct($value = null, $options = [])
    {
        $this->value = $value;
        $this->setOptions((array) $options);
    }

    /**
     * Set options from array.
     *
     * @param array $options  The options array
     * @return \NTLAB\Object\Obj
     */
    public function setOptions($options)
    {
        foreach ($options as $key => $value) {
            $this->setOption($key, $value);
        }

        return $this;
    }

    /**
     * Get option value.
     *
     * @param string $key  Option name
     * @param mixed $default  Default value
     * @return mixed
     */
    public function getOption($key, $default = null)
    {
        return isset($this->options[$key]) ? $this->options[$key] : $default;
    }

    /**
     * Set option value.
     *
     * @param string $key  Option name
     * @param mixed  $value  Option value
     * @return \NTLAB\Object\Obj
     */
    public function setOption($key, $value)
    {
        $this->options[$key] = $value;

        return $this;
    }

    /**
     * Check if array keys is all numeric.
     *
     * @param array $array  The input array
     * @return bool
     */
    protected function isKeysNumeric($array)
    {
        $prev = null;
        foreach (array_keys($array) as $key) {
            if (!is_int($key)) {
                return false;
            }
            if (null !== $prev) {
                if ($key - $prev > 1) {
                    return false;
                }
            } elseif ($key > 0) {
                return false;
            }
            $prev = $key;
        }

        return true;
    }

    /**
     * Quote and escape string.
     *
     * @param string $str  String to quote
     * @param string $quote  Quote string
     * @return string
     */
    protected function quote($str, $quote = self::SINGLE_QUOTE)
    {
        switch ($quote) {
            case static::SINGLE_QUOTE:
                return $quote.str_replace($quote, '\\\'', $str).$quote;
            case static::DOUBLE_QUOTE:
                return $quote.str_replace($quote, '\\"', $str).$quote;
            default:
                return $quote.$str.$quote;
        }
    }

    /**
     * Get line padding.
     *
     * @param int $level Padding level to multiply with indentation
     * @return string
     */
    protected function getPadding($level)
    {
        if (null !== $level) {
            return str_repeat($this->getOption('indentation'), $level);
        }
    }

    /**
     * Wrap text.
     *
     * @param string $lines  The text
     * @param int $level  Indentation level
     * @param int $flags  Wrapping flags
     * @return string
     */
    protected function wrapLines($lines, $level = 0, $flags = self::WRAP_SKIP_FIRST | self::WRAP_SKIP_LAST)
    {
        if ($wrapper = $this->getOption('wrapper')) {
            $pad = $this->getPadding($level);
            $lines = explode(static::EOL, $lines);
            $i = 0;
            $n = count($lines);
            // is first line skipped?
            if (($flags & static::WRAP_SKIP_FIRST) === static::WRAP_SKIP_FIRST) {
                $i++;
            }
            // is last line skipped?
            if (($flags & static::WRAP_SKIP_LAST) === static::WRAP_SKIP_LAST) {
                $n--;
            }
            for (; $i < $n; $i++) {
                $line = $lines[$i];
                if ($level) {
                    if ('' !== $line) {
                        $line = sprintf($wrapper, $pad.$line);
                    }
                }
                if ($lines[$i] !== $line) {
                    $lines[$i] = $line;
                }
            }
            $lines = implode(static::EOL, $lines);
        }

        return $lines;
    }

    /**
     * Join lines.
     *
     * @param array $lines  Lines to join
     * @param string $delimiter  Lines delimiter
     * @param bool $inline  Is inlined join
     * @return string
     */
    protected function joinLines($lines, $delimiter = ',', $inline = null)
    {
        if (is_bool($delimiter)) {
            $inline = $delimiter;
            $delimiter = null;
        }
        $delimiter = null !== $delimiter ? $delimiter : ',';
        $inline = null !== $inline ? $inline : $this->getOption('inline');

        return implode($delimiter.($inline ? ' ' : static::EOL), $lines);
    }

    /**
     * Get value from callback
     *
     * @param string $callback  Callback name
     * @param mixed $value  The value
     * @return mixed
     */
    protected function callCallback($callback, $value)
    {
        if (is_callable($callable = $this->getOption($callback))) {
            return call_user_func_array($callable, [$value]);
        }
    }

    /**
     * Filter value for pre processing.
     * 
     * If a `pre.process` option is passed, then it will be called to
     * filter value before further processing.
     *
     * @param mixed $value  The value
     */
    protected function preProcess(&$value)
    {
        if (null !== ($v = $this->callCallback('pre.process', $value))) {
            $value = $v;
        }
        // convert stdClass to array
        if ($this->getOption('stdclass_as_array') && $value instanceof stdClass) {
            $value = (array) $value;
        }
    }

    /**
     * Filter value for post processing.
     * 
     * If a `post.process` option is passed, then it will be called to
     * filter value after further processing.
     *
     * @param mixed $value  The value
     */
    protected function postProcess(&$value)
    {
        if (null !== ($v = $this->callCallback('post.process', $value))) {
            $value = $v;
        }
    }

    /**
     * Get value using callback.
     * 
     * If a `callback` option is passed, then it will be called to
     * represents the value and will be returned as the representation.
     *
     * @param mixed $value  The value
     * @return string|null
     */
    protected function valueFromCallback($value)
    {
        if (null !== ($v = $this->callCallback('callback', $value))) {
            return $v;
        }
    }

    /**
     * Get value as default representation.
     * 
     * @param mixed $value  The value
     * @return string
     */
    protected function asDefault($value)
    {
        if (is_object($value) && method_exists($value, 'asString')) {
            $value = $value->asString($this);
        }

        return (string) $value;
    }

    /**
     * Decorate generated value.
     *
     * @param string $value  The generated value
     * @return string
     */
    protected function decorate($value)
    {
        return $value;
    }

    /**
     * Convert value as code equivalent.
     *
     * @param mixed $value  The value
     * @return string
     */
    protected function convert($value)
    {
        return $value;
    }

    public function __toString()
    {
        $value = $this->value;
        if (!$this->getOption('raw')) {
            $value = $this->decorate($this->convert($value));
            if (($lvl = $this->getOption('level')) && false !== strpos($value, static::EOL)) {
                $value = $this->wrapLines($value, $lvl, 0);
            }
        }

        return (string) $value;
    }

    /**
     * Create an object.
     *
     * @param mixed $value  The value
     * @return \NTLAB\Object\Obj
     */
    public static function create($value = null, $options = [])
    {
        return new static($value, $options);
    }

    /**
     * Create a raw object.
     *
     * @param mixed $value  The value
     * @return \NTLAB\Object\Obj
     */
    public static function raw($value = null)
    {
        return new static($value, ['raw' => true]);
    }

    /**
     * Create an inlined object.
     *
     * @param mixed $value  The value
     * @return \NTLAB\Object\Obj
     */
    public static function inline($value = null)
    {
        return new static($value, ['inline' => true]);
    }
}
