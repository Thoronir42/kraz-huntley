<?php declare(strict_types=1);

namespace SeStep\NetteExecutives\Controls;

class ActionParamsControlFactory
{
    public function create(?string $type = null, ?string $caption = null)
    {
        // TODO: according to type parameter create specific control

        return new AssociativeArrayControl($caption);
    }
}
