<?php
// Copyright 2011 Toby Zerner, Simon Zerner
// This file is part of esoTalk. Please see the included license file for usage information.

if (!defined("IN_ESOTALK")) exit;

/**
 * The user controller handles session/user-altering actions such as logging in and out, signing up, and
 * resetting a password.
 *
 * @package esoTalk
 */
class ETUserController extends ETController {


/**
 * A message to display on the login form.
 * This is useful to set in the ETController::render404 method where we create a user controller
 * in order to display a login form without redirecting.
 * @var string
 */
public $loginMessage;


/**
 * There's no index method for this controller, so redirect back to the index.
 *
 * @return void
 */
public function action_index()
{
	$this->redirect(URL(""));
}


/**
 * Show the login sheet and handle input from the login form.
 *
 * @return void
 */
public function action_login()
{
	// If we're already logged in, redirect to the forum index.
	if (ET::$session->user) $this->redirect(URL(""));

	redirect(\OC::$server->getURLGenerator()->linkToRoute('core.login.showLoginForm',
		array('redirect_url' => R("return"))));
}


/**
 * Log the user out and redirect.
 *
 * @return void
 */
public function action_logout()
{
	\OC::$server->getUserSession()->logout();
}


}
