<?php

namespace PhpTree\Enum;

use PhpTree\Presenter\ConsolePresenter;
use PhpTree\Presenter\JsonPresenter;

enum FormatTypeEnum: string
{
    case console = ConsolePresenter::class;
    case json    = JsonPresenter::class;
}