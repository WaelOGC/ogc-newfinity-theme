<?php
/**
 * Like + share controls for single posts.
 *
 * @package OGC_NewFinity
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$post_id    = get_the_ID();
$like_count = ogc_get_like_count( $post_id );
$liked      = ogc_visitor_has_liked( $post_id );
$permalink  = get_permalink( $post_id );
$title      = get_the_title( $post_id );

$share_x = add_query_arg(
	array(
		'url'  => $permalink,
		'text' => $title,
	),
	'https://twitter.com/intent/tweet'
);

$share_fb = add_query_arg(
	array( 'u' => $permalink ),
	'https://www.facebook.com/sharer/sharer.php'
);

$share_li = add_query_arg(
	array( 'url' => $permalink ),
	'https://www.linkedin.com/sharing/share-offsite/'
);

$share_reddit = add_query_arg(
	array(
		'url'   => $permalink,
		'title' => $title,
	),
	'https://www.reddit.com/submit'
);
?>

<div class="ogc-engage" data-post-id="<?php echo esc_attr( (string) $post_id ); ?>">
	<button
		type="button"
		class="ogc-engage__like<?php echo $liked ? ' is-liked' : ''; ?>"
		data-ogc-like
		aria-pressed="<?php echo $liked ? 'true' : 'false'; ?>"
		aria-label="<?php echo $liked ? esc_attr__( 'Liked', 'ogc-newfinity' ) : esc_attr__( 'Like this article', 'ogc-newfinity' ); ?>"
		<?php disabled( $liked ); ?>
	>
		<?php
		ogc_icon(
			'heart',
			array(
				'size'  => 'sm',
				'shape' => 'circle',
				'class' => 'ogc-engage__like-icon',
			)
		);
		?>
		<span class="ogc-engage__like-count" data-ogc-like-count><?php echo esc_html( (string) $like_count ); ?></span>
		<span class="ogc-engage__like-label"><?php echo $liked ? esc_html__( 'Liked', 'ogc-newfinity' ) : esc_html__( 'Like', 'ogc-newfinity' ); ?></span>
	</button>

	<div class="ogc-engage__share" role="group" aria-label="<?php esc_attr_e( 'Share this article', 'ogc-newfinity' ); ?>">
		<span class="ogc-engage__share-label"><?php esc_html_e( 'Share', 'ogc-newfinity' ); ?></span>

		<a
			class="ogc-engage__share-btn"
			href="<?php echo esc_url( $share_x ); ?>"
			target="_blank"
			rel="noopener noreferrer"
			aria-label="<?php esc_attr_e( 'Share on X', 'ogc-newfinity' ); ?>"
		>
			<?php ogc_share_icon_chip( 'x' ); ?>
		</a>

		<a
			class="ogc-engage__share-btn"
			href="<?php echo esc_url( $share_fb ); ?>"
			target="_blank"
			rel="noopener noreferrer"
			aria-label="<?php esc_attr_e( 'Share on Facebook', 'ogc-newfinity' ); ?>"
		>
			<?php ogc_share_icon_chip( 'facebook' ); ?>
		</a>

		<a
			class="ogc-engage__share-btn"
			href="<?php echo esc_url( $share_li ); ?>"
			target="_blank"
			rel="noopener noreferrer"
			aria-label="<?php esc_attr_e( 'Share on LinkedIn', 'ogc-newfinity' ); ?>"
		>
			<?php ogc_share_icon_chip( 'linkedin' ); ?>
		</a>

		<a
			class="ogc-engage__share-btn"
			href="<?php echo esc_url( $share_reddit ); ?>"
			target="_blank"
			rel="noopener noreferrer"
			aria-label="<?php esc_attr_e( 'Share on Reddit', 'ogc-newfinity' ); ?>"
		>
			<?php ogc_share_icon_chip( 'reddit' ); ?>
		</a>

		<button
			type="button"
			class="ogc-engage__share-btn"
			data-ogc-copy-link
			data-url="<?php echo esc_url( $permalink ); ?>"
			aria-label="<?php esc_attr_e( 'Copy link', 'ogc-newfinity' ); ?>"
		>
			<?php ogc_share_icon_chip( 'copy' ); ?>
			<span class="ogc-engage__toast" data-ogc-copy-toast hidden><?php esc_html_e( 'Copied!', 'ogc-newfinity' ); ?></span>
		</button>
	</div>
</div>
