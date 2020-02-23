<?php

ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL); 
function bam_reverse_rewrite ($url) {

	$replacements = array(
		'/forum-([0-9]+)\.html/' => 'forumdisplay.php?fid=$1',
		'/forum-([0-9]+)-page-([0-9]+)\.html/' => 'forumdisplay.php?fid=$1&page=$2',
		'/thread-([0-9]+)\.html/' => 'showthread.php?tid=$1 [L,QSA]', 
		'/thread-([0-9]+)-page-([0-9]+)\.html/' =>  'showthread.php?tid=$1&page=$2',
		'/thread-([0-9]+)-lastpost\.html/' => 'showthread.php?tid=$1&action=lastpost',
		'/thread-([0-9]+)-nextnewest\.html/' => 'showthread.php?tid=$1&action=nextnewest',
		'/thread-([0-9]+)-nextoldest\.html/' => 'showthread.php?tid=$1&action=nextoldest',
		'/thread-([0-9]+)-newpost\.html/' => 'showthread.php?tid=$1&action=newpost',
		'/thread-([0-9]+)-post-([0-9]+)\.html/' => 'showthread.php?tid=$1&pid=$2',
		'/post-([0-9]+)\.html/' => 'showthread.php?pid=$1',
		'/announcement-([0-9]+)\.html/' => 'announcements.php?aid=$1',
		'/user-([0-9]+)\.html/' => 'member.php?action=profile&uid=$1', 
		'/calendar-([0-9]+)\.html/' => 'calendar.php?calendar=$1',
		'/calendar-([0-9]+)-year-([0-9]+)\.html/' => 'calendar.php?action=yearview&calendar=$1&year=$2',
		'/calendar-([0-9]+)-year-([0-9]+)-month-([0-9]+)\.html/' => 'calendar.php?calendar=$1&year=$2&month=$3',
		'/calendar-([0-9]+)-year-([0-9]+)-month-([0-9]+)-day-([0-9]+)\.html/' => 'calendar.php?action=dayview&calendar=$1&year=$2&month=$3&day=$4',
		'/calendar-([0-9]+)-week-(n?[0-9]+)\.html/' => 'calendar.php?action=weekview&calendar=$1&week=$2',
		'/event-([0-9]+)\.html/' => 'calendar.php?action=event&eid=$1'
	); 

	foreach ($replacements as $key => $value) {
		$url_preg = preg_replace($key, $value, $url);
		
		// We only need to do one replacement, as ALL htaccess directives are L,QSA
		if (strlen($url_preg) != strlen($url)) {
			break;
		}
	}
    $url = $url_preg; 
    return $url;

	// $content = preg_replace(array_keys( $replacements ), array_values( $replacements ), $url);

}


/*
function printStrings($strings) {

    foreach ($strings as $line) {
        echo $string;
        echo "br />";
    }
}

$valuesToTry = array(




);


$results = array();
*/ 






			
	

/* ORIGINALS IN HTACCESS: 

	$replacements = array(
		'^forum-([0-9]+)\.html$' => 'forumdisplay.php?fid=$1 [L,QSA]',
		'^forum-([0-9]+)-page-([0-9]+)\.html$' => 'forumdisplay.php?fid=$1&page=$2 [L,QSA]',
		'^thread-([0-9]+)\.html$' => 'showthread.php?tid=$1 [L,QSA]', 
		'^thread-([0-9]+)-page-([0-9]+)\.html$' =>  'showthread.php?tid=$1&page=$2 [L,QSA]',
		'^thread-([0-9]+)-lastpost\.html$' => 'showthread.php?tid=$1&action=lastpost [L,QSA]',
		'^thread-([0-9]+)-nextnewest\.html$' => 'showthread.php?tid=$1&action=nextnewest [L,QSA]',
		'^thread-([0-9]+)-nextoldest\.html$' => 'showthread.php?tid=$1&action=nextoldest [L,QSA]',
		'^thread-([0-9]+)-newpost\.html$' => 'showthread.php?tid=$1&action=newpost [L,QSA]',
		'^thread-([0-9]+)-post-([0-9]+)\.html$' => 'showthread.php?tid=$1&pid=$2 [L,QSA]',
		'^post-([0-9]+)\.html$' => 'showthread.php?pid=$1 [L,QSA]',
		'^announcement-([0-9]+)\.html$' => 'announcements.php?aid=$1 [L,QSA]',
		'^user-([0-9]+)\.html$' => 'member.php?action=profile&uid=$1 [L,QSA]', 
		'^calendar-([0-9]+)\.html$' => 'calendar.php?calendar=$1 [L,QSA]',
		'^calendar-([0-9]+)-year-([0-9]+)\.html$' => 'calendar.php?action=yearview&calendar=$1&year=$2 [L,QSA]',
		'^calendar-([0-9]+)-year-([0-9]+)-month-([0-9]+)\.html$' => 'calendar.php?calendar=$1&year=$2&month=$3 [L,QSA]',
		'^calendar-([0-9]+)-year-([0-9]+)-month-([0-9]+)-day-([0-9]+)\.html$' => 'calendar.php?action=dayview&calendar=$1&year=$2&month=$3&day=$4 [L,QSA]',
		'^calendar-([0-9]+)-week-(n?[0-9]+)\.html$' => 'calendar.php?action=weekview&calendar=$1&week=$2 [L,QSA]',
		'^event-([0-9]+)\.html$' => 'calendar.php?action=event&eid=$1 [L,QSA]'
	); */





$valuesToTry = array(
    'https://community.mybb.com/forum-176.html',
    'https://community.mybb.com/user-72858.html',
    'https://community.mybb.com/thread-226579-post-1343836.html#pid1343836',
    'https://community.mybb.com/calendar-1-year-2020-month-1-day-28.html',
    'https://community.mybb.com/forum-199.html',
    'https://community.mybb.com/post-1342108.html#pid1342108',
    'https://community.mybb.com/announcement-32.html',
    'https://community.mybb.com/thread-226401-lastpost.html',
    'https://community.mybb.com/thread-226401-newpost.html',
    'https://community.mybb.com/thread-226401-nextnewest.html',
    'https://community.mybb.com/thread-226401-nextoldest.html',
    'https://community.mybb.com/thread-226401-lastpost.html',
    'https://community.mybb.com/thread-213361-page-3.html',
    'https://community.mybb.com/event-20.html',
    'https://community.mybb.com/forum-213361-page-3.html',
);

foreach ($valuesToTry as $value) {
    echo "original: " . $value . "<br />Replacement: ";
    echo bam_reverse_rewrite($value);
    echo "<br /><br />"; 
}

?>