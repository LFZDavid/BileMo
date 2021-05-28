<?php

namespace App\Security\Voter;

use App\Entity\Customer;
use App\Entity\Supplier;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class CustomerVoter extends Voter
{
    const VIEW = 'view';
    const EDIT = 'edit';
    const DELETE = 'delete';

    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, [self::VIEW, self::EDIT, self::DELETE])
            && $subject instanceof \App\Entity\Customer;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        /** @var Supplier $supplier */
        $supplier = $token->getUser();
        if (!$supplier instanceof UserInterface) {
            return false;
        }

        /** @var Customer $subject */
        switch ($attribute) {
            case self::VIEW:
                return $this->canView($subject, $supplier);
            case self::EDIT:
                return $this->canEdit($subject, $supplier);
            case self::DELETE:
                return $this->canDelete($subject, $supplier);
        }

        throw new \LogicException('This code should not be reached');
    }

    protected function canView(Customer $customer, Supplier $supplier)
    {
        return $customer->getSupplier() == $supplier;
    }
    protected function canEdit(Customer $customer, Supplier $supplier)
    {
        return $customer->getSupplier() == $supplier;
    }
    protected function canDelete(Customer $customer, Supplier $supplier)
    {
        return $customer->getSupplier() == $supplier;
    }
}
