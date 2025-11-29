<?php
/**
 * Must-Use Plugin: Enable Links Manager
 *
 * This mu-plugin enables the Links Manager which is hidden by default
 * in modern WordPress versions.
 *
 * @package Blogroll_Links
 */

// Enable the Links Manager.
add_filter( 'pre_option_link_manager_enabled', '__return_true' );
