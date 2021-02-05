const express = require('express');
const basicAuth = require('express-basic-auth')
require('dotenv').config();
const fetch = require('node-fetch');
const {GraphBuilder, DijkstraStrategy} = require('js-shortest-path');
const bodyParser = require('body-parser');

const app = express();

app.use( bodyParser.json() );
app.use( bodyParser.urlencoded( {extended: true} ) );

let graph = null;
let dijkstra = null;

app.use(basicAuth({
    users: { 'admin': process.env.BASE_AUTH_PASSWORD },
    challenge: true
}));

app.get('/', function (req, res) {
    console.log("Got a GET request for the homepage");
    res.send('Hello GET');
});

app.post('/trip', function (req, res) {
    let from = req.query.from;
    let to = req.query.to;

    let result = dijkstra.shortest(from, to);

    res.json({
        distance: result.cost,
        path: result.path()
    });
});

app.get('/trip', function (req, res) {
    let from = req.query.from;
    let to = req.query.to;

    let result = dijkstra.shortest(from, to);

    res.json({
        distance: result.cost,
        path: result.path()
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
                graph = graph.edge(route['location1_id'], route['location2_id'], route['distance']);
            }
            graph = graph.build();

            dijkstra = DijkstraStrategy(graph);
        });
}



const server = app.listen(process.env.PORT, function () {
    console.log("Started");
    fetchRoutes();
    console.log("Routes fetched");
});
