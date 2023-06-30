![Depression Center Logo](https://github.com/DepressionCenter/.github/blob/main/images/EFDCLogo_375w.png "depressioncenter.org")

# Export-MCommunity-Groups.py

## Description
Python script to export MCommunity group members to a file, using the MCommunityGroups API. This scripts can be used to automate getting a list of group members via Docker container, Windows Scheduled Task, Cron scheduler, Power Automate, or other scripts.



## Quick Start Guide
+ Ensure you have Python installed
+ Download Export-MCommunity-Groups.py and Export-MCommunity-Groups-Config.json
+ Edit Export-MCommunity-Groups-Config.json (in Notepad or another text editor), and enter your API credentials and list of group names.
+ Run the script. It will generate one file per group (group_name.csv), containing the members for that group (one per line).


## Documentation
+ *Export-MCommunity-Groups.py* contains the script that will connect to the MCommunity API
+ *Export-MCommunity-Groups-Config.json* contains settings that the script needs to run. Change the list of gorup names to suit your needs. You can get the group name from [MCommunity](https://mcommunity.umich.edu/my-groups) by copying the Group Email. You may enter the group name with or without the @umich.edu portion. To get an API key, follow the instructions in the [University of Michigan API Directory](https://dir.api.it.umich.edu/).
+ The Mobile Tech Core plans to use this script to refresh group memberships in SharePoint. To do this, we first sync a Document Library to our computer so we can run the script in it every week. Then, we use Power Autoamte (cloud) to pickup the files and sync to specific Lists in SharePoint. Contact us for details.

## Additional Resources
+ This script was developed for the [Track Master](https://github.com/DepressionCenter/TrackMaster) project.



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
+ None



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
