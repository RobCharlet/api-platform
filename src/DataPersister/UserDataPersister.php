<?php


namespace App\DataPersister;


use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\User;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserDataPersister implements ContextAwareDataPersisterInterface
{

    private $userPasswordEncoder;
    private $dataPersister;
    private $logger;

    public function __construct(
        $dataPersister,
        UserPasswordEncoderInterface $userPasswordEncoder,
        LoggerInterface $logger
    )
    {
        $this->userPasswordEncoder = $userPasswordEncoder;
        $this->dataPersister = $dataPersister;
        $this->logger = $logger;
    }

    public function supports($data, array $context= []): bool
    {
        return $data instanceof User;
    }

    /**
     * @param User $data
     */
    public function persist($data, array $context= [])
    {
        if (($context['collection_operation_name'] ?? null) === 'put') {
            $this->logger->info(sprintf('User %s is being updated', $data->getId()));
        }

        /**
         * If object not created
         */
        if (!$data->getId()) {
            // take any actions needed for a new user
            // send registration email
            // integrate into some CRM or payment system
            $this->logger->info(sprintf('User %s just registered', $data->getEmail()));
        }

        /**
         * Encode password before persist
         */
        if ($data->getPlainPassword()) {
            $data->setPassword(
                $this->userPasswordEncoder->encodePassword($data, $data->getPlainPassword())
            );

            $data->eraseCredentials();
        }

        $this->dataPersister->persist($data);
    }

    public function remove($data, array $context= [])
    {
        $this->dataPersister->remove($data);
    }
}