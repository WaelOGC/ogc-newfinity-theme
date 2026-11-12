<?php
/**
 * Public pricing catalogue data (sections 5–10 + customer-facing terms).
 *
 * @package OGC_NewFinity
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Service-line pricing: packages, add-ons, subscriptions, and section notes.
 *
 * @return array<int, array<string, mixed>>
 */
function ogc_get_pricing_data() {
	return array(
		array(
			'slug'  => 'custom-wordpress-development',
			'icon'  => 'wordpress',
			'title' => __( 'Custom WordPress Development', 'ogc-newfinity' ),
			'intro' => __( 'Fixed-price website packages, optional care plans, and repair work — scoped and priced honestly.', 'ogc-newfinity' ),
			'packages' => array(
				array(
					'name'       => __( 'Starter Website', 'ogc-newfinity' ),
					'price'      => '€695',
					'price_note' => __( 'one-off', 'ogc-newfinity' ),
					'included'   => array(
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
					'name'       => __( 'Business Website', 'ogc-newfinity' ),
					'price'      => '€1,295',
					'price_note' => __( 'one-off', 'ogc-newfinity' ),
					'included'   => array(
						__( 'Up to 10 pages', 'ogc-newfinity' ),
						__( 'Brand-aligned design', 'ogc-newfinity' ),
						__( 'Responsive layouts', 'ogc-newfinity' ),
						__( 'Performance baseline', 'ogc-newfinity' ),
						__( 'Analytics/Search Console connection', 'ogc-newfinity' ),
						__( 'Basic on-page SEO', 'ogc-newfinity' ),
						__( 'Contact form', 'ogc-newfinity' ),
						__( 'Up to 2 standard integrations', 'ogc-newfinity' ),
						__( '2 revision rounds', 'ogc-newfinity' ),
					),
				),
				array(
					'name'       => __( 'Professional Website', 'ogc-newfinity' ),
					'price'      => __( 'From €2,495', 'ogc-newfinity' ),
					'price_note' => __( 'one-off', 'ogc-newfinity' ),
					'included'   => array(
						__( 'Custom page layouts', 'ogc-newfinity' ),
						__( 'Advanced forms, booking, or catalogue features', 'ogc-newfinity' ),
						__( 'Up to 3 agreed integrations', 'ogc-newfinity' ),
						__( 'Enhanced performance and technical SEO', 'ogc-newfinity' ),
						__( 'Staging and launch support', 'ogc-newfinity' ),
						__( 'Scope-based testing', 'ogc-newfinity' ),
					),
				),
			),
			'addons' => array(
				array(
					'label' => __( 'Additional standard page', 'ogc-newfinity' ),
					'price' => '€75–€125',
				),
				array(
					'label' => __( 'Copywriting for one standard page', 'ogc-newfinity' ),
					'price' => __( 'From €95', 'ogc-newfinity' ),
				),
				array(
					'label' => __( 'Website migration', 'ogc-newfinity' ),
					'price' => __( 'From €250', 'ogc-newfinity' ),
				),
				array(
					'label' => __( 'Multilingual setup (per additional language, translation excluded)', 'ogc-newfinity' ),
					'price' => __( 'From €250', 'ogc-newfinity' ),
				),
				array(
					'label' => __( 'Booking integration', 'ogc-newfinity' ),
					'price' => __( 'From €250', 'ogc-newfinity' ),
				),
				array(
					'label' => __( 'WooCommerce starter store', 'ogc-newfinity' ),
					'price' => __( 'From €1,495', 'ogc-newfinity' ),
				),
				array(
					'label' => __( 'Custom functionality', 'ogc-newfinity' ),
					'price' => __( 'Quoted after discovery', 'ogc-newfinity' ),
				),
			),
			'subscriptions' => array(
				'heading' => __( 'WordPress Care subscriptions', 'ogc-newfinity' ),
				'rows'    => array(
					array(
						'name'     => __( 'Care Basic', 'ogc-newfinity' ),
						'price'    => '€49/month',
						'included' => __( 'Monthly core/theme/plugin updates; pre-update backup; basic uptime check; maintenance summary', 'ogc-newfinity' ),
					),
					array(
						'name'     => __( 'Care Plus', 'ogc-newfinity' ),
						'price'    => '€79/month',
						'included' => __( 'Basic plus more frequent checks, security/malware monitoring, external backups, up to 30 minutes of small changes/month', 'ogc-newfinity' ),
					),
					array(
						'name'     => __( 'Care Pro', 'ogc-newfinity' ),
						'price'    => '€129/month',
						'included' => __( 'Plus plus priority handling, up to 90 minutes of small changes/month, monthly performance/security review', 'ogc-newfinity' ),
					),
				),
			),
			'repair' => array(
				'heading' => __( 'WordPress Fix & Repair', 'ogc-newfinity' ),
				'rows'    => array(
					array(
						'label' => __( 'Diagnosis', 'ogc-newfinity' ),
						'price' => __( 'From €99', 'ogc-newfinity' ),
					),
					array(
						'label' => __( 'Standard repair (after assessment)', 'ogc-newfinity' ),
						'price' => __( 'From €149', 'ogc-newfinity' ),
					),
					array(
						'label' => __( 'Malware cleanup (subject to scope and backup assessment)', 'ogc-newfinity' ),
						'price' => __( 'From €249', 'ogc-newfinity' ),
					),
					array(
						'label' => __( 'Complex compromise, database recovery, or server-level incident', 'ogc-newfinity' ),
						'price' => __( 'Custom quote', 'ogc-newfinity' ),
					),
				),
			),
			'notes' => array(
				__( 'Hosting should be separate or transparently listed as an add-on. Unused support time does not roll over.', 'ogc-newfinity' ),
				__( 'Do not promise a fixed cleanup price before assessing the infection.', 'ogc-newfinity' ),
			),
		),
		array(
			'slug'  => 'software-app-development',
			'icon'  => 'code',
			'title' => __( 'Software & App Development', 'ogc-newfinity' ),
			'intro' => __( 'Discovery-led builds — fixed starting points where scope is clear; custom software is quoted after specification.', 'ogc-newfinity' ),
			'packages' => array(
				array(
					'name'       => __( 'Technical Discovery & Specification', 'ogc-newfinity' ),
					'price'      => '€450',
					'price_note' => '',
					'included'   => array(
						__( 'Requirements workshop', 'ogc-newfinity' ),
						__( 'Problem definition', 'ogc-newfinity' ),
						__( 'User roles', 'ogc-newfinity' ),
						__( 'Feature list', 'ogc-newfinity' ),
						__( 'Integration review', 'ogc-newfinity' ),
						__( 'Technical approach', 'ogc-newfinity' ),
						__( 'Estimate and plan', 'ogc-newfinity' ),
					),
				),
				array(
					'name'       => __( 'Clickable Prototype / Proof of Concept', 'ogc-newfinity' ),
					'price'      => __( 'From €1,500', 'ogc-newfinity' ),
					'price_note' => '',
					'included'   => array(
						__( 'Narrow demonstration', 'ogc-newfinity' ),
						__( 'Limited screens or one technical proof', 'ogc-newfinity' ),
						__( 'Not production-ready', 'ogc-newfinity' ),
					),
				),
				array(
					'name'       => __( 'Focused MVP Development', 'ogc-newfinity' ),
					'price'      => __( 'From €5,000', 'ogc-newfinity' ),
					'price_note' => '',
					'included'   => array(
						__( 'Tightly scoped first release with agreed core workflow', 'ogc-newfinity' ),
						__( 'Backend/data layer where needed', 'ogc-newfinity' ),
						__( 'Testing', 'ogc-newfinity' ),
						__( 'Deployment plan', 'ogc-newfinity' ),
					),
				),
				array(
					'name'       => __( 'Custom Web Application', 'ogc-newfinity' ),
					'price'      => __( 'From €10,000', 'ogc-newfinity' ),
					'price_note' => '',
					'included'   => array(
						__( 'Custom application with authentication, database, admin interface', 'ogc-newfinity' ),
						__( 'Workflows and agreed integrations', 'ogc-newfinity' ),
					),
				),
				array(
					'name'       => __( 'API Development & Integration', 'ogc-newfinity' ),
					'price'      => __( 'From €450', 'ogc-newfinity' ),
					'price_note' => '',
					'included'   => array(
						__( 'One clearly scoped API connection or data workflow', 'ogc-newfinity' ),
					),
				),
			),
			'addons'        => array(),
			'subscriptions' => array(),
			'notes'         => array(
				__( 'Do not sell complex software as a fixed-price menu item without discovery.', 'ogc-newfinity' ),
				__( 'Quote by milestones: discovery, design, implementation, testing, deployment.', 'ogc-newfinity' ),
				__( 'Define acceptance criteria and change requests.', 'ogc-newfinity' ),
				__( 'Suggested payment schedule: 40% start, 40% milestone, 20% acceptance/handover.', 'ogc-newfinity' ),
				__( 'Hosting, cloud services, external API fees, licences, and maintenance are separate.', 'ogc-newfinity' ),
				__( '“MVP” means a usable, testable first product, not a mockup.', 'ogc-newfinity' ),
			),
		),
		array(
			'slug'  => 'ui-ux-design',
			'icon'  => 'design',
			'title' => __( 'UI/UX Design', 'ogc-newfinity' ),
			'intro' => __( 'Screen-based packages with clear revision rounds — research and testing scoped separately when needed.', 'ogc-newfinity' ),
			'packages' => array(
				array(
					'name'       => __( 'UI Starter', 'ogc-newfinity' ),
					'price'      => '€350',
					'price_note' => '',
					'included'   => array(
						__( 'Up to 3 screens/page layouts', 'ogc-newfinity' ),
						__( 'Visual direction', 'ogc-newfinity' ),
						__( 'Responsive considerations', 'ogc-newfinity' ),
						__( '1 revision round', 'ogc-newfinity' ),
					),
				),
				array(
					'name'       => __( 'UI/UX Business', 'ogc-newfinity' ),
					'price'      => '€850',
					'price_note' => '',
					'included'   => array(
						__( 'Up to 8 key screens', 'ogc-newfinity' ),
						__( 'Basic user flow', 'ogc-newfinity' ),
						__( 'Wireframes', 'ogc-newfinity' ),
						__( 'Polished Figma UI', 'ogc-newfinity' ),
						__( '2 revision rounds', 'ogc-newfinity' ),
					),
				),
				array(
					'name'       => __( 'Product Design Advanced', 'ogc-newfinity' ),
					'price'      => __( 'From €1,500', 'ogc-newfinity' ),
					'price_note' => '',
					'included'   => array(
						__( 'Discovery workshop', 'ogc-newfinity' ),
						__( 'User flows', 'ogc-newfinity' ),
						__( 'Wireframes', 'ogc-newfinity' ),
						__( 'Interactive prototype', 'ogc-newfinity' ),
						__( 'Responsive states', 'ogc-newfinity' ),
						__( 'Design handoff', 'ogc-newfinity' ),
						__( 'Agreed usability review', 'ogc-newfinity' ),
					),
				),
			),
			'addons' => array(
				array(
					'label' => __( 'Additional screen', 'ogc-newfinity' ),
					'price' => '€75–€150',
				),
				array(
					'label' => __( 'Design system/component library', 'ogc-newfinity' ),
					'price' => __( 'From €500', 'ogc-newfinity' ),
				),
				array(
					'label' => __( 'Usability testing', 'ogc-newfinity' ),
					'price' => __( 'Quoted by participant count and research scope', 'ogc-newfinity' ),
				),
				array(
					'label' => __( 'UX audit (small website)', 'ogc-newfinity' ),
					'price' => __( 'From €350', 'ogc-newfinity' ),
				),
				array(
					'label' => __( 'Web application UX review', 'ogc-newfinity' ),
					'price' => __( 'From €595', 'ogc-newfinity' ),
				),
			),
			'subscriptions' => array(),
			'notes'         => array(
				__( 'Do not describe UI-only work as UX research. Interviews, research, testing, and analysis require a separate scope.', 'ogc-newfinity' ),
			),
		),
		array(
			'slug'  => 'marketing-strategy-consulting',
			'icon'  => 'strategy',
			'title' => __( 'Marketing & Strategy Consulting', 'ogc-newfinity' ),
			'intro' => __( 'Local presence, ongoing SEO, and optional social management — deliverables defined, not ranking promises.', 'ogc-newfinity' ),
			'package_groups' => array(
				array(
					'heading'  => __( 'One-time setup', 'ogc-newfinity' ),
					'packages' => array(
						array(
							'name'       => __( 'Local Presence Setup', 'ogc-newfinity' ),
							'price'      => '€199',
							'price_note' => __( 'one-off', 'ogc-newfinity' ),
							'included'   => array(
								__( 'Google Business Profile setup/cleanup', 'ogc-newfinity' ),
								__( 'Categories and services', 'ogc-newfinity' ),
								__( 'Business details and opening hours', 'ogc-newfinity' ),
								__( 'Initial optimization checklist', 'ogc-newfinity' ),
							),
						),
						array(
							'name'       => __( 'Local Growth Setup', 'ogc-newfinity' ),
							'price'      => '€349',
							'price_note' => __( 'one-off', 'ogc-newfinity' ),
							'included'   => array(
								__( 'Everything in Local Presence', 'ogc-newfinity' ),
								__( 'Local keyword mapping', 'ogc-newfinity' ),
								__( 'Review-request process', 'ogc-newfinity' ),
								__( 'Local landing-page recommendations', 'ogc-newfinity' ),
								__( 'Tracking setup', 'ogc-newfinity' ),
							),
						),
					),
				),
			),
			'packages' => array(),
			'subscriptions' => array(
				array(
					'heading' => __( 'Monthly SEO management', 'ogc-newfinity' ),
					'rows'    => array(
						array(
							'name'     => __( 'Local SEO Essential', 'ogc-newfinity' ),
							'price'    => '€299/month',
							'included' => __( 'One location; profile management; 2 posts/month; review-response guidance; basic local keyword tracking; monthly report', 'ogc-newfinity' ),
						),
						array(
							'name'     => __( 'Local SEO Growth', 'ogc-newfinity' ),
							'price'    => '€499/month',
							'included' => __( 'Essential plus 4 posts/month; one local/service content improvement/month; citation consistency checks; competitor snapshot; monthly recommendations', 'ogc-newfinity' ),
						),
						array(
							'name'     => __( 'SEO & Visibility Pro', 'ogc-newfinity' ),
							'price'    => '€799/month',
							'included' => __( 'Growth plus up to 2 agreed content/page improvements/month; technical SEO review; broader keyword tracking; quarterly strategy review', 'ogc-newfinity' ),
						),
					),
				),
				array(
					'heading' => __( 'Optional social media management', 'ogc-newfinity' ),
					'rows'    => array(
						array(
							'name'     => __( 'Social Starter', 'ogc-newfinity' ),
							'price'    => '€399/month',
							'included' => __( '8 posts/month; one platform; content calendar; basic report', 'ogc-newfinity' ),
						),
						array(
							'name'     => __( 'Social Growth', 'ogc-newfinity' ),
							'price'    => '€699/month',
							'included' => __( '12 posts + 2 short-form videos/month; up to 2 platforms; monthly report', 'ogc-newfinity' ),
						),
						array(
							'name'     => __( 'Social Performance', 'ogc-newfinity' ),
							'price'    => __( 'From €1,095/month', 'ogc-newfinity' ),
							'included' => __( 'Content planning; multi-channel publishing; campaign coordination; monthly strategy review', 'ogc-newfinity' ),
						),
					),
				),
			),
			'addons' => array(
				array(
					'label' => __( 'One-off SEO audit', 'ogc-newfinity' ),
					'price' => __( 'From €350', 'ogc-newfinity' ),
				),
				array(
					'label' => __( 'Service/location landing page', 'ogc-newfinity' ),
					'price' => __( 'From €150 per page', 'ogc-newfinity' ),
				),
				array(
					'label' => __( 'Blog article', 'ogc-newfinity' ),
					'price' => __( 'From €150', 'ogc-newfinity' ),
				),
				array(
					'label' => __( 'Additional business location', 'ogc-newfinity' ),
					'price' => __( 'Quoted by number of locations and content scope', 'ogc-newfinity' ),
				),
			),
			'notes' => array(
				__( 'Do not promise a fixed number of quality backlinks without control over publisher availability and quality.', 'ogc-newfinity' ),
				__( 'Ad spend, on-location production, influencer fees, and platform budgets are excluded.', 'ogc-newfinity' ),
				__( 'No ranking guarantee: Google Business Profile and SEO optimization cannot guarantee first place in Google Search or Maps.', 'ogc-newfinity' ),
			),
		),
		array(
			'slug'  => 'cybersecurity',
			'icon'  => 'shield',
			'title' => __( 'Cybersecurity', 'ogc-newfinity' ),
			'intro' => __( 'Website security reviews and hardening — distinct from formal penetration testing, which is scoped and quoted separately.', 'ogc-newfinity' ),
			'packages' => array(
				array(
					'name'       => __( 'Website Security Check', 'ogc-newfinity' ),
					'price'      => '€249',
					'price_note' => __( 'one-off', 'ogc-newfinity' ),
					'included'   => array(
						__( 'Defined basic external/configuration review', 'ogc-newfinity' ),
						__( 'WordPress/plugin update status', 'ogc-newfinity' ),
						__( 'HTTPS/security-header checks where applicable', 'ogc-newfinity' ),
						__( 'Visible exposure review', 'ogc-newfinity' ),
						__( 'Prioritized findings report', 'ogc-newfinity' ),
					),
				),
				array(
					'name'       => __( 'WordPress Protection', 'ogc-newfinity' ),
					'price'      => '€499',
					'price_note' => __( 'one-off', 'ogc-newfinity' ),
					'included'   => array(
						__( 'Agreed WordPress hardening', 'ogc-newfinity' ),
						__( 'Admin/account review', 'ogc-newfinity' ),
						__( 'Backup verification', 'ogc-newfinity' ),
						__( 'Security configuration', 'ogc-newfinity' ),
						__( 'Update and recovery recommendations', 'ogc-newfinity' ),
					),
				),
				array(
					'name'       => __( 'Application Security Review', 'ogc-newfinity' ),
					'price'      => __( 'From €1,200', 'ogc-newfinity' ),
					'price_note' => __( 'one-off', 'ogc-newfinity' ),
					'included'   => array(
						__( 'Scoped review of a small application/API', 'ogc-newfinity' ),
						__( 'Architecture and configuration review', 'ogc-newfinity' ),
						__( 'Findings and remediation priorities', 'ogc-newfinity' ),
						__( 'Not a full pentest unless explicitly scoped', 'ogc-newfinity' ),
					),
				),
			),
			'addons' => array(
				array(
					'label' => __( 'Security monitoring add-on', 'ogc-newfinity' ),
					'price' => __( 'From €49/month', 'ogc-newfinity' ),
				),
				array(
					'label' => __( 'Incident response and malware remediation', 'ogc-newfinity' ),
					'price' => __( 'Priced after triage', 'ogc-newfinity' ),
				),
				array(
					'label' => __( 'Formal penetration test', 'ogc-newfinity' ),
					'price' => __( 'Separately scoped and quoted after discovery', 'ogc-newfinity' ),
				),
			),
			'subscriptions' => array(
				'heading' => '',
				'rows'    => array(),
			),
			'notes' => array(
				__( 'Clearly distinguish a basic website security review from a professional penetration test.', 'ogc-newfinity' ),
				__( 'A formal pentest should be separately scoped and, where needed, delivered by a qualified specialist partner. Never sell a low-cost automated scan as a penetration test.', 'ogc-newfinity' ),
				__( 'Written authorization and defined scope are mandatory for penetration testing.', 'ogc-newfinity' ),
				__( 'Testing is point-in-time and cannot guarantee that a system is secure.', 'ogc-newfinity' ),
				__( 'WordPress Care Plus/Pro may include defined security checks.', 'ogc-newfinity' ),
			),
		),
		array(
			'slug'  => 'ai-solutions',
			'icon'  => 'ai',
			'title' => __( 'AI Solutions', 'ogc-newfinity' ),
			'intro' => __( 'Implementation plus ongoing management — third-party model and platform usage billed transparently.', 'ogc-newfinity' ),
			'packages' => array(
				array(
					'name'       => __( 'AI Starter Assistant', 'ogc-newfinity' ),
					'price'      => '€795',
					'price_note' => __( 'setup', 'ogc-newfinity' ),
					'price_recurring' => '€99/month',
					'included'   => array(
						__( 'One narrow website assistant or workflow', 'ogc-newfinity' ),
						__( 'Approved knowledge sources', 'ogc-newfinity' ),
						__( 'Fallback/handoff', 'ogc-newfinity' ),
						__( 'Basic testing', 'ogc-newfinity' ),
						__( 'Limited monthly monitoring', 'ogc-newfinity' ),
					),
				),
				array(
					'name'       => __( 'AI Business Automation', 'ogc-newfinity' ),
					'price'      => '€1,500',
					'price_note' => __( 'setup', 'ogc-newfinity' ),
					'price_recurring' => '€199/month',
					'included'   => array(
						__( 'Multi-step workflow', 'ogc-newfinity' ),
						__( 'Up to 2 agreed integrations', 'ogc-newfinity' ),
						__( 'Exception handling', 'ogc-newfinity' ),
						__( 'Logs and owner notifications', 'ogc-newfinity' ),
						__( 'Testing and monthly review', 'ogc-newfinity' ),
					),
				),
				array(
					'name'       => __( 'Custom AI Agent', 'ogc-newfinity' ),
					'price'      => __( 'From €2,500', 'ogc-newfinity' ),
					'price_note' => __( 'setup', 'ogc-newfinity' ),
					'price_recurring' => __( 'From €299/month', 'ogc-newfinity' ),
					'included'   => array(
						__( 'Custom agent workflow', 'ogc-newfinity' ),
						__( 'Defined tools and permissions', 'ogc-newfinity' ),
						__( 'API integrations', 'ogc-newfinity' ),
						__( 'Approval rules', 'ogc-newfinity' ),
						__( 'Evaluation tests', 'ogc-newfinity' ),
						__( 'Monitoring and maintenance', 'ogc-newfinity' ),
					),
				),
			),
			'addons' => array(
				array(
					'label' => __( 'AI Opportunity / Process Scan', 'ogc-newfinity' ),
					'price' => '€249',
				),
				array(
					'label' => __( 'AI Workflow Blueprint', 'ogc-newfinity' ),
					'price' => __( 'From €450', 'ogc-newfinity' ),
				),
				array(
					'label' => __( 'Additional workflow', 'ogc-newfinity' ),
					'price' => __( 'From €450', 'ogc-newfinity' ),
				),
				array(
					'label' => __( 'Additional integration', 'ogc-newfinity' ),
					'price' => __( 'From €350', 'ogc-newfinity' ),
				),
				array(
					'label' => __( 'AI knowledge-base preparation', 'ogc-newfinity' ),
					'price' => __( 'From €250', 'ogc-newfinity' ),
				),
				array(
					'label' => __( 'Advanced agent / multi-agent system', 'ogc-newfinity' ),
					'price' => __( 'From €5,000', 'ogc-newfinity' ),
				),
				array(
					'label' => __( 'AI usage and performance review', 'ogc-newfinity' ),
					'price' => __( 'From €149', 'ogc-newfinity' ),
				),
			),
			'subscriptions' => array(),
			'notes'         => array(
				__( 'Third-party model usage and automation-platform charges must be transparent.', 'ogc-newfinity' ),
				__( 'Managed AI: OGC may include a defined usage allowance; overage is billed transparently or the customer upgrades.', 'ogc-newfinity' ),
				__( 'BYOK (Bring Your Own Key): the customer supplies a supported provider key and pays that provider directly. OGC still charges for implementation, platform operations, monitoring, integrations, and support.', 'ogc-newfinity' ),
				__( 'Do not advertise “unlimited AI” unless usage is genuinely unlimited and financially sustainable.', 'ogc-newfinity' ),
			),
		),
	);
}

/**
 * Customer-facing commercial terms (sections 4 and 11).
 *
 * @return array<string, array<int, string>>
 */
function ogc_get_pricing_commercial_terms() {
	return array(
		'separate_costs' => array(
			__( 'Unless specifically included, the customer pays separately for hosting, domain, premium plugins/themes, stock assets, AI model usage, automation platform subscriptions, SMS/WhatsApp fees, payment gateway fees, translation, external security testing, and advertising spend.', 'ogc-newfinity' ),
			__( 'The customer owns their domain, content, and business accounts. OGC receives only access needed to perform the contracted work.', 'ogc-newfinity' ),
		),
		'out_of_scope' => array(
			__( 'Recommended out-of-scope rates: general implementation/support €85/hour.', 'ogc-newfinity' ),
			__( 'Specialist engineering, AI, and security: €95–€125/hour, depending on scope.', 'ogc-newfinity' ),
			__( 'Every fixed-price offer defines scope, revision rounds, response times, acceptance criteria, and exclusions. Work outside scope requires written approval and a separate quote.', 'ogc-newfinity' ),
		),
		'vat' => array(
			__( 'Public catalogue displays prices excluding 21% VAT.', 'ogc-newfinity' ),
			__( 'Quotes and invoices show VAT separately where applicable.', 'ogc-newfinity' ),
			__( 'Confirm VAT treatment for customer location and service type.', 'ogc-newfinity' ),
		),
		'deposits' => array(
			__( 'Fixed-price work below €1,000: suggested 50% upfront, 50% before handover/go-live.', 'ogc-newfinity' ),
			__( 'Projects above €1,000: suggested 40% upfront, 40% at milestone, 20% at acceptance.', 'ogc-newfinity' ),
			__( 'Recurring services invoiced monthly in advance.', 'ogc-newfinity' ),
		),
		'revisions' => array(
			__( 'State included revision rounds.', 'ogc-newfinity' ),
			__( 'A revision changes an agreed deliverable; a new feature or direction is additional work.', 'ogc-newfinity' ),
			__( 'Work starts after required content, access, approvals, and deposit are received.', 'ogc-newfinity' ),
		),
		'recurring' => array(
			__( 'State minimum term, cancellation notice, included support time, response target, and incident definition.', 'ogc-newfinity' ),
			__( 'Suggested launch approach: monthly billing with a 3-month initial term for SEO/marketing/AI management where onboarding and optimization require time. Website care may be monthly cancellable.', 'ogc-newfinity' ),
			__( 'Never guarantee business outcomes.', 'ogc-newfinity' ),
		),
	);
}
