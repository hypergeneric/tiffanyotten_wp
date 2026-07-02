<?php

function tiffanyotten_light_or_dark ( $hex ) {
	if ( ! $hex ) {
		return "dark";
	}
	$hex = str_replace("0x", "", $hex);
	$hex = str_replace("#", "", $hex);
	$r = hexdec(substr($hex, 0, 2));
	$g = hexdec(substr($hex, 2, 2));
	$b = hexdec(substr($hex, 4, 2));
	$contrast = sqrt(
		$r * $r * .241 +
			$g * $g * .691 +
			$b * $b * .068
	);
	return $contrast > 160 ? "dark" : "light";
}

function hex2rgb( $colour, $opacity = 1 ) {
	if ($colour[0] == '#') {
		$colour = substr($colour, 1);
	}
	if (strlen($colour) == 6) {
		list($r, $g, $b) = array($colour[0] . $colour[1], $colour[2] . $colour[3], $colour[4] . $colour[5]);
	} elseif (strlen($colour) == 3) {
		list($r, $g, $b) = array($colour[0] . $colour[0], $colour[1] . $colour[1], $colour[2] . $colour[2]);
	} else {
		return false;
	}
	$r = hexdec($r);
	$g = hexdec($g);
	$b = hexdec($b);
	return 'rgba(' . $r . ',' . $g . ',' . $b . ',' . $opacity . ')';
}

function get_first_taxonomy_term( $post_id, $taxonomy ) {
	$terms = get_the_terms( $post_id, $taxonomy );
	if ( $terms && ! is_wp_error( $terms ) ) {
		return $terms[0]; // Return the first term object
	}
	return false; // Return false if no term is found or there is an error
}

function tiffanyotten_get_categories( $taxonomy, $type = 'radio' ) {
	$terms = get_terms( [
		'taxonomy'   => $taxonomy,
		'hide_empty' => true,
		'orderby'    => 'name',
		'parent'     => 0,
	] );

	// Check if we are on a taxonomy page for the specified taxonomy.
	$current_term_slug = is_tax( $taxonomy ) ? get_queried_object()->slug : '';
	if ( $current_term_slug == '' ) {
		$current_term_slug = isset( $_GET[ $taxonomy ] ) ? sanitize_text_field( $_GET[ $taxonomy ] ) : '';
	}

	if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
		// Sort the terms based on ACF field 'sort_order' first, then by name.
		usort( $terms, function ( $a, $b ) {
			$sort_order_a = get_field( 'sort_order', $a );
			$sort_order_b = get_field( 'sort_order', $b );
			$sort_order_a = $sort_order_a ? intval( $sort_order_a ) : 9999;
			$sort_order_b = $sort_order_b ? intval( $sort_order_b ) : 9999;
			if ( $sort_order_a === $sort_order_b ) {
				return strcmp( $a->name, $b->name ); // Sort alphabetically if sort_order is the same.
			}
			return $sort_order_a - $sort_order_b;
		} );

		if ( $type === 'radio' ) {
			// Start building the radio buttons.
			$output = '<div class="tax-radio-group" id="tax-radio-' . esc_attr( $taxonomy ) . '">';
			foreach ( $terms as $term ) {
				$checked = $current_term_slug === $term->slug ? ' checked' : '';
				$output .= sprintf(
					'<label><input type="radio" name="%s" value="%s"%s>%s</label>',
					esc_attr( $taxonomy ),
					esc_attr( $term->slug ),
					$checked,
					esc_html( $term->name )
				);

				// Check for child terms if taxonomy is hierarchical.
				if ( is_taxonomy_hierarchical( $taxonomy ) ) {
					$child_terms = get_terms( [
						'taxonomy'   => $taxonomy,
						'hide_empty' => true,
						'parent'     => $term->term_id,
						'orderby'    => 'name',
					] );
					if ( ! empty( $child_terms ) && ! is_wp_error( $child_terms ) ) {
						foreach ( $child_terms as $child_term ) {
							$child_checked = $current_term_slug === $child_term->slug ? ' checked' : '';
							$output .= sprintf(
								'<label style="margin-left: 20px;"><input type="radio" name="%s" value="%s"%s>%s</label>',
								esc_attr( $taxonomy ),
								esc_attr( $child_term->slug ),
								$child_checked,
								esc_html( $child_term->name )
							);
						}
					}
				}
			}
			$output .= '</div>';
		} else {
			// Default: Start building the dropdown.
			$output = '<select id="tax-select-' . esc_attr( $taxonomy ) . '" class="select">';
			foreach ( $terms as $term ) {
				$selected = $current_term_slug === $term->slug ? ' selected="selected"' : '';
				$output .= sprintf(
					'<option value="%s"%s>%s</option>',
					esc_attr( $term->slug ),
					$selected,
					esc_html( $term->name )
				);

				// Check for child terms if taxonomy is hierarchical.
				if ( is_taxonomy_hierarchical( $taxonomy ) ) {
					$child_terms = get_terms( [
						'taxonomy'   => $taxonomy,
						'hide_empty' => true,
						'parent'     => $term->term_id,
						'orderby'    => 'name',
					] );
					if ( ! empty( $child_terms ) && ! is_wp_error( $child_terms ) ) {
						foreach ( $child_terms as $child_term ) {
							$child_selected = $current_term_slug === $child_term->slug ? ' selected="selected"' : '';
							$output .= sprintf(
								'<option value="%s"%s>&nbsp;&nbsp;&nbsp;%s</option>',
								esc_attr( $child_term->slug ),
								$child_selected,
								esc_html( $child_term->name )
							);
						}
					}
				}
			}
			$output .= '</select>';
		}

		return $output;
	}
	return '';
}

function tiffanyotten_get_taxonomy_select( $taxonomy ) {
	$select = tiffanyotten_get_categories( $taxonomy );
	$select = apply_filters( 'tiffanyotten_get_taxonomy_select', $select, $taxonomy );
	if ( $select == '' ) {
		return false;
	}
	$type    = ! get_post_type() ? 'post' : get_post_type();
	$label   = get_field( $type . '_archive_listing_taxonomy_' . $taxonomy, 'option' );
	$tooltip = get_field( $type . '_archive_listing_taxonomy_tooltip_' . $taxonomy, 'option' );
	if ( ! $label ) {
		$tax = get_taxonomy( $taxonomy );
		if ( $tax ) {
			$label = $tax->label;
		}
	}
	$select = str_replace( '<select ', '<select autocomplete="off" ', $select );
	$select = str_replace( '<select ', '<select data-placeholder="' . $label . '"', $select );
	$select = str_replace( '<select ', '<select name="' . $taxonomy . '"', $select );
	return '<div class="selector">' . $select . ( $tooltip ? '<span class="tooltip" title="' . esc_attr( $tooltip ) . '">i</span>' : '' ) . '</div>';
}

function reading_time( $content ) {
	$timer = " Min read";
	$word_count = str_word_count( strip_tags( $content ) );
	$readingtime = ceil( $word_count / 200 );
	if ( $readingtime == 0 ) {
		$readingtime = 1;
	}
	$totalreadingtime = $readingtime . $timer;
	return $totalreadingtime;
}

function tiffanyotten_header_theme() {
	$theme = 'dark';
	if ( is_home() ) { // all archive pages including post archive and all cpt's
		$theme = 'light';
	} else if ( is_single() || is_archive() ) { // all single pages including post archive and all cpt's
		$theme = 'dark';
	} else if ( is_page() ) { // all regular pages
		$first = tiffanyotten_get_first_acf_block();
		if ( $first ) {
			$background_color = $first['data']['background_color'] ?? '#fff';
			$theme = tiffanyotten_light_or_dark($background_color);
		}
	}
	return $theme;
}

function print_background_markup( $args=null, $overrides = [] ) {
	// Gather defaults from block values
	$defaults = [
		'background_color'        => tiffanyotten_block_value( 'background_color', $args ),
		'background_image'        => tiffanyotten_block_value( 'background_image', $args ),
		'background_image_mobile' => tiffanyotten_block_value( 'background_image_mobile', $args ),
		'background_video'        => tiffanyotten_block_value( 'background_video', $args ),
		'background_sizing'       => tiffanyotten_block_value( 'background_sizing', $args ),
	];
	// Merge overrides (overrides take precedence)
	$values = array_merge( $defaults, (array) $overrides );
	// Extract to local vars
	$background_color        = $values['background_color'];
	$background_image        = $values['background_image'];
	$background_image_mobile = $values['background_image_mobile'];
	$background_video        = $values['background_video'];
	$background_sizing       = $values['background_sizing'];
	// store some vars
	$is_logged_in = is_user_logged_in();
	$has_bg_image = $background_image || $background_image_mobile;
	?>
	<div style="background-color:<?php echo $background_color; ?>;<?php echo $is_logged_in && $has_bg_image ? 'background-image:url( ' . $background_image['url'] . ');' : ''; ?>"
		data-desktop="<?php echo $background_image ? $background_image['url'] : ''; ?>"
		data-mobile="<?php echo $background_image_mobile ? $background_image_mobile['url'] : ''; ?>"
		class="underlay <?php echo $has_bg_image ? 'responsive-background' : ''; ?> sizing-<?php echo $background_sizing; ?>">
		<?php if ($background_video) : ?>
			<video class="lazy" autoplay muted loop>
				<source <?php echo $is_logged_in ? '' : 'data-'; ?>src="<?php echo $background_video['url']; ?>" type="video/mp4">
			</video>
		<?php endif; ?>
	</div>
	<?php
}

// Setting allowed html for wp_kses function
function tiffanyotten_allowed_html() {
    $allowed_html = array(
        'a' => array(
            'class' => array(),
            'href'  => array(),
            'rel'   => array(),
            'title' => array(),
        ),
        'abbr' => array(
            'title' => array(),
        ),
        'b' => array(),
        'br' => array(),
        'blockquote' => array(
            'cite'  => array(),
        ),
        'cite' => array(
            'title' => array(),
        ),
        'code' => array(),
        'del' => array(
            'datetime' => array(),
            'title' => array(),
        ),
        'dd' => array(),
        'div' => array(
            'class' => array(),
            'title' => array(),
            'style' => array(),
        ),
        'dl' => array(),
        'dt' => array(),
        'em' => array(),
        'h1' => array(),
        'h2' => array(),
        'h3' => array(),
        'h4' => array(),
        'h5' => array(),
        'h6' => array(),
        'i' => array(),
        'img' => array(
            'alt'    => array(),
            'class'  => array(),
            'height' => array(),
            'src'    => array(),
            'width'  => array(),
        ),
        'li' => array(
            'class' => array(),
        ),
        'ol' => array(
            'class' => array(),
        ),
        'p' => array(
            'class' => array(),
        ),
        'q' => array(
            'cite' => array(),
            'title' => array(),
        ),
        'span' => array(
            'class' => array(),
            'title' => array(),
            'style' => array(),
        ),
        'strike' => array(),
        'strong' => array(),
        'ul' => array(
            'class' => array(),
        ),
    );

    return $allowed_html;
}

/**
 * Fetch external page metadata and cache for 1 day.
 */
function tiffanyotten_get_external_link_meta( $url ) {
	if ( empty( $url ) || ! filter_var( $url, FILTER_VALIDATE_URL ) ) {
		return [];
	}

	$cache_key = 'tiffanyotten_ext_meta_' . md5( $url );
	$cached    = get_transient( $cache_key );

	if ( false !== $cached ) {
		return $cached;
	}

	$response = wp_remote_get(
		$url,
		[
			'timeout'     => 10,
			'redirection' => 5,
			'user-agent'  => 'WordPress/' . get_bloginfo( 'version' ) . '; ' . home_url(),
		]
	);

	if ( is_wp_error( $response ) ) {
		return [];
	}

	$code = wp_remote_retrieve_response_code( $response );
	$body = wp_remote_retrieve_body( $response );

	if ( $code < 200 || $code >= 300 || empty( $body ) ) {
		return [];
	}

	$meta = tiffanyotten_parse_external_meta( $body, $url );

	set_transient( $cache_key, $meta, DAY_IN_SECONDS );

	return $meta;
}

function tiffanyotten_get_normalized_entry_props( $postid=null, $size=null ) {
	$title         = get_the_title( $postid );
	$excerpt       = get_custom_excerpt( $postid );
	$image         = tiffanyotten_get_featured_image( $postid, $size );
	$link          = get_the_permalink( $postid );
	$target        = '';
	$external_link = get_field( 'internal_external_link', $postid );
	$sync_title    = get_field( 'internal_external_link_sync', $postid );
	if ( $external_link ) {
		$current_host = parse_url( home_url(), PHP_URL_HOST );
		$link_host    = parse_url( $external_link, PHP_URL_HOST );
		$link         = $external_link;
		$is_external  = $link_host && $link_host !== $current_host;
		if ( $is_external ) {
			$target = '_blank';
		}
		if ( ! $is_external ) {
			$linked_post_id = url_to_postid( $external_link );
			if ( $linked_post_id ) {
				if ( $sync_title ) {
					$title = get_the_title( $linked_post_id );
				}
				if ( empty( $excerpt ) ) {
					$excerpt = get_custom_excerpt( $linked_post_id );
				}
				if ( empty( $image ) ) {
					$image = tiffanyotten_get_featured_image( $linked_post_id, $size );
				}
			}
		} else {
			$external_meta = tiffanyotten_get_external_link_meta( $external_link );
			if ( $sync_title && ! empty( $external_meta['title'] ) ) {
				$title = $external_meta['title'];
			}
			if ( empty( $excerpt ) && ! empty( $external_meta['excerpt'] ) ) {
				$excerpt = $external_meta['excerpt'];
			}
			if ( empty( $image ) && ! empty( $external_meta['image'] ) ) {
				$image = [
					'url' => $external_meta['image'],
					'alt' => $external_meta['title'] ?? $title,
				];
			}
		}
	}
	return [ $title, $link, $target, $image, $excerpt ];
}

/**
 * Parse OG / Twitter / standard meta tags from HTML.
 */
function tiffanyotten_parse_external_meta( $html, $url = '' ) {
	$result = [
		'title'   => '',
		'excerpt' => '',
		'image'   => '',
	];

	if ( empty( $html ) ) {
		return $result;
	}

	libxml_use_internal_errors( true );

	$doc = new DOMDocument();
	$doc->loadHTML( $html );
	$xpath = new DOMXPath( $doc );

	$get_meta = static function ( DOMXPath $xpath, $attr, $value ) {
		$nodes = $xpath->query( "//meta[@{$attr}='{$value}']" );
		if ( $nodes && $nodes->length ) {
			return trim( $nodes->item( 0 )->getAttribute( 'content' ) );
		}
		return '';
	};

	$title = $get_meta( $xpath, 'property', 'og:title' );
	if ( ! $title ) {
		$title = $get_meta( $xpath, 'name', 'twitter:title' );
	}
	if ( ! $title ) {
		$title_nodes = $xpath->query( '//title' );
		if ( $title_nodes && $title_nodes->length ) {
			$title = trim( $title_nodes->item( 0 )->textContent );
		}
	}

	$excerpt = $get_meta( $xpath, 'property', 'og:description' );
	if ( ! $excerpt ) {
		$excerpt = $get_meta( $xpath, 'name', 'twitter:description' );
	}
	if ( ! $excerpt ) {
		$excerpt = $get_meta( $xpath, 'name', 'description' );
	}

	$image = $get_meta( $xpath, 'property', 'og:image' );
	if ( ! $image ) {
		$image = $get_meta( $xpath, 'name', 'twitter:image' );
	}

	$result['title']   = wp_strip_all_tags( $title );
	$result['excerpt'] = wp_trim_words( wp_strip_all_tags( $excerpt ), 30 );
	$result['image']   = esc_url_raw( $image );

	return $result;
}

function get_custom_excerpt($post_id=null, $character_count = 100) {
	$content = get_the_excerpt($post_id);
	$content = preg_replace('/\[(\/?vc_[^\]]*)\]/', '', $content);
	$content = strip_shortcodes($content);
	$content = wp_strip_all_tags($content);
	if ( $character_count == false ) {
		return $content;
	}
	if (strlen($content) > $character_count) {
		$trimmed_content = substr($content, 0, $character_count);
		$last_space = strrpos($trimmed_content, ' ');
		$trimmed_content = substr($trimmed_content, 0, $last_space) . '...';
	} else {
		$trimmed_content = $content;
	}
	return $trimmed_content;
}