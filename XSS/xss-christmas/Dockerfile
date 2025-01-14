FROM node:18

WORKDIR /usr/src/app

COPY vsnippet/package*.json ./

RUN npm install --only=production && \
npm install --save-dev nodemon && \
npm install escape-html

COPY vsnippet/ .

EXPOSE 1337

ENV NODE_ENV=production

CMD ["node", "index.js"]
