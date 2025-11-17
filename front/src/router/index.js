/**
 * Vue Router Configuration
 *
 * Configuration des routes de l'application
 */

import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '../stores/auth'

// Lazy loading des composants pour optimiser les performances
const Home = () => import('../views/Home.vue')
const Login = () => import('../views/Login.vue')
const Register = () => import('../views/Register.vue')
const Builds = () => import('../views/Builds.vue')
const BuildDetail = () => import('../views/BuildDetail.vue')
const BuildCreate = () => import('../views/BuildCreate.vue')
const BuildEdit = () => import('../views/BuildEdit.vue')
const Characters = () => import('../views/Characters.vue')
const CharacterDetail = () => import('../views/CharacterDetail.vue')
const Favorites = () => import('../views/Favorites.vue')
const Profile = () => import('../views/Profile.vue')
const NotFound = () => import('../views/NotFound.vue')

const routes = [
  {
    path: '/',
    name: 'Home',
    component: Home,
    meta: { title: 'Accueil - Genshin Build Manager' }
  },
  {
    path: '/login',
    name: 'Login',
    component: Login,
    meta: { title: 'Connexion', guest: true }
  },
  {
    path: '/register',
    name: 'Register',
    component: Register,
    meta: { title: 'Inscription', guest: true }
  },
  {
    path: '/builds',
    name: 'Builds',
    component: Builds,
    meta: { title: 'Tous les Builds' }
  },
  {
    path: '/builds/:id',
    name: 'BuildDetail',
    component: BuildDetail,
    meta: { title: 'Détails du Build' },
    props: true
  },
  {
    path: '/builds/create',
    name: 'BuildCreate',
    component: BuildCreate,
    meta: { title: 'Créer un Build', requiresAuth: true }
  },
  {
    path: '/builds/:id/edit',
    name: 'BuildEdit',
    component: BuildEdit,
    meta: { title: 'Modifier le Build', requiresAuth: true },
    props: true
  },
  {
    path: '/characters',
    name: 'Characters',
    component: Characters,
    meta: { title: 'Personnages' }
  },
  {
    path: '/characters/:id',
    name: 'CharacterDetail',
    component: CharacterDetail,
    meta: { title: 'Détails du Personnage' },
    props: true
  },
  {
    path: '/favorites',
    name: 'Favorites',
    component: Favorites,
    meta: { title: 'Mes Favoris', requiresAuth: true }
  },
  {
    path: '/profile',
    name: 'Profile',
    component: Profile,
    meta: { title: 'Mon Profil', requiresAuth: true }
  },
  {
    path: '/:pathMatch(.*)*',
    name: 'NotFound',
    component: NotFound,
    meta: { title: 'Page non trouvée' }
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior(to, from, savedPosition) {
    if (savedPosition) {
      return savedPosition
    } else {
      return { top: 0 }
    }
  }
})

// Navigation Guards
router.beforeEach((to, from, next) => {
  const authStore = useAuthStore()

  // Mise à jour du titre de la page
  document.title = to.meta.title || 'Genshin Build Manager'

  // Vérification de l'authentification pour les routes protégées
  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    next({ name: 'Login', query: { redirect: to.fullPath } })
  }
  // Redirection si déjà connecté et accès à login/register
  else if (to.meta.guest && authStore.isAuthenticated) {
    next({ name: 'Home' })
  }
  else {
    next()
  }
})

export default router
