<?php

use Elgg\Exceptions\Http\EntityPermissionsException;

$container = elgg_get_page_owner_entity();
if (!$container->canWriteToContainer(0, 'object', 'blog')) {
	throw new EntityPermissionsException();
}

elgg_push_collection_breadcrumbs('object', 'blog', $container);

echo elgg_view_page(elgg_echo('add:object:blog'), [
	'content' => elgg_view_form('blog/save', [
		'sticky_enabled' => true,
	]),
	'filter_id' => 'blog/edit',
]);
