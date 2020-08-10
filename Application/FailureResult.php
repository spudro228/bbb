<?php

namespace Infra\InfraBot\Application;

use Infra\InfraBot\Application\Messenger\Message;

class FailureResult extends Result
{
    /**
     * @var Message
     */
    private $message;
    /**
     * @var string
     */
    private $cause;

    /**
     * FailureResult constructor.
     * @param Message $message
     * @param string $cause
     */
    public function __construct(Message $message, string $cause)
    {
        $this->message = $message;
        $this->cause = $cause;
    }

    /**
     * @return Message
     */
    public function message(): Message
    {
        return $this->message;
    }

    /**
     * @return string
     */
    public function cause(): string
    {
        return $this->cause;
    }


}
