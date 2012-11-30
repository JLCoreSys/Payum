<?php
namespace Payum\Request;

class UserInputRequiredInteractiveRequest extends InteractiveRequest
{
    /**
     * @var array
     */
    protected $requiredFields;

    /**
     * @param array $requiredFields
     */
    public function __construct(array $requiredFields)
    {
        $this->requiredFields = $requiredFields;
    }

    /**
     * @return array
     */
    public function getRequiredFields()
    {
        return $this->requiredFields;
    }
}