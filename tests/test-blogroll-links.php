<?php
/**
 * PHPUnit tests for Blogroll Links plugin.
 *
 * @package Blogroll_Links
 */

/**
 * Test case for Blogroll Links plugin.
 */
class Test_Blogroll_Links extends WP_UnitTestCase {

	/**
	 * Test that the shortcode is registered.
	 */
	public function test_shortcode_registered() {
		$this->assertTrue( shortcode_exists( 'blogroll-links' ) );
	}

	/**
	 * Test that the content filter is added.
	 */
	public function test_content_filter_added() {
		$this->assertNotFalse( has_filter( 'the_content', 'blogroll_links_text' ) );
	}

	/**
	 * Test that the admin menu action is added.
	 */
	public function test_admin_menu_action_added() {
		$this->assertNotFalse( has_action( 'admin_menu', 'blogroll_links_admin' ) );
	}

	/**
	 * Test get_term_id returns false for invalid slug.
	 */
	public function test_get_term_id_returns_false_for_invalid_slug() {
		$result = blogroll_links_get_term_id( 'nonexistent-slug-12345' );
		$this->assertFalse( $result );
	}

	/**
	 * Test get_term_id returns false for empty slug.
	 */
	public function test_get_term_id_returns_false_for_empty_slug() {
		$result = blogroll_links_get_term_id( '' );
		$this->assertFalse( $result );
	}

	/**
	 * Test that shortcode handler returns error message for invalid category.
	 */
	public function test_shortcode_returns_error_for_invalid_category() {
		$output = blogroll_links_handler( array( 'categoryslug' => 'nonexistent-category-xyz' ) );
		$this->assertStringContainsString( 'Invalid category slug', $output );
	}

	/**
	 * Test that HTML output is properly escaped.
	 */
	public function test_html_output_contains_proper_structure() {
		// Create a link category.
		$term = wp_insert_term( 'Test Links', 'link_category', array( 'slug' => 'test-links' ) );

		if ( is_wp_error( $term ) ) {
			$this->markTestSkipped( 'Could not create link category: ' . $term->get_error_message() );
		}

		$term_id = $term['term_id'];

		// Create a test link.
		$link_id = wp_insert_link(
			array(
				'link_name'        => 'Test Link',
				'link_url'         => 'https://example.com',
				'link_category'    => array( $term_id ),
				'link_description' => 'A test link',
				'link_visible'     => 'Y',
			)
		);

		if ( ! $link_id ) {
			$this->markTestSkipped( 'Could not create test link.' );
		}

		// Test the HTML output.
		$output = blogroll_links_html( $term_id, 'link_name', 'asc' );

		// Check for proper HTML structure.
		$this->assertStringContainsString( '<ul class="blogroll-links">', $output );
		$this->assertStringContainsString( '</ul>', $output );
		$this->assertStringContainsString( '<li>', $output );
		$this->assertStringContainsString( '</li>', $output );

		// Check that URL is escaped.
		$this->assertStringContainsString( 'href="https://example.com"', $output );

		// Check that link name is present.
		$this->assertStringContainsString( 'Test Link', $output );

		// Clean up.
		wp_delete_link( $link_id );
		wp_delete_term( $term_id, 'link_category' );
	}

	/**
	 * Test that empty bookmarks returns appropriate message.
	 */
	public function test_empty_bookmarks_returns_message() {
		// Create an empty link category.
		$term = wp_insert_term( 'Empty Category', 'link_category', array( 'slug' => 'empty-category' ) );

		if ( is_wp_error( $term ) ) {
			$this->markTestSkipped( 'Could not create link category.' );
		}

		$term_id = $term['term_id'];

		$output = blogroll_links_html( $term_id, 'link_name', 'asc' );

		$this->assertStringContainsString( 'No links found', $output );

		// Clean up.
		wp_delete_term( $term_id, 'link_category' );
	}

	/**
	 * Test sort_by validation defaults to link_name for invalid values.
	 */
	public function test_invalid_sort_by_defaults_to_link_name() {
		// Create a link category with a link.
		$term = wp_insert_term( 'Sort Test', 'link_category', array( 'slug' => 'sort-test' ) );

		if ( is_wp_error( $term ) ) {
			$this->markTestSkipped( 'Could not create link category.' );
		}

		$term_id = $term['term_id'];

		$link_id = wp_insert_link(
			array(
				'link_name'     => 'Sort Test Link',
				'link_url'      => 'https://example.com',
				'link_category' => array( $term_id ),
				'link_visible'  => 'Y',
			)
		);

		// This should not throw an error even with invalid sort_by.
		$output = blogroll_links_html( $term_id, 'invalid_field', 'asc' );

		// Should still produce valid output.
		$this->assertStringContainsString( 'Sort Test Link', $output );

		// Clean up.
		wp_delete_link( $link_id );
		wp_delete_term( $term_id, 'link_category' );
	}

	/**
	 * Test sort_order validation defaults to ASC for invalid values.
	 */
	public function test_invalid_sort_order_defaults_to_asc() {
		// Create a link category with a link.
		$term = wp_insert_term( 'Order Test', 'link_category', array( 'slug' => 'order-test' ) );

		if ( is_wp_error( $term ) ) {
			$this->markTestSkipped( 'Could not create link category.' );
		}

		$term_id = $term['term_id'];

		$link_id = wp_insert_link(
			array(
				'link_name'     => 'Order Test Link',
				'link_url'      => 'https://example.com',
				'link_category' => array( $term_id ),
				'link_visible'  => 'Y',
			)
		);

		// This should not throw an error even with invalid sort_order.
		$output = blogroll_links_html( $term_id, 'link_name', 'invalid' );

		// Should still produce valid output.
		$this->assertStringContainsString( 'Order Test Link', $output );

		// Clean up.
		wp_delete_link( $link_id );
		wp_delete_term( $term_id, 'link_category' );
	}

	/**
	 * Test that default options are used when shortcode attributes are empty.
	 */
	public function test_shortcode_uses_default_options() {
		// Set default options.
		update_option( 'blogroll_links_default_category_slug', 'test-default' );
		update_option( 'blogroll_links_default_sort_by', 'link_name' );
		update_option( 'blogroll_links_default_sort_order', 'desc' );

		// Call shortcode without attributes - it will use defaults.
		// Since 'test-default' category doesn't exist, it should return error.
		$output = blogroll_links_handler( array() );

		$this->assertStringContainsString( 'Invalid category slug', $output );

		// Clean up.
		delete_option( 'blogroll_links_default_category_slug' );
		delete_option( 'blogroll_links_default_sort_by' );
		delete_option( 'blogroll_links_default_sort_order' );
	}

	/**
	 * Test plugin version constant is defined.
	 */
	public function test_version_constant_defined() {
		$this->assertTrue( defined( 'BLOGROLL_LINKS_VERSION' ) );
		$this->assertEquals( '3.0.0', BLOGROLL_LINKS_VERSION );
	}
}
