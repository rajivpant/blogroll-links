# Seventeen years ago, I built a tool for the open Web. This weekend, I modernized it using Synthesis Coding with Claude Code AI

In 2008, I released a WordPress plugin called [Blogroll Links](https://wordpress.org/plugins/blogroll-links/). The plugin displayed lists of links on WordPress pages using a shortcode. But the idea behind it was more ambitious than the implementation might suggest.

I built Blogroll Links because I believed people should control their own online presence. At the time, I was excited about [XFN](http://gmpg.org/xfn/) — the XHTML Friends Network — a microformat that let you declare relationships between websites using simple HTML attributes. When you linked to a friend's site, you could add `rel="friend met"` to indicate you'd actually met them in person. When you linked to your own profiles across different domains, you'd use `rel="me"` to declare they were all you.

This was greater than just markup. It was a vision: a web where your identity and relationships lived in open, portable, machine-readable formats rather than locked inside proprietary platforms. Google even launched a [Social Graph API](https://en.wikipedia.org/wiki/Social_Graph_API) to help applications discover these connections.

Then Facebook and Twitter captured most of the social graph, and the open web approach faded from mainstream attention.

## The open Web vision didn't die — it evolved

That vision is now making a comeback, wearing different clothes.

The [Fediverse](https://en.wikipedia.org/wiki/Fediverse) — the network of interconnected servers running Mastodon, Pixelfed, and dozens of other services — operates on the principle I was excited about in 2008. Instead of everyone's social graph living inside one big tech company's database, it's distributed across thousands of independently operated servers that speak a common protocol.

[ActivityPub](https://en.wikipedia.org/wiki/ActivityPub), the W3C standard powering this network, is essentially what we dreamed XFN would enable, but with richer capabilities. The [Social Web Foundation](https://socialwebfoundation.org/) launched in late 2024 to expand this ecosystem. WordPress itself — the same platform I built Blogroll Links for — now supports ActivityPub, meaning any WordPress site can become part of the Fediverse.

The IndieWeb movement calls this approach [POSSE](https://indieweb.org/POSSE): Post on your Own Site, Syndicate Elsewhere. Own your content. Own your relationships. Participate in networks without surrendering control.

My 17-year-old plugin, designed to help people manage links with XFN relationship tags, felt relevant again. But it needed updating — incompatible with modern PHP, with security practices that needed modernization, and an admin panel that needed work.

## Senior product and engineering executives should keep their skills of building products sharp using Synthesis Coding

Let me address the obvious question. Throughout my career — as CTO of The New York Times, Chief Product and Technology Officer at The Wall Street Journal and Hearst Magazines, and earlier leading technology for Condé Nast's digital brands including Reddit — I've led teams of hundreds of engineers building systems serving millions of users.

So why did I personally update my old WordPress plugin?

The answer involves both philosophy and methodology.

The philosophy: I've never believed that seniority means distance from the work. The best product and technology leaders I know maintain what I call "technical currency" — the ability to understand systems from the inside, not just from dashboards and reports. When you can still build, you evaluate architectural decisions differently. You ask better questions. You detect problems earlier.

The methodology: a practice I've been using and writing about called [Synthesis Coding](https://rajiv.com/blog/2025/11/09/synthesis-engineering-the-professional-practice-emerging-in-ai-assisted-development/).

## Synthesis Coding changes what's possible for senior leaders

Synthesis Coding is my term for the disciplined integration of human expertise with AI capabilities to build production-grade software. It's distinct from what some people call "vibe coding" — using AI to quickly hack together prototypes or explore ideas. Vibe coding has its place, but it's not how you ship software that needs to be secure, maintainable, and reliable.

The Synthesis Engineering practice rests on [four principles](https://rajiv.com/blog/2025/11/09/the-synthesis-engineering-framework-how-organizations-build-production-software-with-ai/):

**Human architectural authority.** I decide the security requirements, the coding standards, the compatibility targets. AI implements within those constraints — it doesn't establish them.

**Systematic quality standards.** The same rigor applies to AI-generated code as human-written code. Review it. Test it. Understand it before shipping it.

**Active system understanding.** I, or others, need to be able to debug this code if something breaks in production. That means no accepting code humans don't comprehend.

**Iterative context building.** AI gets more effective as context accumulates. Rich architectural documentation, clear constraints, and maintained session context produce better results over time.

What this enables: a senior leader can maintain hands-on technical capability without it consuming the time it once required. I can personally execute a modernization project that would have taken days, completing it in a focused session while maintaining the engineering discipline that production systems demand.

## The Blogroll Links modernization: a case study in practice

Here's what the plugin needed:

The original code had some security gaps — I was using `sprintf()` with user input in database queries, which modern standards would flag. The admin settings panel needed attention; form fields weren't saving values reliably. PHP 8 compatibility required updates throughout, with better variable handling and null checks. The capability check for admin access used a pattern that's since been deprecated.

Standards evolve, and I've evolved with them.

Using [Synthesis Coding with Claude Code](https://rajiv.com/blog/2025/11/09/synthesis-engineering-with-claude-code-technical-implementation-and-workflows/), I approached this systematically:

**I established the constraints.** WordPress 6.x compatibility. PHP 7.4 minimum with 8.x full support. GPL-2.0-or-later licensing for WordPress.org compliance. Security best practices throughout. WordPress coding standards followed.

**AI executed the implementation.** Refactored SQL queries to use `$wpdb->prepare()`. Added nonce verification for CSRF protection. Implemented proper output escaping with `esc_html()`, `esc_attr()`, and `esc_url()`. Rebuilt the admin panel with reliable form handling. Added PHPDoc documentation throughout. Created a PHPUnit test suite.

**I reviewed, tested, and validated.** Set up a local WordPress environment using wp-env. Created test data — link categories, sample links. Verified the shortcode rendered correctly. Confirmed the admin panel saved and loaded values properly. Checked for PHP warnings with debug mode enabled.

The result: [version 3.0.0](https://github.com/rajivpant/blogroll-links), a complete modernization. The plugin now works correctly on current WordPress and PHP versions, follows security best practices, and is ready for another decade of service.

The [WordPress.org plugin page](https://wordpress.org/plugins/blogroll-links/) is live. You can also find my [WordPress.org contributor profile](https://profiles.wordpress.org/rajivpant/) there — a reminder that some of us who now lead large product and technology organizations started by contributing to open source communities.

## This plugin still matters for the open Web

Blogroll Links lets you display curated lists of links on your WordPress site, with full support for XFN relationship markup.

In the Fediverse era, this matters.

When you publish a blogroll on your own site — your own domain, under your control — you're practicing the POSSE principle. Those links, those declared relationships, exist independently of any platform. If Mastodon disappears tomorrow, your blogroll remains. If Twitter (or X, or whatever it's called next week) changes its API again, your links are unaffected.

The XFN support means these aren't just lists — they're semantic declarations. `rel="me"` links help verify your identity across the web. `rel="friend"` and `rel="colleague"` links create a machine-readable social graph that belongs to you, not to a platform.

This is what I believed in 2008, and I still believe it. The Fediverse proves the model works. ActivityPub proves open protocols can scale. The work happening with WordPress and ActivityPub integration proves that the tools people already use can participate in this ecosystem.

Tools like Blogroll Links are part of this infrastructure. They're not glamorous. They don't have venture funding or growth metrics. They help people own their piece of the web.

## What this means for product and technology leadership

I'm sharing this story because I think it illustrates something important about where product and technology leadership is heading.

The economic pressures on organizations are real. The layers of middle management that once separated senior leaders from technical implementation are compressing. AI capabilities are accelerating this compression.

In this environment, senior product and technology leaders who can go deep when needed — who maintain technical currency rather than just talking points — bring value. They can evaluate architectural proposals from experience, not just slides. They can sense when something is wrong because they've felt similar problems in their own hands.

Synthesis Coding makes this practical. It's not about spending all your time coding instead of leading. It's about having a methodology that lets you engage directly with technical work when it matters, at a pace that doesn't sacrifice the strategic perspective your role requires.

I modernized a WordPress plugin this weekend. It took a few hours. I made architectural decisions, reviewed security-critical code, validated functionality, and published to a repository used by thousands of sites. And then I went back to the strategic work that occupies most of my time.

That combination — strategic leadership with maintained technical capability — is what I think the best product and technology executives will embody going forward.

## The invitation

If you're interested in Synthesis Coding as a practice, I've written a [series exploring the methodology](https://rajiv.com/blog/2025/11/09/synthesis-engineering-the-professional-practice-emerging-in-ai-assisted-development/) in depth, including the [organizational framework](https://rajiv.com/blog/2025/11/09/the-synthesis-engineering-framework-how-organizations-build-production-software-with-ai/) and [specific technical workflows](https://rajiv.com/blog/2025/11/09/synthesis-engineering-with-claude-code-technical-implementation-and-workflows/).

If you're a WordPress user who cares about the open web, the [Blogroll Links plugin](https://wordpress.org/plugins/blogroll-links/) is available and actively maintained again. Use it to curate links on your site. Add XFN relationships. Own your corner of the web.

And if you're a product or technology leader — or aspiring to be one — consider this: what tools from your past deserve attention? What projects taught you something fundamental about how software works? Sometimes the most valuable thing you can do is return to something you built years ago and bring it forward with everything you've learned since.

The open web is worth building. Synthesis Coding makes more building possible. And seventeen years later, I'm still excited about both.

---

*[Blogroll Links](https://wordpress.org/plugins/blogroll-links/) version 3.0.0 is available now on WordPress.org. The source code is on [GitHub](https://github.com/rajivpant/blogroll-links). Read my original 2008 post about the plugin [here](https://rajiv.com/blog/2008/02/10/blogroll-links/).*

---

**About the Author**

*Rajiv Pant is President of Flatiron Software and Snapshot AI, where he leads organizational growth and AI innovation. He is former Chief Product & Technology Officer at The Wall Street Journal, The New York Times, and Hearst Magazines. Earlier in his career, he headed technology for Condé Nast's digital brands including Reddit. Rajiv has been working with AI in software engineering since the early days of natural language processing and was an early investor and advisor to AI search company You.com. He coined the terms "Synthesis Engineering" and "Synthesis Coding" to describe the systematic integration of human expertise with AI capabilities in professional software development. Connect with him on [LinkedIn](https://www.linkedin.com/in/rajivpant/) or read more at [rajiv.com](https://rajiv.com/).*
