<?php
// Copyright 2011 Toby Zerner, Simon Zerner
// This file is part of esoTalk. Please see the included license file for usage information.

if (!defined("IN_ESOTALK")) exit;

/**
 * The Session model represents the current session and the current user. It provides functions for manipluating
 * and managing the session and user, such as storing data, logging in and out, and validating tokens.
 *
 * @package esoTalk
 */
class ETSession extends ETModel {


/**
 * An array of the current user's details, or null if they're not logged in.
 * @var array
 */
public $user;


/**
 * The current user's member ID, or null if they're not logged in.
 * @var int
 */
public $userId;


/**
 * The current valid token.
 * @var string
 */
public $token;


/**
 * The IP address of the current user.
 * @var string
 */
public $ip;


/**
 * Class constructor: starts the session and initializes class properties (ip, token, user, etc.)
 *
 * @return void
 */
public function __construct()
{
	$sql = ET::SQL()
		->select("m.memberId")
		->where("m.ncUid = :uid")
		->bind(":uid", \OC::$server->getUserSession()->getUser()->getUID())
		->from("member m");
	$user = $sql->exec()->firstRow();

	// Set the class properties to reference session variables.
	$this->token = \OC::$server->getSession()->getId();
	$this->ip = \OC::$server->getRequest()->getRemoteAddress();
	$this->userId = $user ? $user["memberId"] : null;

	// If there's a user logged in, get their user data.
	if ($this->userId and C("esoTalk.installed")) $this->refreshUserData();
}


/**
 * Pulls fresh user data from the database into the $user property.
 *
 * @return void
 */
public function refreshUserData()
{
	if (!$this->userId) return;
	$this->user = ET::memberModel()->getById($this->userId);
}


/**
 * Get the value of a specific preference for the currently logged in user.
 *
 * @return mixed
 */
public function preference($key, $default = false)
{
	return isset($this->user["preferences"][$key]) ? $this->user["preferences"][$key] : $default;
}


/**
 * Set preferences for the current user.
 *
 * @param array $values An array of preferences to set.
 * @return void
 */
public function setPreferences($values)
{
	if (!$this->userId) return;
	$this->user["preferences"] = ET::memberModel()->setPreferences($this->user, $values);
}


/**
 * Update the current session's local user data.
 *
 * @param string $key The key to set.
 * @param mixed $value The value to set.
 * @return void
 */
public function updateUser($key, $value)
{
	$this->user[$key] = $value;
}


/**
 * Check a token against the current valid token.
 *
 * @param string $token The token to check.
 * @return bool Whether or not the token is valid.
 */
public function validateToken($token)
{
	return $token == $this->token;
}


/**
 * Push an item onto the top of the navigation breadcrumb stack.
 *
 * When adding an item to the navigation breadcrumb stack, we first go through all the items in the stack and
 * check if there's an item with the same ID. If it is found, we go back to that point in the breadcrumb,
 * discarding everything afterwards.
 *
 * @param string $id The navigation ID (a unique ID for this item in the breadcrumb.)
 * @param string $type The type of page this is (search/conversation/etc - will be used in the "back to [type]" text.)
 * @param string $url The URL to this page.
 * @return void
 */
public function pushNavigation($id, $type, $url)
{
	$navigation = $this->get("navigation");
	if (!is_array($navigation)) $navigation = array();

	// Look for an item with this $id that might already by in the navigation. If found, delete everything after it.
	foreach ($navigation as $k => $item) {
		if ($item["id"] == $id) {
			array_splice($navigation, $k);
			break;
		}
	}
	$navigation[] = array("id" => $id, "type" => $type, "url" => $url);

	$this->store("navigation", $navigation);
}


/**
 * Get the item that is on top of the navigation stack. The navigation ID of the current page will be used to
 * make sure the item returned isn't the item for the current page.
 *
 * @param string $currentId The unqiue navigation ID of the current page.
 * @return bool|array The navigation item, or false if there is none (if the current page is the top.)
 */
public function getNavigation($currentId)
{
	$navigation = $this->get("navigation");
	if (!empty($navigation)) {
		$return = end($navigation);
		if ($return["id"] == $currentId) $return = prev($navigation);
		return $return;
	}
	else return false;
}


/**
 * Return whether or not the current user is an administrator.
 *
 * @return bool
 */
public function isAdmin()
{
	return $this->user["account"] == ACCOUNT_ADMINISTRATOR or $this->userId == C("esoTalk.rootAdmin");
}


/**
 * Return whether or not the current user is suspended.
 *
 * @return bool
 */
public function isSuspended()
{
	return $this->user["account"] == ACCOUNT_SUSPENDED;
}


/**
 * Return whether or not the current user is flooding.
 *
 * @return bool
 */
public function isFlooding()
{
	// If there's no wait time between posting configured, they're not flooding.
	if (C("esoTalk.conversation.timeBetweenPosts") <= 0) return false;

	// Otherwise, make sure the time of their most recent conversation/post is more than the time limit ago.
	$time = time() - C("esoTalk.conversation.timeBetweenPosts");
	$recentConversation = (bool)ET::SQL()
		->select("MAX(startTime)>$time")
		->from("conversation")
		->where("startMemberId", $this->userId)
		->exec()
		->result();
	$recentPost = (bool)ET::SQL()
		->select("MAX(time)>$time")
		->from("post p")
		->where("memberId", $this->userId)
		->exec()
		->result();

	return $recentConversation or $recentPost;
}


/**
 * Get a list of group IDs which the current user is in.
 *
 * @return array
 */
public function getGroupIds()
{
	if ($this->user) return ET::groupModel()->getGroupIds($this->user["account"], array_keys($this->user["groups"]));
	else return ET::groupModel()->getGroupIds(false, false);
}


/**
 * Store a value in the session data store.
 *
 * @return void
 */
public function store($key, $value)
{
	$_SESSION[$key] = $value;
}


/**
 * Retrieve a value from the session data store.
 *
 * @return mixed
 */
public function get($key = null, $default = null)
{
	return isset($_SESSION[$key]) ? $_SESSION[$key] : $default;
}


/**
 * Remove a value from the session data store.
 *
 * @return void
 */
public function remove($key)
{
	unset($_SESSION[$key]);
}

}
