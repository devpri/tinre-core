<div class="p-4 self-start text-white">
    <div class="sidebar-inner">
        <ul class="sidebar-menu">
            <li class="text-white no-underline">
                <router-link :to="{ name: 'home' }" class="item block my-4 lg:px-4">
                    <span class="align-middle">{{ __('Home') }}</span>
                </router-link>
            </li>
            @if(Auth::user() && Auth::user()->can('viewAny', \Devpri\Tinre\Models\User::class))
                <li>
                    <router-link :to="{ name: 'users.index' }" class="item block my-4 lg:px-4">
                        <span class="align-middle">{{ __('Users') }}</span>
                    </router-link>
                </li>
            @endif
            @if(Auth::user() && Auth::user()->hasAnyPermission(['access_token:view', 'access_token:view:any']))
                <li>
                    <router-link :to="{ name: 'access-tokens.index' }" class="item block my-4 lg:px-4">
                        <span class="align-middle">{{ __('Access Tokens') }}</span>
                    </router-link>
                </li>
            @endif
            <li>
                <router-link :to="{ name: 'profile' }" class="item block my-4 lg:px-4">
                    <span class="align-middle">{{ __('Profile') }}</span>
                </router-link>
            </li>
            <li>
                <a class="item block my-4 lg:px-4" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <span class="align-middle">{{ __('Logout') }}</span>
                </a>
            </li>
        </ul>
    </div>
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>
</div>