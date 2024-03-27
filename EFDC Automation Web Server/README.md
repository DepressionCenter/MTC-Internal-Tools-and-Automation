![Depression Center Logo](https://github.com/DepressionCenter/.github/blob/main/images/EFDCLogo_375w.png "depressioncenter.org")

# EFDC Automation and APIs Web Server

## Description
Code used by the automation and APIs web server from the Mobile Technologies Core. Some of the code used here is also available under the individual script files in the top-level directories, but are compiled here in a conhesive way to be served over Apache HTTP server.



## Quick Start Guide
+ Copy all files in the *html* directory to your web server root (e.g. /var/www or /Public/html).
+ Note that PHP 8 is required for all .php scripts, and Python 3.x or all .py scripts.
+ Call the respective webhook or API from a web browser, using the URL for your web server.
  + To use the MCommunity RSS API, visit: https://efdc-automation.web.itd.umich.edu/api/GetMCommunityGroupMembersRSS.php?groupName=efdc-mobiletech@umich.edu
  + To use the GitHub Organization Repositoies RSS API, visit: https://efdc-automation.web.itd.umich.edu/api/GetGitHubOrganizationRepositoriesRSS.php?orgName=DepressionCenter



## Documentation
### html/api/GetMCommunityGroupMembersRSS.php
	Title: *EFDC GetMCommunityGroupMembersRSS Web Service*
	Summary: Gets members of public MCommunity groups in RSS format (intended for consumption via PowerAutomate).
	Author(s): Gabriel Mongefranco <mongefrg@umich.edu>
	Created Date: 11/16/2023
	Last Modified Date: 03/27/2024
	
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


### html/api/GetGitHubOrganizationRepositoriesRSS.php
	Title: *EFDC GetGitHubOrganizationRepositoriesRSS Web Service*
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



## Additional Resources
+ Mobile Technologies KB Articles - [Code Documentation](https://teamdynamix.umich.edu/TDClient/210/DepressionCenter/KB/?CategoryID=847)



## About the Team
The Mobile Technologies Core provides investigators across the University of Michigan the support and guidance needed to utilize mobile technologies and digital mental health measures in their studies. Experienced faculty and staff offer hands-on consultative services to researchers throughout the University – regardless of specialty or research focus.



## Contact
To get in touch, contact the individual developers in the check-in history.

If you need assistance identifying a contact person, email the EFDC's Mobile Technologies Core at: efdc-mobiletech@umich.edu.



## Credits
#### Contributors:
+ Eisenberg Family Depression Center [(@DepressionCenter)](https://github.com/DepressionCenter/)
+ Gabriel Mongefranco [(@gabrielmongefranco)](https://github.com/gabrielmongefranco)



#### This work is based in part on the following projects, libraries and/or studies:
+ See individual script README files in top-level directory.



## License
### Copyright Notice
Copyright © 2023 The Regents of the University of Michigan


### Software and Library License
This program is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program. If not, see <https://www.gnu.org/licenses/gpl-3.0-standalone.html>.


### Documentation License
Permission is granted to copy, distribute and/or modify this document 
under the terms of the GNU Free Documentation License, Version 1.3 
or any later version published by the Free Software Foundation; 
with no Invariant Sections, no Front-Cover Texts, and no Back-Cover Texts. 
You should have received a copy of the license included in the section entitled "GNU 
Free Documentation License". If not, see <https://www.gnu.org/licenses/fdl-1.3-standalone.html>



## Citation
If you find this repository, code or paper useful for your research, please cite it.

----

Copyright © 2023 The Regents of the University of Michigan
