<dropdown-trigger class="h-9 flex items-center">
    <span class="text-90">
        {{ $user->name ?? $user->email ?? __('Nova User') }}
    </span>
</dropdown-trigger>

<dropdown-menu slot="menu" width="200" direction="rtl">
    <ul class="list-reset">
        <li>
            <router-link :to="{
                name: 'detail',
                params: {
                resourceName: 'users',
                resourceId: '{{ $user->id }}'
                }
                }" class="block no-underline text-90 hover:bg-30 p-3">
                Profile
            </router-link>
            <a href="{{ route('logout') }}" class="block no-underline text-90 hover:bg-30 p-3">
                {{ __('Logout') }}
            </a>
        </li>
    </ul>
</dropdown-menu>
