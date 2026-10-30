<?php
namespace AIOWPS\Firewall;

/**
 * This class handles logging the firewall's audit log events
 */
class Audit_Events {

	/**
	 * Constructs our object
	 */
	public function __construct() {
		Event::capture("rule_triggered", function(string $event, Rule $rule) {
			$this->handle_rule_triggered($event, $rule);
		});
	}

	/**
	 * Log when a firewall rule has been triggered
	 *
	 * @param string $event
	 * @param Rule   $rule
	 * @return void
	 */
	private function handle_rule_triggered(string $event, Rule $rule) : void {
		global $aiowps_firewall_audit_log;

		$aiowps_firewall_audit_log->log($event, Audit_Log::LEVEL_WARNING, array(
			'event'       => 'triggered',
			'rule_name'   => $rule->name,
			'rule_family' => $rule->family
		));
	}
}
