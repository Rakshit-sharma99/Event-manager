@props(['url', 'color' => 'blue'])

@php
$colors = [
    'blue' => 'background-color: #2563eb; color: #ffffff;',
    'green' => 'background-color: #16a34a; color: #ffffff;',
    'red' => 'background-color: #dc2626; color: #ffffff;',
    'gray' => 'background-color: #4b5563; color: #ffffff;',
];

$style = $colors[$color] ?? $colors['blue'];
@endphp

<table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin-top: 20px; margin-bottom: 20px;">
    <tr>
        <td align="center">
            <table border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td align="center" style="border-radius: 6px;" bgcolor="{{ $color == 'blue' ? '#2563eb' : ($color == 'green' ? '#16a34a' : ($color == 'red' ? '#dc2626' : '#4b5563')) }}">
                        <a href="{{ $url }}" target="_blank" style="font-size: 16px; font-family: Helvetica, Arial, sans-serif; color: #ffffff; text-decoration: none; border-radius: 6px; padding: 12px 24px; border: 1px solid transparent; display: inline-block; font-weight: bold;">
                            {{ $slot }}
                        </a>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
