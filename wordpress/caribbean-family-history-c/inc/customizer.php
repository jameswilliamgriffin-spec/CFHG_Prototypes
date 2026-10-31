<?php
/**
 * Customizer: header buttons, meetings, contact, newsletter, homepage text.
 *
 * @package caribbean-family-history-c
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Default value for every Customizer setting, taken from content.json.
 *
 * @return array<string, string>
 */
function cfhg_c_mod_defaults() {
	return array(
		'cfhg_c_donate_label'       => (string) cfhg_c_content( 'header.donate.label', 'Donate' ),
		'cfhg_c_donate_url'         => (string) cfhg_c_content( 'header.donate.url', '/support-us/' ),
		'cfhg_c_member_label'       => (string) cfhg_c_content( 'header.member.label', 'Become a member' ),
		'cfhg_c_member_url'         => (string) cfhg_c_content( 'header.member.url', '/membership/' ),
		'cfhg_c_meeting_time'       => (string) cfhg_c_content( 'site.meeting_time', '' ),
		'cfhg_c_meeting_where'      => (string) cfhg_c_content( 'site.meeting_where', '' ),
		'cfhg_c_meeting_override'   => '',
		'cfhg_c_email'              => (string) cfhg_c_content( 'site.email', '' ),
		'cfhg_c_newsletter_action'  => '',
		'cfhg_c_newsletter_field'   => 'email',
		'cfhg_c_instagram_url'      => '',
		'cfhg_c_x_url'              => '',
		'cfhg_c_hero_title'         => (string) cfhg_c_content( 'home.hero.title', '' ),
		'cfhg_c_hero_lead'          => (string) cfhg_c_content( 'home.hero.lead', '' ),
		'cfhg_c_intro_statement'    => (string) cfhg_c_content( 'home.intro.statement', '' ),
	);
}

/**
 * A Customizer value, falling back to the content.json default.
 *
 * @param string $key Setting id.
 * @return string
 */
function cfhg_c_mod( $key ) {
	$defaults = cfhg_c_mod_defaults();
	$default  = isset( $defaults[ $key ] ) ? $defaults[ $key ] : '';
	$value    = get_theme_mod( $key, $default );

	return ( '' === $value || null === $value ) ? $default : (string) $value;
}

/**
 * Keep only a real Y-m-d date.
 *
 * @param string $value Input.
 * @return string
 */
function cfhg_c_sanitize_date( $value ) {
	$value = trim( (string) $value );
	$date  = DateTime::createFromFormat( '!Y-m-d', $value );

	return ( $date && $date->format( 'Y-m-d' ) === $value ) ? $value : '';
}

/**
 * Accept full URLs, site-relative paths and #anchors.
 *
 * @param string $value Input.
 * @return string
 */
function cfhg_c_sanitize_link( $value ) {
	$value = trim( (string) $value );

	if ( '' === $value ) {
		return '';
	}
	if ( '/' === $value[0] || '#' === $value[0] ) {
		return sanitize_text_field( $value );
	}

	return esc_url_raw( $value );
}

/**
 * Register Customizer panel, sections and controls.
 *
 * @param WP_Customize_Manager $wp_customize Manager.
 */
function cfhg_c_customize_register( $wp_customize ) {
	$defaults = cfhg_c_mod_defaults();

	$wp_customize->add_panel(
		'cfhg_c_panel',
		array(
			'title'    => __( 'Caribbean Family History Group', 'caribbean-family-history-c' ),
			'priority' => 30,
		)
	);

	$sections = array(
		'cfhg_c_header'   => __( 'Header buttons', 'caribbean-family-history-c' ),
		'cfhg_c_meetings' => __( 'Meetings', 'caribbean-family-history-c' ),
		'cfhg_c_contact'  => __( 'Contact, newsletter and social', 'caribbean-family-history-c' ),
		'cfhg_c_home'     => __( 'Homepage text', 'caribbean-family-history-c' ),
	);
	foreach ( $sections as $id => $title ) {
		$wp_customize->add_section(
			$id,
			array(
				'title' => $title,
				'panel' => 'cfhg_c_panel',
			)
		);
	}

	$controls = array(
		array( 'cfhg_c_donate_label', 'cfhg_c_header', __( 'Donate button label', 'caribbean-family-history-c' ), 'text', 'sanitize_text_field', '' ),
		array( 'cfhg_c_donate_url', 'cfhg_c_header', __( 'Donate button link', 'caribbean-family-history-c' ), 'text', 'cfhg_c_sanitize_link', __( 'A full address, or a path on this site such as /support-us/.', 'caribbean-family-history-c' ) ),
		array( 'cfhg_c_member_label', 'cfhg_c_header', __( 'Membership button label', 'caribbean-family-history-c' ), 'text', 'sanitize_text_field', '' ),
		array( 'cfhg_c_member_url', 'cfhg_c_header', __( 'Membership button link', 'caribbean-family-history-c' ), 'text', 'cfhg_c_sanitize_link', '' ),

		array( 'cfhg_c_meeting_time', 'cfhg_c_meetings', __( 'Meeting time', 'caribbean-family-history-c' ), 'text', 'sanitize_text_field', __( 'For example: 10am to 1pm.', 'caribbean-family-history-c' ) ),
		array( 'cfhg_c_meeting_where', 'cfhg_c_meetings', __( 'Where', 'caribbean-family-history-c' ), 'text', 'sanitize_text_field', '' ),
		array( 'cfhg_c_meeting_override', 'cfhg_c_meetings', __( 'Move the next meeting to', 'caribbean-family-history-c' ), 'date', 'cfhg_c_sanitize_date', __( 'Meetings are worked out automatically: the last Saturday of each month except May, August and December. Set a date here only when a meeting moves. It replaces the usual date in that month and is ignored once it has passed.', 'caribbean-family-history-c' ) ),

		array( 'cfhg_c_email', 'cfhg_c_contact', __( 'Contact email', 'caribbean-family-history-c' ), 'email', 'sanitize_email', '' ),
		array( 'cfhg_c_newsletter_action', 'cfhg_c_contact', __( 'Newsletter form address', 'caribbean-family-history-c' ), 'url', 'esc_url_raw', __( 'The form action from your mailing list provider (Mailchimp, MailerLite and so on). Leave empty to send sign-ups to the contact email instead.', 'caribbean-family-history-c' ) ),
		array( 'cfhg_c_newsletter_field', 'cfhg_c_contact', __( 'Newsletter email field name', 'caribbean-family-history-c' ), 'text', 'sanitize_key', __( 'Mailchimp uses EMAIL. Most others use email.', 'caribbean-family-history-c' ) ),
		array( 'cfhg_c_instagram_url', 'cfhg_c_contact', __( 'Instagram link', 'caribbean-family-history-c' ), 'url', 'esc_url_raw', '' ),
		array( 'cfhg_c_x_url', 'cfhg_c_contact', __( 'X (Twitter) link', 'caribbean-family-history-c' ), 'url', 'esc_url_raw', '' ),

		array( 'cfhg_c_hero_title', 'cfhg_c_home', __( 'Hero heading', 'caribbean-family-history-c' ), 'text', 'sanitize_text_field', __( 'Used when the homepage is not built with Elementor.', 'caribbean-family-history-c' ) ),
		array( 'cfhg_c_hero_lead', 'cfhg_c_home', __( 'Hero paragraph', 'caribbean-family-history-c' ), 'textarea', 'sanitize_textarea_field', '' ),
		array( 'cfhg_c_intro_statement', 'cfhg_c_home', __( 'Intro statement', 'caribbean-family-history-c' ), 'textarea', 'sanitize_textarea_field', '' ),
	);

	foreach ( $controls as $control ) {
		list( $id, $section, $label, $type, $sanitize, $description ) = $control;

		$wp_customize->add_setting(
			$id,
			array(
				'default'           => $defaults[ $id ],
				'sanitize_callback' => $sanitize,
				'transport'         => 'refresh',
			)
		);
		$wp_customize->add_control(
			$id,
			array(
				'label'       => $label,
				'description' => $description,
				'section'     => $section,
				'type'        => $type,
			)
		);
	}
}
add_action( 'customize_register', 'cfhg_c_customize_register' );
