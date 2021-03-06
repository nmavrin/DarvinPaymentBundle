<?php
/**
 * Created by PhpStorm.
 * User: levsemin
 * Date: 08.07.2018
 * Time: 2:44
 */

namespace Darvin\PaymentBundle\Repository;


use Darvin\PaymentBundle\Entity\PaymentInterface;
use Doctrine\ORM\EntityRepository;

class PaymentRepository extends EntityRepository
{
    /**
     * @param $token
     *
     * @return PaymentInterface|null|object
     */
    public function findByActionToken($token)
    {
        return $this->findOneBy([
            'actionToken' => $token
        ]);
    }
}