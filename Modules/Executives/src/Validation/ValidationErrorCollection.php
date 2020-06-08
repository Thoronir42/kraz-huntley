<?php declare(strict_types=1);

namespace SeStep\Executives\Validation;

class ValidationErrorCollection implements \Iterator, \Countable
{
    private $errors;

    /**
     * ValidationErrorCollection constructor.
     * @param ParamValidationError[]|ParamValidationError[][] $errors
     */
    public function __construct(array $errors)
    {
        $this->errors = $errors;
    }

    /**
     * @return ParamValidationError|void
     */
    public function current()
    {
        $current = current($this->errors);

        if (is_array($current)) {
            return current($current);
        }

        return $current;
    }

    public function next()
    {
        $i = key($this->errors);

        if (is_array($this->errors[$i])) {
            next($this->errors[$i]);
            if (!current($this->errors[$i])) {
                next($this->errors);
            }
        } else {
            next($this->errors);
        }
    }

    public function key()
    {
        return key($this->errors);
    }

    public function valid()
    {
        return $this->current();
    }

    public function rewind()
    {
        reset($this->errors);
    }

    public function count()
    {
        return count($this->errors);
    }
}
