const express = require('express');
const basicAuth = require('express-basic-auth');
require('dotenv').config();
const fetch = require('node-fetch');
const bodyParser = require('body-parser');
const fs = require('fs');
const https = require('https');

const graphlib = require('graphlib');
const ksp = require('k-shortest-path');

const app = express();

app.use( bodyParser.json() );
app.use( bodyParser.urlencoded( {extended: true} ) );

let graph = null;
let paths = {};

app.use(basicAuth({
    users: { 'admin': process.env.BASE_AUTH_PASSWORD },
    challenge: true
}));

app.post('/Y29ubmV0aW9u', function (req, res) {
    res.json({
        'status': 'OK'
    });
});

app.post('/ZmV0Y2hSb3V0ZXM', function (req, res) {

    fetchRoutes();

    res.json({
        'status': 'OK'
    });
});

app.post('/dHJpcA', function (req, res) {
    let loc_from = req.body.from;
    let loc_to = req.body.to;

    let result;

    if (paths[loc_from + '-' + loc_to]) {
        result = paths[loc_from + '-' + loc_to];
    } else {
        result = ksp.ksp(graph, loc_from, loc_to, 3);
        paths[loc_from + '-' + loc_to] = result;
        paths[loc_to + '-' + loc_from] = result;
    }

    res.json({
        result: result
    });
});

app.get('*', function(req, res){
    res.status(404).send('Not found');
});

function fetchRoutes() {
    fetch(process.env.LARAVEL_ROUTES_API)
        .then(res => res.json())
        .then(json => {
            graph = new graphlib.Graph();
            for (let route of json['routes']) {
                graph.setEdge(route['location1_id'], route['location2_id'], route['distance']);
                graph.setEdge(route['location2_id'], route['location1_id'], route['distance']);
            }
        });
}

if (process.env.ENVIROMENT === 'local') {
    const server = app.listen(process.env.PORT, function () {
        console.log("Started");
        console.log(process.env.PORT);
        fetchRoutes();
    });
} else {
    const httpsServer = https.createServer({
        key: fs.readFileSync(process.env.PRIVKEY_PATH),
        cert: fs.readFileSync(process.env.FULLCHAIN_PATH),
    }, app);

    httpsServer.listen(process.env.PORT, () => {
        console.log('HTTPS Server running on port 443');
    });
}


