<?php
namespace App\Form;

use App\Entity\Keyword;
use App\Entity\Workshop;
use App\Entity\Theme;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\PercentType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Vich\UploaderBundle\Form\Type\VichImageType;
use \Symfony\Component\Form\Extension\Core\Type\SubmitType;
use \Symfony\Component\Validator\Constraints\File;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class WorkshopType extends AbstractType
{

	public function buildForm(FormBuilderInterface $builder, array $options)
	{

		$builder
				->add('theme', EntityType::class, [
					'class' => Theme::class,
					'choice_label'	=> 'name'
				])
				->add('name')
				->add('description', TextareaType::class, array('attr' => array('class' => 'ckeditor')))
				->add('imageFile', VichImageType::class, [
					'required'		 => false,
					'allow_delete'	 => true,
				])
				->add('dateBegin')
				->add('dateEnd')
                ->add('dateVoteBegin')
                ->add('dateVoteEnd')
				->add('rightsSeeWorkshop', ChoiceType::class, ['choices' => ['Everyone' => 'everyone']])
				->add('rightsVoteProposals', ChoiceType::class, ['choices' => ['Everyone' => 'everyone']])
				->add('rightsWriteProposals', ChoiceType::class, ['choices' => ['Everyone' => 'everyone']])
				->add('quorumRequired', PercentType::class)
				->add('rightsDelegation')
                ->add('keytext',TextType::class,[
                    'help' => 'keyword.help',
                    'label' => 'keyword'
                ])
				->add('Submit', SubmitType::class)
		;
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults([
			'data_class' => Workshop::class,
		]);
	}

}