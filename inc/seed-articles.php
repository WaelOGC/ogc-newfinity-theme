<?php
/**
 * Seed publication-ready blog articles into WordPress posts.
 *
 * @package OGC_NewFinity
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Bump this version string EVERY time any file in inc/seed-content/ changes, or edits will not sync.
define( 'OGC_ARTICLES_SEED_VERSION', '1.9.0' );

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

		// Fenced blocks (svg-diagram or placeholder/callout).
		if ( preg_match( '/^```(\S*)/', $line, $fence_m ) ) {
			$fence  = isset( $fence_m[1] ) ? $fence_m[1] : '';
			$block  = array();
			++$i;
			while ( $i < $count && ! preg_match( '/^```/', $lines[ $i ] ) ) {
				$block[] = $lines[ $i ];
				++$i;
			}
			++$i; // closing fence

			if ( 'svg-diagram' === $fence ) {
				$html[] = ogc_article_svg_diagram_html( $block );
			} else {
				$html[] = ogc_article_placeholder_html( $block );
			}
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
			$level  = strlen( $m[1] );
			$text   = ogc_article_inline( $m[2] );
			$html[] = sprintf( '<h%1$d>%2$s</h%1$d>', $level, $text );
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
 * Inline markdown: links, bold, code.
 *
 * @param string $text Text.
 * @return string
 */
function ogc_article_inline( $text ) {
	$links = array();
	$text  = preg_replace_callback(
		'/\[([^\]]+)\]\(([^)]+)\)/',
		function ( $m ) use ( &$links ) {
			$key           = '%%OGC_LINK_' . count( $links ) . '%%';
			$links[ $key ] = '<a href="' . esc_url( $m[2] ) . '" target="_blank" rel="noopener">' . esc_html( $m[1] ) . '</a>';
			return $key;
		},
		(string) $text
	);

	$text = esc_html( $text );
	$text = preg_replace( '/\*\*(.+?)\*\*/', '<strong>$1</strong>', $text );
	$text = preg_replace( '/`([^`]+)`/', '<code>$1</code>', $text );

	if ( ! empty( $links ) ) {
		$text = str_replace( array_keys( $links ), array_values( $links ), $text );
	}

	return $text;
}

/**
 * Trusted SVG diagram fence → figure HTML (not escaped).
 *
 * @param array $lines SVG source lines.
 * @return string
 */
function ogc_article_svg_diagram_html( $lines ) {
	// Flatten to one line so wpautop cannot inject <p>/<br> into the SVG markup.
	$svg_flat = implode( ' ', array_map( 'trim', $lines ) );
	$svg_flat = preg_replace( '/\s+/', ' ', $svg_flat );
	$svg_flat = trim( (string) $svg_flat );

	$out  = '<figure class="ogc-article-figure ogc-article-figure--diagram">';
	$out .= '<div class="ogc-article-figure__diagram">' . $svg_flat . '</div>';
	$out .= '</figure>';
	return $out;
}

/**
 * Visual placeholder / callout from fenced block lines.
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
		$line   = trim( $line, '|' );
		$cells  = array_map( 'trim', explode( '|', $line ) );
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
			'title'    => 'Building Scalable Full-Stack Architectures',
			'slug'     => 'building-scalable-fullstack-architectures',
			'category' => 'build-development',
			'excerpt'  => 'A technical blueprint for scalable full-stack platforms — concurrency runtimes, PostgreSQL pooling and Redis caching, Dockerized environment parity, and enterprise architecture benchmarks.',
			'source'   => 'inc/seed-content/article-fullstack-architecture.md',
			'seed_key' => 'article-fullstack-architecture',
			'author'   => 'Wael Safan',
		),
		array(
			'title'    => 'Cloud vs On-Premise: Choosing the Right Architecture',
			'slug'     => 'cloud-vs-onprem-choosing-the-right-architecture',
			'category' => 'build-development',
			'excerpt'  => 'A practical engineering comparison of Public Cloud, On-Premise Bare-Metal, and Hybrid Cloud — elasticity, latency, total cost of ownership, and disaster recovery.',
			'source'   => 'inc/seed-content/article-cloud-vs-onprem.md',
			'seed_key' => 'article-cloud-vs-onprem',
			'author'   => 'Wael Safan',
		),
		array(
			'title'    => 'Engineering Conversion-Driven UI/UX',
			'slug'     => 'engineering-conversion-driven-ui-ux',
			'category' => 'design-experience',
			'excerpt'  => 'How UX logic, structural visual hierarchy, and frontend performance combine to engineer digital interfaces that actually convert.',
			'source'   => 'inc/seed-content/article-conversion-driven-ui-ux.md',
			'seed_key' => 'article-conversion-driven-ui-ux',
			'author'   => 'Wael Safan',
		),
		array(
			'title'    => 'Dark Mode as Strategic Brand Identity',
			'slug'     => 'dark-mode-strategic-brand-identity',
			'category' => 'design-experience',
			'excerpt'  => 'Why dark mode, visual layering, and geometric branding function as strategic brand positioning tools for technology companies — not just aesthetic trends.',
			'source'   => 'inc/seed-content/article-dark-mode-brand-identity.md',
			'seed_key' => 'article-dark-mode-brand-identity',
			'author'   => 'Wael Safan',
		),
		array(
			'title'    => 'Engineering Event-Driven Automation',
			'slug'     => 'engineering-event-driven-automation',
			'category' => 'strategy-growth',
			'excerpt'  => 'How event-driven automation architecture — webhooks, validated pipelines, and resilient retry queues — replaces manual operational overhead at scale.',
			'source'   => 'inc/seed-content/article-event-driven-automation.md',
			'seed_key' => 'article-event-driven-automation',
			'author'   => 'Wael Safan',
		),
		array(
			'title'    => 'Engineering Sustainable SEO Authority',
			'slug'     => 'engineering-sustainable-seo-authority',
			'category' => 'strategy-growth',
			'excerpt'  => 'How topic cluster architecture and E-E-A-T content principles combine to build durable organic search authority for B2B technology platforms.',
			'source'   => 'inc/seed-content/article-seo-authority.md',
			'seed_key' => 'article-seo-authority',
			'author'   => 'Wael Safan',
		),
		array(
			'title'    => 'Engineering Multi-Model AI Pipelines',
			'slug'     => 'engineering-multi-model-ai-pipelines',
			'category' => 'ai-innovation',
			'excerpt'  => 'How multi-model orchestration, structured data extraction, and RAG pipelines turn AI from a chat widget into a production-grade backend component.',
			'source'   => 'inc/seed-content/article-multi-model-ai-pipelines.md',
			'seed_key' => 'article-multi-model-ai-pipelines',
			'author'   => 'Wael Safan',
		),
		array(
			'title'    => 'Engineering the AI-Driven Web',
			'slug'     => 'engineering-the-ai-driven-web',
			'category' => 'ai-innovation',
			'excerpt'  => 'How intent-based routing and streaming UI are reshaping web architecture — moving from static templates to context-aware, AI-driven platforms.',
			'source'   => 'inc/seed-content/article-ai-driven-web.md',
			'seed_key' => 'article-ai-driven-web',
			'author'   => 'Wael Safan',
		),
	);
}

/**
 * Find an existing seeded post by permanent seed key (with legacy slug fallback).
 *
 * @param array $article Article definition.
 * @return int Post ID or 0.
 */
function ogc_find_seeded_article_id( $article ) {
	$seed_key = isset( $article['seed_key'] ) ? (string) $article['seed_key'] : '';
	if ( $seed_key ) {
		$query = new WP_Query(
			array(
				'post_type'              => 'post',
				'post_status'            => 'any',
				'meta_key'               => '_ogc_seed_key',
				'meta_value'             => $seed_key,
				'posts_per_page'         => 1,
				'fields'                 => 'ids',
				'no_found_rows'          => true,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
			)
		);
		if ( ! empty( $query->posts[0] ) ) {
			return (int) $query->posts[0];
		}
	}

	// One-time migration: locate pre-seed-key posts by current or legacy slug.
	$slugs = array_filter(
		array(
			isset( $article['slug'] ) ? $article['slug'] : '',
			'engineering-high-performance-full-stack-architectures',
		)
	);
	foreach ( array_unique( $slugs ) as $slug ) {
		$existing = get_page_by_path( $slug, OBJECT, 'post' );
		if ( $existing instanceof WP_Post ) {
			return (int) $existing->ID;
		}
	}

	return 0;
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

	// Front-end init often has no unfiltered_html — without this, wp_kses strips <svg>.
	$kses_removed = false;
	if ( function_exists( 'kses_remove_filters' ) ) {
		kses_remove_filters();
		$kses_removed = true;
	}

	foreach ( ogc_seed_article_definitions() as $article ) {
		$path = OGC_THEME_DIR . '/' . ltrim( $article['source'], '/' );
		if ( ! is_readable( $path ) ) {
			continue;
		}

		$markdown = file_get_contents( $path );
		if ( false === $markdown || '' === trim( $markdown ) ) {
			continue;
		}

		$content     = ogc_article_markdown_to_html( $markdown );
		$existing_id = ogc_find_seeded_article_id( $article );

		$postarr = array(
			'post_title'   => $article['title'],
			'post_name'    => $article['slug'],
			'post_content' => $content,
			'post_excerpt' => $article['excerpt'],
			'post_status'  => 'publish',
			'post_type'    => 'post',
			'post_author'  => 1,
		);

		if ( $existing_id > 0 ) {
			$postarr['ID'] = $existing_id;
			$post_id       = wp_update_post( $postarr, true );
		} else {
			$post_id = wp_insert_post( $postarr, true );
		}

		if ( is_wp_error( $post_id ) || ! $post_id ) {
			continue;
		}

		if ( ! empty( $article['seed_key'] ) ) {
			update_post_meta( (int) $post_id, '_ogc_seed_key', $article['seed_key'] );
		}

		if ( ! empty( $article['author'] ) ) {
			update_post_meta( (int) $post_id, '_ogc_article_author', $article['author'] );
		}

		$term = get_term_by( 'slug', $article['category'], 'category' );
		if ( $term && ! is_wp_error( $term ) ) {
			wp_set_object_terms( (int) $post_id, array( (int) $term->term_id ), 'category' );
		}
	}

	if ( $kses_removed && function_exists( 'kses_init_filters' ) ) {
		kses_init_filters();
	}

	update_option( 'ogc_articles_seed_version', OGC_ARTICLES_SEED_VERSION );
}
add_action( 'init', 'ogc_seed_blog_articles', 30 );
