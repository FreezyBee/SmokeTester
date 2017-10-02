<?php
declare(strict_types=1);

namespace FreezyBee\SmokeTester\Tests\AcmeApp;

use Nette\Application\BadRequestException;
use Nette\Application\UI\Presenter;
use Nette\Http\IResponse;

/**
 * @author Jakub Janata <jakubjanata@gmail.com>
 */
class DefaultPresenter extends Presenter
{
    public function actionDefault()
    {
        $this->sendJson(['status' => 'ok']);
    }

    public function actionRedirect()
    {
        $this->redirect('default');
    }

    public function actionError()
    {
        $this->getHttpResponse()->setCode(IResponse::S400_BAD_REQUEST);
        $this->sendJson(['status' => 'error']);
    }

    public function actionException()
    {
        throw new BadRequestException;
    }
}
