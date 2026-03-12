<tr>
    <td class="header">
        <a href="{{ $url }}">
            @php
                $logo = config('adminlte.login_logo');
                // Força o uso do domínio base configurado no .env (config/app.php)
                $baseUrl = rtrim(config('app.url'), '/');
                if (strpos($logo, 'src="/') !== false) {
                    $logo = str_replace('src="/', 'src="' . $baseUrl . '/', $logo);
                }
            @endphp
            {!! $logo !!}
        </a>
    </td>
</tr>