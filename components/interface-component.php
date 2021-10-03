<?php
namespace components;

interface Component {

	/**
	 * Register handlers for hooks
	 */
	public function register() : void;

	/**
	 * Unregister all previously registered actions handlers
	 */
	public function unregister() : void;
}
