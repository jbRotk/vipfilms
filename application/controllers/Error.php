<?php

class ErrorController extends WebController
{
    public function errorAction(Exception $exception)
    {
        _DEBUG === true ? trace("{$exception->getCode()} => {$exception->getMessage()}", ERROR) :
            ($exception->getCode() === StatusSqlError ? trace("{$exception->getCode()} => {$exception->getMessage()}", ERROR) : null);
        $this->render_view();
    }
}