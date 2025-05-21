import { createRouter, createWebHistory } from 'vue-router';

const routes = [
  {
    path: '/',
    name: 'home',
    component: () => import('../views/Home.vue'),
  },
  {
    path: '/players',
    name: 'players',
    component: () => import('../views/Players.vue'),
  },
  {
    path: '/players/:id',
    name: 'player-detail',
    component: () => import('../views/PlayerDetail.vue'),
    props: true,
  },
  {
    path: '/games',
    name: 'games',
    component: () => import('../views/Games.vue'),
  },
  {
    path: '/games/create',
    name: 'game-create',
    component: () => import('../views/GameCreate.vue'),
  },
  {
    path: '/games/:id',
    name: 'game-detail',
    component: () => import('../views/GameDetail.vue'),
    props: true,
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router; 