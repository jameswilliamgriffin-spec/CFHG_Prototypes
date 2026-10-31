/**
 * Content shared by every option's preview build, so the inner page and the
 * sample article stay identical across options. Copy is drawn from the
 * group's own document; the research article was drafted for review.
 */

/** Sample posts for the stories pages. Each option maps its own images. */
export const POST_COPY = [
	{
		title: 'Tracing ancestors back to the period of slavery',
		cats: ['Stories', 'Research'],
		date: '2026-10-14',
		excerpt: 'Where the usual records stop, and how slave registers and estate papers can take you further back.',
		image: 'researcher-taking-notes-from-records',
		href: 'single',
	},
	{
		title: 'Ursula’s story: from Montserrat to Africa',
		cats: ['Stories', 'Member story'],
		date: '2026-10-02',
		excerpt: 'How one of our members traced her family from Montserrat back to Africa, and the records that got her there.',
		image: 'research-notes-and-old-photographs',
		href: 'archive',
	},
	{
		title: 'The British West India Regiment',
		cats: ['Stories', 'Military history'],
		date: '2026-09-19',
		excerpt: 'Finding Caribbean soldiers in military records, and the story behind the photograph of Dennis carrying the regiment’s standard in Belgium.',
		image: '',
		href: 'archive',
	},
	{
		title: 'Jewish ancestry in Jamaica',
		cats: ['Stories', 'Research'],
		date: '2026-09-05',
		excerpt: 'Jamaica’s Jewish community goes back to the 1600s, and its records can help families with Jewish ancestry find where they fit.',
		image: 'caribbean-map-research',
		href: 'archive',
	},
	{
		title: 'The Windrush Dinner and Dance',
		cats: ['Stories', 'News'],
		date: '2026-06-30',
		excerpt: 'Our annual evening with the WAWI Project, held every June.',
		image: '',
		href: 'archive',
	},
];

/** "Who we are": the standard inner page. Pass an escaped email address. */
export const innerProse = ({ mail, email }) => `
<p>We are like-minded people who enjoy family history research. We are all still learning, and we share our resources and what we have found along the way.</p>
<p>Some of us have been researching for more than forty years; some of us started a few months ago. A few of us are experts in particular islands or in military records. Since the pandemic moved our meetings online, members have joined us from across the UK, the USA, Jamaica and Trinidad.</p>
<h2 id="our-history">Our history</h2>
<p>In 2006 and 2007, Solihull Libraries and Solihull College ran a series of ten-week family history courses for people tracing ancestors from the Caribbean. They were the first full-length Caribbean family history courses in the UK, and were funded by the European Social Fund’s Equal Engage programme.</p>
<p>At our request, Solihull Central Library became an affiliate FamilySearch Centre in 2011, so that members could build up a collection of Caribbean microfilms at the library for anyone to use. Until then, most of those records were not online, and members travelled to the London Family History Centre to see them.</p>
<p>In 2017 the microfilm service was replaced by online access to digitised collections, which are free to use at FamilySearch affiliate libraries. Solihull became the first local authority in the UK to have every one of its libraries designated as a FamilySearch affiliate.</p>
<blockquote><p>Every library in Solihull now gives free access to FamilySearch’s digitised Caribbean records.</p></blockquote>
<h2 id="partners">Who we work with</h2>
<h3>Solihull Libraries</h3>
<p>Our meetings take place at Solihull Core Library, and each year the library gives us space for a Black History Month exhibition.</p>
<h3>The WAWI Project</h3>
<p>Why Are West Indians in this Country has been our community partner since around 2010. We work closely together and promote each other’s events, and when WAWI is invited to speak at community events they make time for us to say a few words too. Together we host the Windrush Dinner and Dance every June.</p>
<h3>Family History Federation</h3>
<p>The Federation promotes family history research and encourages people to join its member societies. We are a member, and our membership payments are handled through the Federation.</p>
<h2 id="meetings">Meetings and membership</h2>
<ul>
	<li>We meet on the last Saturday of the month, except in May, August and December.</li>
	<li>Meetings are on Zoom and in the computer room on the first floor of Solihull Core Library.</li>
	<li>Your first visit is free. After that it is £5 a visit, or £20 a year for membership.</li>
	<li>Our AGM is held every May, when committee roles open up.</li>
</ul>
<h2 id="committee">The committee</h2>
<table>
	<thead><tr><th scope="col">Role</th><th scope="col">What they do</th></tr></thead>
	<tbody>
		<tr><td>Chair</td><td>Represents the group and chairs meetings, making sure everyone gets a say.</td></tr>
		<tr><td>Secretary</td><td>Takes the minutes at meetings.</td></tr>
		<tr><td>Communications</td><td>Answers the group’s emails, contacts speakers and keeps members informed.</td></tr>
		<tr><td>Treasurer</td><td>Looks after membership fees, the accounts and spending.</td></tr>
		<tr><td>Senior researcher</td><td>Answers research questions about Jamaica and other islands.</td></tr>
		<tr><td>Military research</td><td>Answers questions about military records.</td></tr>
		<tr><td>Membership secretary</td><td>Looks after membership applications and queries.</td></tr>
		<tr><td>Events co-ordinator</td><td>Plans meetings, talks, socials and training.</td></tr>
		<tr><td>Newsletter co-ordinator</td><td>Puts together the quarterly newsletter.</td></tr>
	</tbody>
</table>
<p>The roles are flexible, because we all give our time freely. If you would like to help, tell us at a meeting or by email.</p>
<h2 id="photography">Photographs and videos</h2>
<p>We take photographs and videos at our events. Coming to an event means you agree to appear in them, unless you have filled in and returned our non-permission form.</p>
<h2 id="privacy">Your privacy</h2>
<p>We only collect what we need to reply to you and run the group: usually your name, your email address and your message. We do not sell or share your details for marketing, and we keep enquiry emails for up to 12 months unless we are still in touch. You can ask to see, correct or delete what we hold about you at any time.</p>
<h2 id="contact">Contact us</h2>
<p>Email <a href="${mail}">${email}</a>. We are all volunteers, so a reply can take a little while.</p>`;

/** "Tracing ancestors back to the period of slavery": the sample article. */
export const singleProse = ({ home }) => `
<p>For most Caribbean families, the paper trail gets harder around 1838. That was the year apprenticeship ended and people who had been enslaved in the British Caribbean became fully free. Before then, records were rarely kept about enslaved people as individuals with surnames, so the usual sources start to run out.</p>
<p>It is still possible to go further back. It takes patience, and it helps to know which records exist and what they can and cannot tell you.</p>
<h2>Start from 1838 and work backwards</h2>
<p>Before you go near the earlier records, get as far as you can with baptisms, marriages and burials in church registers from the 1830s and 1840s. Many people were baptised as adults in the years around emancipation, often with a surname recorded for the first time. The church, the parish and any estate named in these records are your best clues for the next step.</p>
<h2>The slave registers, 1813 to 1834</h2>
<p>From 1813, owners in the British colonies had to register the people they enslaved, and update the registers every few years until 1834. The registers usually give a first name, an approximate age, and whether the person was born in Africa or in the Caribbean. Later registers note births, deaths and sales since the previous return.</p>
<p>Copies are held at The National Archives at Kew in series T 71, and they can be searched online through Ancestry. Because there are no surnames, the way in is usually the name of the estate or the owner from your later records.</p>
<h2>Compensation records</h2>
<p>When slavery was abolished, the British government paid compensation to the owners, not to the people who had been enslaved. The claims list estates and owners, and can confirm who owned the estate where your ancestor lived. The Legacies of British Slavery database at University College London lets you search the claims free online.</p>
<h2>Estate papers and island archives</h2>
<p>Some estate records survive, such as plantation journals, inventories and lists of workers. They are scattered between archives in the Caribbean and in Britain, and some are still in private hands. Island archives, such as the Jamaica Archives in Spanish Town and the Barbados Department of Archives, are worth contacting directly.</p>
<h2>Matching people across the records</h2>
<ol>
	<li>Note the estate, the parish and any owner’s name from your records after 1838.</li>
	<li>Find that estate in the 1834 register, then work back through the earlier registers.</li>
	<li>Use ages to match people from one register to the next, allowing for some guesswork in the ages recorded.</li>
	<li>Look for mothers and children listed together, which can reveal family groups.</li>
</ol>
<blockquote><p>A first name and an age on a list is not much. Put three registers side by side, and a family can start to appear.</p></blockquote>
<h2>Bring it to a meeting</h2>
<p>This kind of research is slow, and it can be hard going, emotionally as well as practically. Several of our members have done it and are glad to help. <a href="${home}#next-meeting">Bring what you have found to one of our meetings.</a></p>`;
