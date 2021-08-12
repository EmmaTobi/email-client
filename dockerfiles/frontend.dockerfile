FROM node:14
 
WORKDIR /var/www/html
 
COPY ../frontend/ .

EXPOSE 3000

CMD npm install && npm start
