<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Notification' }}</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #f3f4f6;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            color: #374151;
            -webkit-font-smoothing: antialiased;
        }
        table {
            border-spacing: 0;
            width: 100%;
        }
        td {
            padding: 0;
        }
        .wrapper {
            width: 100%;
            table-layout: fixed;
            background-color: #f3f4f6;
            padding-bottom: 60px;
        }
        .main {
            background-color: #ffffff;
            margin: 0 auto;
            width: 100%;
            max-width: 600px;
            border-spacing: 0;
            border-radius: 8px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            overflow: hidden;
            margin-top: 40px;
        }
        .header {
            padding: 30px 40px;
            background-color: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            text-align: center;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #1d4ed8;
            text-decoration: none;
        }
        .content {
            padding: 40px;
            background-color: #ffffff;
        }
        .title {
            font-size: 20px;
            font-weight: 600;
            color: #111827;
            margin-bottom: 20px;
            margin-top: 0;
        }
        p {
            font-size: 16px;
            line-height: 24px;
            color: #4b5563;
            margin-top: 0;
            margin-bottom: 20px;
        }
        .footer {
            padding: 30px 40px;
            background-color: #f8fafc;
            text-align: center;
            border-top: 1px solid #e2e8f0;
        }
        .footer p {
            font-size: 14px;
            color: #6b7280;
            margin: 0 0 10px 0;
        }
        .footer a {
            color: #1d4ed8;
            text-decoration: underline;
        }
        @media screen and (max-width: 600px) {
            .main {
                border-radius: 0;
                margin-top: 0;
            }
            .content {
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>
    <center class="wrapper">
        <table class="main" width="100%">
            <!-- Header -->
            <tr>
                <td class="header">
                    <a href="{{ config('app.url') }}" class="logo">
                        {{ config('app.name') }}
                    </a>
                </td>
            </tr>

            <!-- Content -->
            <tr>
                <td class="content">
                    @if(isset($title))
                        <h1 class="title">{{ $title }}</h1>
                    @endif
                    
                    @yield('content')
                </td>
            </tr>

            <!-- Footer -->
            <tr>
                <td class="footer">
                    @include('emails.components.footer')
                </td>
            </tr>
        </table>
    </center>
</body>
</html>
