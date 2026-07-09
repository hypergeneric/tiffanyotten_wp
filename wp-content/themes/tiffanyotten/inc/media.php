<?php

add_action( 'after_setup_theme', function () {
	add_image_size( 'square-600', 600, 600, true );
	add_image_size( 'wide-16:9', 1600, 900, true );
} );

add_filter( 'image_size_names_choose', function ( $sizes ) {
	return array_merge( $sizes, [
		'square-600' => __( 'Square 600x600', 'tiffanyotten' ),
		'wide-16:9' => __( 'Wide 16:9', 'tiffanyotten' ),
	] );
} );

function tiffanyotten_get_image_by_id( $thumbnail_id, $size="full" ) {
	$thumbnail     = wp_get_attachment_image_src( $thumbnail_id, $size );
	if ( ! $thumbnail ) {
		return false;
	}
	$thumbnail_alt = get_post_meta( $thumbnail_id , '_wp_attachment_image_alt', true );
	$image = [ 
		'url'    => $thumbnail[0],
		'alt'    => $thumbnail_alt, 
		'width'  => $thumbnail[1], 
		'height' => $thumbnail[2]
	];
	return $image;
}

function tiffanyotten_get_image_by_url( $url ) {
	$thumbnail_id  = attachment_url_to_postid( $url );
	return tiffanyotten_get_image_by_id( $thumbnail_id );
}

function tiffanyotten_get_featured_image_by_url( $url ) {
	$id = url_to_postid( $url );
	if ( $id ) {
		return tiffanyotten_get_featured_image( $id );
	}
	return false;
}

function tiffanyotten_get_featured_image( $id=null, $size="full" ) {
	if ( post_password_required( $id ) || is_attachment( $id ) || ! has_post_thumbnail( $id ) ) {
		return false;
	}
	$thumbnail_id = get_post_thumbnail_id( $id );
	return tiffanyotten_get_image_by_id( $thumbnail_id, $size );
}

function tiffanyotten_getimagesize( $image_url )  {
	$parse_url = parse_url( $image_url );
	$ext = strtolower( pathinfo( $image_url,  PATHINFO_EXTENSION ) );
	$local_path = ABSPATH . ltrim( $parse_url['path'], '/' );
	if ( file_exists( $local_path ) ) {
		if ( $ext === 'svg' ){
			$svgfile = simplexml_load_file( $local_path );
			$width   = explode( ' ', (string) $svgfile->attributes()->width );
			$height  = explode( ' ', (string) $svgfile->attributes()->height );
			if ( ! empty( $width[0] ) && ! empty( $height[0] ) ) {
				$size[] = str_replace( 'px', '', $width[0] );
				$size[] = str_replace( 'px', '', $height[0] );
				$size[] = 0; // adding image type - 0 for SVG since there is no IMAGETYPE_SVG constant
				$size[] = 'width="' . $width[0] . '" height="' . $height[0] . '"'; 
				return $size;
			}
			$viewBox = explode( ' ', (string) $svgfile->attributes()->viewBox );
			if ( ! empty ( $viewBox ) ) {
				$size = array_splice( $viewBox, -2 );
				$size[] = 0; // adding image type - 0 for SVG since there is no IMAGETYPE_SVG constant
				$size[] = 'width="' . $size[0] . '" height="' . $size[1] . '"';
				return $size;
			}
		} else {
			$size = getimagesize( $local_path );
			return $size;
		}
	} else {
		if ( $ext === 'svg' ){
			$size[] = 0;
			$size[] = 0;
			$size[] = 0; // adding image type - 0 for SVG since there is no IMAGETYPE_SVG constant
			$size[] = 'width="0" height="0"'; 
			return $size;
		} else {
			if ( ! isset( $parse_url['hostname'] ) ) {
				$image_url = 'https://www.tiffanyotten.com/' . ltrim( $image_url, '/' );
			}
			$size = @getimagesize( $image_url );
			return $size ? $size : [1600,1200];
		}
	}
}

function tiffanyotten_print_img_bg_src( $image ) {
	if ( ! $image ) {
		return;
	}
	if ( ! is_array( $image ) ) {
		$image = [ 'url' => $image ];
	}
	$response = 'data-bg="' . $image['url'] . '"';
	if ( is_user_logged_in() ) {
		$response = 'style="background-image: url( ' . $image['url'] . ' );"';
	}
	return $response;
}

function tiffanyotten_print_img_src( $image, $classes=[], $style="", $inline=false ) {
	if ( ! $image ) {
		return;
	}
	if ( ! is_array( $image ) ) {
		$image = is_numeric( $image ) ? [ 'ID' => $image ] : [ 'url' => $image ];
	}
	if ( empty( $image['url'] ) ) {
		$attachment_id = ! empty( $image['ID'] ) ? intval( $image['ID'] ) : ( ! empty( $image['id'] ) ? intval( $image['id'] ) : 0 );
		if ( $attachment_id ) {
			$image['url'] = wp_get_attachment_image_url( $attachment_id, 'full' );
		}
	}
	if ( empty( $image['url'] ) ) {
		return;
	}
	$ext = strtolower( pathinfo( $image['url'],  PATHINFO_EXTENSION ) );
	if ( $inline && $ext === 'svg' ) {
		$filepath = $image['url'];
		$fileinfo = parse_url( $filepath );
		$siteinfo = parse_url( site_url() );
		if ( ! isset( $fileinfo['host'] ) || $fileinfo['host'] == $fileinfo['host'] ) {
			$filepath = ABSPATH . $fileinfo['path'];
		}
		print file_get_contents( $filepath );
		return;
	}
	if ( ! isset( $image['alt'] ) ) {
		$title = pathinfo( $image['url'], PATHINFO_FILENAME );
		$title = str_replace( '-', ' ', $title );
		$title = str_replace( '_', ' ', $title );
 		$title = ucwords($title);
		$image['alt'] = $title;
	}
	if ( isset( $image['width'] ) && $image['width'] == 0 ) {
		unset( $image['width'] );
	}
	if ( isset( $image['height'] ) && $image['height'] == 0 ) {
		unset( $image['height'] );
	}
	if ( ! isset( $image['width'] ) && ! isset( $image['height'] ) ) {
		$size = tiffanyotten_getimagesize( $image['url'] );
		$image['width'] = $size[0];
		$image['height'] = $size[1];
	}
	$mobile     = isset( $image['mobile'] ) ? $image['mobile'] : false;
	$breakpoint = isset( $image['breakpoint'] ) ? $image['breakpoint'] : '640';
	if ( $mobile ) {
		if ( ! is_array( $mobile ) ) {
			$mobile = [ 'url' => $mobile ];
		}
		if ( ! isset( $mobile['width'] ) || ! isset( $mobile['height'] ) ) {
			$size = tiffanyotten_getimagesize( $mobile['url'] );
			$mobile['width']  = $size[0];
			$mobile['height'] = $size[1];
		}
	}
	ob_start();
	$svg = "data:image/svg+xml;base64," . base64_encode(
		"<svg xmlns='http://www.w3.org/2000/svg' width='" . intval( $image['width'] ) . "' height='" . intval( $image['height'] ) . "'><rect width='100%' height='100%' fill='none'/></svg>"
	);
	?>
	<?php if ( $mobile ) : ?>
		<picture>
			<source
				media="(max-width: <?php echo intval( $breakpoint - 1 ); ?>px)"
				data-srcset="<?php echo esc_url( $mobile['url'] ); ?>">

			<img class="lazy <?php echo esc_attr( implode( ' ', $classes ) ); ?>"
				src="{{SVG}}"
				style="<?php echo esc_attr( $style ); ?>"
				data-src="<?php echo esc_url( $image['url'] ); ?>"
				alt="<?php echo esc_attr( $image['alt'] ); ?>"
				width="<?php echo intval( $image['width'] ); ?>"
				height="<?php echo intval( $image['height'] ); ?>" />
		</picture>
	<?php else : ?>
		<img class="lazy <?php echo esc_attr( implode( ' ', $classes ) ); ?>"
			src="{{SVG}}"
			style="<?php echo esc_attr( $style ); ?>"
			data-src="<?php echo esc_url( $image['url'] ); ?>"
			alt="<?php echo esc_attr( $image['alt'] ); ?>"
			width="<?php echo intval( $image['width'] ); ?>"
			height="<?php echo intval( $image['height'] ); ?>" />
	<?php endif; ?>
	<?php

	$string = ob_get_clean();

	if ( is_user_logged_in() ) {
		$string = str_replace( 'class="lazy ', 'class="', $string );
		$string = str_replace( 'class="lazy"', 'class=""', $string );
		$string = str_replace( 'src="{{SVG}}"', 'data-svg="{{SVG}}"', $string );
		$string = str_replace( 'data-src=', 'src=', $string );
		$string = str_replace( 'data-srcset=', 'srcset=', $string );
		$string = str_replace( 'data-sizes=', 'sizes=', $string );
	}

	$string = str_replace( '{{SVG}}', $svg, $string );

	return $string;
}

function tiffanyotten_print_video_src( $video, $image, $atts=[], $classes=[], $id=null ) {
	if ( ! $video ) {
		return;
	}
	if ( ! is_array( $video ) ) {
		$video = [ 'url' => $video ];
	}
	ob_start();
	// Check if the video URL is YouTube or Vimeo
	$url_parts = parse_url( $video['url'] );
	if ( isset( $url_parts['host'] ) && in_array( $url_parts['host'], [ 'www.youtube.com', 'youtube.com', 'youtu.be', 'vimeo.com' ] ) ) {
		// Handle YouTube
		if ( strpos( $url_parts['host'], 'youtube' ) !== false || strpos( $url_parts['host'], 'youtu.be' ) !== false ) {
			$youtube_id = '';
			if ( isset( $url_parts['query'] ) ) {
				parse_str( $url_parts['query'], $query_vars );
				$youtube_id = $query_vars['v'] ?? '';
			} elseif ( isset( $url_parts['path'] ) ) {
				$youtube_id = trim( $url_parts['path'], '/' );
			}
			$src = "https://www.youtube.com/embed/" . $youtube_id . "?autoplay=0&controls=1&showinfo=0&rel=0&fs=0&modestbranding=1";
		}
		// Handle Vimeo
		elseif ( strpos( $url_parts['host'], 'vimeo.com' ) !== false ) {
			$vimeo_id = trim( $url_parts['path'], '/' );
			$src = "https://player.vimeo.com/video/" . $vimeo_id . "?byline=0&portrait=0";
		}
		// Output the iframe for YouTube/Vimeo
		?>
		<iframe <?php echo $id ? 'id="' . $id . '"' : ''; ?> 
			scrolling="no" 
			class="lazy <?php echo implode( ' ', $classes ); ?>" 
			style="aspect-ratio: 16 / 9;"
			height="480" width="100%" data-src="<?php echo $src; ?>" 
			<?php echo implode( ' ', $atts ); ?>></iframe>
		<?php
	} elseif ( $video['subtype'] == 'json' ) {
		// Handle Lottie JSON
		?>
		<lottie-player <?php echo implode( ' ', $atts ); ?>
			class="<?php echo implode( ' ', $classes ); ?>" 
			src="<?php echo $video['url']; ?>"></lottie-player>
		<?php
	} else {
		// Handle video element
		?>
		<video <?php echo $id ? 'id="' . $id . '"' : ''; ?> <?php echo implode( ' ', $atts ); ?>
			width="<?php echo $video['width']; ?>" 
			height="<?php echo $video['height']; ?>" 
			class="lazy <?php echo implode( ' ', $classes ); ?>" 
			<?php echo $image ? 'data-poster="' . $image['url'] . '"' : ''; ?>
			data-src="<?php echo $video['url']; ?>"></video>
		<?php
	}
	// Adjust lazy loading for logged-in users
	$string = '<div class="video-object"><div class="video-overlay"></div>' . ob_get_clean() .'</div>';
	if ( is_user_logged_in() ) {
		$string = str_replace( 'class="lazy', 'class="', $string );
		$string = str_replace( 'data-src=', 'src=', $string );
	}
	return $string;
}
