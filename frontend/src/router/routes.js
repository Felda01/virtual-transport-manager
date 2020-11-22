
function view (path) {
    return () => import('../views/' + path).then(m => m.default || m);
}

export default [
    { path: '/', name: 'dashboard', component: view('Home.vue')},
    { path: '/login', name: 'login', component: view('auth/Login.vue')},

    { path: '*', name: '404', component: view('errors/404.vue') }
];
