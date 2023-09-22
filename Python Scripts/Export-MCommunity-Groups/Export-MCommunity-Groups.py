# Export-MCommunity-Groups.py
# Summary:
    # Python script to export MCommunity group members to a file
# Notes:
    # Enter the MCommunity API credentials and group names into Export-MCommunity-Groups-Config.json
# Author(s):
    # Gabriel Mongefranco (mongefrg@umich.edu)

# json and requests libraries are used for calling the MCommunity API
import json
import requests
import requests.auth

# CSV and StringIO are used to convert comma-separated member lists to one member per line
import csv
from io import StringIO

import sys

# Read configuration file
with open("Export-MCommunity-Groups-Config.json", "rb") as f:
    config_data = json.load(f)

# Get API Authentication Token
try:
    print("Authenticating...")
    API_Auth_Request = requests.post(
        config_data["API_Auth_URL"],
        data={"scope":config_data["API_Scope"] , "grant_type":"client_credentials"},
        auth=(config_data["API_Username"], config_data["API_Password"])
    )
    API_Auth_Response = API_Auth_Request.json()
    API_Token = API_Auth_Response["access_token"]
    print("Authentication successful.")
except:
    print("Authentication failed. Check config file (Export-MCommunity-Groups-Config.json).")
    sys.exit()

# Loop through each Group
for group_name in config_data["Groups"]:
        group_name = group_name.replace("@umich.edu","").replace("@med.umich.edu","")
        print("Processing group " + group_name)
        
        # Get group members from MCommunityGroups API
        API_Request = requests.get(
            # The following is a work-around for group names that contain periods
            config_data["API_Endpoint_URL"] + "/" + group_name.replace(".","%20"),
            headers = {"Authorization":"Bearer " + API_Token, "Content-Length":"0"}
            )
        API_Response = API_Request.json()
        
        # Delete existing group membership file contents, or create the file
        open(group_name + ".csv", "w").close()
        
        # Save group members to file
        try:
            group_members = API_Response["MCommunityInfo"]["MCommunityGroupData"]["MemberList"]
            print(API_Response)
        except:
            print("Group not found: " + group_name)
        try:
            csv_reader = csv.reader(StringIO(group_members), delimiter=",")
            tmpMemberList = []
            for row in csv_reader:
                with open(group_name+".csv", "a") as group_membership_file:
                    for m in row:
                        if m.find("<")>1:
                            m = m.split("<")[1].replace(">","")
                        m = m.replace("@umich.edu","").replace("@med.umich.edu","")
                        if m not in tmpMemberList:
                            tmpMemberList.append(m)
                            group_membership_file.write(f'{m}\n')
                    group_membership_file.close()
            tmpMemberList.clear()
        except:
            print("Unknown error while processing group and/or saving to file.")
        print("")
