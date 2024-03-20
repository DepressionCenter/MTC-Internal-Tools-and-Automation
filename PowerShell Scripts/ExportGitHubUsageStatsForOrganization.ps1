# Export Git Hub Usage Stats For Organization
# This PowerShell script can be used to export daily GitHub repository statistics,
# for all repositories under an organization.
# 
# Author: Gabriel Mongefranco (@gabrielmongefranco)
#         See README for other contributors, if any.
# Created: 3/15/24
# License: See attached license file
# Website: https://github.com/DepressionCenter  |  https://depressioncenter.org


# Set the working directory and inputs
$organizationName = 'ENTER_ORGANIZATION_NAME_HERE_WITHOUT_SPACES'
$username = 'ENTER_GITHUB_USERNAME_HERE'
# To use interactive login, leave the apiToken string blank and uncomment where indicated in the authentication section
$apiToken = ConvertTo-SecureString 'ENTER_GITHUB_API_TOKEN_HERE' -AsPlainText -Force
$jsonOutputPath = 'c:\GitHubStats\github-stats.json'
$csvOutputPath = 'c:\GitHubStats\github-stats.csv'


# Ensure you have PowerShellForGitHub module installed
Import-Module PowerShellForGitHub



# Begin
Clear-Host
Write-Host -f Yellow " === Export GitHub Usage Stats For Organization Script === "

# Authentication
# To authenticate interactively, comment this section, and use this instead: Set-GitHubAuthentication $username
Write-Host "Authenticating to GitHub API as $username..."
$githubCredential = New-Object System.Management.Automation.PSCredential $username, $apiToken
Set-GitHubAuthentication -Credential $githubCredential -SessionOnly
$apiToken = ''


# Set some GitHub parameters
Set-GitHubConfiguration -DisableTelemetry

# Get all repositories under the given organization
Write-Host "Getting repos..."
$repoCount = [int]0
try
{
	$repos = Get-GitHubRepository -OrganizationName $organizationName
	$repoCount = [int]$repos.Count
} catch {
	Write-Host -f Red "Error while getting repository information. Ensure the organization and credentials are correct."
}

if ($repoCount -eq 0)
{
	Write-Host -f Red "No repositories found."
	Start-Sleep -Seconds 3
	Exit
} else {
	Write-Host "Found $repoCount repo(s)."
}

# Add custom properties to the repository variable
$repos | Add-Member -Force -MemberType NoteProperty -Name contributions_count -Value @()
$repos | Add-Member -Force -MemberType NoteProperty -Name contributors -Value @()
$repos | Add-Member -Force -MemberType NoteProperty -Name contributors_count -Value 0
$repos | Add-Member -Force -MemberType NoteProperty -Name referrer_traffic -Value @()
$repos | Add-Member -Force -MemberType NoteProperty -Name referrer_traffic_count -Value 0
$repos | Add-Member -Force -MemberType NoteProperty -Name referrer_traffic_uniques -Value 0
$repos | Add-Member -Force -MemberType NoteProperty -Name path_traffic -Value @()
$repos | Add-Member -Force -MemberType NoteProperty -Name path_traffic_count -Value 0
$repos | Add-Member -Force -MemberType NoteProperty -Name path_traffic_uniques -Value 0
$repos | Add-Member -Force -MemberType NoteProperty -Name view_traffic -Value @()
$repos | Add-Member -Force -MemberType NoteProperty -Name view_traffic_count -Value 0
$repos | Add-Member -Force -MemberType NoteProperty -Name view_traffic_uniques -Value 0
$repos | Add-Member -Force -MemberType NoteProperty -Name clone_traffic -Value @()
$repos | Add-Member -Force -MemberType NoteProperty -Name clone_traffic_count -Value 0
$repos | Add-Member -Force -MemberType NoteProperty -Name clone_traffic_uniques -Value 0
Write-Host "Getting usage stats..."

# Contributions
$repos | ForEach-Object {$_.contributions_count = $_.stats.contributions }

# Contributors
Write-Host "Getting contributors..."
$repos | ForEach-Object {$_.contributors = Get-GitHubRepositoryContributor -Uri $_.url }
$repos | ForEach-Object {$_.contributors_count = ($_.contributors | Select-Object -ExpandProperty login -Unique).Count }

# Referrer Traffic
Write-Host "Getting referrer traffic..."
$repos | ForEach-Object {$_.referrer_traffic = Get-GitHubReferrerTraffic -Uri $_.url }
$repos | ForEach-Object {$_.referrer_traffic_count = (Get-GitHubReferrerTraffic -Uri $_.url | Measure-Object -Sum count).Sum }
$repos | ForEach-Object {$_.referrer_traffic_uniques = (Get-GitHubReferrerTraffic -Uri $_.url | Measure-Object -Sum uniques).Sum }

# Path Traffic
Write-Host "Getting path traffic..."
$repos | ForEach-Object {$_.path_traffic = Get-GitHubPathTraffic -Uri $_.url }
$repos | ForEach-Object {$_.path_traffic_count = (Get-GitHubPathTraffic -Uri $_.url | Measure-Object -Sum count).Sum }
$repos | ForEach-Object {$_.path_traffic_uniques = (Get-GitHubPathTraffic -Uri $_.url | Measure-Object -Sum uniques).Sum }

# View Traffic
Write-Host "Getting view traffic..."
$repos | ForEach-Object {$_.view_traffic = Get-GitHubViewTraffic -Uri $_.url }
$repos | ForEach-Object {$_.view_traffic_count = (Get-GitHubViewTraffic -Uri $_.url | Measure-Object -Sum count).Sum }
$repos | ForEach-Object {$_.view_traffic_uniques = (Get-GitHubViewTraffic -Uri $_.url | Measure-Object -Sum uniques).Sum }

# Clone Traffic
Write-Host "Getting clone traffic..."
$repos | ForEach-Object {$_.clone_traffic = Get-GitHubCloneTraffic -Uri $_.url }
$repos | ForEach-Object {$_.clone_traffic_count = (Get-GitHubCloneTraffic -Uri $_.url | Measure-Object -Sum count).Sum }
$repos | ForEach-Object {$_.clone_traffic_uniques = (Get-GitHubCloneTraffic -Uri $_.url | Measure-Object -Sum uniques).Sum }


# Extract pertinent extracts and convert to array of objects for easy export
Write-Host "Processing usage stats..."
$usageStats = @()
foreach ($repo in $repos)
{
	$usageStats += [PSCustomObject]@{
		name = $repo.name
		full_name = $repo.full_name
		owner = $repo.owner.login
		description = $repo.description
		url = $repo.RepositoryUrl
		created = $repo.created_at
		updated = $repo.updated_at
		pushed = $repo.pushed_at
		size = $repo.size
		visibility = $repo.visibility
		stargazers_count = [int]$repo.stargazers_count
		watchers_count = [int]$repo.watchers_count
		forks_count = [int]$repo.forks_count
		open_issues_count = [int]$repo.open_issues_count
		contributors_count = [int]$repo.contributors_count
		referrer_traffic_count = [int]$repo.referrer_traffic_count
		referrer_traffic_uniques = [int]$repo.referrer_traffic_uniques
		path_traffic_count = [int]$repo.path_traffic_count
		path_traffic_uniques = [int]$repo.path_traffic_uniques
		view_traffic_count = [int]$repo.view_traffic_count
		view_traffic_uniques = [int]$repo.view_traffic_uniques
		clone_traffic_count = [int]$repo.clone_traffic_count
		clone_traffic_uniques = [int]$repo.clone_traffic_uniques
		}
}


# Export results to JSON
Write-Host "Saving results in JSON format at: $jsonOutputPath"
$usageStats | ConvertTo-Json | Out-File -FilePath $jsonOutputPath

# Export only pertinent stats to CSV
Write-Host "Saving results in CSV format at: $csvOutputPath"
$usageStats | Export-Csv -Path $csvOutputPath -NoTypeInformation

Write-Host "Done."
