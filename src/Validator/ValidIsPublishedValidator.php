<?php

namespace App\Validator;

use App\Entity\CheeseListing;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ValidIsPublishedValidator extends ConstraintValidator
{
    private $entityManager;
    /**
     * @var Security
     */
    private $security;

    public function __construct(EntityManagerInterface $entityManager, Security $security)
    {
        $this->entityManager = $entityManager;
        $this->security = $security;
    }

    public function validate($value, Constraint $constraint)
    {
        /* @var $constraint \App\Validator\ValidIsPublished */

        if (!$value instanceof CheeseListing) {
            throw new \LogicException("Only CheeseListing is supported");
        }

        $originalData = $this->entityManager
            ->getUnitOfWork()
            ->getOriginalEntityData($value);

        $previousIsPublished = $originalData['isPublished'] ?? false;

        if ($previousIsPublished === $value->getIsPublished()) {
            // isPublished didn't change
            return;
        }

        if ($value->getIsPublished()) {
            // we are publishing

            // Don't allow short description, unless you are an admin
            if(strlen($value->getDescription() < 100) && !$this->security->isGranted('ROLE_ADMIN')) {
                $this->context->buildViolation('Cannot publish: description is too short')
                    ->atPath('description')
                    ->addViolation();
            }

            return;
        }

        // we are UNpublishing
        if(!$this->security->isGranted('ROLE_ADMIN')) {
//            throw new AccessDeniedException('Only admin user can unpublish');

            $this->context->buildViolation('Only admin user can unpublish')
                ->addViolation();
        }
    }
}
