<?php


namespace Infra\InfraBot\Application;


class SuccessResult extends Result
{
    private $result;

    public function __construct($result)
    {
        $this->result = $result;
    }

    /**
     * @return mixed
     */
    public function result()
    {
        return $this->result;
    }


}
