<?php

namespace Icybee\Modules\Seo;

use Brickrouge;
use Icybee;

$hooks = Hooks::class . '::';

return [

	Icybee\Modules\Pages\Block\EditBlock::class . '::alter_children' => $hooks . 'on_page_editblock_alter_children',
	Icybee\Modules\Pages\Operation\ExportOperation::class . '::process' => $hooks . 'on_operation_export',
	Icybee\Modules\Pages\PageRenderer::class . '::render' => $hooks . 'on_page_renderer_render',
	Icybee\Modules\Sites\Block\EditBlock::class . '::alter_children' => $hooks . 'on_site_editblock_alter_children',
	Brickrouge\Document::class . '::render_title:before' => $hooks . 'before_document_render_title',
	Brickrouge\Document::class . '::render_meta:before' => $hooks . 'before_document_render_meta',
	Brickrouge\Document::class . '::render_meta' => $hooks . 'on_document_render_meta'

];
