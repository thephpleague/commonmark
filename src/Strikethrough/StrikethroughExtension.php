<?php
namespace CommonMarkExt\Strikethrough;

use League\CommonMark\Extension\Extension;
use League\CommonMark\Inline\Parser\InlineParserInterface;
use League\CommonMark\Inline\Processor\InlineProcessorInterface;
use League\CommonMark\Inline\Renderer\InlineRendererInterface;

class StrikethroughExtension extends Extension
{
    public function getName(){ return 'Strikethrough'; }
    public function getInlineParsers(){ return [ new StrikethroughParser() ]; }
    public function getInlineProcessors(){ return []; }
    public function getInlineRenderers(){ return [ Strikethrough::class => new StrikethroughRenderer() ]; }
}

