#!/usr/bin/env php
<?php

require "../application/console.php";

$html = <<<EOT
xclr8r writes "Eric Massa, a congressman representing a district in western New York, has a bill ready that would start treating Internet providers like a utility and stop the use of caps. Nearby locales have been used as test beds for the new caps, so this may have made the constituents raise the issue with their representative."<p><a href="http://news.slashdot.org/story/09/06/18/1521237/Bill-Ready-To-Ban-ISP-Caps-In-the-US?from=rss"><img src="http://slashdot.org/slashdot-it.pl?from=rss&amp;op=image&amp;style=h0&amp;sid=09/06/18/1521237" /></a></p><p><a href="http://news.slashdot.org/story/09/06/18/1521237/Bill-Ready-To-Ban-ISP-Caps-In-the-US?from=rss">Read more of this story</a> at Slashdot.</p>
<p><a href="http://feedads.g.doubleclick.net/~at/IJwh0_8_PLqdZAGlHefrlf3bt1M/0/da"><img border="0" ismap="true" src="http://feedads.g.doubleclick.net/~at/IJwh0_8_PLqdZAGlHefrlf3bt1M/0/di" /></a><br />
<a href="http://feedads.g.doubleclick.net/~at/IJwh0_8_PLqdZAGlHefrlf3bt1M/1/da"><img border="0" ismap="true" src="http://feedads.g.doubleclick.net/~at/IJwh0_8_PLqdZAGlHefrlf3bt1M/1/di" /></a></p><img height="1" src="http://feeds2.feedburner.com/~r/Slashdot/slashdot/~4/ZbsetFjjqiU" width="1" />
EOT;

L_Utility::keywords($html);

echo $html . "\n";
