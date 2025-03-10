<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Homeful\Contacts\Models\Customer as Contact;
use Homeful\Contacts\Classes\ContactMetaData;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Homeful\References\Models\Reference;
use Illuminate\Bus\Queueable;

class OnboardedToPaidSellerNotification extends Notification implements ShouldQueue
{
    use Queueable;
    protected Reference $reference;

    protected string $reference_code;

    public function __construct($reference_code)
    {
        $this->reference_code = $reference_code;
    }
    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $name = '';
        $contact = $this->getContact();
        if ($contact instanceof ContactMetaData) {
            $name = $contact->name;
        }

        return (new MailMessage)
            ->line('Hi ' . $notifiable->name)
            ->line($name . 'has paid the consultation fee.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }

    protected function getContact(): ?ContactMetaData
    {
        $reference = Reference::where('code', $this->reference_code)->first();
        $contact = $reference->getContact();

        return $contact instanceof Contact
            ? $contact->getData()
            : null;
    }
}
