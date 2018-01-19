<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class Reminder extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($pdf, $claveComprobante)

    {       
        $this->pdf = $pdf;
        $this->claveComprobante = $claveComprobante;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {   
        return $this->view('emails.mail')->subject('Cotización')->attachData($this->pdf->output(), "$this->claveComprobante.pdf");
    }
}
