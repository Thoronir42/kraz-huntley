<?php declare(strict_types=1);

namespace SeStep\Executives;

class ExecutivesLocalization
{
    public function getActionPlaceholder(string $name)
    {
        return $this->getPlaceholder($name, 'action');
    }

    public function getConditionPlaceholder(string $name)
    {
        return $this->getPlaceholder($name, 'condition');
    }

    public function getPlaceholder(string $name, string $type)
    {
        $pos = mb_strrpos($name, '.');
        return mb_substr($name, 0, $pos)
            . ".executives.$type"
            . mb_substr($name, $pos);
    }
}
