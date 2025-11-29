<?php
/**
 * Plugin Name: Blogroll Links
 * Plugin URI: https://github.com/rajivpant/blogroll-links
 * Description: Displays blogroll links on a Page or Post. Use shortcode <code>[blogroll-links categoryslug="blogroll"]</code> to display your links.
 * Author: Rajiv Pant
 * Version: 3.0.0
 * Author URI: https://www.rajiv.com/
 * License: GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: blogroll-links
 * Domain Path: /languages
 * Requires at least: 5.0
 * Requires PHP: 7.4
 * Tested up to: 6.7
 *
 * @package Blogroll_Links
 */

/*
 * Blogroll Links is a WordPress Plugin that displays a list of blogroll links
 * in a Post or Page on your WordPress Blog.
 *
 * Version 1.1 includes modifications made to the admin panel layout to make it
 * better compliant with the WordPress guidelines. Thanks to @federicobond.
 *
 * Version 2 switches over the tag format to WordPress shortcodes.
 * The old format is still supported for backwards compatibility.
 *
 * Version 3.0 modernizes the plugin for WordPress 6.x and PHP 8+, fixing
 * security vulnerabilities and following current coding standards.
 *
 * Copyright (C) 2008-2025 Rajiv Pant
 * Thanks to Dave Grega, Adam E. Falk (xenograg) for their contributions to this code.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 *
 * Examples of use:
 *
 * WordPress shortcode syntax:
 *
 * [blogroll-links categoryslug="rajiv-web" sortby="link_name"]
 * [blogroll-links categoryslug="people" sortby="link_name" sortorder="desc"]
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Plugin version constant.
 */
define( 'BLOGROLL_LINKS_VERSION', '3.0.0' );

/**
 * Get term ID from category slug.
 *
 * @since 3.0.0
 *
 * @param string $category_slug The category slug to look up.
 * @return int|false The term ID or false if not found.
 */
function blogroll_links_get_term_id( $category_slug ) {
	global $wpdb;

	// Sanitize the slug.
	$category_slug = sanitize_title( $category_slug );

	if ( empty( $category_slug ) ) {
		return false;
	}

	// Use prepared statement to prevent SQL injection.
	$sql = $wpdb->prepare(
		"SELECT term_id FROM {$wpdb->terms} WHERE slug = %s LIMIT 1",
		$category_slug
	);

	$term_id = $wpdb->get_var( $sql );

	return $term_id ? (int) $term_id : false;
}

/**
 * Generate HTML for blogroll links.
 *
 * @since 1.0.0
 * @since 3.0.0 Added output escaping for security.
 *
 * @param int    $category_id The category term ID.
 * @param string $sort_by     Field to sort by (link_name, link_url, etc.).
 * @param string $sort_order  Sort order (asc or desc).
 * @return string HTML output of the links list.
 */
function blogroll_links_html( $category_id, $sort_by, $sort_order ) {
	// Validate sort_by against allowed values.
	$allowed_sort_by = array( 'link_id', 'link_name', 'link_url', 'link_rating', 'link_updated', 'link_title' );
	if ( ! in_array( $sort_by, $allowed_sort_by, true ) ) {
		$sort_by = 'link_name';
	}

	// Validate sort_order.
	$sort_order = strtoupper( $sort_order );
	if ( ! in_array( $sort_order, array( 'ASC', 'DESC' ), true ) ) {
		$sort_order = 'ASC';
	}

	$bookmarks = get_bookmarks(
		array(
			'orderby'        => $sort_by,
			'order'          => $sort_order,
			'limit'          => -1,
			'category'       => $category_id,
			'category_name'  => null,
			'hide_invisible' => 1,
			'show_updated'   => 0,
			'include'        => null,
			'exclude'        => null,
			'search'         => '.',
		)
	);

	if ( empty( $bookmarks ) ) {
		return '<p>' . esc_html__( 'No links found for this category.', 'blogroll-links' ) . '</p>';
	}

	$links = '<ul class="blogroll-links">';

	foreach ( $bookmarks as $bookmark ) {
		// Build rel attribute with proper escaping.
		$rel_tag_part = '';
		if ( ! empty( $bookmark->link_rel ) ) {
			$rel_tag_part = ' rel="' . esc_attr( $bookmark->link_rel ) . '"';
		}

		// Build target attribute with proper escaping.
		$target_tag_part = '';
		if ( ! empty( $bookmark->link_target ) ) {
			$target_tag_part = ' target="' . esc_attr( $bookmark->link_target ) . '"';
		}

		// Build description with proper escaping.
		$description_tag = '';
		if ( ! empty( $bookmark->link_description ) ) {
			$description_tag = ' - ' . esc_html( $bookmark->link_description );
		}

		// Build image tag with proper escaping.
		$image_tag = '';
		if ( ! empty( $bookmark->link_image ) ) {
			$image_tag = '<br /><img src="' . esc_url( $bookmark->link_image ) . '" alt="" />';
		}

		$links .= sprintf(
			'<li><a href="%s"%s%s>%s</a>%s%s</li>',
			esc_url( $bookmark->link_url ),
			$rel_tag_part,
			$target_tag_part,
			esc_html( $bookmark->link_name ),
			$description_tag,
			$image_tag
		);
	}

	$links .= '</ul>';

	return $links;
}

/**
 * Shortcode handler for [blogroll-links].
 *
 * @since 2.0.0
 * @since 3.0.0 Added security fixes and validation.
 *
 * @param array $atts Shortcode attributes.
 * @return string HTML output of the links.
 */
function blogroll_links_handler( $atts ) {
	$attributes = shortcode_atts(
		array(
			'categoryslug' => get_option( 'blogroll_links_default_category_slug', 'blogroll' ),
			'sortby'       => get_option( 'blogroll_links_default_sort_by', 'link_name' ),
			'sortorder'    => get_option( 'blogroll_links_default_sort_order', 'asc' ),
			'debug'        => '0',
		),
		$atts,
		'blogroll-links'
	);

	$category_slug = $attributes['categoryslug'];
	$sort_by       = $attributes['sortby'];
	$sort_order    = $attributes['sortorder'];

	// Get term ID using helper function (handles sanitization and prepared statements).
	$category_id = blogroll_links_get_term_id( $category_slug );

	if ( false === $category_id ) {
		return '<p>' . esc_html__( 'Invalid category slug specified.', 'blogroll-links' ) . '</p>';
	}

	return blogroll_links_html( $category_id, $sort_by, $sort_order );
}

/**
 * Legacy content filter for HTML comment syntax.
 *
 * Replaces the <!--blogroll-links--> tag and its contents with the blogroll links.
 * This function supports a previous (now deprecated) syntax for backward compatibility.
 *
 * @since 1.0.0
 * @since 3.0.0 Added security fixes.
 *
 * @param string $text The post content.
 * @return string Modified post content with blogroll links.
 */
function blogroll_links_text( $text ) {
	// Only perform plugin functionality if post/page contains the legacy tag.
	while ( preg_match( '{<!--blogroll-links\b(.*?)-->.*?<!--/blogroll-links-->}', $text, $matches ) ) {
		// Get default values.
		$category_slug = get_option( 'blogroll_links_default_category_slug', 'blogroll' );
		$sort_by       = get_option( 'blogroll_links_default_sort_by', 'link_name' );
		$sort_order    = get_option( 'blogroll_links_default_sort_order', 'asc' );

		$attributes = $matches[1];

		// Parse legacy attributes.
		if ( preg_match( '{\bcategory-slug\b="(.*?)"}', $attributes, $attr_matches ) ) {
			$category_slug = $attr_matches[1];
		}

		if ( preg_match( '{\bsort-by\b="(.*?)"}', $attributes, $attr_matches ) ) {
			$sort_by = $attr_matches[1];
		}

		if ( preg_match( '{\bsort-order\b="(.*?)"}', $attributes, $attr_matches ) ) {
			$sort_order = $attr_matches[1];
		}

		// Get term ID using helper function.
		$category_id = blogroll_links_get_term_id( $category_slug );

		if ( false === $category_id ) {
			$links = '<p>' . esc_html__( 'Invalid category slug specified.', 'blogroll-links' ) . '</p>';
		} else {
			$links = blogroll_links_html( $category_id, $sort_by, $sort_order );
		}

		// Replace only the first occurrence per iteration.
		$text = preg_replace( '{<!--blogroll-links\b.*?-->.*?<!--/blogroll-links-->}', $links, $text, 1 );
	}

	return $text;
}

/**
 * Register admin menu page.
 *
 * @since 1.0.0
 * @since 3.0.0 Fixed capability check.
 *
 * @return void
 */
function blogroll_links_admin() {
	add_options_page(
		__( 'Blogroll Links', 'blogroll-links' ),
		__( 'Blogroll Links', 'blogroll-links' ),
		'manage_options',
		'blogroll-links',
		'blogroll_links_admin_panel'
	);
}

/**
 * Render admin settings panel.
 *
 * @since 1.0.0
 * @since 3.0.0 Complete rewrite with security fixes and working form fields.
 *
 * @return void
 */
function blogroll_links_admin_panel() {
	// Check user capabilities.
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	// Initialize default options if they don't exist.
	add_option( 'blogroll_links_default_category_slug', 'blogroll' );
	add_option( 'blogroll_links_default_sort_by', 'link_name' );
	add_option( 'blogroll_links_default_sort_order', 'asc' );

	$message = '';

	// Handle form submission.
	if ( isset( $_POST['blogroll_links_submit'] ) ) {
		// Verify nonce for security.
		if ( ! isset( $_POST['blogroll_links_nonce'] ) ||
			! wp_verify_nonce( $_POST['blogroll_links_nonce'], 'blogroll_links_settings' ) ) {
			wp_die( esc_html__( 'Security check failed.', 'blogroll-links' ) );
		}

		// Sanitize and save options.
		$category_slug = isset( $_POST['blogroll_links_default_category_slug'] )
			? sanitize_title( wp_unslash( $_POST['blogroll_links_default_category_slug'] ) )
			: '';

		$sort_by = isset( $_POST['blogroll_links_default_sort_by'] )
			? sanitize_text_field( wp_unslash( $_POST['blogroll_links_default_sort_by'] ) )
			: 'link_name';

		$sort_order = isset( $_POST['blogroll_links_default_sort_order'] )
			? sanitize_text_field( wp_unslash( $_POST['blogroll_links_default_sort_order'] ) )
			: 'asc';

		// Validate sort_by against allowed values.
		$allowed_sort_by = array( 'link_id', 'link_name', 'link_url', 'link_rating', 'link_updated' );
		if ( ! in_array( $sort_by, $allowed_sort_by, true ) ) {
			$sort_by = 'link_name';
		}

		// Validate sort_order.
		$sort_order = strtolower( $sort_order );
		if ( ! in_array( $sort_order, array( 'asc', 'desc' ), true ) ) {
			$sort_order = 'asc';
		}

		update_option( 'blogroll_links_default_category_slug', $category_slug );
		update_option( 'blogroll_links_default_sort_by', $sort_by );
		update_option( 'blogroll_links_default_sort_order', $sort_order );

		$message = __( 'Settings saved.', 'blogroll-links' );
	}

	// Load current values.
	$category_slug = get_option( 'blogroll_links_default_category_slug', 'blogroll' );
	$sort_by       = get_option( 'blogroll_links_default_sort_by', 'link_name' );
	$sort_order    = get_option( 'blogroll_links_default_sort_order', 'asc' );
	?>
	<div class="wrap">
		<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

		<?php if ( ! empty( $message ) ) : ?>
			<div class="notice notice-success is-dismissible">
				<p><?php echo esc_html( $message ); ?></p>
			</div>
		<?php endif; ?>

		<form method="post" action="">
			<?php wp_nonce_field( 'blogroll_links_settings', 'blogroll_links_nonce' ); ?>

			<h2><?php esc_html_e( 'Default Settings', 'blogroll-links' ); ?></h2>
			<p><?php esc_html_e( 'These defaults are used when shortcode attributes are not specified.', 'blogroll-links' ); ?></p>

			<table class="form-table" role="presentation">
				<tr>
					<th scope="row">
						<label for="blogroll_links_default_category_slug">
							<?php esc_html_e( 'Default Category Slug', 'blogroll-links' ); ?>
						</label>
					</th>
					<td>
						<input type="text"
							id="blogroll_links_default_category_slug"
							name="blogroll_links_default_category_slug"
							value="<?php echo esc_attr( $category_slug ); ?>"
							class="regular-text" />
						<p class="description">
							<?php esc_html_e( 'The link category slug to display by default.', 'blogroll-links' ); ?>
						</p>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="blogroll_links_default_sort_by">
							<?php esc_html_e( 'Sort By', 'blogroll-links' ); ?>
						</label>
					</th>
					<td>
						<select id="blogroll_links_default_sort_by" name="blogroll_links_default_sort_by">
							<option value="link_name" <?php selected( $sort_by, 'link_name' ); ?>>
								<?php esc_html_e( 'Name', 'blogroll-links' ); ?>
							</option>
							<option value="link_url" <?php selected( $sort_by, 'link_url' ); ?>>
								<?php esc_html_e( 'URL', 'blogroll-links' ); ?>
							</option>
							<option value="link_rating" <?php selected( $sort_by, 'link_rating' ); ?>>
								<?php esc_html_e( 'Rating', 'blogroll-links' ); ?>
							</option>
							<option value="link_id" <?php selected( $sort_by, 'link_id' ); ?>>
								<?php esc_html_e( 'ID', 'blogroll-links' ); ?>
							</option>
							<option value="link_updated" <?php selected( $sort_by, 'link_updated' ); ?>>
								<?php esc_html_e( 'Last Updated', 'blogroll-links' ); ?>
							</option>
						</select>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="blogroll_links_default_sort_order">
							<?php esc_html_e( 'Sort Order', 'blogroll-links' ); ?>
						</label>
					</th>
					<td>
						<select id="blogroll_links_default_sort_order" name="blogroll_links_default_sort_order">
							<option value="asc" <?php selected( $sort_order, 'asc' ); ?>>
								<?php esc_html_e( 'Ascending (A-Z)', 'blogroll-links' ); ?>
							</option>
							<option value="desc" <?php selected( $sort_order, 'desc' ); ?>>
								<?php esc_html_e( 'Descending (Z-A)', 'blogroll-links' ); ?>
							</option>
						</select>
					</td>
				</tr>
			</table>

			<?php submit_button( __( 'Save Changes', 'blogroll-links' ), 'primary', 'blogroll_links_submit' ); ?>
		</form>

		<hr />

		<h2><?php esc_html_e( 'Usage', 'blogroll-links' ); ?></h2>
		<p><?php esc_html_e( 'Add this shortcode to any post or page to display your blogroll links:', 'blogroll-links' ); ?></p>
		<p><code>[blogroll-links categoryslug="your-category-slug"]</code></p>

		<h3><?php esc_html_e( 'Shortcode Options', 'blogroll-links' ); ?></h3>
		<table class="widefat" style="max-width: 600px;">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Attribute', 'blogroll-links' ); ?></th>
					<th><?php esc_html_e( 'Description', 'blogroll-links' ); ?></th>
					<th><?php esc_html_e( 'Default', 'blogroll-links' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><code>categoryslug</code></td>
					<td><?php esc_html_e( 'The slug of the link category to display', 'blogroll-links' ); ?></td>
					<td><code><?php echo esc_html( $category_slug ); ?></code></td>
				</tr>
				<tr>
					<td><code>sortby</code></td>
					<td><?php esc_html_e( 'Field to sort by (link_name, link_url, link_rating, link_id)', 'blogroll-links' ); ?></td>
					<td><code><?php echo esc_html( $sort_by ); ?></code></td>
				</tr>
				<tr>
					<td><code>sortorder</code></td>
					<td><?php esc_html_e( 'Sort direction (asc or desc)', 'blogroll-links' ); ?></td>
					<td><code><?php echo esc_html( $sort_order ); ?></code></td>
				</tr>
			</tbody>
		</table>

		<h3><?php esc_html_e( 'Examples', 'blogroll-links' ); ?></h3>
		<p><code>[blogroll-links categoryslug="friends" sortby="link_name" sortorder="asc"]</code></p>
		<p><code>[blogroll-links categoryslug="resources" sortby="link_rating" sortorder="desc"]</code></p>

		<hr />

		<h2><?php esc_html_e( 'Links Manager', 'blogroll-links' ); ?></h2>
		<p>
			<?php
			printf(
				/* translators: %s: URL to Links Manager admin page */
				esc_html__( 'Manage your links in the %s.', 'blogroll-links' ),
				'<a href="' . esc_url( admin_url( 'link-manager.php' ) ) . '">' . esc_html__( 'Links Manager', 'blogroll-links' ) . '</a>'
			);
			?>
		</p>
		<p class="description">
			<?php esc_html_e( 'Note: The Links Manager is hidden by default in WordPress. If you do not see it in your admin menu, you may need to enable it by installing the "Link Manager" plugin or adding this code to your theme:', 'blogroll-links' ); ?>
		</p>
		<p><code>add_filter( 'pre_option_link_manager_enabled', '__return_true' );</code></p>
	</div>
	<?php
}

// Register hooks.
add_filter( 'the_content', 'blogroll_links_text', 2 );
add_shortcode( 'blogroll-links', 'blogroll_links_handler' );
add_action( 'admin_menu', 'blogroll_links_admin' );
