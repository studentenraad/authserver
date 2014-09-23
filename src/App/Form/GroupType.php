<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class GroupType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $id = $options['data']->getId()?:0;
        $builder
            ->add('name')
            ->add('groups', null, array(
                'label'=>'Member of',
                'query_builder'=>function(EntityRepository $repo)use($id) {
                    return $repo->createQueryBuilder('g')
                        ->leftJoin('g.memberGroups', 'm')
                        ->where('g.noGroups = false OR m.id = :id')
                        ->andWhere('g.id != :id')
                        ->setParameter('id', $id);
                },
                'property'=>'name',
                'required'=>false,
                'expanded' => true,
            ))
            ->add('exportable', 'checkbox', array(
                'required' => false,
                'attr' => array(
                    'align_with_widget' => true,
                ),
            ))
            ->add('noGroups', 'checkbox', array(
                'required' => false,
                'label'=>'No groups can be member of this group',
                'attr' => array(
                    'align_with_widget' => true,
                ),
            ))
            ->add('noUsers', 'checkbox', array(
                'required' => false,
                'label'=> 'No users can be member of this group',
                'attr' => array(
                    'align_with_widget' => true,
                ),
            ))
            ->add('submit', 'submit')
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\Group'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'app_group';
    }
}
