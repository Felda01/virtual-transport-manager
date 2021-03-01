const express = require('express');
const basicAuth = require('express-basic-auth')
require('dotenv').config();
const fetch = require('node-fetch');
const {GraphBuilder, GreedStrategy} = require('js-shortest-path');
const bodyParser = require('body-parser');
const fs = require('fs')
const https = require('https')

const app = express();

app.use( bodyParser.json() );
app.use( bodyParser.urlencoded( {extended: true} ) );

let graph = null;
let greedy = null;
let paths = {};

app.use(basicAuth({
    users: { 'admin': process.env.BASE_AUTH_PASSWORD },
    challenge: true
}));

app.post('/Y29ubmV0aW9u', function (req, res) {
    res.json({
        'status': 'OK'
    });
    console.log('OK');
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
        result = threeShortestRoutes(greedy.paths(loc_from, loc_to));
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
            graph = GraphBuilder();
            for (let route of json['routes']) {
                graph = graph.edge(route['location1_id'], route['location2_id'], route['distance'])
                    .edge(route['location2_id'], route['location1_id'], route['distance']);
            }
            graph = graph.build();

            greedy = GreedStrategy(graph);
        });
}

function threeShortestRoutes(routes) {
    let MAX = Number.MAX_SAFE_INTEGER;
    let first = {cost: MAX};
    let second = {cost: MAX};
    let third = {cost: MAX};
    for (let i = 0; i < routes.length; i++) {
        if (routes[i].cost < first.cost) {
            third = second;
            second = first;
            first = routes[i];
        } else if (routes[i].cost < second.cost) {
            third = second;
            second = routes[i];
        } else if (routes[i].cost < third.cost) {
            third = routes[i];
        }
    }

    return [first, second, third];
}

if (process.env.ENVIROMENT === 'local') {
    const server = app.listen(process.env.PORT, function () {
        console.log("Started");
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


