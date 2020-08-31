<div class="content-inner flex justify-end">
    <v-popover ref="menu" placement="bottom-start">
        <button class="pointer px-2 py-1 flex">
            <svg width="30" height="30" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path d="M16 132h416c8.837 0 16-7.163 16-16V76c0-8.837-7.163-16-16-16H16C7.163 60 0 67.163 0 76v40c0 8.837 7.163 16 16 16zm0 160h416c8.837 0 16-7.163 16-16v-40c0-8.837-7.163-16-16-16H16c-8.837 0-16 7.163-16 16v40c0 8.837 7.163 16 16 16zm0 160h416c8.837 0 16-7.163 16-16v-40c0-8.837-7.163-16-16-16H16c-8.837 0-16 7.163-16 16v40c0 8.837 7.163 16 16 16z" fill="#fff"/></svg>
        </button>
      <template class="hidden" slot="popover">
        <ul class="text-gray-700 shadow pt-1 mx-4">
            <li>
                <router-link :to="{ name: 'home' }" @click.native="$refs.menu.hide()" class="rounded-t bg-gray-200 hover:bg-gray-400 py-2 px-4 block whitespace-no-wrap w-40">
                    {{ __('Home') }}
                </router-link>
            </li>
            @if(Auth::user() && Auth::user()->can('viewAny', \Devpri\Tinre\Models\User::class))
                <li>
                    <router-link :to="{ name: 'users.index' }" @click.native="$refs.menu.hide()" class="rounded-t bg-gray-200 hover:bg-gray-400 py-2 px-4 block whitespace-no-wrap w-40">
                        {{ __('Users') }}
                    </router-link>
                </li>
            @endif
            @if(Auth::user() && Auth::user()->hasAnyPermission(['access_token:view', 'access_token:view:any']))
                <li>
                    <router-link :to="{ name: 'access-tokens.index' }" @click.native="$refs.menu.hide()" class="rounded-t bg-gray-200 hover:bg-gray-400 py-2 px-4 block whitespace-no-wrap w-40">
                        {{ __('Access Tokens') }}
                    </router-link>
                </li>
            @endif
            <li>
                <router-link :to="{ name: 'profile' }" @click.native="$refs.menu.hide()" class="rounded-t bg-gray-200 hover:bg-gray-400 py-2 px-4 block whitespace-no-wrap w-40">
                    {{ __('Profile') }}
                </router-link>
            </li>
            <li><a class="rounded-t bg-gray-200 hover:bg-gray-400 py-2 px-4 block whitespace-no-wrap w-40" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">{{ __('Logout') }}</a></li>
        </ul>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
      </template>
    </v-popover>
</div>