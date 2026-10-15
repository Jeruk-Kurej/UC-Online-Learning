<?php

namespace App\Traits;

/**
 * wa.me chat link built from the model's whatsapp number (digits only), or null without a number.
 */
trait HasWhatsAppLink
{
    public function getWhatsappLinkAttribute(): ?string
    {
        return $this->whatsapp ? 'https://wa.me/'.preg_replace('/[^0-9]/', '', $this->whatsapp) : null;
    }
}
