<?php
namespace AIOWPS\Firewall;

/**
 * Trait to set the header to redirect
 */
trait Action_Redirect_Trait {
	
	/**
	 * Redirect to the location.
	 *
	 * @var string
	 */
	public $location = '127.0.0.1';
	
	/**
	 * Redirect the rule condition is satisfied.
	 *
	 * @return void
	 */
	public function do_action() {
		header('Cache-Control: no-cache, no-store, must-revalidate, max-age=0');
		header('Pragma: no-cache');
		header('Expires: 0');
		header("Location: $this->location", true, 302);
	}
}
