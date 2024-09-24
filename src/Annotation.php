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
 * Represents PHP Object as annotation.
 *
 * @author Toha <tohenk@yahoo.com>
 */
class Annotation extends Obj
{
    protected function decorate($value)
    {
        return $this->getOption('annotation').$value;
    }

    /**
     * (non-PHPdoc)
     * @see \NTLAB\Object\Obj::convert()
     */
    protected function convert($value, $options = [])
    {
        $this->preProcess($value);

        if (null !== ($v = $this->valueFromCallback($value))) {
            return $v;
        }

        $topLevel = isset($options['top']) ? (bool) $options['top'] : true;
        $inlineList = isset($options['inline']) ? (bool) $options['inline'] : false;

        if ($value instanceof Annotation) {
            $value = (string) $value;
        } elseif (is_bool($value)) {
            $value = $value ? 'true' : 'false';
        } elseif (is_string($value)) {
            $value = $this->quote($value, static::DOUBLE_QUOTE);
        } elseif (null === $value && !$topLevel) {
            $value = 'NULL';
        } elseif (is_array($value)) {
            $tmp = [];
            $useKey = !$this->isKeysNumeric($value);
            $multiline = !$this->getOption('inline') && count($value) > 1;
            $eol = $multiline ? static::EOL : '';
            $skips = $this->getOption('skip_keys');
            foreach ($value as $k => $v) {
                // skip null value
                if (null === $v && $this->getOption('skip_null')) {
                    continue;
                }
                // check for skipped keys
                if (is_array($skips) && count($skips) && in_array($k, $skips)) {
                    continue;
                }
                $v = $this->convert($v, ['top' => false, 'inline' => true]);
                if (false === $topLevel) {
                    $k = sprintf('"%s"', $k);
                }

                $tmp[] = $useKey ? sprintf("%s%s%s", $k, ($inlineList ? ': ' : '='), ($v === null ? 'NULL' : $v)) : $v;
            }
            $value = $this->joinLines($tmp, !$multiline).$eol;
            if ($topLevel) {
                $value = sprintf('(%s)', $value);
            } else {
                $value = sprintf('{%s}', $value);
            }
            if ($multiline) {
                $value = $this->wrapLines($value, 1);
            }
        } else {
            $value = $this->asDefault($value);
        }
        $this->postProcess($value);

        return $value;
    }

    /**
     * Create an annotation object.
     *
     * @param string $annotation  Annotation identifier
     * @param mixed $value  The value
     * @return \NTLAB\Object\Annotation
     */
    public static function of($annotation, $value = null)
    {
        return new static($value, ['annotation' => $annotation]);
    }
}
