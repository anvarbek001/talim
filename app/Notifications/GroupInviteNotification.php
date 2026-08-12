<?php

namespace App\Notifications;

use App\Models\GroupInvite;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class GroupInviteNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected GroupInvite $invite) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $group = $this->invite->group;
        $teacher = $this->invite->invitedBy;

        return (new MailMessage)
            ->subject("{$teacher->name} sizni \"{$group->name}\" guruhiga taklif qildi — DarsQil")
            ->greeting('Assalomu alaykum!')
            ->line("{$teacher->name} sizni DarsQil platformasidagi \"{$group->name}\" guruhiga taklif qilmoqda.")
            ->line("Guruhga qo'shilib, jonli darslarda ishtirok etishingiz mumkin bo'ladi.")
            ->action("Guruhga qo'shilish", route('group-invites.show', $this->invite->token))
            ->line('Agar bu taklifni kutmagan bo\'lsangiz, xatni e\'tiborsiz qoldirishingiz mumkin.');
    }
}
