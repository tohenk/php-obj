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

/**
 * Represents PHP object as string.
 *
 * @author Toha <tohenk@yahoo.com>
 */
class PHP extends Obj
{
    /**
     * (non-PHPdoc)
     * @see \NTLAB\Object\Obj::convert()
     */
    protected function convert($value)
    {
        $this->preProcess($value);

        if (null !== ($v = $this->valueFromCallback($value))) {
            return $v;
        }

        if ($value instanceof PHP) {
            $value = (string) $value;
        } elseif (is_bool($value)) {
            $value = $value ? 'true' : 'false';
        } elseif (is_string($value)) {
            $value = $this->quote($value);
        } elseif (null === $value) {
            $value = 'null';
        } elseif (is_array($value)) {
            $tmp = [];
            $useKey = !$this->isKeysNumeric($value);
            $multiline = !$this->getOption('inline') && !$this->isKeysNumeric($value);
            $eol = $multiline ? static::EOL : '';
            $flags = $multiline ? static::JOIN_MULTILINE : static::JOIN_INLINE;
            if ($this->getOption('trailing_delimiter')) {
                $flags |= static::JOIN_LAST_DELIMITER;
            }
            foreach ($value as $k => $v) {
                // skip null value
                if (null === $v && $this->getOption('skip_null')) {
                    continue;
                }
                $v = $this->convert($v, 1);
                $tmp[] = $useKey ? sprintf('\'%s\' => %s', $k, $v) : $v;
            }
            $value = sprintf('[%s]', $eol.$this->joinLines($tmp, $flags).$eol);
            if ($multiline) {
                $value = $this->wrapLines($value, 1);
            }
        } else {
            $value = $this->asDefault($value);
        }
        $this->postProcess($value);

        return $value;
    }
}
