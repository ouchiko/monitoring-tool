FROM ubuntu:latest
MAINTAINER docker@ekito.fr
RUN apt-get update && apt-get -y install cron wget
 
# Add crontab file in the cron directory
COPY containers/scheduler/cronfile /etc/cron.d/trains-cron
 
# Give execution rights on the cron job
RUN chmod 0644 /etc/cron.d/trains-cron
 
# Create the log file to be able to run tail
RUN touch /var/log/cron.log
 
# Run the command on container startup
CMD cron && tail -f /var/log/cron.log
