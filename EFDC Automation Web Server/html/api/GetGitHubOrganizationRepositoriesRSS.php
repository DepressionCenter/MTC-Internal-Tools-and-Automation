<?php
/*
	GetGitHubOrganizationRepositoriesRSS.php
	Title: EFDC GetGitHubOrganizationRepositoriesRSS Web Service
	Summary: Gets a list of public GitHub repos belonging to the specified organization and returns it in RSS format.
	Author(s): Gabriel Mongefranco <mongefrg@umich.edu>
	Created Date: 3/27/2024
	Last Modified Date: 3/27/2024
	
	Return values:
		Channel/Category - SUCCESS if successful, or ERROR otherwise
		Channel/Comments - error message, if any
		Item/Title - repository name (sanitized)
		Item/Description - repository description (sanitized)
		Item/Category - SUCCESS if successful, or ERROR otherwise
		Item/Comments - error message, if any
		
	Remarks:
		Only public repos can be retrieved without an API key.
		
		
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

// Configuration variables
$orgUrl = 'https://github.com/[ORG_NAME]';
$orgDataUrl = 'https://github.com/orgs/[ORG_NAME]/repositories?format=json';

// Return values
$githubRepos = null;
$errorMessage = "";
$orgName = ""; // from input params
$orgNameHTMLSanitized = ""; // sanitized for displaying in RSS feed


// Get query string parameters and Sanitize them
if (isset($_GET['orgName'])) {
	$orgName = $_GET['orgName']; // Get alue from URL parameter
	$orgNameHTMLSanitized = htmlentities($orgName, ENT_QUOTES | ENT_QUOTES,ENT_XHTML); // Sanitize for displaying in RSS feed
}


if($orgName != '') {
	// Build GitHub URL for organization
	$orgUrl = str_replace('[ORG_NAME]', $orgName, $orgUrl);
	// Build GitHub API URL for organization
	$orgDataUrl = str_replace('[ORG_NAME]', $orgName, $orgDataUrl); // Build GitHub URL for
} else {
	$errorMessage .= "Organization name was not provided. Please provide a valid GitHub Organization name without spaces, e.g.:  DepressionCenter \n";
	$orgUrl = "";
	$orgDataUrl = "";
}


try {
  if($orgName != '' and $orgDataUrl != '' and $errorMessage == "") {
		// Query GitHub site
		$contents = file_get_contents($orgDataUrl);
		
		if($contents != '' and $contents != '{"error":"Not Found"}' and str_contains($contents, '{"payload":{')) {
			// Decode JSON data
			$githubRepos = json_decode($contents);
		} else {
			$errorMessage .= "Repository not found.\n";
		}
	}
} catch(Exception $e) {
	$errorMessage .= "Could not connect to or parse data from remote site. \n\n" . $e->getMessage() . "\n";	
}
?>

<rss version="2.0">
<channel>
 <title><?php if($errorMessage=="") { echo trim($orgNameHTMLSanitized . " GitHub Repositories"); } else { echo "EFDC Get GitHub Organization Repositories Web Service"; } ?></title>
 <category><?php if($errorMessage=="") {echo "SUCCESS";} else {echo "ERROR";} ?></category>
 <description><?php if($errorMessage=="") {echo 'List of public GitHub repos from @' . $orgNameHTMLSanitized . ". \n(Generated by EFDC Get GitHub Organization Repositories Web Service - github.com/DepressionCenter).";} else { echo "Gets a list of public GitHub repos belonging to the specified organization and returns it in RSS format. Parameters: ?orgName=DepressionCenter";} ?></description>
 <link><?php echo $orgUrl; ?></link>
 <docs>https://michmed.org/efdc-kb</docs>
 <webMaster>efdc-mobiletech@umich.edu</webMaster>
 <copyright>© 2024 Regents of the University of Michigan</copyright>
 <lastBuildDate><?php echo date('r'); ?></lastBuildDate>
 <pubDate><?php echo date('r'); ?></pubDate>
 <ttl>720</ttl>
 <comments><?php echo htmlentities($errorMessage, ENT_QUOTES | ENT_QUOTES,ENT_XHTML); ?></comments>

<?php
// If connected successfully, extract values, sanitize, and re-format as individual RSS items
if ($orgName!="" and !is_null($githubRepos) and $errorMessage=="") {
	foreach ($githubRepos->payload->repositories as $repo) {
		echo "<item>";
		if($repo->name=="") {echo "<title>InvalidRepository</title>";} else {echo "\n<title>" . htmlentities($repo->name, ENT_QUOTES | ENT_QUOTES,ENT_XHTML) . "</title>";}
		if($repo->name=="") {echo "\n<link>" . "https://github.com/" . $orgName . "</link>";} else {echo "<link>" . "https://github.com/" . $orgName . "/" . htmlentities($repo->name, ENT_QUOTES | ENT_QUOTES,ENT_XHTML) . "</link>";}
		if($repo->name=="") {echo "\n<source>" . "https://github.com/" . $orgNameHTMLSanitized . "</source>";} else {echo "<source>" . "https://github.com/" . $orgNameHTMLSanitized . "/" . htmlentities($repo->name, ENT_QUOTES | ENT_QUOTES,ENT_XHTML) . "</source>";}
		if (isset($repo->lastUpdated->timestamp) and $repo->lastUpdated->timestamp != '') {
			$pubDate = date_create($repo->lastUpdated->timestamp);
		} else {
			$pubDate = getdate();
		}
		echo "\n<pubDate>" . $pubDate->format('r') . "</pubDate>";
		echo "<description>" . htmlentities($repo->description, ENT_QUOTES | ENT_QUOTES,ENT_XHTML) . "</description>";
		echo "<comments>" . htmlentities($errorMessage, ENT_QUOTES | ENT_QUOTES,ENT_XHTML) . "</comments>";
		if($errorMessage=='') { echo "<category>SUCCESS</category>"; } else { echo "<category>ERROR</category>"; }
		echo "</item>\n";
	}
}
?>
</channel>
</rss>
