<?php
/*
	GetMCommunityGroupMembersRSS.php
	Title: EFDC GetMCommunityGroupMembersRSS Web Service
	Summary: Gets members of public MCommunity groups in RSS format (intended for consumption via PowerAutomate).
	Author(s): Gabriel Mongefranco <mongefrg@umich.edu>
	Created Date: 11/16/2023
	Last Modified Date: 12/01/2023
	
	Return values:
		Channel/Category - SUCCESS if successful, or ERROR otherwise
		Channel/Comments - error message, if any
		Item/Title - group name (sanitized)
		Item/Description - unique group members in CSV format, including members of sub-groups and external members
		Item/Category - SUCCESS if successful, or ERROR otherwise
		Item/Comments - error message, if any
		
	Remarks:
		The MCommunity Group's visibility needs to be set to Public in MCommunity
		(Settings -> Who can view members -> Anyone with an @umich.edu address)
		For Python and Power Automate Desktop versions, see:
		https://github.com/DepressionCenter/MTC-Internal-Tools-and-Automation/tree/main
		
		
*/

// Turn off error reporting (only use in Development environment)
error_reporting(0);
ini_set('display_errors',0);
?>
<?php
header( "Content-type: text/xml");
echo '<?xml version="1.0" encoding="UTF-8" ?>';
?>
 
<?php
// Runtime config
set_time_limit(10);

// LDAP config
$ldapserver = 'ldap.umich.edu';
$ldaptree    = "ou=Groups,dc=umich,dc=edu";

// Return values
$memberArray = array();
$memberArrayString = "";
$errorMessage = "";
$groupName = ""; // from input params
$groupNameLDAPSanitized = ""; // sanitized for use in LDAP search queries
$groupNameHTMLSanitized = ""; // sanitized for displaying in RSS feed


// Sanitize function
function sanitizeLdapQueryCN($val) {
    $sanitized=array('\\' => '\5c',
                     '*' => '\2a',
                     '(' => '\28',
                     ')' => '\29',
                     "\x00" => '\00',
					 '.' => ' ', // work-around for group names that contain periods
					 '—' => '-' // Long dash to short dash
					 );

	$val = explode("@", $val, 2)[0];
    $returnValue = str_replace(array_keys($sanitized),array_values($sanitized),$val);
	
	return $returnValue;
}


// Get query string parameters and Sanitize them
if (isset($_GET['groupName'])) {
	$groupName = trim(explode("@", trim($_GET['groupName']), 2)[0]); // Remove @umich.edu and @med.umich.edu
	$groupNameLDAPSanitized = sanitizeLdapQueryCN($groupName); // Sanitize for use in LDAP search query
	$groupNameHTMLSanitized = htmlentities($groupName, ENT_QUOTES | ENT_QUOTES,ENT_XHTML); // Sanitize for displaying in RSS feed
}
if($groupName=="") {
		$errorMessage .= "Group name was not provided. \nPlease provide a valid MCommunity group name or email address, e.g.:  /GetMCommunityGroupMembersRSS?groupName=MyMCommunityGroup@umich.edu \n";
	}


// Get data from AD
try {
	$ldapConn = ldap_connect("ldap://".$ldapserver);
	if ($ldapConn) {
		// Bind anonymously
		// See https://documentation.its.umich.edu/node/271/ details about public access to UMICH MCommunity LDAP
		$ldapbind = ldap_bind($ldapConn);
	}
	if($ldapbind and $groupName!="" and $errorMessage=="")
	{
		// Bind was successful. Query the LDAP directory.
		$ldapSearchResult = ldap_search(
								$ldapConn,
								$ldaptree,
								"(cn=".$groupNameLDAPSanitized.")",
								array("dn","cn","displayName","mail","umichgroupemail","umichprivate","description","membersonly","member","rfc822mail")
								) or die ("Error in search query.");
        $ldapData = ldap_get_entries($ldapConn, $ldapSearchResult);
	}
	ldap_close($ldapConn);
} catch (Exception $ex) {
	$errorMessage .= "Could not connect to LDAP server. \n";
}

// If connected successfully, extract list of group members, sanitize, and exclude duplicates
if ($groupName!="" and $errorMessage=="") {
	// Proceed if there are results
	if(isset($ldapData["count"]) and $ldapData["count"]>0) {
		// Check if group is private
		if(isset($ldapData[0]["umichprivate"]) and strtolower($ldapData[0]["umichprivate"][0])=="true") {
			// Private group, so do not return any results
			$errorMessage .= "This MCommunity Group is private. Members cannot be shown. To show private groups, you must use a service that calls the MCommunity API. \n";
		} else {
			// Public group, so return results if available
			
			// Add umich members to the results, if any
			if (isset($ldapData[0]["member"]) and $ldapData[0]["member"]["count"]>0) {
				foreach($ldapData[0]["member"] as $x => $mbr) {
					if($x != "count") {
						$cleanMbr = strtolower(
							trim(
								str_replace(
									"@umich.edu",
									"",
									str_replace(
										"@med.umich.edu",
										"",
										explode(",",str_replace("uid=","",$mbr),2)[0]
										)
									)
								)
							);
						// Append to memberArray only if not already there
						$memberArray = array_unique(array_merge($memberArray, array($cleanMbr)));
						
					}
				}
			}
			
			// Add external members to the results, if any
			if (isset($ldapData[0]["rfc822mail"]) and $ldapData[0]["rfc822mail"]["count"]>0) {
				foreach($ldapData[0]["rfc822mail"] as $x => $mbr) {
					if($x != "count") {
						$cleanMbr = strtolower(
							trim(
								str_replace(
									"@umich.edu",
									"",
									str_replace(
										"@med.umich.edu",
										"",
										explode(",",str_replace("uid=","",$mbr),2)[0]
										)
									)
								)
							);
						// Extract email from optional MCommunity format: first last <email@domain.com>
						$cleanMbr = str_replace( ">", "", preg_replace("/(.)*[<]+/i", "", $cleanMbr) );
						
						// Append to memberArray only if not already there
						$memberArray = array_unique(array_merge($memberArray, array($cleanMbr)));
						
					}
				}
			}
			
			// Final array sort and deduplication, just in case something above did not work
			sort($memberArray, SORT_STRING|SORT_FLAG_CASE);
			$memberArray = array_unique($memberArray, SORT_STRING|SORT_FLAG_CASE);
			
			
			// Convert member array to CSV string to return in RSS feed
			if ($errorMessage=="") {
				$memberArrayString = "";
				foreach($memberArray as $mbr) {
					$memberArrayString .= $mbr.",";
				}
				$memberArrayString = rtrim($memberArrayString, ",");
			}
		}
	} else {
		// No results, so return error
		$errorMessage .= "Group not found: '".$groupNameHTMLSanitized."' \n";
	}
}

?>
<rss version="2.0">
<channel>
 <title>EFDC GetMCommunityGroupMembersRSS Web Service</title>
 <category><?php if($errorMessage=="") {echo "SUCCESS";} else {echo "ERROR";} ?></category>
 <description>Gets members of public MCommunity groups in RSS format (intended for consumption via PowerAutomate). Parameters: ?groupName=group@umich.edu</description>
 <link>https://michmed.org/8NrNR</link>
 <docs>https://michmed.org/8NrNR</docs>
 <webMaster>efdc-mobiletech@umich.edu</webMaster>
 <copyright>© 2023 Regents of the University of Michigan</copyright>
 <lastBuildDate><?php date('r'); ?></lastBuildDate>
 <pubDate><?php date('r'); ?></pubDate>
 <ttl>720</ttl>
 <comments><?php echo $errorMessage; ?></comments>
 
 <item>
	<title><?php if($groupName=="") {echo "InvalidGroupName";} else {echo $groupNameHTMLSanitized;} ?></title>
	<link><?php if($groupName!="" and $errorMessage=="") { echo "https://mcommunity.umich.edu/group/".str_replace(" ","%20",$groupName); } ?></link>
	<source><?php if($groupName!="" and $errorMessage=="") { echo "https://mcommunity.umich.edu/group/".str_replace(" ","%20",$groupName); } ?></source>
	<pubDate><?php date('r'); ?></pubDate>
	<description><?php echo $memberArrayString; ?></description>
	<comments><?php echo $errorMessage; ?></comments>
	<category><?php if($errorMessage=="") {echo "SUCCESS";} else {echo "ERROR";} ?></category>
 </item>
 
</channel>
</rss>