<?php

namespace App\Services\Notification;

use App\Models\User;
use App\Mail\DefaultMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\View;

class NotificationService
{
  protected $recipients = [];

  protected $ccmails = [];
  protected $bccmails = [];

  public function toUsers(array $users)
  {
    $this->recipients = array_map(function ($user) {
      return $user->email;
    }, $users);

    return $this;
  }

  public function toEmails($emails = [])
  {
    if (!empty($emails)) {
      $this->recipients = array_merge($this->recipients, $emails);
    }
    return $this;
  }

  public function withCCMails(array $ccmail)
  {

    if ($ccmail[0]!=null) {

      $this->ccmails = array_merge($this->ccmails, $ccmail);
    }

    return $this;
  }

  public function withBCCMails($bccmail = [])
  {
    if ($bccmail[0]!=null) {
      $this->bccmails = array_merge($this->bccmails, $bccmail);
    }

    return $this;
  }

  public function sendMail(MailObject $mailObject)
  {

    foreach ($this->recipients as $recipient) {

      if (filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
        Mail::to($recipient)->cc($this->ccmails)->bcc($this->bccmails)->send(new DefaultMail($mailObject));
      }
    }
    return $this;
  }

  public function sendInApp()
  {
  }
}
