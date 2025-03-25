<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Account;
use App\Repository\AccountRepository;
use App\Form\RegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class RegisterController extends AbstractController
{
    public function __construct(
        private readonly AccountRepository $accountRepository,
        private readonly EntityManagerInterface $em,
        private readonly ValidatorInterface $validator,
        private readonly UserPasswordHasherInterface $passwordHasher
    ){}


    #[Route('/register', name: 'app_register_addaccount')]
    public function addAccount(Request $request): Response
    {
        $msg = "";
        $type = "";

        //Créer un onjet account
        $account = new Account();

        $account->setStatus(false); //Par défaut le compte est désactivé

        //Créer un objet register type (formulaire)
        $form = $this->createForm(RegisterType::class, $account);
        //Récypérer le résulat de la requête 
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            /*  $errors = $validator->validate($account);
            //Test si l'entité est valide (validation)
            if(count($errors) > 0) {
                $msg = $errors[0]->getMessage();
                $type = "warning";
            }  */
            //Sinon on ajoute en BDD
            //else{

                //Test si le compte existe déjà
                if(!$this->accountRepository->findOneBy(["email"=> $account->getEmail()])){
                    $account->setRoles(['ROLE_USER']);
                    $this->em->persist($account);
                    $this->em->flush();
                    $msg = "Votre compte a été créé avec succès";
                    $type = "success";

                } 
                else{
                        $msg = "Ce compte existe déjà";
                        $type = "danger";
                }
                $this->addFlash($type, $msg);
            //}
        }

        //Passer à la vue
        return $this->render('register/addAccount.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/activate/{id}', name: 'app_activate_Id')]
    public function activate(int $id){
        $this->accountRepository->findOneBy(["id"=> $id])->setStatus(true);
    }
}