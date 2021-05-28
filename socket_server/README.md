#  EMS_SocketServer
EMS Socket Server - A Simple NodeJS socket server for handling realtime functions for EMS

# Requirements
 1. [Node.js](https://nodejs.org/en/)

# Installation

 1. Create a copy of the `config_sample.js` and rename to `config.js` on your local directory
 2. Edit your EMS' `.env` file and add an `APP_DOMAIN` variable. Typically `localhost` will work, but this should be the domain where you point to your browser to access your local EMS installation.
e.g. if you run access your local EMS through `https://localhost/EMS` set your `APP_DOMAIN` env variable to `localhost`. For reference, I run mine on `https://192.168.18.99/evaluation` and my `APP_DOMAIN` is set to `192.168.18.99`
 3. Edit the `config.general.cors` variable and replace `192\.168\.18\.99` with  the value you set as `APP_DOMAIN` on Step 2
 5.  On your terminal, point to where you extracted this directory and run: `node main.js`
 6. You can view how it works live by visiting the ems sandbox (check `routes.php` for the url, but it should be `https://<your ems host>/<your ems sub-directory>/sandbox`)


# Usage
1. Currently, the socket server is used to relay messages between logged in EMS users on the Engagement Wall.
2. On the view `people.wallcustom`,  every successful ajax request has a `socket.emit` equivalent, which instructs the socket server to broadcast these events to everyone viewing the Engagement Wall.
3. The socket server is limited to listening only to the `send` emit events. We can add more emit events to listen to as necessary in the future.
