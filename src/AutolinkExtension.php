<?php

/*
 * This file is part of the league/commonmark-ext-autolink package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Ext\Autolink;

use League\CommonMark\Extension\Extension;

final class AutolinkExtension extends Extension
{
    public function getDocumentProcessors()
    {
        return [
            new EmailAutolinkProcessor(),
            new UrlAutolinkProcessor(),
        ];
    }
}
