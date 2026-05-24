<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Mail\OtpVerificationMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Throwable;

class MailTestController extends Controller
{
    public function index()
    {
        $config = [
            'MAIL_MAILER' => env('MAIL_MAILER', config('mail.default')),
            'MAIL_HOST' => env('MAIL_HOST', config('mail.mailers.smtp.host')),
            'MAIL_PORT' => env('MAIL_PORT', config('mail.mailers.smtp.port')),
            'MAIL_USERNAME' => env('MAIL_USERNAME', config('mail.mailers.smtp.username')),
            'MAIL_ENCRYPTION' => env('MAIL_ENCRYPTION', config('mail.mailers.smtp.encryption')),
            'MAIL_FROM_ADDRESS' => env('MAIL_FROM_ADDRESS', config('mail.from.address')),
            'QUEUE_CONNECTION' => env('QUEUE_CONNECTION', config('queue.default')),
        ];

        try {
            $failedJobs = \Illuminate\Support\Facades\DB::table('failed_jobs')->count();
        } catch (\Exception $e) {
            $failedJobs = 'Could not connect to database';
        }

        return view('mail-debug.index', compact('config', 'failedJobs'));
    }

    public function sendSync(Request $request)
    {
        return $this->processMailSend($request, false);
    }

    public function sendQueue(Request $request)
    {
        return $this->processMailSend($request, true);
    }

    private function processMailSend(Request $request, bool $queue)
    {
        $type = $request->input('type', 'plain');
        $to = $request->input('to', env('MAIL_FROM_ADDRESS'));

        $user = new User([
            'name' => 'Debug User',
            'email' => $to,
        ]);
        $user->id = 'debug-user-id';

        $startTime = microtime(true);

        try {
            if ($type === 'otp') {
                $mailable = new OtpVerificationMail($user, '123456');
            } else if ($type === 'html') {
                $mailable = null;
            }

            if ($type === 'plain' || $type === 'html') {
                $method = $queue ? 'queue' : 'send';
                if ($type === 'plain') {
                    Mail::raw('This is a plain text test email from Laravel Debugger.', function ($message) use ($to) {
                        $message->to($to)->subject('Plain Text Test Mail');
                    });
                } else {
                    Mail::html('<h1>Hello!</h1><p>This is an HTML test email from Laravel Debugger.</p>', function ($message) use ($to) {
                        $message->to($to)->subject('HTML Test Mail');
                    });
                }
            } else {
                if ($queue) {
                    Mail::to($to)->queue($mailable);
                } else {
                    Mail::to($to)->send($mailable);
                }
            }

            $duration = round((microtime(true) - $startTime) * 1000);

            return back()->with('success', "Email successfully " . ($queue ? 'queued' : 'sent') . " to {$to} in {$duration}ms.");
        } catch (TransportExceptionInterface $e) {
            return back()->with('error_raw', $this->formatException($e));
        } catch (Throwable $e) {
            return back()->with('error_raw', $this->formatException($e));
        }
    }

    public function testSmtpConnection()
    {
        try {
            $host = config('mail.mailers.smtp.host');
            $port = config('mail.mailers.smtp.port');
            $timeout = 5;

            $startTime = microtime(true);
            $fp = @fsockopen($host, $port, $errno, $errstr, $timeout);
            $duration = round((microtime(true) - $startTime) * 1000);

            if (!$fp) {
                return back()->with('error', "Connection to {$host}:{$port} failed. Error [{$errno}]: {$errstr}");
            }

            fclose($fp);
            return back()->with('success', "Successfully connected to {$host}:{$port} in {$duration}ms.");
        } catch (Throwable $e) {
            return back()->with('error_raw', $this->formatException($e));
        }
    }

    public function runCommand(Request $request)
    {
        $command = $request->input('command');
        $validCommands = ['config:clear', 'optimize:clear', 'queue:restart', 'queue:retry all'];
        
        if (!in_array($command, $validCommands)) {
            return back()->with('error', 'Invalid command.');
        }

        try {
            Artisan::call($command);
            $output = Artisan::output();
            return back()->with('success', "Command `php artisan {$command}` executed successfully.\n" . $output);
        } catch (Throwable $e) {
            return back()->with('error_raw', $this->formatException($e));
        }
    }

    private function formatException(Throwable $e)
    {
        $msg = "Exception: " . get_class($e) . "\n";
        $msg .= "Message: " . $e->getMessage() . "\n";
        $msg .= "File: " . $e->getFile() . " on line " . $e->getLine() . "\n\n";
        $msg .= "Trace:\n" . $e->getTraceAsString();
        return $msg;
    }
}
