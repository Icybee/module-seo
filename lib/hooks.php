<?php

/*
 * This file is part of the Icybee package.
 *
 * (c) Olivier Laviale <olivier.laviale@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Icybee\Modules\Seo;

use ICanBoogie\Event;
use ICanBoogie\Operation;

use Brickrouge\Element;
use Brickrouge\Group;
use Brickrouge\Text;

use Icybee\Modules\Nodes\Node;
use Patron\Engine as Patron;

use Icybee\Block\EditBlock\AlterChildrenEvent;
use Icybee\Modules\Contents\Content;
use Icybee\Modules\Pages\PageRenderer;

// http://www.google.com/webmasters/docs/search-engine-optimization-starter-guide.pdf

class Hooks
{
	/**
	 * Adds the Google Analytics script at the end of the body, unless one of the following
	 * conditions is met:
	 *
	 * - "localhost" is in the server name.
	 * - The user is the admin.
	 * - The page or the displayed record is offline.
	 *
	 * @param PageRenderer\RenderEvent $event
	 * @param PageRenderer $target
	 */
	static public function on_page_renderer_render(PageRenderer\RenderEvent $event, PageRenderer $target)
	{
		$page = $event->page;

		if (strpos($_SERVER['SERVER_NAME'], 'localhost') !== false || \ICanBoogie\app()->user_id == 1 || !$page->is_online || ($page->node && !$page->node->is_online))
		{
			return;
		}

		$ua = $page->site->metas['google_analytics_ua'];

		if (!$ua)
		{
			return;
		}

		// http://googlecode.blogspot.com/2009/12/google-analytics-launches-asynchronous.html
		// http://code.google.com/intl/fr/apis/analytics/docs/tracking/asyncUsageGuide.html
		// http://www.google.com/support/googleanalytics/bin/answer.py?answer=174090&cbid=-yb2wwt7lxo0o&src=cb&lev=%20index
		// http://developer.yahoo.com/blogs/ydn/posts/2007/07/high_performanc_5/

		$insert = <<<EOT


<script type="text/javascript">

	var _gaq = _gaq || [];
	_gaq.push(['_setAccount', '$ua']);
	_gaq.push(['_trackPageview']);

	(function() {
		var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
		ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
		var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
	})();

</script>


EOT;

		$event->html = str_replace('</body>', $insert . '</body>', $event->html);
	}

	/**
	 * Replaces the title of the document with the SEO title before the title is rendered.
	 *
	 * @param \Icybee\Document\BeforeRenderTitleEvent $event
	 */
	static public function before_document_render_title(\Icybee\Document\BeforeRenderTitleEvent $event)
	{
		$page = \ICanBoogie\app()->request->context->page;
		$title = $page->document_title;
		$site_title = $page->site->title;

		$event->title = $title . $event->separator . $site_title;
	}

	/**
	 * Adds the `Description` and `google-site-verification` metas.
	 *
	 * @param \Icybee\Document\BeforeRenderMetasEvent $event
	 */
	static public function before_document_render_metas(\Icybee\Document\BeforeRenderMetasEvent $event)
	{
		$page = \ICanBoogie\app()->request->context->page;
		$node = isset($page->node) ? $page->node : null;
		$description = $page->description;

		if ($node instanceof Content)
		{
			$description = $page->node->excerpt;
		}

		if ($description)
		{
			$description = html_entity_decode($description, ENT_QUOTES, \ICanBoogie\CHARSET);
			$description = trim(strip_tags($description));
			$description = preg_replace('#\s+#', ' ', $description);

			$event->metas['Description'] = $description;
		}

		if ($page->is_home)
		{
			$value = $page->site->metas['google_site_verification'];

			if ($value)
			{
				$event->metas['google-site-verification'] = $value;
			}
		}
	}

	/**
	 * Adds the canonical address of the document.
	 *
	 * @param \Icybee\Document\RenderMetasEvent $event
	 */
	static public function on_document_render_metas(\Icybee\Document\RenderMetasEvent $event)
	{
		/* @var $node Node */

		$page = \ICanBoogie\app()->request->context->page;
		$node = isset($page->node) ? $page->node : null;

		#
		# canonical
		#

		// http://yoast.com/articles/duplicate-content/

		if ($node && $node->has_property('absolute_url'))
		{
			$event->html .= '<link rel="canonical" href="' . $node->absolute_url . '" />' . PHP_EOL;
		}
	}

	/**
	 * Extends the site edit block with a `SEO` group and controls for the Google Analytics UA
	 * and the Google Site Verification key.
	 *
	 * @param AlterChildrenEvent $event
	 * @param \Icybee\Modules\Sites\Block\EditBlock $block
	 */
	static public function on_site_editblock_alter_children(AlterChildrenEvent $event, \Icybee\Modules\Sites\Block\EditBlock $block)
	{
		$event->attributes[Element::GROUPS]['seo'] = [

			'title' => 'SEO',
			'weight' => 40

		];

		$event->children = array_merge($event->children, [

			'metas[google_analytics_ua]' => new Text([

					Group::LABEL => 'Google Analytics UA',
					Element::GROUP => 'seo'

			]),

			'metas[google_site_verification]' => new Text([

				Group::LABEL => 'Google Site Verification',
				Element::GROUP => 'seo'

			])
		]);
	}

	/**
	 * Adds controls to edit the SEO title and description of the page.
	 *
	 * @param AlterChildrenEvent $event
	 * @param \Icybee\Modules\Pages\Block\EditBlock $block
	 */
	static public function on_page_editblock_alter_children(AlterChildrenEvent $event, \Icybee\Modules\Pages\Block\EditBlock $block)
	{
		$event->attributes[Element::GROUPS]['seo'] = [

			'title' => 'SEO',
			'weight' => 40

		];

		#
		# http://www.google.com/support/webmasters/bin/answer.py?answer=35264&hl=fr
		# http://googlewebmastercentral.blogspot.com/2009/09/google-does-not-use-keywords-meta-tag.html
		# http://www.google.com/support/webmasters/bin/answer.py?answer=79812
		#

		$event->children['metas[document_title]'] = new Text([

			Group::LABEL => 'document_title',
			Element::GROUP => 'seo',
			Element::DESCRIPTION => 'document_title'

		]);

		$event->children['metas[description]'] = new Element('textarea', [

			Group::LABEL => 'description',
			Element::GROUP => 'seo',
			Element::DESCRIPTION => 'description',

			'rows' => 3

		]);
	}

	/**
	 * Adds SEO properties to exported pages.
	 *
	 * @param Operation\ProcessEvent $event
	 * @param Operation $operation
	 */
	static public function on_operation_export(Operation\ProcessEvent $event, Operation $operation)
	{
		$records = &$event->rc;
		$keys = array_keys($records);

		$metas = self::app()
			->models['registry/node']
			->where([ 'targetid' => $keys, 'name' => [ 'document_title', 'description' ] ])
			->all(\PDO::FETCH_NUM);

		foreach ($metas as $meta)
		{
			list($page_id, $property, $value) = $meta;

			$records[$page_id]->seo[$property] = $value;
		}
	}

	/*
	 * Support
	 */

	/**
	 * @return \ICanBoogie\Core|\Icybee\Binding\CoreBindings
	 */
	static private function app()
	{
		return \ICanBoogie\app();
	}
}
