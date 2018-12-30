<?php

namespace SchoolDiaryBundle\Form;


use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SignInFormFactory
{
    protected $formFactory;
    protected $authenticationUtils;

    public function __construct(FormFactoryInterface $formFactory, AuthenticationUtils $authenticationUtils)
    {
        $this->formFactory = $formFactory;
        $this->authenticationUtils = $authenticationUtils;
    }

    public function createForm(): FormInterface
    {
        $form = $this->formFactory->create(UserLogin::class);
        $form->get('_username')->setData($this->authenticationUtils->getLastUsername());

        if ($error = $this->authenticationUtils->getLastAuthenticationError()) {
            $form->addError(new FormError($error->getMessage()));
        }

        return $form;
    }
}