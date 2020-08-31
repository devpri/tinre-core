import Home from '../pages/Home'
import Url from '../pages/Url'
import Profile from '../pages/Profile'
import UsersIndex from '../pages/users/Index'
import UsersShow from '../pages/users/Show'
import AccessTokensIndex from '../pages/access-tokens/Index'
import AccessTokensShow from '../pages/access-tokens/Show'
import Error from '../pages/404'

export default [
  { path: '/', name: 'home', component: Home },
  { path: '/urls/:path', name: 'urls.show', component: Url },
  { path: '/users', name: 'users.index', component: UsersIndex },
  { path: '/users/:id', name: 'users.show', component: UsersShow },
  { path: '/profile', name: 'profile', component: Profile },
  {
    path: '/access-tokens',
    name: 'access-tokens.index',
    component: AccessTokensIndex,
  },
  {
    path: '/access-tokens/:id',
    name: 'access-tokens.show',
    component: AccessTokensShow,
  },
  { path: '*', name: '404', component: Error },
]
