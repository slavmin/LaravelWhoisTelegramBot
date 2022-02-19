<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use NotificationChannels\Telegram\TelegramMessage;

class BotResponseNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected string $message;
    protected array $buttons;
    protected string $buttons_type;

    /**
     * Create a new notification instance.
     *
     * @param string $message
     * @param array $buttons
     * @param string $buttons_type
     */
    public function __construct(string $message, array $buttons = [], string $buttons_type = 'inline')
    {
        $this->message = $message;
        $this->buttons = $buttons;
        $this->buttons_type = $buttons_type;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['telegram'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }

    /**
     * Get the telegram representation of the notification.
     *
     * @param $notifiable
     * @return TelegramMessage
     */
    public function toTelegram($notifiable)
    {
        $message = strip_tags($this->message, '<b><strong><i><em><u><ins><s><strike><del><a><code><pre>');

        $telegramMessage = TelegramMessage::create()->to($notifiable->telegram_chat_id)->content($message);

        if (!empty($this->buttons)) {
            if ($this->buttons_type == 'inline') {
                foreach ($this->buttons as $button) {
                    $telegramMessage->buttonWithCallback($button['text'], $button['callback_data']);
                }
            }

            if ($this->buttons_type == 'reply') {
                foreach ($this->buttons as $button) {
                    $telegramMessage->button($button['text'], $button['callback_data']);
                }
            }
        }

        $telegramMessage->options(['parse_mode' => 'HTML', 'disable_web_page_preview' => true]);

        return $telegramMessage;
    }
}
