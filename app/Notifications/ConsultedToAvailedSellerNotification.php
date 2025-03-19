<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Homeful\Contacts\Models\Customer as Contact;
use Homeful\Contacts\Classes\ContactMetaData;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Homeful\References\Models\Reference;
use Homeful\Properties\Data\ProjectData;
use Homeful\Properties\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Http;

class ConsultedToAvailedSellerNotification extends Notification
{
    use Queueable;

    protected string $reference_code;
    protected string $project_code;

    public function __construct($reference_code, string $project_code)
    {
        $this->reference_code = $reference_code;
        $this->project_code = $project_code;
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
        $reference = Reference::where('code', $this->reference_code)->first();
        $contact =$reference->getEntities(\Homeful\Contacts\Models\Customer::class)->first();
        $name = implode(' ', array_filter([
            $contact->first_name ?? '',
            mb_substr($contact->middle_name ?? '', 0, 1) ? mb_substr($contact->middle_name, 0, 1) . '.' : '',
            $contact->last_name ?? '',
        ]));

        $project_name = '';
        $project = $this->getProject();
        if ($project instanceof Project) {
            $project_name = $project->name;
        }

        return (new MailMessage)
            ->line('Hi ' . $notifiable->name)
            ->line($name . ' has booked a unit at ' . $project_name);
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

    protected function getProject(): ?Project
    {
        return Project::where('code', $this->project_code)->first();
    }
}
