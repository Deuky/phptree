<?php

namespace PhpTree\Enum;

use PhpTree\Presenter\ConsolePresenter;
use PhpTree\Presenter\JsonPresenter;
use PhpTree\Presenter\MarkdownPresenter;

enum FormatTypeEnum: string
{
    case console  = ConsolePresenter::class;
    case json     = JsonPresenter::class;
    case markdown = MarkdownPresenter::class;
    // case sqlite = SqlitePresenter::class;  (phase 4)
    // case csv    = CsvPresenter::class;     (phase 5)
    // case html   = HtmlPresenter::class;    (phase 5)
}