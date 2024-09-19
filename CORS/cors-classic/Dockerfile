#PHP Apache setup
FROM node:14

#Install and update system dependencies
RUN apt update -y && apt install -y supervisor npm sqlite3
RUN npm install express

#Prepare and setup the working directory
WORKDIR /var/www/html/
COPY vsnippet .
RUN npm install

#Copy configs
COPY config/supervisord.conf /etc/supervisord.conf

EXPOSE 1337

ENTRYPOINT [ "/usr/bin/supervisord", "-c", "/etc/supervisord.conf" ]
