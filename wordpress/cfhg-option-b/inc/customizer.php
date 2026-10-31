<?php
/**
 * Customizer settings.
 *
 * Every piece of homepage copy, every image and the meeting details can be
 * edited in Appearance > Customize. Fields are defined as data below, so adding
 * one is a single line.
 *
 * @package CFHG
 */

defined( 'ABSPATH' ) || exit;

/**
 * Field definitions grouped by Customizer section.
 *
 * Each field: key => array( type, label, description ).
 * Types: text, inline (text with simple formatting), textarea, list, link,
 * email, url, checkbox, image, date, months.
 *
 * @return array
 */
function cfhg_customizer_fields() {
	$list_help = __( 'One item per line. Separate the parts with a | character.', 'cfhg' );
	$link_help = __( 'A page address such as /join/, an anchor such as #next-meeting, or a full web address.', 'cfhg' );
	$para_help = __( 'Leave a blank line between paragraphs.', 'cfhg' );

	$toggles = array();
	$names   = array(
		'hero'     => __( 'Opening panel', 'cfhg' ),
		'intro'    => __( 'Who we are', 'cfhg' ),
		'meeting'  => __( 'Next meeting', 'cfhg' ),
		'numbers'  => __( 'Our group in numbers', 'cfhg' ),
		'start'    => __( 'Start here', 'cfhg' ),
		'islands'  => __( 'Research by island', 'cfhg' ),
		'stories'  => __( 'Stories', 'cfhg' ),
		'help'     => __( 'Research help', 'cfhg' ),
		'join'     => __( 'Membership', 'cfhg' ),
		'partners' => __( 'Partners', 'cfhg' ),
	);
	foreach ( $names as $key => $label ) {
		/* translators: %s: homepage section name. */
		$toggles[ 'show_' . $key ] = array( 'checkbox', sprintf( __( 'Show: %s', 'cfhg' ), $label ), '' );
	}

	return array(
		'cfhg_sections' => array(
			'title'  => __( 'Homepage sections', 'cfhg' ),
			'panel'  => 'cfhg_home',
			'fields' => $toggles,
		),
		'cfhg_hero'     => array(
			'title'  => __( 'Opening panel', 'cfhg' ),
			'panel'  => 'cfhg_home',
			'fields' => array(
				'hero_kicker'          => array( 'text', __( 'Label above the heading', 'cfhg' ), '' ),
				'hero_heading'         => array( 'inline', __( 'Heading', 'cfhg' ), __( 'Put one word in <em></em> to set it in italic on a yellow highlight.', 'cfhg' ) ),
				'hero_text'            => array( 'textarea', __( 'Introduction', 'cfhg' ), '' ),
				'hero_primary_label'   => array( 'text', __( 'Main button label', 'cfhg' ), '' ),
				'hero_primary_url'     => array( 'link', __( 'Main button link', 'cfhg' ), $link_help ),
				'hero_secondary_label' => array( 'text', __( 'Second link label', 'cfhg' ), '' ),
				'hero_secondary_url'   => array( 'link', __( 'Second link', 'cfhg' ), $link_help ),
				'hero_proof_text'      => array( 'text', __( 'Line beside the small portraits', 'cfhg' ), '' ),
				'hero_image'           => array( 'image', __( 'Photograph in the circle', 'cfhg' ), __( 'A square image works best. A cut-out portrait on a transparent background (PNG or WebP) sits on the sea-blue circle.', 'cfhg' ) ),
			),
		),
		'cfhg_intro'    => array(
			'title'  => __( 'Who we are', 'cfhg' ),
			'panel'  => 'cfhg_home',
			'fields' => array(
				'intro_title'      => array( 'text', __( 'Title', 'cfhg' ), '' ),
				'intro_text'       => array( 'textarea', __( 'Text', 'cfhg' ), $para_help ),
				'intro_link_label' => array( 'text', __( 'Link label', 'cfhg' ), '' ),
				'intro_link_url'   => array( 'link', __( 'Link', 'cfhg' ), $link_help ),
				'intro_photo_1'    => array( 'image', __( 'Snapshot, left (portrait)', 'cfhg' ), '' ),
				'intro_photo_2'    => array( 'image', __( 'Snapshot, right (landscape)', 'cfhg' ), '' ),
			),
		),
		'cfhg_meetings' => array(
			'title'       => __( 'Meetings and prices', 'cfhg' ),
			'description' => __( 'The next meeting date is worked out automatically: the last Saturday of each month, skipping the months below.', 'cfhg' ),
			'fields'      => array(
				'meeting_kicker'          => array( 'text', __( 'Label', 'cfhg' ), '' ),
				'meeting_heading'         => array( 'inline', __( 'Heading', 'cfhg' ), '' ),
				'meeting_venue'           => array( 'text', __( 'Where', 'cfhg' ), '' ),
				'meeting_short_where'     => array( 'text', __( 'Where (short version)', 'cfhg' ), __( 'Used in the opening panel, menu and footer.', 'cfhg' ) ),
				'meeting_online'          => array( 'text', __( 'Online', 'cfhg' ), '' ),
				'meeting_pattern'         => array( 'text', __( 'When (in words)', 'cfhg' ), '' ),
				'meeting_time'            => array( 'text', __( 'Time', 'cfhg' ), __( 'For example 10:30am to 1pm. Leave blank to hide.', 'cfhg' ) ),
				'meeting_skip_months'     => array( 'months', __( 'Months with no meeting', 'cfhg' ), __( 'Month numbers separated by commas. 5,8,12 means May, August and December.', 'cfhg' ) ),
				'meeting_override_date'   => array( 'date', __( 'Override the next date', 'cfhg' ), __( 'Use this if a meeting moves. It stops applying once the date has passed.', 'cfhg' ) ),
				'meeting_note'            => array( 'text', __( 'Note', 'cfhg' ), '' ),
				'meeting_image'           => array( 'image', __( 'Image', 'cfhg' ), '' ),
				'meeting_image_label'     => array( 'text', __( 'Label on the image', 'cfhg' ), '' ),
				'meeting_cta_label'       => array( 'text', __( 'Main button label', 'cfhg' ), '' ),
				'meeting_cta_url'         => array( 'link', __( 'Main button link', 'cfhg' ), $link_help ),
				'meeting_secondary_label' => array( 'text', __( 'Email link label', 'cfhg' ), '' ),
				'price_first_label'       => array( 'text', __( 'Price 1 label', 'cfhg' ), '' ),
				'price_first'             => array( 'text', __( 'Price 1', 'cfhg' ), '' ),
				'price_visit_label'       => array( 'text', __( 'Price 2 label', 'cfhg' ), '' ),
				'price_visit'             => array( 'text', __( 'Price 2', 'cfhg' ), '' ),
				'price_year_label'        => array( 'text', __( 'Price 3 label', 'cfhg' ), '' ),
				'price_year'              => array( 'text', __( 'Price 3', 'cfhg' ), '' ),
			),
		),
		'cfhg_numbers'  => array(
			'title'  => __( 'Our group in numbers', 'cfhg' ),
			'panel'  => 'cfhg_home',
			'fields' => array(
				'numbers_title' => array( 'text', __( 'Title', 'cfhg' ), '' ),
				'numbers_list'  => array( 'list', __( 'Numbers', 'cfhg' ), $list_help . ' ' . __( 'Number | what it means. Up to six look best.', 'cfhg' ) ),
			),
		),
		'cfhg_start'    => array(
			'title'  => __( 'Start here', 'cfhg' ),
			'panel'  => 'cfhg_home',
			'fields' => array(
				'start_kicker'     => array( 'text', __( 'Label', 'cfhg' ), '' ),
				'start_heading'    => array( 'inline', __( 'Heading', 'cfhg' ), '' ),
				'start_text'       => array( 'textarea', __( 'Paragraph', 'cfhg' ), '' ),
				'start_steps'      => array( 'list', __( 'Steps', 'cfhg' ), $list_help . ' ' . __( 'Title | text', 'cfhg' ) ),
				'start_link_label' => array( 'text', __( 'Link label', 'cfhg' ), '' ),
				'start_link_url'   => array( 'link', __( 'Link', 'cfhg' ), $link_help ),
			),
		),
		'cfhg_islands'  => array(
			'title'  => __( 'Research by island', 'cfhg' ),
			'panel'  => 'cfhg_home',
			'fields' => array(
				'islands_kicker'  => array( 'text', __( 'Label', 'cfhg' ), '' ),
				'islands_heading' => array( 'inline', __( 'Heading', 'cfhg' ), '' ),
				'islands_text'    => array( 'textarea', __( 'Paragraph', 'cfhg' ), '' ),
				'islands_list'    => array( 'list', __( 'Islands', 'cfhg' ), $list_help . ' ' . __( 'Name | link | note', 'cfhg' ) ),
				'islands_image'   => array( 'image', __( 'Image (shown in an oval)', 'cfhg' ), '' ),
			),
		),
		'cfhg_stories'  => array(
			'title'       => __( 'Stories', 'cfhg' ),
			'panel'       => 'cfhg_home',
			'description' => __( 'The latest three posts in the category below are shown. Until there are any, starter cards are shown instead.', 'cfhg' ),
			'fields'      => array(
				'stories_kicker'     => array( 'text', __( 'Label', 'cfhg' ), '' ),
				'stories_heading'    => array( 'inline', __( 'Heading', 'cfhg' ), '' ),
				'stories_read_label' => array( 'text', __( 'Button label on each story', 'cfhg' ), '' ),
				'stories_category'   => array( 'text', __( 'Category slug', 'cfhg' ), '' ),
				'stories_link_label' => array( 'text', __( 'Link label', 'cfhg' ), '' ),
				'stories_link_url'   => array( 'link', __( 'Link', 'cfhg' ), $link_help ),
			),
		),
		'cfhg_help'     => array(
			'title'  => __( 'Research help', 'cfhg' ),
			'panel'  => 'cfhg_home',
			'fields' => array(
				'help_kicker'    => array( 'text', __( 'Label', 'cfhg' ), '' ),
				'help_heading'   => array( 'inline', __( 'Heading', 'cfhg' ), '' ),
				'help_text'      => array( 'textarea', __( 'Paragraph', 'cfhg' ), '' ),
				'help_cta_label' => array( 'text', __( 'Button label', 'cfhg' ), '' ),
				'help_cta_url'   => array( 'link', __( 'Button link', 'cfhg' ), $link_help ),
				'help_image'     => array( 'image', __( 'Image', 'cfhg' ), '' ),
			),
		),
		'cfhg_join'     => array(
			'title'  => __( 'Membership', 'cfhg' ),
			'panel'  => 'cfhg_home',
			'fields' => array(
				'join_big'             => array( 'text', __( 'Large words across the photograph', 'cfhg' ), '' ),
				'join_kicker'          => array( 'text', __( 'Label', 'cfhg' ), '' ),
				'join_heading'         => array( 'inline', __( 'Heading', 'cfhg' ), '' ),
				'join_text'            => array( 'textarea', __( 'Paragraph', 'cfhg' ), '' ),
				'join_cta_label'       => array( 'text', __( 'Button label', 'cfhg' ), '' ),
				'join_cta_url'         => array( 'link', __( 'Button link', 'cfhg' ), $link_help ),
				'join_secondary_label' => array( 'text', __( 'Email link label', 'cfhg' ), '' ),
				'join_image'           => array( 'image', __( 'Photograph', 'cfhg' ), '' ),
			),
		),
		'cfhg_partners' => array(
			'title'  => __( 'Partners', 'cfhg' ),
			'panel'  => 'cfhg_home',
			'fields' => array(
				'partners_kicker'  => array( 'text', __( 'Label', 'cfhg' ), '' ),
				'partners_heading' => array( 'inline', __( 'Heading', 'cfhg' ), '' ),
				'partners_list'    => array( 'list', __( 'Partners', 'cfhg' ), $list_help . ' ' . __( 'Name | text | link', 'cfhg' ) ),
			),
		),
		'cfhg_contact'  => array(
			'title'  => __( 'Contact, social, header and footer', 'cfhg' ),
			'fields' => array(
				'header_join_label' => array( 'text', __( 'Header button label', 'cfhg' ), '' ),
				'header_join_url'   => array( 'link', __( 'Header button link', 'cfhg' ), $link_help ),
				'contact_email'     => array( 'email', __( 'Email address', 'cfhg' ), '' ),
				'social_instagram'  => array( 'url', __( 'Instagram', 'cfhg' ), __( 'Leave blank to hide.', 'cfhg' ) ),
				'social_x'          => array( 'url', __( 'X', 'cfhg' ), __( 'Leave blank to hide.', 'cfhg' ) ),
				'social_facebook'   => array( 'url', __( 'Facebook', 'cfhg' ), __( 'Leave blank to hide.', 'cfhg' ) ),
				'social_youtube'    => array( 'url', __( 'YouTube', 'cfhg' ), __( 'Leave blank to hide.', 'cfhg' ) ),
				'footer_big'        => array( 'text', __( 'Large words across the footer photograph', 'cfhg' ), '' ),
				'footer_image'      => array( 'image', __( 'Footer photograph', 'cfhg' ), '' ),
				'footer_tagline'    => array( 'textarea', __( 'Footer description', 'cfhg' ), '' ),
				'footer_photo_note' => array( 'textarea', __( 'Photography notice', 'cfhg' ), '' ),
				'footer_photo_url'  => array( 'link', __( 'Photography notice link', 'cfhg' ), $link_help ),
				'privacy_url'       => array( 'link', __( 'Privacy and cookies link', 'cfhg' ), __( 'Used when no privacy page is set in Settings > Privacy.', 'cfhg' ) ),
			),
		),
	);
}

/**
 * Register panels, sections, settings and controls.
 *
 * @param WP_Customize_Manager $wp_customize Customizer.
 */
function cfhg_customize_register( $wp_customize ) {
	$defaults = cfhg_defaults();

	$wp_customize->add_panel(
		'cfhg_home',
		array(
			'title'       => __( 'CFHG homepage', 'cfhg' ),
			'description' => __( 'Used when the homepage is not built with Elementor.', 'cfhg' ),
			'priority'    => 30,
		)
	);

	$sanitizers = array(
		'text'     => 'sanitize_text_field',
		'inline'   => 'cfhg_sanitize_inline',
		'textarea' => 'cfhg_sanitize_inline',
		'list'     => 'sanitize_textarea_field',
		'link'     => 'cfhg_sanitize_link',
		'email'    => 'sanitize_email',
		'url'      => 'esc_url_raw',
		'checkbox' => 'cfhg_sanitize_checkbox',
		'image'    => 'absint',
		'date'     => 'cfhg_sanitize_date',
		'months'   => 'cfhg_sanitize_months',
	);

	$priority = 40;
	foreach ( cfhg_customizer_fields() as $section_id => $section ) {
		$wp_customize->add_section(
			$section_id,
			array(
				'title'       => $section['title'],
				'panel'       => isset( $section['panel'] ) ? $section['panel'] : '',
				'description' => isset( $section['description'] ) ? $section['description'] : '',
				'priority'    => $priority++,
			)
		);

		foreach ( $section['fields'] as $key => $field ) {
			list( $type, $label, $description ) = $field;

			$wp_customize->add_setting(
				$key,
				array(
					'default'           => isset( $defaults[ $key ] ) ? $defaults[ $key ] : '',
					'sanitize_callback' => $sanitizers[ $type ],
					'transport'         => 'refresh',
				)
			);

			if ( 'image' === $type ) {
				$wp_customize->add_control(
					new WP_Customize_Media_Control(
						$wp_customize,
						$key,
						array(
							'label'       => $label,
							'description' => $description,
							'section'     => $section_id,
							'mime_type'   => 'image',
						)
					)
				);
				continue;
			}

			$control_types = array(
				'inline'   => 'text',
				'list'     => 'textarea',
				'link'     => 'text',
				'months'   => 'text',
				'date'     => 'date',
				'textarea' => 'textarea',
			);

			$wp_customize->add_control(
				$key,
				array(
					'label'       => $label,
					'description' => $description,
					'section'     => $section_id,
					'type'        => isset( $control_types[ $type ] ) ? $control_types[ $type ] : $type,
				)
			);
		}
	}
}
add_action( 'customize_register', 'cfhg_customize_register' );

/**
 * Allow simple inline formatting.
 *
 * @param string $value Value.
 * @return string
 */
function cfhg_sanitize_inline( $value ) {
	return wp_kses( (string) $value, cfhg_inline_kses() );
}

/**
 * Accept relative paths, anchors, mailto and full URLs.
 *
 * @param string $value Value.
 * @return string
 */
function cfhg_sanitize_link( $value ) {
	$value = trim( (string) $value );
	if ( '' === $value ) {
		return '';
	}
	if ( '/' === $value[0] || '#' === $value[0] ) {
		return preg_replace( '#[^a-zA-Z0-9/_\-\#\.\?=&%]#', '', $value );
	}
	return esc_url_raw( $value, array( 'http', 'https', 'mailto', 'tel' ) );
}

/**
 * Checkbox.
 *
 * @param mixed $value Value.
 * @return bool
 */
function cfhg_sanitize_checkbox( $value ) {
	return (bool) $value;
}

/**
 * Date in Y-m-d or empty.
 *
 * @param string $value Value.
 * @return string
 */
function cfhg_sanitize_date( $value ) {
	$value = trim( (string) $value );
	return preg_match( '/^\d{4}-\d{2}-\d{2}$/', $value ) ? $value : '';
}

/**
 * Comma-separated month numbers.
 *
 * @param string $value Value.
 * @return string
 */
function cfhg_sanitize_months( $value ) {
	$months = array_filter(
		array_map( 'intval', explode( ',', (string) $value ) ),
		static function ( $m ) {
			return $m >= 1 && $m <= 12;
		}
	);
	return implode( ',', array_unique( $months ) );
}
