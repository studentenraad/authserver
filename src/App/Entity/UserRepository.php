<?php

namespace App\Entity;

use App\Doctrine\EntityRepository;
use vierbergenlars\Bundle\RadRestBundle\Pagination\EmptyPageDescription;
use App\Search\SearchGrammar;
use vierbergenlars\Bundle\RadRestBundle\Doctrine\QueryBuilderPageDescription;
use App\Search\SearchFieldException;
use App\Search\SearchValueException;

class UserRepository extends EntityRepository
{
    protected $fieldSearchWhitelist = array('username', 'email');

    public function search($terms)
    {
        if(is_string($terms)) {
            $parser = new SearchGrammar();
            $blocks = $parser->parse($terms);
        } else if(is_array($terms)) {
            $blocks = array();
            foreach($terms as $name=>$value) {
                $blocks[] = array(
                    'name' => $name,
                    'value' => $value,
                );
            }
        }

        $queryBuilder = $this->createQueryBuilder('u');
        $and = $queryBuilder->expr()->andX();

        foreach($blocks as $i=>$block) {
            if(!in_array($block['name'], $this->fieldSearchWhitelist)) {
                $this->handleUnknownSearchField($block);
            }

            if($block['name'] === 'email') {
                $queryBuilder->select('DISTINCT u')
                        ->leftJoin('AppBundle:EmailAddress', 'e', 'WITH', 'e.email LIKE ?'.$i);
                $queryBuilder->setParameter($i, str_replace('*', '%', $block['value']));
            } else if(strpos($block['value'], '*') !== false) {
                $and->add($queryBuilder->expr()->like('u.'.$block['name'], '?'.$i));
                $queryBuilder->setParameter($i, str_replace('*', '%', $block['value']));
            } else {
                $and->add($queryBuilder->expr()->eq('u.'.$block['name'], '?'.$i));
                $queryBuilder->setParameter($i, $block['value']);
            }
        }

        if($and->count()) {
            $queryBuilder->where($and);
        }
        return new QueryBuilderPageDescription($queryBuilder);
    }
    
    public function handleUnknownSearchField(array &$block)
    {
        switch($block['name']) {
            case 'is':
                switch(strtolower($block['value'])) {
                    case 'admin':
                        $block['name']  = 'role';
                        $block['value'] = 'ROLE_*ADMIN'; // ROLE_ADMIN and ROLE_SUPER_ADMIN
                        break;
                    case 'superadmin':
                    case 'super_admin':
                    case 'su':
                        $block['name']  = 'role';
                        $block['value'] = 'ROLE_SUPER_ADMIN';
                        break;
                    case 'user':
                        $block['name']  = 'role';
                        $block['value'] = 'ROLE_USER';
                        break;
                    case 'enabled':
                        $block['name']  = 'isActive';
                        $block['value'] = true;
                        break;
                    case 'disabled':
                        $block['name']  = 'isActive';
                        $block['value'] = false;
                        break;
                    default:
                        throw new SearchValueException($block['name'], $block['value'], array('admin', 'superadmin', 'super_admin', 'su', 'enabled', 'disabled'));
                }
                break;
            default:
                parent::handleUnknownSearchField($block);
        }
    }

    private function updateEmails($object) {
        foreach($object->getEmailAddresses() as $email) {
            $email->setUser($object);
            $this->getEntityManager()->persist($email);
        }
    }
    public function create($object) {
        $this->updateEmails($object);
        parent::create($object);
        $this->getEntityManager()->flush($object->getEmailAddresses()->toArray());
    }

    public function update($object) {
        $this->updateEmails($object);
        parent::update($object);
        $this->getEntityManager()->flush($object->getEmailAddresses()->toArray());
    }
}
