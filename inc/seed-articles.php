<?php
/**
 * Seed publication-ready blog articles into WordPress posts.
 *
 * @package OGC_NewFinity
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Bump to re-seed / update seeded articles. */
define( 'OGC_ARTICLES_SEED_VERSION', '1.1.2' );

/**
 * Convert article markdown (subset) into post HTML.
 *
 * @param string $markdown Markdown source.
 * @return string HTML.
 */
function ogc_article_markdown_to_html( $markdown ) {
	$lines = preg_split( "/\r\n|\n|\r/", (string) $markdown );
	$html  = array();
	$i     = 0;
	$count = count( $lines );

	// Skip title + meta lines at the top.
	while ( $i < $count ) {
		$line = trim( $lines[ $i ] );
		if ( '' === $line || 0 === strpos( $line, '#' ) || 0 === strpos( $line, '**Category:' ) || 0 === strpos( $line, '**Audience:' ) || '---' === $line ) {
			++$i;
			if ( '---' === $line ) {
				break;
			}
			continue;
		}
		break;
	}

	while ( $i < $count ) {
		$raw  = $lines[ $i ];
		$line = rtrim( $raw );

		if ( '' === trim( $line ) ) {
			++$i;
			continue;
		}

		if ( '---' === trim( $line ) ) {
			++$i;
			continue;
		}

		// Fenced placeholder / code block.
		if ( preg_match( '/^```/', $line ) ) {
			$block = array();
			++$i;
			while ( $i < $count && ! preg_match( '/^```/', $lines[ $i ] ) ) {
				$block[] = $lines[ $i ];
				++$i;
			}
			++$i; // closing fence
			$html[] = ogc_article_placeholder_html( $block );
			continue;
		}

		// Table.
		if ( false !== strpos( $line, '|' ) && isset( $lines[ $i + 1 ] ) && preg_match( '/^\|?\s*-+/', $lines[ $i + 1 ] ) ) {
			$table_lines = array();
			while ( $i < $count && false !== strpos( $lines[ $i ], '|' ) ) {
				$table_lines[] = $lines[ $i ];
				++$i;
			}
			$html[] = ogc_article_table_html( $table_lines );
			continue;
		}

		// Headings.
		if ( preg_match( '/^(#{2,4})\s+(.+)$/', $line, $m ) ) {
			$level   = strlen( $m[1] );
			$text    = ogc_article_inline( $m[2] );
			$html[]  = sprintf( '<h%1$d>%2$s</h%1$d>', $level, $text );
			++$i;
			continue;
		}

		// Unordered list.
		if ( preg_match( '/^-\s+(.+)$/', $line, $m ) ) {
			$items = array();
			while ( $i < $count && preg_match( '/^-\s+(.+)$/', rtrim( $lines[ $i ] ), $im ) ) {
				$items[] = '<li>' . ogc_article_inline( $im[1] ) . '</li>';
				++$i;
			}
			$html[] = '<ul>' . implode( '', $items ) . '</ul>';
			continue;
		}

		// Ordered list.
		if ( preg_match( '/^\d+\.\s+(.+)$/', $line, $m ) ) {
			$items = array();
			while ( $i < $count && preg_match( '/^\d+\.\s+(.+)$/', rtrim( $lines[ $i ] ), $im ) ) {
				$items[] = '<li>' . ogc_article_inline( $im[1] ) . '</li>';
				++$i;
			}
			$html[] = '<ol>' . implode( '', $items ) . '</ol>';
			continue;
		}

		// Paragraph (consume consecutive non-blank, non-special lines).
		$para = array( $line );
		++$i;
		while ( $i < $count ) {
			$next = rtrim( $lines[ $i ] );
			if ( '' === trim( $next ) || '---' === trim( $next ) || preg_match( '/^#{2,4}\s+/', $next ) || preg_match( '/^```/', $next ) || preg_match( '/^-\s+/', $next ) || preg_match( '/^\d+\.\s+/', $next ) || ( false !== strpos( $next, '|' ) && isset( $lines[ $i + 1 ] ) && preg_match( '/^\|?\s*-+/', $lines[ $i + 1 ] ) ) ) {
				break;
			}
			$para[] = $next;
			++$i;
		}
		$html[] = '<p>' . ogc_article_inline( implode( ' ', $para ) ) . '</p>';
	}

	return implode( "\n\n", $html );
}

/**
 * Inline markdown: bold + code.
 *
 * @param string $text Text.
 * @return string
 */
function ogc_article_inline( $text ) {
	$text = esc_html( $text );
	$text = preg_replace( '/\*\*(.+?)\*\*/', '<strong>$1</strong>', $text );
	$text = preg_replace( '/`([^`]+)`/', '<code>$1</code>', $text );
	return $text;
}

/**
 * Visual placeholder / callout from fenced block lines.
 *
 * Diagram placeholders become figure slots.
 * Named callouts (Best Practice, Warning, etc.) become styled aside boxes.
 *
 * @param array $lines Block lines.
 * @return string
 */
function ogc_article_placeholder_html( $lines ) {
	$type    = '';
	$title   = '';
	$purpose = '';
	$comp    = '';
	$details = array();
	$in_body = false;

	foreach ( $lines as $line ) {
		$trim = trim( $line );
		if ( '' === $trim ) {
			if ( $in_body ) {
				$details[] = '';
			}
			continue;
		}
		if ( preg_match( '/^\[(.+)\]$/', $trim, $m ) ) {
			// Only treat bare labels (e.g. [Architecture Diagram]) as the type — not flow nodes.
			if ( '' === $type && false === strpos( $m[1], '→' ) && false === strpos( $m[1], '->' ) && strlen( $m[1] ) < 64 ) {
				$type = $m[1];
				continue;
			}
		}
		if ( 0 === stripos( $trim, 'Title:' ) ) {
			$title = trim( substr( $trim, 6 ) );
			continue;
		}
		if ( 0 === stripos( $trim, 'Purpose:' ) ) {
			$purpose = trim( substr( $trim, 8 ) );
			continue;
		}
		if ( 0 === stripos( $trim, 'Suggested Component:' ) ) {
			$comp = trim( substr( $trim, 20 ) );
			continue;
		}
		$in_body   = true;
		$details[] = $trim;
	}

	$label      = $type ? $type : 'Visual';
	$slug       = sanitize_title( $label );
	$is_diagram = (bool) preg_match( '/diagram|flow|workflow|table|benchmark|example|illustration/i', $label );
	$is_callout = (bool) preg_match( '/best practice|warning|security|performance|production|architecture decision|common mistake|engineering callout|key takeaways/i', $label );

	if ( $is_diagram && ! $is_callout ) {
		$out  = '<figure class="ogc-article-figure" data-placeholder="' . esc_attr( $slug ) . '" data-component="' . esc_attr( $comp ) . '">';
		$out .= '<div class="ogc-article-figure__frame" role="img" aria-label="' . esc_attr( $title ? $title : $label ) . '">';
		$out .= '<span class="ogc-article-figure__badge">' . esc_html( $label ) . '</span>';
		if ( $title ) {
			$out .= '<p class="ogc-article-figure__title">' . esc_html( $title ) . '</p>';
		}
		if ( ! empty( $details ) ) {
			$out .= '<div class="ogc-article-figure__sketch"><pre>' . esc_html( implode( "\n", $details ) ) . '</pre></div>';
		} else {
			$out .= '<p class="ogc-article-figure__slot">' . esc_html__( 'Illustration placeholder — final diagram to be inserted in production.', 'ogc-newfinity' ) . '</p>';
		}
		$out .= '</div>';
		if ( $purpose || $comp ) {
			$out .= '<figcaption class="ogc-article-figure__caption">';
			if ( $purpose ) {
				$out .= '<span class="ogc-article-figure__purpose">' . esc_html( $purpose ) . '</span>';
			}
			if ( $comp ) {
				$out .= '<span class="ogc-article-figure__meta">' . esc_html( $comp ) . '</span>';
			}
			$out .= '</figcaption>';
		}
		$out .= '</figure>';
		return $out;
	}

	$out  = '<aside class="ogc-article-callout ogc-article-callout--' . esc_attr( $slug ) . '" data-component="' . esc_attr( $comp ) . '">';
	$out .= '<p class="ogc-article-callout__label">' . esc_html( $label ) . '</p>';
	if ( $title ) {
		$out .= '<p class="ogc-article-callout__title">' . esc_html( $title ) . '</p>';
	}
	if ( $purpose ) {
		$out .= '<p class="ogc-article-callout__body">' . esc_html( $purpose ) . '</p>';
	}
	$out .= '</aside>';
	return $out;
}

/**
 * Markdown table → HTML table.
 *
 * @param array $lines Table lines.
 * @return string
 */
function ogc_article_table_html( $lines ) {
	$rows = array();
	foreach ( $lines as $line ) {
		$line = trim( $line );
		if ( preg_match( '/^\|?\s*-+/', $line ) ) {
			continue;
		}
		$line  = trim( $line, '|' );
		$cells = array_map( 'trim', explode( '|', $line ) );
		$rows[] = $cells;
	}

	if ( empty( $rows ) ) {
		return '';
	}

	$head = array_shift( $rows );
	$out  = '<figure class="ogc-article-table"><table><thead><tr>';
	foreach ( $head as $cell ) {
		$out .= '<th>' . ogc_article_inline( $cell ) . '</th>';
	}
	$out .= '</tr></thead><tbody>';
	foreach ( $rows as $row ) {
		$out .= '<tr>';
		foreach ( $row as $cell ) {
			$out .= '<td>' . ogc_article_inline( $cell ) . '</td>';
		}
		$out .= '</tr>';
	}
	$out .= '</tbody></table></figure>';
	return $out;
}

/**
 * Definitions for articles to seed.
 *
 * @return array<int, array<string, string>>
 */
function ogc_seed_article_definitions() {
	return array(
		array(
			'title'    => 'Engineering High-Performance Full-Stack Architectures: A Blueprint for Scalable Web Platforms',
			'slug'     => 'engineering-high-performance-full-stack-architectures',
			'category' => 'build-development',
			'excerpt'  => 'A technical blueprint for scalable full-stack platforms — concurrency runtimes, PostgreSQL pooling and Redis caching, Dockerized environment parity, and enterprise architecture benchmarks.',
			'source'   => 'docs/article-high-performance-fullstack-architectures.md',
		),
	);
}

/**
 * Insert or update seeded blog articles (idempotent per version).
 */
function ogc_seed_blog_articles() {
	$stored = get_option( 'ogc_articles_seed_version', '' );
	if ( OGC_ARTICLES_SEED_VERSION === $stored ) {
		return;
	}

	ogc_ensure_blog_categories();

	foreach ( ogc_seed_article_definitions() as $article ) {
		$path = OGC_THEME_DIR . '/' . ltrim( $article['source'], '/' );
		if ( ! is_readable( $path ) ) {
			continue;
		}

		$markdown = file_get_contents( $path );
		if ( false === $markdown || '' === trim( $markdown ) ) {
			continue;
		}

		$content = ogc_article_markdown_to_html( $markdown );
		$existing = get_page_by_path( $article['slug'], OBJECT, 'post' );

		$postarr = array(
			'post_title'   => $article['title'],
			'post_name'    => $article['slug'],
			'post_content' => $content,
			'post_excerpt' => $article['excerpt'],
			'post_status'  => 'publish',
			'post_type'    => 'post',
			'post_author'  => 1,
		);

		if ( $existing instanceof WP_Post ) {
			$postarr['ID'] = (int) $existing->ID;
			$post_id       = wp_update_post( $postarr, true );
		} else {
			$post_id = wp_insert_post( $postarr, true );
		}

		if ( is_wp_error( $post_id ) || ! $post_id ) {
			continue;
		}

		$term = get_term_by( 'slug', $article['category'], 'category' );
		if ( $term && ! is_wp_error( $term ) ) {
			wp_set_object_terms( (int) $post_id, array( (int) $term->term_id ), 'category' );
		}
	}

	update_option( 'ogc_articles_seed_version', OGC_ARTICLES_SEED_VERSION );
}
add_action( 'init', 'ogc_seed_blog_articles', 30 );
