<?php

namespace Icybee\Modules\Seo;

use ICanBoogie\Module\Descriptor;

return [

	Descriptor::CATEGORY => 'features',
	Descriptor::DESCRIPTION => "Provides SEO to your website.",
	Descriptor::NS => __NAMESPACE__,
	Descriptor::PERMISSION => false,
	Descriptor::REQUIRES => [ 'pages' ],
	Descriptor::TITLE => 'SEO'

];
