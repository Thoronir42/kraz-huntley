<?php declare(strict_types=1);

namespace CP\TreasureHunt\Executives\Actions;


use CP\TreasureHunt\Model\Service\NotebookService;
use PHPUnit\Framework\TestCase;

class BanAnswerSubmissionTest extends TestCase
{

    /**
     * @param string $duration
     * @param int $expectedErrorCount
     *
     * @dataProvider dataForTestValidateDateParams
     */
    public function testValidateDuration(string $duration, $expectedErrorCount = 0)
    {
        /** @var NotebookService $notebookService */
        $notebookService = $this->createMock(NotebookService::class);
        $action = new BanAnswerSubmission($notebookService);

        $errors = $action->validateParams([
            'duration' => $duration,
        ]);

        self::assertCount($expectedErrorCount, $errors);

    }

    public function dataForTestValidateDateParams()
    {
        return [
            ['+15mins'],
            ['+15minutes'],
            ['+1day'],
            ['+1days'],
            ['+1min20sec'],
            ['not-a-time-entry', 2]
        ];
    }
}
