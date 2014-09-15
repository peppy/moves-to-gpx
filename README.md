moves-to-gpx
============

A simple php script to export data from Moves (http://www.moves-app.com) to .GPX format using the Moves API.

This is a script I made for my own purposes, which is geo-tagging photos with the most efficient workflow possible. While my new mirrorless camera (Olympus E-P5) has a companion iOS app which can log GPS data, it is a manually activated always-active app. Meanwhile, Moves is a smart background app that reduces battery usage to a level I can leave it running 24/7.

Exporting my Moves data was only logical.

 - Authentication (OAuth) must be done manually for the moment. Once you have an access token, you can enter it manually in the script.
 - You should set an initial date which will trigger the end of exporting.
 - The script will always export all data, no matter of what already exists on disk. This is to allow for re-fetching changes to Place metadata etc.

This is a very raw script. If anyone has a use for it and needs further help or improvement, don't hesitate to get in touch.

No licence – free for use however you see fit. Credit always welcome.
