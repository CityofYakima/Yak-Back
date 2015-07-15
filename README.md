# Yak-Back

This project is the citizen response system built by the City of Yakima. 

##Requirements
*PHP/MYSQL
*Webserver
*ESRI Geocode Service

##Installation
This system was not fully prepared to be re-used, so there are several steps you must take to get it working in your environment.
* Download the files and place them in a folder name yak-back on your webserver *you can place this in another folder, but you will have to change all references in the files to yak-back*
* Create a  mysql database entitled yakimaconnect and import the yak-back.sql file
* Edit /includes/php/include.php to use your connect information for your mySQL database
* Edit /includes/js/map.js on line 185 to use your ESRI Geocode Service

##Further Production
In a future release, the mySQL dependecy will be replace with a geoDB. It will also heavily be rewritten to be more map-centric.
