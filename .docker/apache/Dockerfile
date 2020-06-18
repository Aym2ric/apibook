FROM debian:stretch

ENV HTTPD_PREFIX /usr/local/apache2
ENV PATH $HTTPD_PREFIX/bin:$PATH
RUN mkdir -p "$HTTPD_PREFIX" \
    && chown www-data:www-data "$HTTPD_PREFIX"
WORKDIR $HTTPD_PREFIX

#RUN apt-get update
#RUN apt install wget libnss3-tools systemd nano -y
#RUN wget -O mkcert https://github.com/FiloSottile/mkcert/releases/download/v1.3.0/mkcert-v1.3.0-linux-amd64
#RUN chmod +x  mkcert
#RUN mv mkcert /usr/local/bin
#RUN mkcert 127.0.0.1
#RUN mkcert -install

RUN apt-get update
RUN apt-get install nano -y
#RUN apt install wget -y
#RUN wget https://dl.eff.org/certbot-auto
#RUN mv certbot-auto /usr/local/bin/certbot-auto
#RUN chown root /usr/local/bin/certbot-auto
#RUN chmod 0755 /usr/local/bin/certbot-auto
#CMD ["/usr/local/bin/certbot-auto certonly --apache", "-y"]
#RUN cd /usr/local/bin/
#CMD certbot-auto certonly

COPY config/sites-available/dufresne.dev.crt /etc/ssl/certs/dufresne.dev.crt
COPY config/sites-available/GandiStandardSSLCA2.pem /etc/ssl/certs/GandiStandardSSLCA2.pem

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
    apache2 curl \
    && rm -r /var/lib/apt/lists/*
RUN a2enmod proxy_fcgi ssl rewrite proxy proxy_balancer proxy_http proxy_ajp
RUN sed -i '/Global configuration/a \
ServerName localhost \
' /etc/apache2/apache2.conf

# -------
#RUN cat ./dufresne.dev+2.pem
#RUN cd /etc/apache2/sites-available/
#RUN rm -f default-ssl.conf
#COPY config/sites-available/sf4.conf /etc/apache2/sites-available/sf4.conf
#RUN a2enmod ssl
#RUN a2enmod rewrite
#RUN a2ensite sf4
#CMD ["/usr/sbin/apache2ctl", "-D", "FOREGROUND"]
# -------

EXPOSE 80 443
RUN rm -f /run/apache2/apache2.pid
CMD apachectl  -DFOREGROUND -e info
