<?php

namespace App\Mail;

use App\Models\Claim;
use App\Models\ClaimStatus;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use MagicLink\Actions\LoginAction as MagicLinkLoginAction;
use MagicLink\MagicLink;

class ClaimStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var App\Models\Claim $claim
     */
    public $claim;
    public $author;
    public $recipient;
    public $published_at;
    public $manager;
    public $link;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $recipient, Claim $claim)
    {
        $this->claim = $claim;
        $this->recipient = $recipient;
        $this->author = $claim->user->name;
        $this->published_at = \Carbon\Carbon::parse($claim->created_at)->format(config('app.dateformat'));
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = "BROKEN";

        $this->link = MagicLink::create(new MagicLinkLoginAction($this->recipient, redirect(route('claims.show', $this->claim->claim_id))))->url;

        if ($this->claim->status == ClaimStatus::OPEN) {
            $subject = "Поступило завяление #{$this->claim->claim_id}";

            /**
             * @var \App\Models\File $file
             */
            foreach ($this->claim->files as $file) {
                $this->attach(storage_path($file->stored_name), ['as' => $file->original_name]);
            }
        } elseif ($this->claim->status == ClaimStatus::PROCESSED) {
            $subject = "Завяление #{$this->claim->claim_id} принято в обработку";
            $this->manager = $this->claim->manager;
        } elseif ($this->claim->status == ClaimStatus::CLOSED) {
            $subject = "Завяление #{$this->claim->claim_id} закрыто";
        }

        return $this->markdown('email.claim.status')->subject($subject);
    }
}
