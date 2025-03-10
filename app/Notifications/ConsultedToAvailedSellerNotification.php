<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Homeful\Contacts\Models\Customer as Contact;
use Homeful\Contacts\Classes\ContactMetaData;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Homeful\References\Models\Reference;
use Homeful\Properties\Models\Project;
use Illuminate\Bus\Queueable;
use Homeful\Properties\Data\ProjectData;


class ConsultedToAvailedSellerNotification extends Notification implements ShouldQueue
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
        $contact = $this->getContact();
        if ($contact instanceof ContactMetaData) {
            $name = $contact->name;
        }

        $project_name = '';
        $project = $this->getProject();
        if ($project instanceof ProjectData) {
            $project_name = $project->name;
        }

        return (new MailMessage)
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
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

    protected function getProject(): ?ProjectData
    {
        $project = Project::where('code', $this->project_code)->first();

        return $project instanceof Project ? ProjectData::fromModel($project) : null;
    }
}
