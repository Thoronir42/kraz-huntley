<?php declare(strict_types=1);

namespace CP\TreasureHunt;

use CP\TreasureHunt\Typeful\Types\ClueType;

class Navigation
{
    const ADVANCE_TYPE = 'advanceType';
    const TARGET = 'target';
    const ARGS = 'args';

    const ADVANCE_REDIRECT = 'redirect';
    const ADVANCE_FORWARD = 'forward';

    const TARGET_NOTEBOOK_PAGE = 'notebookPage';
    const TARGET_NARRATIVE = ClueType::NARRATIVE;
}
