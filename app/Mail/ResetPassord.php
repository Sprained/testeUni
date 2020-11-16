<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResetPassord extends Mailable
{
    use Queueable, SerializesModels;


    private $user;
    private $senha;
    private $nome;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user)
    {
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $this->subject('Recuperar senha');
        $this->to($this->user->email, $this->user->nome);
        return $this->view('mail.resetPassword', [
            'senha' => $this->user->senha
        ]);
    }
}
