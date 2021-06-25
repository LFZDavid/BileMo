<?php

namespace App\Service;

use App\ApiProblem;
use App\Exception\ApiProblemException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\ConstraintViolationList;

class EntityChecker
{

    protected ValidatorInterface $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function check(Object $entity): void
    {
        /** @var ConstraintViolationList $errors */
        $errors = $this->validator->validate($entity);
        if($errors->count() > 0){
            foreach ($errors->getIterator()->getArrayCopy() as $constraintViolation)
                /** @var ConstraintViolation  $constraintViolation */
                $errorMessages[$constraintViolation->getPropertyPath()] = $constraintViolation->getMessage();
            $apiProblem = new ApiProblem(JsonResponse::HTTP_BAD_REQUEST, ApiProblem::TYPE_VALIDATION_ERROR);
            $apiProblem->set('errors', $errorMessages, 'json');
            throw new ApiProblemException($apiProblem);
        }
    }
}