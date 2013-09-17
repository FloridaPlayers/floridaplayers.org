use warnings;
use strict;
use LWP::Simple;
use File::Basename;



my $url = 'http://www.google.com/calendar/ical/webmaster%40floridaplayers.org/public/basic.ics';
my $file = '/calendar.cache';
my $dirname = dirname(__FILE__);

getstore( $url, $dirname . $file);
print "ran it\n";

