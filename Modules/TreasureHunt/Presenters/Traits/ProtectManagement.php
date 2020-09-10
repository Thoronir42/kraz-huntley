<?php declare(strict_types=1);

namespace CP\TreasureHunt\Presenters\Traits;

use App\Security\UserManager;

trait ProtectManagement
{
    public function checkRequirements($element): void
    {
        if (!$this->user->isInRole(UserManager::ROLE_POWER_USER)) {
            $this->flashMessage('Nedovolený přístup');
            $this->redirect('TreasureHunt:sign');
        }
    }
}
