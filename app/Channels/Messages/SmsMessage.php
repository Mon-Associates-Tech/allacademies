<?php

namespace App\Channels\Messages;

class SmsMessage
{
    protected string $content = '';

    protected ?string $from = null;

    /**
     * Create a new SMS message instance.
     */
    public function __construct(string $content = '')
    {
        $this->content = $content;
    }

    /**
     * Set the message content.
     */
    public function content(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Set the sender/from number.
     */
    public function from(string $from): self
    {
        $this->from = $from;

        return $this;
    }

    /**
     * Get the message content.
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * Get the sender/from number.
     */
    public function getFrom(): ?string
    {
        return $this->from;
    }

    /**
     * Convert the message to an array.
     *
     * @return array{content: string, from: string|null}
     */
    public function toArray(): array
    {
        return [
            'content' => $this->content,
            'from' => $this->from,
        ];
    }
}
