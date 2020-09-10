<?php declare(strict_types=1);

namespace CP\TreasureHunt\Presenters;

use Nette\Application\UI\Presenter;

class ManagementPresenter extends Presenter
{
    use Traits\ProtectManagement;

    public function actionDashboard()
    {

    }

    protected function beforeRender()
    {
        $this->layout = 'meta';
    }
}
