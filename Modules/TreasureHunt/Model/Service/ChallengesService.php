<?php declare(strict_types=1);

namespace Services;

class ChallengesService
{
    /** @var string */
    private $challengesDir;

    /** @var array */
    private $treasureHunt;

    public function __construct(string $challengesDir)
    {
        if ($challengesDir[-1] != DIRECTORY_SEPARATOR) {
            $challengesDir .= DIRECTORY_SEPARATOR;
        }
        if (!is_dir($challengesDir)) {
            throw new \InvalidArgumentException("Challenge directory '$challengesDir' not found");
        }

        $this->challengesDir = $challengesDir;
        $treasureHuntJson = file_get_contents("$challengesDir/treasure-hunt.json");
        $this->treasureHunt = json_decode($treasureHuntJson, true);

        dump($this);exit;
    }

    public function getChallenge(string $name): ?array
    {
        $filename = $this->challengesDir . "challenges/$name.json";
        if (!file_exists($filename)) {
            return null;
        }

        $challenge = json_decode(file_get_contents($filename), true);

    }
}
