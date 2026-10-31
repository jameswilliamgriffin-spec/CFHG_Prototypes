<?php
/**
 * Default content for the homepage and site-wide settings (Option B).
 *
 * All copy is written for the Caribbean Family History Group from the content
 * the group supplied. Every value can be changed in Appearance > Customize.
 * Lines marked ASSUMPTION need checking with the group.
 *
 * List fields use one item per line, with parts separated by a | character.
 * Paragraph fields use a blank line between paragraphs.
 *
 * @package CFHG
 */

defined( 'ABSPATH' ) || exit;

/**
 * Default values for every theme setting.
 *
 * @return array<string, mixed>
 */
function cfhg_defaults() {
	static $defaults = null;
	if ( null !== $defaults ) {
		return $defaults;
	}

	$defaults = array(
		/* Header */
		'header_join_label'      => __( 'Join', 'cfhg' ),
		'header_join_url'        => '/join/',

		/* Opening panel: words on the left, a photograph in a large circle on the right. */
		'hero_kicker'            => __( 'Volunteer family history group', 'cfhg' ),
		'hero_heading'           => __( '<em>Trace</em> your Caribbean family with people who’ve done it', 'cfhg' ),
		'hero_text'              => __( 'We’re a volunteer group helping people trace families from Jamaica, Barbados, Trinidad, St Kitts and Nevis and right across the Caribbean. We meet on the last Saturday of the month at Solihull Core Library and on Zoom.', 'cfhg' ),
		'hero_primary_label'     => __( 'Come to a meeting', 'cfhg' ),
		'hero_primary_url'       => '#next-meeting',
		'hero_secondary_label'   => __( 'New to this? Start here', 'cfhg' ),
		'hero_secondary_url'     => '#start-here',
		'hero_proof_text'        => __( 'Members join us from across the UK, the USA, Jamaica and Trinidad.', 'cfhg' ),
		'hero_image'             => 0,

		/* Who we are: a white card between two snapshot photographs. */
		'intro_title'            => __( 'Who we are', 'cfhg' ),
		'intro_text'             => __( "We’re like-minded people who enjoy family history. Some of us have been researching for more than forty years; some of us started a few months ago. A few of us are experts in a particular island or in military records.\n\nWe’re all still learning, so we share what we find. Bring a name, a photograph or a question, and someone at the table will usually know where to look next.\n\nMembers join us from across the UK, the USA, Jamaica and Trinidad.", 'cfhg' ),
		'intro_link_label'       => __( 'About the group', 'cfhg' ),
		'intro_link_url'         => '/who-we-are/',
		'intro_photo_1'          => 0,
		'intro_photo_2'          => 0,

		/* Meetings */
		'meeting_kicker'         => __( 'Next meeting', 'cfhg' ),
		'meeting_heading'        => __( 'Your first visit is free', 'cfhg' ),
		'meeting_venue'          => __( 'Solihull Core Library, first floor computer room', 'cfhg' ),
		'meeting_short_where'    => __( 'Solihull Core Library and Zoom', 'cfhg' ),
		/* ASSUMPTION: the Zoom link is sent out by email rather than published. */
		'meeting_online'         => __( 'On Zoom. Email us and we’ll send you the link.', 'cfhg' ),
		'meeting_pattern'        => __( 'The last Saturday of every month, except May, August and December', 'cfhg' ),
		/* ASSUMPTION: start time not supplied. Leave blank to hide it. */
		'meeting_time'           => '',
		'meeting_skip_months'    => '5,8,12',
		'meeting_override_date'  => '',
		'meeting_note'           => __( 'Our AGM takes place every May.', 'cfhg' ),
		'meeting_image_label'    => __( 'Solihull Core Library', 'cfhg' ),
		'meeting_cta_label'      => __( 'Plan your first visit', 'cfhg' ),
		'meeting_cta_url'        => '/whats-on/',
		'meeting_secondary_label'=> __( 'Ask for the Zoom link', 'cfhg' ),
		'meeting_image'          => 0,

		/* Prices */
		'price_first_label'      => __( 'Your first visit', 'cfhg' ),
		'price_first'            => __( 'Free', 'cfhg' ),
		'price_visit_label'      => __( 'Each visit after that', 'cfhg' ),
		'price_visit'            => __( '£5', 'cfhg' ),
		'price_year_label'       => __( 'Annual membership', 'cfhg' ),
		'price_year'             => __( '£20', 'cfhg' ),

		/* The group in numbers. Number | what it means. Every figure is from the group's own document. */
		'numbers_title'          => __( 'Our group in numbers', 'cfhg' ),
		'numbers_list'           => implode(
			"\n",
			array(
				__( '40+ | years: the longest any of us has been researching', 'cfhg' ),
				__( '2011 | the year Solihull Libraries became a FamilySearch affiliate, at our request', 'cfhg' ),
				__( '9 | meetings a year, on the last Saturday of the month', 'cfhg' ),
				__( '3 | island research groups: Jamaica, Barbados, and St Kitts and Nevis', 'cfhg' ),
				__( '4 | countries our members join from', 'cfhg' ),
				__( '£0 | for your first visit', 'cfhg' ),
			)
		),

		/* Start here: five coloured step tiles. */
		'start_kicker'           => __( 'New to family history?', 'cfhg' ),
		'start_heading'          => __( 'Start here: five steps', 'cfhg' ),
		'start_text'             => __( 'You don’t need a subscription or any experience. You need a notebook, some patience and the people in your family who remember.', 'cfhg' ),
		/* Title | text. ASSUMPTION: drafted for the group to check against the 5-steps video. */
		'start_steps'            => implode(
			"\n",
			array(
				__( 'Write down what you know | Start with yourself, then your parents and grandparents. Names, dates, places, and any certificates you have at home.', 'cfhg' ),
				__( 'Talk to your elders | Ask about names, villages, churches and nicknames, and how the family came to Britain or wherever they settled.', 'cfhg' ),
				__( 'Draw your family tree | Put everything on a pedigree chart so you can see what’s missing. Our charts are free to download.', 'cfhg' ),
				__( 'Search the records | Many Caribbean church registers are free on FamilySearch, and every Solihull library gives free access to its digitised collections.', 'cfhg' ),
				__( 'Bring us your brick wall | When you get stuck, come to a meeting. Someone in the room has usually been stuck in the same place.', 'cfhg' ),
			)
		),
		'start_link_label'       => __( 'Watch our five steps video', 'cfhg' ),
		'start_link_url'         => '/research/start-here/',

		/* Research by island */
		'islands_kicker'         => __( 'Research by island', 'cfhg' ),
		'islands_heading'        => __( 'Where was your family from?', 'cfhg' ),
		'islands_text'           => __( 'Every island kept its records differently, so where you start depends on where your family came from. Each guide covers the key records, the archives, what you can search online and who in the group knows that island best.', 'cfhg' ),
		/* Name | link | note. ASSUMPTION: page addresses. */
		'islands_list'           => implode(
			"\n",
			array(
				__( 'Jamaica | /research/jamaica/ | Island research group', 'cfhg' ),
				__( 'Barbados | /research/barbados/ | Island research group', 'cfhg' ),
				__( 'St Kitts and Nevis | /research/st-kitts-and-nevis/ | Island research group', 'cfhg' ),
				__( 'Trinidad and Tobago | /research/trinidad-and-tobago/ |', 'cfhg' ),
				__( 'Other Caribbean islands | /research/other-islands/ |', 'cfhg' ),
			)
		),
		'islands_image'          => 0,

		/* Stories carousel */
		'stories_kicker'         => __( 'Stories and research', 'cfhg' ),
		'stories_heading'        => __( 'From the group', 'cfhg' ),
		'stories_read_label'     => __( 'Read the story', 'cfhg' ),
		'stories_link_label'     => __( 'All stories', 'cfhg' ),
		'stories_link_url'       => '/stories/',
		'stories_category'       => 'stories',

		/* Research help */
		'help_kicker'            => __( 'Research help', 'cfhg' ),
		'help_heading'           => __( 'Stuck? Ask us.', 'cfhg' ),
		'help_text'              => __( 'Between us we cover Jamaica, the other islands and military records. Tell us who you’re looking for and what you’ve tried so far, and we’ll pass your question to the person best placed to help. We’re all volunteers, so a reply can take a little while.', 'cfhg' ),
		'help_cta_label'         => __( 'Ask a research question', 'cfhg' ),
		'help_cta_url'           => '/research/get-help/',
		'help_image'             => 0,

		/* Membership: a photograph band with a large word across it. */
		'join_big'               => __( 'Come along', 'cfhg' ),
		'join_kicker'            => __( 'Membership', 'cfhg' ),
		'join_heading'           => __( 'Join the group for £20 a year', 'cfhg' ),
		'join_text'              => __( 'Come to a meeting first and see if we’re for you. Membership is paid through the Family History Federation. Our AGM is held every May, and committee roles open up each year if you’d like to get more involved.', 'cfhg' ),
		'join_cta_label'         => __( 'Become a member', 'cfhg' ),
		'join_cta_url'           => '/join/',
		'join_secondary_label'   => __( 'Questions? Email us', 'cfhg' ),
		'join_image'             => 0,

		/* Partners. Name | text | link */
		'partners_kicker'        => __( 'Who we work with', 'cfhg' ),
		'partners_heading'       => __( 'The partners we rely on', 'cfhg' ),
		'partners_list'          => implode(
			"\n",
			array(
				__( 'Solihull Libraries | Our meetings happen at Solihull Core Library. At our request it became a FamilySearch affiliate in 2011, and every Solihull library now gives free access to FamilySearch’s digitised records. | https://www.solihull.gov.uk/libraries', 'cfhg' ),
				__( 'The WAWI Project | Why Are West Indians in this Country. We have worked alongside each other since around 2010 and host the Windrush Dinner and Dance together every June. |', 'cfhg' ),
				__( 'Family History Federation | We are a member society of the Federation, which promotes family history research and handles our membership payments. | https://familyhistoryfederation.com/', 'cfhg' ),
			)
		),

		/* Contact and footer */
		'contact_email'          => 'cfhg.sol.birm@gmail.com',
		/* ASSUMPTION: profile addresses not supplied. Leave blank to hide. */
		'social_instagram'       => '',
		'social_x'               => '',
		'social_facebook'        => '',
		'social_youtube'         => '',
		'footer_big'             => __( 'Caribbean Family History Group', 'cfhg' ),
		'footer_tagline'         => __( 'Volunteers helping people trace Caribbean family history. Meeting in Solihull and online.', 'cfhg' ),
		'footer_photo_note'      => __( 'We take photographs at our events. If you’d rather not appear in them, fill in our non-permission form.', 'cfhg' ),
		'footer_photo_url'       => '/photography-and-video/',
		'privacy_url'            => '/privacy-and-cookies/',
		'footer_image'           => 0,
	);

	foreach ( cfhg_home_section_keys() as $section ) {
		$defaults[ 'show_' . $section ] = true;
	}

	return $defaults;
}

/**
 * Homepage sections in their default order.
 *
 * @return string[]
 */
function cfhg_home_section_keys() {
	return array( 'hero', 'intro', 'meeting', 'numbers', 'start', 'islands', 'stories', 'help', 'join', 'partners' );
}

/**
 * Story cards shown until real posts exist in the Stories category.
 *
 * ASSUMPTION: summaries are written from the titles in the group's sitemap.
 *
 * @return array<int, array<string, mixed>>
 */
function cfhg_default_stories() {
	return array(
		array(
			'tags'  => array( __( 'Research', 'cfhg' ) ),
			'meta'  => __( 'Stephen D. Porter', 'cfhg' ),
			'title' => __( 'Tracing ancestors back to the period of slavery', 'cfhg' ),
			'text'  => __( 'Where the usual records stop, and how slave registers and estate papers can take you further back.', 'cfhg' ),
			'url'   => '/stories/tracing-ancestors-back-to-slavery/',
			'image' => 'copying-details-from-a-register',
		),
		array(
			'tags'  => array( __( 'Member story', 'cfhg' ) ),
			'meta'  => __( 'Montserrat', 'cfhg' ),
			'title' => __( 'Ursula’s story: from Montserrat to Africa', 'cfhg' ),
			'text'  => __( 'How one of our members traced her family from Montserrat back to Africa, and the records that got her there.', 'cfhg' ),
			'url'   => '/stories/ursulas-story/',
			'image' => 'notebook-and-old-photographs',
		),
		array(
			'tags'  => array( __( 'Military history', 'cfhg' ) ),
			'meta'  => __( 'Belgium', 'cfhg' ),
			'title' => __( 'The British West India Regiment', 'cfhg' ),
			'text'  => __( 'Finding Caribbean soldiers in military records, and the story behind the photograph of Dennis carrying the regiment’s standard in Belgium.', 'cfhg' ),
			'url'   => '/stories/british-west-india-regiment/',
			'image' => '',
		),
	);
}
