<?php

/*
 * This is part of the webuni/commonmark-table-extension package.
 *
 * (c) Martin Hasoň <martin.hason@gmail.com>
 * (c) Webuni s.r.o. <info@webuni.cz>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Webuni\CommonMark\TableExtension;

use League\CommonMark\Extension\Extension;

class TableExtension extends Extension
{
    public function getBlockParsers()
    {
        return [
            new TableParser(),
        ];
    }

    public function getBlockRenderers()
    {
        return [
            __NAMESPACE__.'\\Table' => new TableRenderer(),
            __NAMESPACE__.'\\TableCaption' => new TableCaptionRenderer(),
            __NAMESPACE__.'\\TableRows' => new TableRowsRenderer(),
            __NAMESPACE__.'\\TableRow' => new TableRowRenderer(),
            __NAMESPACE__.'\\TableCell' => new TableCellRenderer(),
        ];
    }

    public function getName()
    {
        return 'table';
    }
}
