<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Lang;

class AdminResetPassword extends ResetPassword
{
    public function toMail($notifiable): MailMessage
    {
        $url = $this->resetUrl($notifiable);
        $expire = (int) config('auth.passwords.admins.expire', 60);
        $isCmsAdmin = method_exists($notifiable, 'isCmsAdmin') && $notifiable->isCmsAdmin();

        return (new MailMessage)
            ->subject(Lang::get('Reset Password Notification'))
            ->view('emails.admin-reset-password', [
                'subject' => Lang::get('Reset Password Notification'),
                'actionUrl' => $url,
                'expire' => $expire,
                'accountLabel' => $isCmsAdmin ? 'Pinoycoop CMS admin account' : 'request admin account',
            ]);
    }

    protected function resetUrl($notifiable): string
    {
        $route = method_exists($notifiable, 'isCmsAdmin') && $notifiable->isCmsAdmin()
            ? 'admin.cms.password.reset'
            : 'admin.password.reset';

        return url(route($route, [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));
    }

    protected function buildMailMessage($url): MailMessage
    {
        return (new MailMessage)
            ->subject(Lang::get('Reset Password Notification'))
            ->view('emails.admin-reset-password', [
                'subject' => Lang::get('Reset Password Notification'),
                'actionUrl' => $url,
                'expire' => (int) config('auth.passwords.admins.expire', 60),
                'accountLabel' => 'request admin account',
            ]);
    }
}
