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
use App\Service\UtilsService;

final class RegisterController extends AbstractController
{
    public function __construct(
        private readonly AccountRepository $accountRepository,
        private readonly EntityManagerInterface $em,
        private readonly ValidatorInterface $validator,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly UtilsService $utilsService
    ){}


    #[Route('/register', name: 'app_register_addaccount')]
    public function addAccount(Request $request): Response
    {
        $msg = "";
        $type = "";

        //Créer un onjet account
        $account = new Account();

        //Créer un objet register type (formulaire)
        $form = $this->createForm(RegisterType::class, $account);
        //Récypérer le résulat de la requête 
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            
            //Test si le compte existe déjà
            if(!$this->accountRepository->findOneBy(["email"=> $account->getEmail()])){
                $account->setStatus(false); //Par défaut le compte est désactivé
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
            
        }

        //Passer à la vue
        return $this->render('register/addAccount.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/activate/{id}', name: 'app_activate_Id')]
    public function activate(mixed $id){
        try{
            $id = $this->utilsService->decodeBase64($id);
            if(is_numeric($id)){
                $account = $this->accountRepository->findOneBy(["id"=> $id]);
                if(!$account->isStatus()){
                    $account->setStatus(true);
                    $this->em->persist($account);
                    $this->em->flush();
                    $msg = "compte activé";
                }
            }
        } catch(\Exception $e){
            $this->addFlash("warning", $e->getMessage());
        }
        return $this->redirect('app_home');
    }
}