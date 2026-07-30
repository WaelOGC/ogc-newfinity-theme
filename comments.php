<?php
/**
 * Custom comments template — simplified form, numbered pagination.
 *
 * @package OGC_NewFinity
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( post_password_required() ) {
	return;
}
?>

<section id="comments" class="ogc-comments">

	<?php if ( have_comments() ) : ?>
		<h2 class="ogc-comments__title">
			<?php
			$count = get_comments_number();
			printf(
				/* translators: %d: number of comments */
				esc_html( _n( '%d Comment', '%d Comments', $count, 'ogc-newfinity' ) ),
				(int) $count
			);
			?>
		</h2>

		<ol class="ogc-comments__list">
			<?php
			wp_list_comments(
				array(
					'style'       => 'ol',
					'short_ping'  => true,
					'avatar_size' => 40,
					'callback'    => 'ogc_comment_list_item',
				)
			);
			?>
		</ol>

		<?php
		$pagination = paginate_comments_links(
			array(
				'echo'      => false,
				'type'      => 'list',
				'prev_text' => __( 'Prev', 'ogc-newfinity' ),
				'next_text' => __( 'Next', 'ogc-newfinity' ),
				'mid_size'  => 1,
				'end_size'  => 1,
			)
		);

		if ( $pagination ) :
			?>
			<nav class="ogc-comments__pagination" aria-label="<?php esc_attr_e( 'Comment pages', 'ogc-newfinity' ); ?>">
				<?php echo wp_kses_post( $pagination ); ?>
			</nav>
		<?php endif; ?>

	<?php endif; ?>

	<?php if ( ! comments_open() && get_comments_number() ) : ?>
		<p class="ogc-comments__closed"><?php esc_html_e( 'Comments are closed.', 'ogc-newfinity' ); ?></p>
	<?php endif; ?>

	<?php
	$commenter = wp_get_current_commenter();

	comment_form(
		array(
			'title_reply'          => __( 'Leave a comment', 'ogc-newfinity' ),
			'title_reply_before'   => '<h2 id="reply-title" class="ogc-comments__form-title">',
			'title_reply_after'    => '</h2>',
			'class_form'           => 'ogc-comment-form',
			'class_submit'         => 'ogc-btn',
			'label_submit'         => __( 'Post comment', 'ogc-newfinity' ),
			'submit_button'        => '<button name="%1$s" type="submit" id="%2$s" class="%3$s">%4$s</button>',
			'submit_field'         => '<p class="ogc-comment-form__submit">%1$s %2$s</p>',
			'comment_notes_before' => '<p class="ogc-comment-form__note">' . esc_html__( 'Email is required. Comments are moderated before they appear.', 'ogc-newfinity' ) . '</p>',
			'comment_notes_after'  => '',
			'fields'               => array(
				'author' => sprintf(
					'<p class="ogc-comment-form__field"><label for="author">%1$s <span class="ogc-comment-form__optional">(%2$s)</span></label><input id="author" name="author" type="text" value="%3$s" size="30" autocomplete="name"></p>',
					esc_html__( 'Name', 'ogc-newfinity' ),
					esc_html__( 'optional', 'ogc-newfinity' ),
					esc_attr( $commenter['comment_author'] )
				),
				'email'  => sprintf(
					'<p class="ogc-comment-form__field"><label for="email">%1$s <span class="required" aria-hidden="true">*</span></label><input id="email" name="email" type="email" value="%2$s" size="30" required autocomplete="email"></p>',
					esc_html__( 'Email', 'ogc-newfinity' ),
					esc_attr( $commenter['comment_author_email'] )
				),
				// URL field intentionally omitted.
			),
			'comment_field'        => sprintf(
				'<p class="ogc-comment-form__field ogc-comment-form__field--comment"><label for="comment">%1$s <span class="required" aria-hidden="true">*</span></label><textarea id="comment" name="comment" cols="45" rows="5" required placeholder="%2$s"></textarea></p>',
				esc_html__( 'Message', 'ogc-newfinity' ),
				esc_attr__( 'Share your thoughts — links aren’t allowed.', 'ogc-newfinity' )
			),
		)
	);
	?>

</section>
