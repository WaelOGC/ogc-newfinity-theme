<?php
/**
 * Public pricing catalogue data (mockup-aligned).
 *
 * @package OGC_NewFinity
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Service-line pricing: packages, extras, and notes.
 *
 * @return array<int, array<string, mixed>>
 */
function ogc_get_pricing_data() {
	return array(
		array(
			'slug'  => 'custom-wordpress-development',
			'icon'  => 'wordpress',
			'title' => __( 'Custom WordPress Development', 'ogc-newfinity' ),
			'intro' => __( 'Fixed-price websites, monthly care plans and repair work.', 'ogc-newfinity' ),
			'packages' => array(
				array(
					'name'          => __( 'Starter Website', 'ogc-newfinity' ),
					'fit'           => __( 'A first professional site for a small business.', 'ogc-newfinity' ),
					'price_prefix'  => '',
					'price'         => '€549',
					'unit'          => __( 'one-off', 'ogc-newfinity' ),
					'popular'       => false,
					'features'      => array(
						__( 'Up to 5 standard pages', 'ogc-newfinity' ),
						__( 'Responsive WordPress setup', 'ogc-newfinity' ),
						__( 'One approved design direction', 'ogc-newfinity' ),
						__( 'Contact form', 'ogc-newfinity' ),
						__( 'Basic on-page SEO', 'ogc-newfinity' ),
						__( 'Essential security configuration', 'ogc-newfinity' ),
						__( 'Launch checklist', 'ogc-newfinity' ),
						__( '2 revision rounds', 'ogc-newfinity' ),
					),
				),
				array(
					'name'          => __( 'Business Website', 'ogc-newfinity' ),
					'fit'           => __( 'A brand-aligned site that connects to your tools.', 'ogc-newfinity' ),
					'price_prefix'  => '',
					'price'         => '€995',
					'unit'          => __( 'one-off', 'ogc-newfinity' ),
					'popular'       => true,
					'features'      => array(
						__( 'Up to 10 pages', 'ogc-newfinity' ),
						__( 'Brand-aligned design', 'ogc-newfinity' ),
						__( 'Responsive layouts', 'ogc-newfinity' ),
						__( 'Performance baseline', 'ogc-newfinity' ),
						__( 'Analytics and Search Console connection', 'ogc-newfinity' ),
						__( 'Basic on-page SEO and contact form', 'ogc-newfinity' ),
						__( 'Up to 2 standard integrations', 'ogc-newfinity' ),
						__( '2 revision rounds', 'ogc-newfinity' ),
					),
				),
				array(
					'name'          => __( 'Professional Website', 'ogc-newfinity' ),
					'fit'           => __( 'Custom layouts and features, scoped with you.', 'ogc-newfinity' ),
					'price_prefix'  => __( 'FROM', 'ogc-newfinity' ),
					'price'         => '€1,995',
					'unit'          => __( 'one-off', 'ogc-newfinity' ),
					'popular'       => false,
					'features'      => array(
						__( 'Custom page layouts', 'ogc-newfinity' ),
						__( 'Advanced forms, booking or catalogue features', 'ogc-newfinity' ),
						__( 'Up to 3 agreed integrations', 'ogc-newfinity' ),
						__( 'Enhanced performance and technical SEO', 'ogc-newfinity' ),
						__( 'Staging and launch support', 'ogc-newfinity' ),
						__( 'Scope-based testing', 'ogc-newfinity' ),
					),
				),
			),
			'extras' => array(
				array(
					'title' => __( 'Add-ons', 'ogc-newfinity' ),
					'rows'  => array(
						array( 'label' => __( 'Additional standard page', 'ogc-newfinity' ), 'price' => '€60–€100' ),
						array( 'label' => __( 'Copywriting, one page', 'ogc-newfinity' ), 'price' => __( 'From €75', 'ogc-newfinity' ) ),
						array( 'label' => __( 'Website migration', 'ogc-newfinity' ), 'price' => __( 'From €250', 'ogc-newfinity' ) ),
						array( 'label' => __( 'Multilingual setup, per language', 'ogc-newfinity' ), 'price' => __( 'From €250', 'ogc-newfinity' ) ),
						array( 'label' => __( 'Booking integration', 'ogc-newfinity' ), 'price' => __( 'From €250', 'ogc-newfinity' ) ),
						array( 'label' => __( 'WooCommerce starter store', 'ogc-newfinity' ), 'price' => __( 'Custom quote', 'ogc-newfinity' ) ),
					),
				),
				array(
					'title' => __( 'WordPress Care plans', 'ogc-newfinity' ),
					'rows'  => array(
						array( 'label' => __( 'Care Basic: updates, backup, uptime check', 'ogc-newfinity' ), 'price' => '€39/month' ),
						array( 'label' => __( 'Care Plus: adds security monitoring, 30 min changes', 'ogc-newfinity' ), 'price' => '€59/month' ),
						array( 'label' => __( 'Care Pro: priority, 90 min changes, monthly review', 'ogc-newfinity' ), 'price' => '€99/month' ),
					),
				),
				array(
					'title' => __( 'Fix & Repair', 'ogc-newfinity' ),
					'rows'  => array(
						array( 'label' => __( 'Diagnosis', 'ogc-newfinity' ), 'price' => '€79' ),
						array( 'label' => __( 'Standard repair', 'ogc-newfinity' ), 'price' => '€119' ),
						array( 'label' => __( 'Malware cleanup', 'ogc-newfinity' ), 'price' => '€199' ),
						array( 'label' => __( 'Complex incident or recovery', 'ogc-newfinity' ), 'price' => __( 'Custom quote', 'ogc-newfinity' ) ),
					),
				),
			),
			'notes' => array(
				__( 'Hosting is billed separately or listed clearly as an add-on.', 'ogc-newfinity' ),
				__( 'Standard malware cleanup is €199; complex recovery work is quoted separately after we assess the infection and your backups.', 'ogc-newfinity' ),
			),
		),
		array(
			'slug'  => 'software-app-development',
			'icon'  => 'code',
			'title' => __( 'Software & App Development', 'ogc-newfinity' ),
			'intro' => __( 'Custom software starts with a discovery session, then a milestone-based quote.', 'ogc-newfinity' ),
			'packages' => array(
				array(
					'name'         => __( 'Technical Discovery', 'ogc-newfinity' ),
					'fit'          => __( 'Know what to build and what it will cost.', 'ogc-newfinity' ),
					'price_prefix' => '',
					'price'        => '€450',
					'unit'         => __( 'fixed', 'ogc-newfinity' ),
					'popular'      => true,
					'features'     => array(
						__( 'Requirements workshop', 'ogc-newfinity' ),
						__( 'Problem definition and user roles', 'ogc-newfinity' ),
						__( 'Feature list', 'ogc-newfinity' ),
						__( 'Integration review', 'ogc-newfinity' ),
						__( 'Technical approach', 'ogc-newfinity' ),
						__( 'Estimate and plan', 'ogc-newfinity' ),
					),
				),
				array(
					'name'         => __( 'Focused MVP', 'ogc-newfinity' ),
					'fit'          => __( 'A usable, testable first release.', 'ogc-newfinity' ),
					'price_prefix' => '',
					'price'        => __( 'Custom quote', 'ogc-newfinity' ),
					'unit'         => __( 'typically from €5,000', 'ogc-newfinity' ),
					'popular'      => false,
					'features'     => array(
						__( 'Tightly scoped first release', 'ogc-newfinity' ),
						__( 'Agreed core workflow', 'ogc-newfinity' ),
						__( 'Backend and data layer where needed', 'ogc-newfinity' ),
						__( 'Testing', 'ogc-newfinity' ),
						__( 'Deployment plan', 'ogc-newfinity' ),
					),
				),
				array(
					'name'         => __( 'Custom Web Application', 'ogc-newfinity' ),
					'fit'          => __( 'A full application built around your process.', 'ogc-newfinity' ),
					'price_prefix' => '',
					'price'        => __( 'Custom quote', 'ogc-newfinity' ),
					'unit'         => __( 'typically from €10,000', 'ogc-newfinity' ),
					'popular'      => false,
					'features'     => array(
						__( 'Authentication and user roles', 'ogc-newfinity' ),
						__( 'Database', 'ogc-newfinity' ),
						__( 'Admin interface', 'ogc-newfinity' ),
						__( 'Workflows', 'ogc-newfinity' ),
						__( 'Agreed integrations', 'ogc-newfinity' ),
					),
				),
			),
			'extras' => array(
				array(
					'title' => __( 'Also available', 'ogc-newfinity' ),
					'rows'  => array(
						array( 'label' => __( 'Clickable prototype or proof of concept', 'ogc-newfinity' ), 'price' => __( 'From €1,500', 'ogc-newfinity' ) ),
						array( 'label' => __( 'API development and integration', 'ogc-newfinity' ), 'price' => __( 'From €450', 'ogc-newfinity' ) ),
					),
				),
				array(
					'title' => __( 'Payment milestones', 'ogc-newfinity' ),
					'rows'  => array(
						array( 'label' => __( 'Project start', 'ogc-newfinity' ), 'price' => '40%' ),
						array( 'label' => __( 'Agreed milestone', 'ogc-newfinity' ), 'price' => '40%' ),
						array( 'label' => __( 'Acceptance and handover', 'ogc-newfinity' ), 'price' => '20%' ),
					),
				),
			),
			'notes' => array(
				__( 'A prototype demonstrates an idea; it is not production-ready software.', 'ogc-newfinity' ),
				__( 'Hosting, cloud services, external API fees, licences and maintenance are separate.', 'ogc-newfinity' ),
			),
		),
		array(
			'slug'  => 'ui-ux-design',
			'icon'  => 'design',
			'title' => __( 'UI/UX Design', 'ogc-newfinity' ),
			'intro' => __( 'Screen-based design packages with clear revision rounds.', 'ogc-newfinity' ),
			'packages' => array(
				array(
					'name'         => __( 'UI Starter', 'ogc-newfinity' ),
					'fit'          => __( 'A visual direction for a few key screens.', 'ogc-newfinity' ),
					'price_prefix' => '',
					'price'        => '€350',
					'unit'         => __( 'one-off', 'ogc-newfinity' ),
					'popular'      => false,
					'features'     => array(
						__( 'Up to 3 screens or page layouts', 'ogc-newfinity' ),
						__( 'Visual direction', 'ogc-newfinity' ),
						__( 'Responsive considerations', 'ogc-newfinity' ),
						__( '1 revision round', 'ogc-newfinity' ),
					),
				),
				array(
					'name'         => __( 'UI/UX Business', 'ogc-newfinity' ),
					'fit'          => __( 'Flows, wireframes and polished UI.', 'ogc-newfinity' ),
					'price_prefix' => '',
					'price'        => '€850',
					'unit'         => __( 'one-off', 'ogc-newfinity' ),
					'popular'      => true,
					'features'     => array(
						__( 'Up to 8 key screens', 'ogc-newfinity' ),
						__( 'Basic user flow', 'ogc-newfinity' ),
						__( 'Wireframes', 'ogc-newfinity' ),
						__( 'Polished Figma UI', 'ogc-newfinity' ),
						__( '2 revision rounds', 'ogc-newfinity' ),
					),
				),
				array(
					'name'         => __( 'Product Design Advanced', 'ogc-newfinity' ),
					'fit'          => __( 'From workshop to developer handoff.', 'ogc-newfinity' ),
					'price_prefix' => __( 'FROM', 'ogc-newfinity' ),
					'price'        => '€1,500',
					'unit'         => __( 'one-off', 'ogc-newfinity' ),
					'popular'      => false,
					'features'     => array(
						__( 'Discovery workshop', 'ogc-newfinity' ),
						__( 'User flows and wireframes', 'ogc-newfinity' ),
						__( 'Interactive prototype', 'ogc-newfinity' ),
						__( 'Responsive states', 'ogc-newfinity' ),
						__( 'Design handoff', 'ogc-newfinity' ),
						__( 'Agreed usability review', 'ogc-newfinity' ),
					),
				),
			),
			'extras' => array(
				array(
					'title' => __( 'Add-ons', 'ogc-newfinity' ),
					'rows'  => array(
						array( 'label' => __( 'Additional screen', 'ogc-newfinity' ), 'price' => '€75–€150' ),
						array( 'label' => __( 'Design system or component library', 'ogc-newfinity' ), 'price' => __( 'From €500', 'ogc-newfinity' ) ),
						array( 'label' => __( 'UX audit, small website', 'ogc-newfinity' ), 'price' => __( 'From €350', 'ogc-newfinity' ) ),
						array( 'label' => __( 'UX audit, web application', 'ogc-newfinity' ), 'price' => __( 'From €595', 'ogc-newfinity' ) ),
						array( 'label' => __( 'Usability testing', 'ogc-newfinity' ), 'price' => __( 'Quoted', 'ogc-newfinity' ) ),
					),
				),
			),
			'notes' => array(
				__( 'User interviews, research and testing are scoped separately from visual design.', 'ogc-newfinity' ),
			),
		),
		array(
			'slug'  => 'marketing-strategy-consulting',
			'icon'  => 'strategy',
			'title' => __( 'Marketing & Strategy Consulting', 'ogc-newfinity' ),
			'intro' => __( 'Local SEO and visibility, managed monthly with defined deliverables.', 'ogc-newfinity' ),
			'packages' => array(
				array(
					'name'         => __( 'Local SEO Essential', 'ogc-newfinity' ),
					'fit'          => __( 'One location, kept active and reported.', 'ogc-newfinity' ),
					'price_prefix' => '',
					'price'        => '€249',
					'unit'         => __( 'per month', 'ogc-newfinity' ),
					'popular'      => false,
					'features'     => array(
						__( 'One location', 'ogc-newfinity' ),
						__( 'Google Business Profile management', 'ogc-newfinity' ),
						__( '2 posts per month', 'ogc-newfinity' ),
						__( 'Review-response guidance', 'ogc-newfinity' ),
						__( 'Basic local keyword tracking', 'ogc-newfinity' ),
						__( 'Monthly report', 'ogc-newfinity' ),
					),
				),
				array(
					'name'         => __( 'Local SEO Growth', 'ogc-newfinity' ),
					'fit'          => __( 'More content and a view on competitors.', 'ogc-newfinity' ),
					'price_prefix' => '',
					'price'        => '€399',
					'unit'         => __( 'per month', 'ogc-newfinity' ),
					'popular'      => true,
					'features'     => array(
						__( 'Everything in Essential', 'ogc-newfinity' ),
						__( '4 posts per month', 'ogc-newfinity' ),
						__( 'One content improvement per month', 'ogc-newfinity' ),
						__( 'Citation consistency checks', 'ogc-newfinity' ),
						__( 'Competitor snapshot', 'ogc-newfinity' ),
						__( 'Monthly recommendations', 'ogc-newfinity' ),
					),
				),
				array(
					'name'         => __( 'SEO & Visibility Pro', 'ogc-newfinity' ),
					'fit'          => __( 'Technical SEO and quarterly strategy.', 'ogc-newfinity' ),
					'price_prefix' => '',
					'price'        => '€649',
					'unit'         => __( 'per month', 'ogc-newfinity' ),
					'popular'      => false,
					'features'     => array(
						__( 'Everything in Growth', 'ogc-newfinity' ),
						__( 'Up to 2 content or page improvements per month', 'ogc-newfinity' ),
						__( 'Technical SEO review', 'ogc-newfinity' ),
						__( 'Broader keyword tracking', 'ogc-newfinity' ),
						__( 'Quarterly strategy review', 'ogc-newfinity' ),
					),
				),
			),
			'extras' => array(
				array(
					'title' => __( 'One-time setup', 'ogc-newfinity' ),
					'rows'  => array(
						array( 'label' => __( 'Local Presence Setup', 'ogc-newfinity' ), 'price' => '€159' ),
						array( 'label' => __( 'Local Growth Setup', 'ogc-newfinity' ), 'price' => '€279' ),
						array( 'label' => __( 'One-off SEO audit', 'ogc-newfinity' ), 'price' => __( 'From €350', 'ogc-newfinity' ) ),
					),
				),
				array(
					'title' => __( 'Social media management', 'ogc-newfinity' ),
					'rows'  => array(
						array( 'label' => __( 'Social Starter: 8 posts, one platform', 'ogc-newfinity' ), 'price' => '€399/month' ),
						array( 'label' => __( 'Social Growth: 12 posts, 2 videos, 2 platforms', 'ogc-newfinity' ), 'price' => '€699/month' ),
						array( 'label' => __( 'Social Performance: multi-channel', 'ogc-newfinity' ), 'price' => __( 'From €1,095/month', 'ogc-newfinity' ) ),
					),
				),
				array(
					'title' => __( 'Add-ons', 'ogc-newfinity' ),
					'rows'  => array(
						array( 'label' => __( 'Service or location landing page', 'ogc-newfinity' ), 'price' => __( 'From €120', 'ogc-newfinity' ) ),
						array( 'label' => __( 'Blog article', 'ogc-newfinity' ), 'price' => __( 'From €120', 'ogc-newfinity' ) ),
						array( 'label' => __( 'Additional business location', 'ogc-newfinity' ), 'price' => __( 'Quoted', 'ogc-newfinity' ) ),
					),
				),
			),
			'notes' => array(
				__( 'We commit to defined deliverables; no one can guarantee a position in Google Search or Maps.', 'ogc-newfinity' ),
				__( 'Advertising spend is paid directly to the platform and is not included.', 'ogc-newfinity' ),
			),
		),
		array(
			'slug'  => 'cybersecurity',
			'icon'  => 'shield',
			'title' => __( 'Cybersecurity', 'ogc-newfinity' ),
			'intro' => __( 'Website security reviews and hardening. Penetration testing is scoped separately.', 'ogc-newfinity' ),
			'packages' => array(
				array(
					'name'         => __( 'Website Security Check', 'ogc-newfinity' ),
					'fit'          => __( 'A clear picture of where your site stands.', 'ogc-newfinity' ),
					'price_prefix' => '',
					'price'        => '€249',
					'unit'         => __( 'one-off', 'ogc-newfinity' ),
					'popular'      => false,
					'features'     => array(
						__( 'Basic external and configuration review', 'ogc-newfinity' ),
						__( 'WordPress and plugin update status', 'ogc-newfinity' ),
						__( 'HTTPS and security-header checks', 'ogc-newfinity' ),
						__( 'Visible exposure review', 'ogc-newfinity' ),
						__( 'Prioritized findings report', 'ogc-newfinity' ),
					),
				),
				array(
					'name'         => __( 'WordPress Protection', 'ogc-newfinity' ),
					'fit'          => __( 'Hardening and recovery readiness.', 'ogc-newfinity' ),
					'price_prefix' => '',
					'price'        => '€499',
					'unit'         => __( 'one-off', 'ogc-newfinity' ),
					'popular'      => true,
					'features'     => array(
						__( 'Agreed WordPress hardening', 'ogc-newfinity' ),
						__( 'Admin and account review', 'ogc-newfinity' ),
						__( 'Backup verification', 'ogc-newfinity' ),
						__( 'Security configuration', 'ogc-newfinity' ),
						__( 'Update and recovery recommendations', 'ogc-newfinity' ),
					),
				),
				array(
					'name'         => __( 'Application Security Review', 'ogc-newfinity' ),
					'fit'          => __( 'A scoped review of a small app or API.', 'ogc-newfinity' ),
					'price_prefix' => __( 'FROM', 'ogc-newfinity' ),
					'price'        => '€1,200',
					'unit'         => __( 'scoped', 'ogc-newfinity' ),
					'popular'      => false,
					'features'     => array(
						__( 'Review of a small application or API', 'ogc-newfinity' ),
						__( 'Architecture and configuration review', 'ogc-newfinity' ),
						__( 'Findings and remediation priorities', 'ogc-newfinity' ),
						__( 'Written scope and authorization', 'ogc-newfinity' ),
					),
				),
			),
			'extras' => array(
				array(
					'title' => __( 'Ongoing and incident work', 'ogc-newfinity' ),
					'rows'  => array(
						array( 'label' => __( 'Security monitoring', 'ogc-newfinity' ), 'price' => __( 'From €49/month', 'ogc-newfinity' ) ),
						array( 'label' => __( 'Incident response and malware remediation', 'ogc-newfinity' ), 'price' => __( 'Custom quote', 'ogc-newfinity' ) ),
						array( 'label' => __( 'Penetration test', 'ogc-newfinity' ), 'price' => __( 'Custom quote', 'ogc-newfinity' ) ),
					),
				),
			),
			'notes' => array(
				__( 'A security check is a point-in-time review, not a penetration test.', 'ogc-newfinity' ),
				__( 'All testing requires written authorization and a defined scope.', 'ogc-newfinity' ),
			),
		),
		array(
			'slug'  => 'ai-solutions',
			'icon'  => 'ai',
			'title' => __( 'AI Solutions', 'ogc-newfinity' ),
			'intro' => __( 'Implementation plus monthly management, with AI usage costs shown openly.', 'ogc-newfinity' ),
			'packages' => array(
				array(
					'name'         => __( 'AI Starter Assistant', 'ogc-newfinity' ),
					'fit'          => __( 'One assistant or workflow, done properly.', 'ogc-newfinity' ),
					'price_prefix' => __( 'SETUP', 'ogc-newfinity' ),
					'price'        => '€595',
					'unit'         => __( '+ €79 per month', 'ogc-newfinity' ),
					'popular'      => false,
					'features'     => array(
						__( 'One narrow website assistant or workflow', 'ogc-newfinity' ),
						__( 'Approved knowledge sources', 'ogc-newfinity' ),
						__( 'Fallback and human handoff', 'ogc-newfinity' ),
						__( 'Basic testing', 'ogc-newfinity' ),
						__( 'Limited monthly monitoring', 'ogc-newfinity' ),
					),
				),
				array(
					'name'         => __( 'AI Business Automation', 'ogc-newfinity' ),
					'fit'          => __( 'A multi-step workflow tied into your tools.', 'ogc-newfinity' ),
					'price_prefix' => __( 'SETUP', 'ogc-newfinity' ),
					'price'        => '€1,500',
					'unit'         => __( '+ €199 per month', 'ogc-newfinity' ),
					'popular'      => true,
					'features'     => array(
						__( 'Multi-step workflow', 'ogc-newfinity' ),
						__( 'Up to 2 agreed integrations', 'ogc-newfinity' ),
						__( 'Exception handling', 'ogc-newfinity' ),
						__( 'Logs and owner notifications', 'ogc-newfinity' ),
						__( 'Testing and monthly review', 'ogc-newfinity' ),
					),
				),
				array(
					'name'         => __( 'Custom AI Agent', 'ogc-newfinity' ),
					'fit'          => __( 'An agent with defined tools and approvals.', 'ogc-newfinity' ),
					'price_prefix' => __( 'SETUP FROM', 'ogc-newfinity' ),
					'price'        => '€2,500',
					'unit'         => __( '+ from €299 per month', 'ogc-newfinity' ),
					'popular'      => false,
					'features'     => array(
						__( 'Custom agent workflow', 'ogc-newfinity' ),
						__( 'Defined tools and permissions', 'ogc-newfinity' ),
						__( 'API integrations', 'ogc-newfinity' ),
						__( 'Approval rules', 'ogc-newfinity' ),
						__( 'Evaluation tests', 'ogc-newfinity' ),
						__( 'Monitoring and maintenance', 'ogc-newfinity' ),
					),
				),
			),
			'extras' => array(
				array(
					'title' => __( 'Optional services', 'ogc-newfinity' ),
					'rows'  => array(
						array( 'label' => __( 'AI Opportunity / Process Scan', 'ogc-newfinity' ), 'price' => '€249' ),
						array( 'label' => __( 'AI Workflow Blueprint', 'ogc-newfinity' ), 'price' => __( 'From €450', 'ogc-newfinity' ) ),
						array( 'label' => __( 'Additional workflow', 'ogc-newfinity' ), 'price' => __( 'From €450', 'ogc-newfinity' ) ),
						array( 'label' => __( 'Additional integration', 'ogc-newfinity' ), 'price' => __( 'From €350', 'ogc-newfinity' ) ),
					),
				),
				array(
					'title' => __( 'More', 'ogc-newfinity' ),
					'rows'  => array(
						array( 'label' => __( 'Knowledge-base preparation', 'ogc-newfinity' ), 'price' => __( 'From €250', 'ogc-newfinity' ) ),
						array( 'label' => __( 'Usage and performance review', 'ogc-newfinity' ), 'price' => __( 'From €149', 'ogc-newfinity' ) ),
						array( 'label' => __( 'Advanced or multi-agent system', 'ogc-newfinity' ), 'price' => __( 'Custom quote', 'ogc-newfinity' ) ),
					),
				),
			),
			'notes' => array(
				__( 'AI model usage is either included up to a stated allowance or paid directly by you with your own key.', 'ogc-newfinity' ),
				__( 'Every assistant has a human handoff for questions it should not answer.', 'ogc-newfinity' ),
			),
		),
	);
}

/**
 * Allowed HTML for pricing FAQ answers (inline links).
 *
 * @return array<string, array<string, bool>>
 */
function ogc_pricing_faq_allowed_html() {
	return array(
		'a' => array(
			'href' => array(),
		),
	);
}

/**
 * Pricing page FAQ.
 *
 * @return array<int, array{question: string, answer: string}>
 */
function ogc_get_pricing_faq() {
	$services_url = home_url( '/services/' );
	$contact_url  = home_url( '/contact/' );
	$submit_url   = home_url( '/submit-idea/' );
	$terms_url    = home_url( '/terms-and-conditions/' );
	$refund_url   = home_url( '/refund-policy/' );
	$support_url  = home_url( '/support/' );

	return array(
		array(
			'question' => __( 'What is billed separately?', 'ogc-newfinity' ),
			'answer'   => sprintf(
				/* translators: 1: Our Services link, 2: contact link */
				__(
					'Hosting, your domain, premium plugins or themes, stock assets, AI model usage, automation platform fees and advertising spend. You own your domain, content and accounts. See what each service covers on %1$s, or %2$s.',
					'ogc-newfinity'
				),
				'<a href="' . esc_url( $services_url ) . '">' . esc_html__( 'Our Services', 'ogc-newfinity' ) . '</a>',
				'<a href="' . esc_url( $contact_url ) . '">' . esc_html__( 'ask us for a full cost overview', 'ogc-newfinity' ) . '</a>'
			),
		),
		array(
			'question' => __( 'How do payments work?', 'ogc-newfinity' ),
			'answer'   => sprintf(
				/* translators: %s: Terms and Conditions link */
				__(
					'Fixed-price work under €1,000: 50%% upfront and 50%% before go-live. Projects above €1,000: 40%% at start, 40%% at an agreed milestone and 20%% at acceptance. Monthly plans are invoiced in advance. Full details are in our %s.',
					'ogc-newfinity'
				),
				'<a href="' . esc_url( $terms_url ) . '">' . esc_html__( 'Terms and Conditions', 'ogc-newfinity' ) . '</a>'
			),
		),
		array(
			'question' => __( 'How many revisions are included?', 'ogc-newfinity' ),
			'answer'   => sprintf(
				/* translators: %s: Our Services link */
				__(
					'Each package lists its revision rounds. A revision changes something already agreed; a new feature or a new direction is extra work that we quote first. Read how a project runs on %s.',
					'ogc-newfinity'
				),
				'<a href="' . esc_url( $services_url ) . '">' . esc_html__( 'Our Services', 'ogc-newfinity' ) . '</a>'
			),
		),
		array(
			'question' => __( 'Can I cancel a monthly plan?', 'ogc-newfinity' ),
			'answer'   => sprintf(
				/* translators: %s: Refund and Cancellation Policy link */
				__(
					'Website care can be cancelled monthly. SEO, social media and AI management start with a 3-month term, then continue monthly. The rules are set out in our %s.',
					'ogc-newfinity'
				),
				'<a href="' . esc_url( $refund_url ) . '">' . esc_html__( 'Refund and Cancellation Policy', 'ogc-newfinity' ) . '</a>'
			),
		),
		array(
			'question' => __( 'What if I need something outside a package?', 'ogc-newfinity' ),
			'answer'   => sprintf(
				/* translators: %s: Submit Your Idea link */
				__(
					'We quote it before any work starts. Extra work is €85 per hour, and €95–€125 per hour for specialist engineering, AI and security. %s and you get a written scope and price.',
					'ogc-newfinity'
				),
				'<a href="' . esc_url( $submit_url ) . '">' . esc_html__( 'Submit your idea', 'ogc-newfinity' ) . '</a>'
			),
		),
		array(
			'question' => __( 'Is VAT included?', 'ogc-newfinity' ),
			'answer'   => sprintf(
				/* translators: %s: Help & Support link */
				__(
					'No. All prices are in EUR, excluding VAT. The VAT rate depends on your location and business status (including EU reverse charge where applicable). Your written quote and invoice state the exact amount. For billing questions, visit %s.',
					'ogc-newfinity'
				),
				'<a href="' . esc_url( $support_url ) . '">' . esc_html__( 'Help & Support', 'ogc-newfinity' ) . '</a>'
			),
		),
	);
}
