<?php
namespace smart\rbac\exception;

class UserException extends \yii\base\UserException{

    protected $errors = null;

    public function setErrors($errors)
    {
        $this->errors = $errors;
    }

    public function getErrors()
    {
        return $this->errors;
    }
}