<?php

namespace App\Mail;

use App\Models\Response;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ClaimResponseMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var App\Models\Response $response
     */
    public $response;
    public $author;
    public $published_at;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Response $response)
    {
        $this->response = $response;
        $this->author = $response->author->name;
        $this->published_at = \Carbon\Carbon::parse($response->created_at)->format(config('app.dateformat'));
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        /**
         * @var \App\Models\File $file
         */
        foreach ($this->response->files as $file) {
            $this->attach(storage_path($file->stored_name), ['as' => $file->original_name]);
        }

        return $this->markdown('email.claim.response')->subject("Завяление #{$this->response->claim_id}: Новое сообщение");
    }
}
