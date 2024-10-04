<?php

namespace Elgg\Views;

use Elgg\Exceptions\HttpException;
use Elgg\Plugins\IntegrationTestCase;

/**
 * Abstract class for testing view output
 */
abstract class ViewRenderingIntegrationTestCase extends IntegrationTestCase {

	public function up() {
		_elgg_services()->logger->disable();
	}

	/**
	 * Returns an array of view names to test with given default view vars
	 *
	 * @return array
	 */
	abstract public static function getViewNames();

	/**
	 * Returns default view vars to testing rendering
	 * @return array
	 */
	abstract public function getDefaultViewVars();

	/**
	 * An array of views to test
	 *
	 * @return array
	 */
	public static function viewListProvider() {
		self::createApplication();

		$provides = [];

		$data = _elgg_services()->views->getInspectorData();

		foreach ($data['locations'] as $viewtype => $views) {
			foreach ($views as $view => $path) {
				if (in_array($view, static::getViewNames())) {
					$provides[] = [$view, $viewtype];
				}
			}
		}

		return $provides;
	}

	/**
	 * Assert expected view output
	 *
	 * @param string $expected  Expected string
	 * @param string $view      View to test
	 * @param array  $view_vars View vars
	 * @param string $message   Error message
	 *
	 * @return void
	 */
	public function assertViewOutput($expected, $view, $view_vars = [], $viewtype = 'default', $message = '') {
		$actual = $this->view($view, $view_vars, $viewtype);
		$this->assertXmlStringEqualsXmlString($expected, $actual, $message);
	}

	/**
	 * @dataProvider viewListProvider
	 */
	public function testCanRenderViewWithEmptyVars($view, $viewtype) {
		try {
			$output = $this->view($view, [], $viewtype);
			$this->assertIsString($output);
		} catch (HttpException $e) {

		}
	}

	/**
	 * @dataProvider viewListProvider
	 */
	public function testCanRenderViewWithVars($view, $viewtype) {
		try {
			$output = $this->view($view, $this->getDefaultViewVars(), $viewtype);
			$this->assertIsString($output);
		} catch (HttpException $e) {

		}
	}

	/**
	 * Render a view using a correct elgg_view_* function
	 */
	public function view($view, array $vars = [], $viewtype = 'default', array $component_vars = []) {
		list($component, $subview) = explode('/', $view, 2);

		switch ($component) {
			case 'forms' :
				$prev_viewtype = elgg_get_viewtype();
				elgg_set_viewtype($viewtype);
				$output = elgg_view_form($subview, $component_vars, $vars);
				elgg_set_viewtype($prev_viewtype);
				return $output;

			case 'resources' :
				$prev_viewtype = elgg_get_viewtype();
				elgg_set_viewtype($viewtype);
				$output = elgg_view_resource($subview, $vars);
				elgg_set_viewtype($prev_viewtype);
				return $output;

			default:
				return elgg_view($view, $vars, $viewtype);
		}
	}
}
