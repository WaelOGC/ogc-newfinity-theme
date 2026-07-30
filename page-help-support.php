<?php
/**
 * Template Name: Help & Support
 *
 * Help & Support page template — intended slug: support (/support/).
 * Also loaded via page-support.php for WordPress slug hierarchy.
 *
 * @package OGC_NewFinity
 */

get_header();

$contact_url = home_url( '/contact/' );
$mail_url    = 'mailto:contact@ogcnewfinity.com';

$categories = array(
	array(
		'icon'  => 'support',
		'title' => 'General',
		'text'  => 'Services, response times, consultations, and where we work.',
	),
	array(
		'icon'  => 'code',
		'title' => 'Technical Support',
		'text'  => 'Post-launch help, maintenance, fixes, and project updates.',
	),
	array(
		'icon'  => 'collab',
		'title' => 'Collaboration',
		'text'  => 'How we communicate, milestones, and staying in the loop.',
	),
);

$faq_sections = array(
	array(
		'id'    => 'faq-general',
		'icon'  => 'support',
		'title' => 'General',
		'items' => array(
			array(
				'q' => 'What services does OGC NewFinity offer?',
				'a' => 'We cover six core capabilities: software and app development, custom WordPress builds, UI/UX design, marketing and strategy consulting, cybersecurity, and AI solutions. Every engagement is scoped to what you actually need — not a one-size-fits-all package.',
			),
			array(
				'q' => 'How quickly will I get a first response?',
				'a' => 'Every inquiry gets a real, considered reply within 48 hours — guaranteed. That includes Submit Your Idea submissions, contact messages, and support questions.',
			),
			array(
				'q' => 'Is the first consultation free?',
				'a' => 'Yes. Your first conversation with us is free and confidential. We use it to understand your goals, constraints, and whether we\'re the right fit — with no obligation to continue.',
			),
			array(
				'q' => 'Where are you based, and do you work with clients outside the Netherlands?',
				'a' => 'We\'re based in The Hague, Netherlands, and work with clients across the EU and internationally. Most collaboration happens online; we adapt meeting times and delivery to what works for your team.',
			),
		),
	),
	array(
		'id'    => 'faq-technical',
		'icon'  => 'code',
		'title' => 'Technical Support',
		'items' => array(
			array(
				'q' => 'What does post-launch support look like?',
				'a' => 'We don\'t disappear after go-live. Post-launch support is part of how we work — monitoring for issues, answering questions, and helping you settle into the new system. Exact coverage is agreed in your project scope so expectations stay clear.',
			),
			array(
				'q' => 'Do you offer regular maintenance?',
				'a' => 'Yes. We can set up ongoing maintenance for updates, security patches, performance checks, and small improvements. Maintenance is scoped separately from the initial build so you only pay for the cadence you need.',
			),
			array(
				'q' => 'What happens if something breaks after delivery?',
				'a' => 'Contact us as soon as you notice an issue. We\'ll diagnose the cause, prioritize a fix, and keep you informed. Defects covered by your warranty or support agreement are handled under those terms; anything outside that scope is quoted before work starts.',
			),
			array(
				'q' => 'Can I request updates on a project you already delivered?',
				'a' => 'Absolutely. Former and current clients can request enhancements, new features, or content changes on earlier work. We\'ll review the request, confirm effort and timeline, and schedule it without forcing you into a new sales process from scratch.',
			),
		),
	),
	array(
		'id'    => 'faq-collaboration',
		'icon'  => 'collab',
		'title' => 'Collaboration',
		'items' => array(
			array(
				'q' => 'How does communication work during a project?',
				'a' => 'You work directly with the people building your project — not a relay through account managers. We agree channels and check-in rhythm up front so decisions stay fast and context never gets lost in translation.',
			),
			array(
				'q' => 'Do you work in defined milestones?',
				'a' => 'Yes. Projects are broken into clear milestones with deliverables, review points, and next steps. That keeps scope honest, timelines visible, and progress measurable for both sides.',
			),
			array(
				'q' => 'Who is my main point of contact?',
				'a' => 'You get a named lead who stays with the project from scoping through delivery. They\'re your day-to-day contact for questions, feedback, and decisions — backed by the specialists actually doing the work.',
			),
			array(
				'q' => 'Can I check project progress at any time?',
				'a' => 'Yes. You\'re never left guessing. We share status against the agreed milestones, and you can ask for an update whenever you need one — between scheduled check-ins included.',
			),
		),
	),
);
?>

<article class="ogc-page ogc-page--support">

	<section class="ogc-page-hero ogc-split-hero" aria-labelledby="ogc-support-hero-heading">
		<div class="ogc-split-hero__copy">
			<p class="ogc-badge"><?php esc_html_e( 'OGC NEWFINITY', 'ogc-newfinity' ); ?></p>
			<h1 id="ogc-support-hero-heading" class="ogc-page-hero__title">
				<?php esc_html_e( 'Help &', 'ogc-newfinity' ); ?>
				<span class="hero-shimmer"><?php esc_html_e( 'Support', 'ogc-newfinity' ); ?></span>
			</h1>
			<p class="ogc-page-hero__lead">
				<?php esc_html_e( 'Browse FAQs · 48h Response Guaranteed', 'ogc-newfinity' ); ?>
			</p>
		</div>
		<div class="ogc-split-hero__visual">
			<?php get_template_part( 'template-parts/hero', 'support-network' ); ?>
		</div>
	</section>

	<section class="ogc-page-pillars" aria-labelledby="ogc-support-categories-heading">
		<h2 id="ogc-support-categories-heading" class="screen-reader-text">
			<?php esc_html_e( 'Support categories', 'ogc-newfinity' ); ?>
		</h2>

		<div class="ogc-page-pillars__grid">
			<?php foreach ( $categories as $category ) : ?>
				<div class="glass-card ogc-pillar-card">
					<?php
					ogc_icon(
						$category['icon'],
						array(
							'size'  => 'lg',
							'class' => 'ogc-pillar-card__icon',
						)
					);
					?>
					<h3 class="ogc-pillar-card__title"><?php echo esc_html( $category['title'] ); ?></h3>
					<p class="ogc-pillar-card__text"><?php echo esc_html( $category['text'] ); ?></p>
				</div>
			<?php endforeach; ?>
		</div>
	</section>

	<section class="ogc-support-faqs" aria-labelledby="ogc-support-faqs-heading" data-ogc-faq-filter>
		<header class="ogc-section-header">
			<h2 id="ogc-support-faqs-heading" class="ogc-section-title">
				<?php esc_html_e( 'Frequently Asked Questions', 'ogc-newfinity' ); ?>
			</h2>
			<p class="ogc-section-intro">
				<?php esc_html_e( 'Search by keyword, or browse by category below.', 'ogc-newfinity' ); ?>
			</p>
		</header>

		<div class="ogc-support-search">
			<label class="screen-reader-text" for="ogc-faq-search">
				<?php esc_html_e( 'Browse FAQs', 'ogc-newfinity' ); ?>
			</label>
			<input
				type="search"
				id="ogc-faq-search"
				class="ogc-support-search__input"
				placeholder="<?php echo esc_attr__( 'Browse FAQs', 'ogc-newfinity' ); ?>"
				autocomplete="off"
			>
		</div>

		<p class="ogc-support-search__empty" data-ogc-faq-empty hidden>
			<?php esc_html_e( 'No matching questions. Try a different keyword, or clear the search to see all FAQs.', 'ogc-newfinity' ); ?>
		</p>

		<?php foreach ( $faq_sections as $section ) : ?>
			<div class="ogc-support-faq-group" id="<?php echo esc_attr( $section['id'] ); ?>" data-ogc-faq-group>
				<header class="ogc-support-faq-group__header">
					<?php
					ogc_icon(
						$section['icon'],
						array(
							'size'  => 'md',
							'class' => 'ogc-support-faq-group__icon',
						)
					);
					?>
					<h3 class="ogc-support-faq-group__title"><?php echo esc_html( $section['title'] ); ?></h3>
				</header>

				<div class="ogc-faq-list">
					<?php foreach ( $section['items'] as $index => $item ) : ?>
						<?php
						$panel_id = $section['id'] . '-panel-' . (string) ( $index + 1 );
						$btn_id   = $section['id'] . '-btn-' . (string) ( $index + 1 );
						?>
						<div class="ogc-faq-item glass-card" data-ogc-faq-item data-question="<?php echo esc_attr( strtolower( $item['q'] ) ); ?>">
							<button
								type="button"
								id="<?php echo esc_attr( $btn_id ); ?>"
								class="ogc-faq-item__trigger"
								aria-expanded="false"
								aria-controls="<?php echo esc_attr( $panel_id ); ?>"
							>
								<span class="ogc-faq-item__question"><?php echo esc_html( $item['q'] ); ?></span>
								<span class="ogc-faq-item__chevron" aria-hidden="true"></span>
							</button>
							<div
								id="<?php echo esc_attr( $panel_id ); ?>"
								class="ogc-faq-item__panel"
								role="region"
								aria-labelledby="<?php echo esc_attr( $btn_id ); ?>"
								hidden
							>
								<p class="ogc-faq-item__answer"><?php echo esc_html( $item['a'] ); ?></p>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		<?php endforeach; ?>
	</section>

	<section class="ogc-home-cta" aria-labelledby="ogc-support-cta-heading">
		<div class="glass-card ogc-home-cta__inner">
			<h2 id="ogc-support-cta-heading" class="ogc-home-cta__title">
				<?php esc_html_e( 'Didn\'t find your answer?', 'ogc-newfinity' ); ?>
			</h2>
			<p class="ogc-home-cta__text">
				<?php
				esc_html_e(
					'Send us a message — every support question gets a real reply within 48 hours.',
					'ogc-newfinity'
				);
				?>
			</p>
			<a class="ogc-btn" href="<?php echo esc_url( $contact_url ); ?>">
				<?php esc_html_e( 'Get in Touch', 'ogc-newfinity' ); ?>
			</a>
			<p class="ogc-support-cta__mail">
				<a href="<?php echo esc_url( $mail_url ); ?>">contact@ogcnewfinity.com</a>
			</p>
		</div>
	</section>

</article>

<?php
get_footer();
