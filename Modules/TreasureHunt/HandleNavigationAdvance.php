<?php declare(strict_types=1);

namespace CP\TreasureHunt;

use Nette\NotImplementedException;

trait HandleNavigationAdvance
{
    private function checkAdvance(\SeStep\Executives\Execution\ExecutionResult $result)
    {
        if (!$result->isOk()) {
            return;
        }

        $data = $result->getData();
        if (!array_key_exists(Navigation::ADVANCE_TYPE, $data)) {
            return;
        }

        [$target, $args] = $this->prepareAdvanceRequestSpecification($data);

        if ($data[Navigation::ADVANCE_TYPE] === Navigation::ADVANCE_FORWARD) {
            $this->forward($target, $args);
        } else {
            $this->redirect($target, $args);
        }
    }

    private function prepareAdvanceRequestSpecification(array &$data): array
    {
        $navArgs = $data[Navigation::ARGS];
        switch ($data[Navigation::TARGET]) {
            case Navigation::TARGET_NOTEBOOK_PAGE:
                return [':TreasureHunt:Notebook:page', ['page' => $navArgs['pageNumber']]];
//            case Navigation::TARGET_NARRATIVE: TODO: implement properly
//                return [':TreasureHunt:Clue:narrative', ['narrative' => $navArgs['clueId']]];

            default:
                throw new NotImplementedException("Invalid target '{$data[Navigation::TARGET]}'");
        }
    }
}
