<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mail Debugger - Eventra</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8 font-sans">
    <div class="max-w-6xl mx-auto space-y-6">
        <h1 class="text-3xl font-bold text-gray-800">Eventra Mail Debugger</h1>

        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-sm whitespace-pre-wrap">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-sm whitespace-pre-wrap">
                {{ session('error') }}
            </div>
        @endif

        @if(session('error_raw'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-sm">
                <h3 class="font-bold">Raw Exception:</h3>
                <pre class="mt-2 text-sm overflow-x-auto whitespace-pre-wrap">{{ session('error_raw') }}</pre>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Config Panel -->
            <div class="bg-white p-6 rounded-lg shadow">
                <h2 class="text-xl font-bold mb-4 border-b pb-2">Configuration</h2>
                <ul class="space-y-2 text-sm">
                    <li><strong>Mail Driver:</strong> {{ $config['MAIL_MAILER'] }}</li>
                    <li><strong>Host:</strong> {{ $config['MAIL_HOST'] }}</li>
                    <li><strong>Port:</strong> {{ $config['MAIL_PORT'] }}</li>
                    <li><strong>Username:</strong> {{ $config['MAIL_USERNAME'] }}</li>
                    <li><strong>Encryption:</strong> {{ $config['MAIL_ENCRYPTION'] }}</li>
                    <li><strong>From Address:</strong> {{ $config['MAIL_FROM_ADDRESS'] }}</li>
                    <li class="pt-2 border-t mt-2"><strong>Queue Connection:</strong> 
                        <span class="{{ $config['QUEUE_CONNECTION'] === 'sync' ? 'text-orange-600 font-bold' : 'text-green-600 font-bold' }}">
                            {{ $config['QUEUE_CONNECTION'] }}
                        </span> 
                        <span class="text-xs text-gray-500">(For debugging, 'sync' is recommended)</span>
                    </li>
                    <li><strong>Failed Jobs:</strong> <span class="text-red-500 font-bold">{{ $failedJobs }}</span></li>
                </ul>
            </div>

            <!-- Testing Actions -->
            <div class="bg-white p-6 rounded-lg shadow space-y-6">
                
                <div>
                    <h2 class="text-xl font-bold mb-4 border-b pb-2">1. Test SMTP Connection</h2>
                    <p class="text-sm text-gray-600 mb-2">Opens a direct socket to verify network/firewall access.</p>
                    <a href="{{ route('mail.debug.smtp') }}" class="inline-block bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Test Connection</a>
                </div>

                <div>
                    <h2 class="text-xl font-bold mb-4 border-b pb-2">2. Send Test Mail</h2>
                    <form action="{{ route('mail.debug.sync') }}" method="POST" class="space-y-3">
                        @csrf
                        <div>
                            <label class="block text-sm font-bold text-gray-700">Send To:</label>
                            <input type="email" name="to" value="{{ $config['MAIL_FROM_ADDRESS'] }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm border p-2" required>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700">Mail Type:</label>
                            <select name="type" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm border p-2">
                                <option value="plain">Plain Text</option>
                                <option value="html">HTML</option>
                                <option value="otp">OTP Style Template</option>
                            </select>
                        </div>
                        <div class="flex space-x-2 pt-2">
                            <button type="submit" formaction="{{ route('mail.debug.sync') }}" class="bg-purple-500 text-white px-4 py-2 rounded hover:bg-purple-600">Send Direct (Sync)</button>
                            <button type="submit" formaction="{{ route('mail.debug.queue') }}" class="bg-indigo-500 text-white px-4 py-2 rounded hover:bg-indigo-600">Send via Queue</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>

        <!-- Debugging Commands -->
        <div class="bg-white p-6 rounded-lg shadow">
            <h2 class="text-xl font-bold mb-4 border-b pb-2">Debugging Commands</h2>
            <div class="flex flex-wrap gap-2">
                <form action="{{ route('mail.debug.command') }}" method="POST">
                    @csrf
                    <input type="hidden" name="command" value="config:clear">
                    <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded hover:bg-gray-700 text-sm">config:clear</button>
                </form>
                <form action="{{ route('mail.debug.command') }}" method="POST">
                    @csrf
                    <input type="hidden" name="command" value="optimize:clear">
                    <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded hover:bg-gray-700 text-sm">optimize:clear</button>
                </form>
                <form action="{{ route('mail.debug.command') }}" method="POST">
                    @csrf
                    <input type="hidden" name="command" value="queue:restart">
                    <button type="submit" class="bg-orange-500 text-white px-4 py-2 rounded hover:bg-orange-600 text-sm">queue:restart</button>
                </form>
                <form action="{{ route('mail.debug.command') }}" method="POST">
                    @csrf
                    <input type="hidden" name="command" value="queue:retry all">
                    <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 text-sm">queue:retry all</button>
                </form>
            </div>
            <p class="text-xs text-gray-500 mt-2">* Note: You still need to manually restart `php artisan queue:work` and `php artisan serve` in your terminal if you change .env values.</p>
        </div>

        <!-- Brevo Instructions -->
        <div class="bg-white p-6 rounded-lg shadow border-l-4 border-blue-500">
            <h2 class="text-xl font-bold mb-2">Brevo Transactional Log Instructions</h2>
            <div class="text-sm text-gray-700 space-y-2">
                <p>If Laravel reports "Successfully sent" but the email is not in your inbox, check the Brevo logs:</p>
                <ol class="list-decimal ml-5 space-y-1">
                    <li>Log into your <a href="https://app.brevo.com/" target="_blank" class="text-blue-600 underline">Brevo Account</a>.</li>
                    <li>Go to <strong>Transactional > Logs</strong> (or "Emails").</li>
                    <li>Look for your email in the list. Check its status:
                        <ul class="list-disc ml-5 mt-1 text-gray-600">
                            <li><span class="font-bold text-green-600">Delivered</span>: Brevo sent it, and the receiving server accepted it. Check your Spam/Promotions folder.</li>
                            <li><span class="font-bold text-orange-500">Deferred / Soft Bounce</span>: The receiving server is temporarily rejecting it (e.g., rate limits, graylisting).</li>
                            <li><span class="font-bold text-red-600">Hard Bounce / Blocked</span>: The email address is invalid, or you are blacklisted.</li>
                        </ul>
                    </li>
                    <li>If you don't see the email at all in Brevo, Laravel is either not sending it, or it is stuck in the Laravel Queue (check the Failed Jobs count above).</li>
                </ol>
            </div>
        </div>
    </div>
</body>
</html>
