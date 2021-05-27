/*
 simple socket server for live comments to show on employee engagement wall
 NOTE: please set a APP_DOMAIN that points to your node server's address in your laravel .env file
 e.g.:
  APP_DOMAIN=192.168.18.99

 if any files in this directory is updated:
 1. get the PROCESS_ID by running: netstat -lpn | grep :'<PORT>'
  - (FOR WINDOWS: netstat -ano | find "LISTENING" | find "<PORT>")
  - (FOR MAC: lsof -Pi :<PORT>)
 2. kill -9 <PROCESS_ID>
  - (windows: taskkill /pid <PROCESS_ID>)
  - (osx: kill <PROCESS_ID>)
 3. then run: `node main.js` again
*/
var config = {};
config.general = {};
config.general.port = 9972;
config.general.debug = true; // debug mode on/off
config.general.cors = /(192\.168\.18\.99|172\.17\.0\.2|115\.85\.25\.3|internal\.openaccess\.bpo)(.)?/;
// export
module.exports = config;