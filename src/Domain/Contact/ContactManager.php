<?php

declare(strict_types=1);

namespace App\Domain\Contact;

use App\Domain\Contact\Model\Contact;
use App\Domain\Contact\Notification\ContactNotification;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\Recipient;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class ContactManager
{
    public function __construct(
        private NotifierInterface $notifier,
        private TranslatorInterface $translator,
        private string $contactEmail,
        private string $contactPhone,
    ) {
    }

    public function notify(Contact $contact): void
    {
        $subject = $this->translator->trans(id: 'contact.subject', parameters: ['name' => $contact->name], domain: 'notification');

        $notification = (new ContactNotification($contact, $subject))
            ->importance(Notification::IMPORTANCE_URGENT)
        ;

        $recipient = new Recipient($this->contactEmail, $this->contactPhone);

        $this->notifier->send($notification, $recipient);
    }
}
