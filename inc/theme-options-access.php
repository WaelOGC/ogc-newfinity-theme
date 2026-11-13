<?php
/**
 * Theme Options — overview helpers and per-tab access control.
 *
 * @package OGC_NewFinity
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Theme mod key for tab access map (JSON). */
define( 'OGC_THEME_OPTIONS_TAB_ACCESS_KEY', 'ogc_theme_options_tab_access' );

/** Virtual admin view id (not a settings tab). */
define( 'OGC_THEME_OPTIONS_OVERVIEW_TAB', 'overview' );

/**
 * Whether the user is a full theme-options administrator (always all tabs + access UI).
 *
 * @param int $user_id User ID. 0 = current user.
 * @return bool
 */
function ogc_user_is_theme_options_super_admin( $user_id = 0 ) {
	$user_id = $user_id ? (int) $user_id : get_current_user_id();
	if ( $user_id <= 0 ) {
		return false;
	}
	if ( user_can( $user_id, 'manage_options' ) ) {
		return true;
	}
	// Legacy: full editors of theme options until an access map is saved.
	if ( user_can( $user_id, OGC_OPTIONS_CAPABILITY ) && empty( ogc_get_theme_options_tab_access_map() ) ) {
		return true;
	}
	return false;
}

/**
 * Normalized tab access map from storage.
 *
 * @return array<string, array{roles: string[], users: int[]}>
 */
function ogc_get_theme_options_tab_access_map() {
	$raw = (string) get_theme_mod( OGC_THEME_OPTIONS_TAB_ACCESS_KEY, '{}' );
	$decoded = json_decode( $raw, true );
	if ( ! is_array( $decoded ) ) {
		return array();
	}

	$out = array();
	foreach ( $decoded as $tab_id => $entry ) {
		$tab_id = sanitize_key( (string) $tab_id );
		if ( '' === $tab_id || ! is_array( $entry ) ) {
			continue;
		}
		$roles = isset( $entry['roles'] ) && is_array( $entry['roles'] ) ? $entry['roles'] : array();
		$users = isset( $entry['users'] ) && is_array( $entry['users'] ) ? $entry['users'] : array();

		$roles = array_values( array_filter( array_map( 'sanitize_key', $roles ) ) );
		$users = array_values( array_filter( array_map( 'absint', $users ) ) );

		if ( empty( $roles ) && empty( $users ) ) {
			continue;
		}
		$out[ $tab_id ] = array(
			'roles' => $roles,
			'users' => $users,
		);
	}

	return $out;
}

/**
 * Sanitize posted tab access and persist.
 *
 * @param mixed $raw Posted `ogc_tab_access` array.
 * @return string JSON for theme mod.
 */
function ogc_sanitize_theme_options_tab_access( $raw ) {
	if ( ! is_array( $raw ) ) {
		return wp_json_encode( array() );
	}

	$schema = ogc_theme_options_schema();
	$clean  = array();

	foreach ( $raw as $tab_id => $entry ) {
		$tab_id = sanitize_key( (string) $tab_id );
		if ( '' === $tab_id || ! isset( $schema[ $tab_id ] ) || ! is_array( $entry ) ) {
			continue;
		}

		$roles = array();
		if ( isset( $entry['roles'] ) && is_array( $entry['roles'] ) ) {
			$editable = array_keys( ogc_theme_options_editable_roles() );
			foreach ( $entry['roles'] as $role ) {
				$role = sanitize_key( (string) $role );
				if ( in_array( $role, $editable, true ) ) {
					$roles[] = $role;
				}
			}
		}

		$users = array();
		if ( isset( $entry['users'] ) && is_array( $entry['users'] ) ) {
			foreach ( $entry['users'] as $user_id ) {
				$user_id = absint( $user_id );
				if ( $user_id > 0 && get_userdata( $user_id ) ) {
					$users[] = $user_id;
				}
			}
		} elseif ( isset( $entry['users_csv'] ) ) {
			foreach ( preg_split( '/[\s,;]+/', (string) $entry['users_csv'], -1, PREG_SPLIT_NO_EMPTY ) as $chunk ) {
				$user_id = absint( $chunk );
				if ( $user_id > 0 && get_userdata( $user_id ) ) {
					$users[] = $user_id;
				}
			}
		}

		$roles = array_values( array_unique( $roles ) );
		$users = array_values( array_unique( $users ) );

		if ( empty( $roles ) && empty( $users ) ) {
			continue;
		}

		$clean[ $tab_id ] = array(
			'roles' => $roles,
			'users' => $users,
		);
	}

	return wp_json_encode( $clean );
}

/**
 * Roles that may be granted tab access (never administrator).
 *
 * @return array<string, string> role slug => translated name.
 */
function ogc_theme_options_editable_roles() {
	$roles = array();
	if ( ! function_exists( 'get_editable_roles' ) ) {
		wp_roles();
	}
	foreach ( get_editable_roles() as $slug => $data ) {
		if ( 'administrator' === $slug ) {
			continue;
		}
		$roles[ $slug ] = translate_user_role( $data['name'] );
	}
	return $roles;
}

/**
 * Tab ids the user may view and save.
 *
 * @param int $user_id User ID. 0 = current user.
 * @return string[]
 */
function ogc_user_allowed_theme_options_tabs( $user_id = 0 ) {
	$user_id = $user_id ? (int) $user_id : get_current_user_id();

	if ( ogc_user_is_theme_options_super_admin( $user_id ) ) {
		return array_keys( ogc_theme_options_schema() );
	}

	$user = get_userdata( $user_id );
	if ( ! $user ) {
		return array();
	}

	$allowed = array();
	$map     = ogc_get_theme_options_tab_access_map();

	foreach ( $map as $tab_id => $grants ) {
		if ( ogc_tab_access_grants_include_user( $user, $grants ) ) {
			$allowed[] = $tab_id;
		}
	}

	return array_values( array_unique( $allowed ) );
}

/**
 * Whether a grant entry includes the user.
 *
 * @param WP_User                            $user   User object.
 * @param array{roles: string[], users: int[]} $grants Grant entry.
 * @return bool
 */
function ogc_tab_access_grants_include_user( $user, $grants ) {
	if ( ! $user instanceof WP_User ) {
		return false;
	}

	if ( ! empty( $grants['users'] ) && in_array( (int) $user->ID, array_map( 'intval', $grants['users'] ), true ) ) {
		return true;
	}

	if ( empty( $grants['roles'] ) ) {
		return false;
	}

	foreach ( $grants['roles'] as $role ) {
		if ( in_array( $role, (array) $user->roles, true ) ) {
			return true;
		}
	}

	return false;
}

/**
 * May the user open the Theme Options admin screen at all?
 *
 * @param int $user_id User ID. 0 = current user.
 * @return bool
 */
function ogc_user_can_access_theme_options_screen( $user_id = 0 ) {
	$user_id = $user_id ? (int) $user_id : get_current_user_id();

	if ( ogc_user_is_theme_options_super_admin( $user_id ) ) {
		return true;
	}

	if ( user_can( $user_id, OGC_OPTIONS_CAPABILITY ) ) {
		return true;
	}

	return ! empty( ogc_user_allowed_theme_options_tabs( $user_id ) );
}

/**
 * May the user view/edit a specific settings tab?
 *
 * @param string $tab_id  Tab id.
 * @param int    $user_id User ID. 0 = current user.
 * @return bool
 */
function ogc_user_can_edit_theme_options_tab( $tab_id, $user_id = 0 ) {
	$tab_id = sanitize_key( $tab_id );

	if ( OGC_THEME_OPTIONS_OVERVIEW_TAB === $tab_id ) {
		return ogc_user_can_access_theme_options_screen( $user_id );
	}

	if ( ogc_user_is_theme_options_super_admin( $user_id ) ) {
		return isset( ogc_theme_options_schema()[ $tab_id ] );
	}

	return in_array( $tab_id, ogc_user_allowed_theme_options_tabs( $user_id ), true );
}

/**
 * Require screen access or die.
 */
function ogc_theme_options_require_screen_access() {
	if ( ! ogc_user_can_access_theme_options_screen() ) {
		wp_die(
			esc_html__( 'You do not have permission to open OGC Theme Options.', 'ogc-newfinity' ),
			'',
			array( 'response' => 403 )
		);
	}
}

/**
 * Require tab access or die.
 *
 * @param string $tab_id Tab id.
 */
function ogc_theme_options_require_tab_access( $tab_id ) {
	ogc_theme_options_require_screen_access();

	if ( ! ogc_user_can_edit_theme_options_tab( $tab_id ) ) {
		wp_die(
			esc_html__( 'You do not have permission to edit this section of theme options.', 'ogc-newfinity' ),
			'',
			array( 'response' => 403 )
		);
	}
}

/**
 * One-line dashboard subtitles keyed by tab id.
 *
 * @return array<string, string>
 */
function ogc_theme_options_tab_subtitles() {
	return array(
		'general'  => __( 'Contact, company details, tracking, and maintenance.', 'ogc-newfinity' ),
		'header'   => __( 'Logo, colours, and primary navigation.', 'ogc-newfinity' ),
		'footer'   => __( 'Tagline, link columns, socials, and copyright.', 'ogc-newfinity' ),
		'colors'   => __( 'Brand palette, buttons, and shadows.', 'ogc-newfinity' ),
		'fonts'    => __( 'Google Fonts pairing and type scale.', 'ogc-newfinity' ),
		'buttons'  => __( 'Shape, hover behaviour, and motion.', 'ogc-newfinity' ),
		'images'   => __( 'Optional logo variant for light backgrounds.', 'ogc-newfinity' ),
		'posts'    => __( 'Blog archive card composition.', 'ogc-newfinity' ),
		'ads'      => __( 'AdSense, custom creatives, and placement caps.', 'ogc-newfinity' ),
		'advanced' => __( 'Custom code, import, export, and reset tools.', 'ogc-newfinity' ),
	);
}

/**
 * First checkbox field in a tab (for overview quick toggle), if any.
 *
 * @param string $tab_id Tab id.
 * @return array<string, mixed>|null Field definition.
 */
function ogc_theme_options_tab_quick_toggle_field( $tab_id ) {
	$schema = ogc_theme_options_schema();
	if ( ! isset( $schema[ $tab_id ]['fields'] ) ) {
		return null;
	}
	foreach ( $schema[ $tab_id ]['fields'] as $field ) {
		if ( isset( $field['type'] ) && 'checkbox' === $field['type'] && ! empty( $field['key'] ) ) {
			return ogc_theme_options_field( $field['key'] );
		}
	}
	return null;
}

/**
 * Overview dashboard stats.
 *
 * @return array{tab_count: int, tabs_with_toggle_on: int, version: string}
 */
function ogc_theme_options_overview_stats() {
	$tab_count            = count( ogc_theme_options_schema() );
	$tabs_with_toggle_on  = 0;

	foreach ( ogc_theme_options_schema() as $tab_id => $tab ) {
		foreach ( $tab['fields'] as $field ) {
			if ( 'checkbox' !== ( $field['type'] ?? '' ) || empty( $field['key'] ) ) {
				continue;
			}
			if ( (bool) ogc_mod( $field['key'] ) ) {
				++$tabs_with_toggle_on;
				break;
			}
		}
	}

	return array(
		'tab_count'           => $tab_count,
		'tabs_with_toggle_on' => $tabs_with_toggle_on,
		'version'             => (string) OGC_THEME_VERSION,
	);
}

/**
 * Infer tooltip copy when a schema field omits it.
 *
 * @param array<string, mixed> $field Field definition.
 * @return string
 */
function ogc_theme_options_infer_tooltip( $field ) {
	if ( ! empty( $field['tooltip'] ) ) {
		return (string) $field['tooltip'];
	}
	if ( 'heading' === ( $field['type'] ?? '' ) ) {
		return sprintf(
			/* translators: %s: section heading label. */
			__( 'Settings grouped under %s.', 'ogc-newfinity' ),
			$field['label'] ?? ''
		);
	}
	return sprintf(
		/* translators: %s: field label. */
		__( 'Controls the “%s” setting on the site.', 'ogc-newfinity' ),
		$field['label'] ?? ''
	);
}

/**
 * Render the shared (?) tooltip bubble.
 *
 * @param string $tooltip Tooltip text.
 */
function ogc_render_option_tooltip_bubble( $tooltip ) {
	if ( '' === trim( $tooltip ) ) {
		return;
	}
	?>
	<span class="ogc-opt__tip" tabindex="0" role="note" aria-label="<?php echo esc_attr( $tooltip ); ?>">
		<span aria-hidden="true">?</span>
		<span class="ogc-opt__tip-bubble"><?php echo esc_html( $tooltip ); ?></span>
	</span>
	<?php
}

/**
 * Render the Overview admin panel.
 *
 * @param string[] $visible_tab_ids Tabs to show as cards.
 * @param bool       $is_active       Whether this panel is the visible view.
 */
function ogc_render_theme_options_overview_panel( $visible_tab_ids, $is_active = true ) {
	$schema   = ogc_theme_options_schema();
	$stats    = ogc_theme_options_overview_stats();
	$subtitles = ogc_theme_options_tab_subtitles();
	$access   = ogc_get_theme_options_tab_access_map();
	$roles    = ogc_theme_options_editable_roles();
	?>
	<section
		class="ogc-opt__panel<?php echo $is_active ? ' is-active' : ''; ?>"
		id="ogc-panel-overview"
		data-panel="<?php echo esc_attr( OGC_THEME_OPTIONS_OVERVIEW_TAB ); ?>"
		role="tabpanel"
		aria-labelledby="ogc-tab-overview"
		<?php echo $is_active ? '' : 'hidden'; ?>
	>
		<div class="ogc-opt__panel-head">
			<h2><?php esc_html_e( 'Overview', 'ogc-newfinity' ); ?></h2>
			<p><?php esc_html_e( 'Jump into a section or flip a quick switch — then save once.', 'ogc-newfinity' ); ?></p>
		</div>

		<div class="ogc-opt__overview-stats" role="list">
			<div class="ogc-opt__stat" role="listitem">
				<span class="ogc-opt__stat-value"><?php echo (int) $stats['tab_count']; ?></span>
				<span class="ogc-opt__stat-label"><?php esc_html_e( 'Settings tabs', 'ogc-newfinity' ); ?></span>
			</div>
			<div class="ogc-opt__stat" role="listitem">
				<span class="ogc-opt__stat-value"><?php echo (int) $stats['tabs_with_toggle_on']; ?></span>
				<span class="ogc-opt__stat-label"><?php esc_html_e( 'Tabs with a switch on', 'ogc-newfinity' ); ?></span>
			</div>
			<div class="ogc-opt__stat" role="listitem">
				<span class="ogc-opt__stat-value ogc-opt__stat-value--version"><?php echo esc_html( $stats['version'] ); ?></span>
				<span class="ogc-opt__stat-label"><?php esc_html_e( 'Theme version', 'ogc-newfinity' ); ?></span>
			</div>
		</div>

		<div class="ogc-opt__overview-grid">
			<?php foreach ( $visible_tab_ids as $tab_id ) : ?>
				<?php
				if ( ! isset( $schema[ $tab_id ] ) ) {
					continue;
				}
				$tab      = $schema[ $tab_id ];
				$subtitle = $subtitles[ $tab_id ] ?? '';
				$toggle   = ogc_theme_options_tab_quick_toggle_field( $tab_id );
				$can_edit = ogc_user_can_edit_theme_options_tab( $tab_id );
				?>
				<article class="ogc-opt__overview-card<?php echo $can_edit ? '' : ' is-disabled'; ?>">
					<div class="ogc-opt__overview-card-icon">
						<span class="dashicons <?php echo esc_attr( $tab['icon'] ); ?>" aria-hidden="true"></span>
					</div>
					<h3 class="ogc-opt__overview-card-title"><?php echo esc_html( $tab['label'] ); ?></h3>
					<?php if ( '' !== $subtitle ) : ?>
						<p class="ogc-opt__overview-card-sub"><?php echo esc_html( $subtitle ); ?></p>
					<?php endif; ?>

					<?php if ( $toggle && $can_edit ) : ?>
						<div class="ogc-opt__overview-card-toggle">
							<label class="ogc-opt__switch">
								<input
									type="checkbox"
									class="ogc-opt__overview-toggle"
									data-toggle-for="<?php echo esc_attr( $toggle['key'] ); ?>"
									<?php checked( (bool) ogc_mod( $toggle['key'] ), true ); ?>
								>
								<span class="ogc-opt__switch-track" aria-hidden="true"></span>
								<span class="ogc-opt__switch-text"><?php echo esc_html( $toggle['label'] ); ?></span>
							</label>
							<?php ogc_render_option_tooltip_bubble( ogc_theme_options_infer_tooltip( $toggle ) ); ?>
						</div>
					<?php endif; ?>

					<?php if ( $can_edit ) : ?>
						<button type="button" class="ogc-opt__overview-link ogc-opt__tab-jump" data-tab="<?php echo esc_attr( $tab_id ); ?>">
							<?php esc_html_e( 'Settings →', 'ogc-newfinity' ); ?>
						</button>
					<?php else : ?>
						<p class="ogc-opt__overview-locked"><?php esc_html_e( 'No access', 'ogc-newfinity' ); ?></p>
					<?php endif; ?>
				</article>
			<?php endforeach; ?>
		</div>

		<?php if ( ogc_user_is_theme_options_super_admin() ) : ?>
			<div class="ogc-opt__access" id="ogc-tab-access">
				<div class="ogc-opt__section-head">
					<div class="ogc-opt__section-head-row">
						<h3><?php esc_html_e( 'Tab access', 'ogc-newfinity' ); ?></h3>
						<?php
						ogc_render_option_tooltip_bubble(
							__( 'Grant non-administrators access to individual tabs. Administrators always have full access.', 'ogc-newfinity' )
						);
						?>
					</div>
				</div>
				<p class="ogc-opt__access-lead"><?php esc_html_e( 'Choose roles and user IDs per tab. Leave empty to restrict that tab to administrators only.', 'ogc-newfinity' ); ?></p>

				<div class="ogc-opt__access-grid">
					<?php foreach ( array_keys( $schema ) as $tab_id ) : ?>
						<?php
						$grants    = $access[ $tab_id ] ?? array( 'roles' => array(), 'users' => array() );
						$users_csv = ! empty( $grants['users'] ) ? implode( ', ', $grants['users'] ) : '';
						?>
						<div class="ogc-opt__access-row">
							<div class="ogc-opt__access-tab">
								<span class="dashicons <?php echo esc_attr( $schema[ $tab_id ]['icon'] ); ?>" aria-hidden="true"></span>
								<strong><?php echo esc_html( $schema[ $tab_id ]['label'] ); ?></strong>
							</div>
							<fieldset class="ogc-opt__access-roles">
								<legend class="screen-reader-text"><?php echo esc_html( $schema[ $tab_id ]['label'] ); ?></legend>
								<?php foreach ( $roles as $role_slug => $role_name ) : ?>
									<label class="ogc-opt__access-role">
										<input
											type="checkbox"
											name="ogc_tab_access[<?php echo esc_attr( $tab_id ); ?>][roles][]"
											value="<?php echo esc_attr( $role_slug ); ?>"
											<?php checked( in_array( $role_slug, $grants['roles'] ?? array(), true ) ); ?>
										>
										<?php echo esc_html( $role_name ); ?>
									</label>
								<?php endforeach; ?>
							</fieldset>
							<label class="ogc-opt__access-users">
								<span><?php esc_html_e( 'User IDs', 'ogc-newfinity' ); ?></span>
								<input
									type="text"
									name="ogc_tab_access[<?php echo esc_attr( $tab_id ); ?>][users_csv]"
									value="<?php echo esc_attr( $users_csv ); ?>"
									placeholder="<?php esc_attr_e( 'e.g. 12, 48', 'ogc-newfinity' ); ?>"
									class="ogc-opt__input"
									autocomplete="off"
								>
							</label>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		<?php endif; ?>
	</section>
	<?php
}
