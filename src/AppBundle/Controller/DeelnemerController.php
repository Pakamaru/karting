<?php

namespace AppBundle\Controller;


use AppBundle\Entity\User;
use AppBundle\Form\UserType;
use AppBundle\Form\UserWeizigType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class DeelnemerController extends Controller
{
    /**
     * @Route("/user/activiteiten", name="activiteiten")
     */
    public function activiteitenAction()
    {
        $usr= $this->get('security.token_storage')->getToken()->getUser();

        $beschikbareActiviteiten=$this->getDoctrine()
            ->getRepository('AppBundle:Activiteit')
        ->getBeschikbareActiviteiten($usr->getId());

        $ingeschrevenActiviteiten=$this->getDoctrine()
            ->getRepository('AppBundle:Activiteit')
            ->getIngeschrevenActiviteiten($usr->getId());

        $totaal=$this->getDoctrine()
            ->getRepository('AppBundle:Activiteit')
            ->getTotaal($ingeschrevenActiviteiten);


        return $this->render('deelnemer/activiteiten.html.twig', [
                'beschikbare_activiteiten'=>$beschikbareActiviteiten,
                'ingeschreven_activiteiten'=>$ingeschrevenActiviteiten,
                'totaal'=>$totaal,
                'date'=>date('Y-m-d-H-i'),
        ]);
    }

    /**
     * @Route("/user/weizig", name="weizig")
     */
    public function weizigAction(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $user= $this->get('security.token_storage')->getToken()->getUser();

        $form = $this->createForm(UserWeizigType::class, $user);
        $form->add('save', SubmitType::class, array('label'=>"Update!"));
        // 2) handle the submit (will only happen on POST)
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $repository = $this->getDoctrine()->getRepository(User::class);
            $bestaande_user = $repository->findOneBy(['username' => $form->getData()->getUsername()]);
            $updUser = $form->getData();

            if ($bestaande_user !== null && $user !== $bestaande_user) {
                $this->addFlash(
                    'error',
                    $user->getUsername() . " bestaat al!"
                );
                return $this->redirectToRoute('weizig');

            } else {
                if ($updUser->getPlainPassword() == null || $updUser->getPlainPassword() == "") {
                    $plainPassword = $user->getPlainPassword();
                } else {
                    $plainPassword = $updUser->getPlainPassword();


                }
                $password = $passwordEncoder->encodePassword($user, $plainPassword);
                $updUser->setPassword($password);
                $em = $this->getDoctrine()->getManager();
                $em->persist($updUser);
                $em->flush();

                $this->addFlash(
                    'notice',
                    $user->getNaam() . ' is geupdate!'
                );

                return $this->redirectToRoute('activiteiten');
            }
        }


        return $this->render('deelnemer/info.html.twig', [
            'form'=>$form->createView()
        ]);


    }

    /**
     * @Route("/user/inschrijven/{id}", name="inschrijven")
     */
    public function inschrijvenActiviteitAction($id)
    {

        $activiteit = $this->getDoctrine()
            ->getRepository('AppBundle:Activiteit')
            ->find($id);

        if($activiteit->getDatum() < date('Y-m-d')) {
            $this->addFlash(
                'notice',
                'Dit activiteit is al geweest'
            );
        }elseif($activiteit->getTijd() < date('H-i')) {
            $this->addFlash(
                'notice',
                'Dit activiteit is al geweest'
            );
        }elseif ($activiteit->getMaxDeelnemers() <= count($activiteit->getUsers())){
            $this->addFlash(
                'notice',
                'Dit activiteit is vol'
            );
        }elseif ($activiteit->getMaxDeelnemers() > count($activiteit->getUsers())) {
            $this->addFlash(
                'notice',
                'inschrijfing vooltooid!'
            );
            $usr = $this->get('security.token_storage')->getToken()->getUser();
            $usr->addActiviteit($activiteit);

            $em = $this->getDoctrine()->getManager();
            $em->persist($usr);
            $em->flush();
        }


        return $this->redirectToRoute('activiteiten');
    }

    /**
     * @Route("/user/uitschrijven/{id}", name="uitschrijven")
     */
    public function uitschrijvenActiviteitAction($id)
    {
        $activiteit = $this->getDoctrine()
            ->getRepository('AppBundle:Activiteit')
            ->find($id);
        $usr= $this->get('security.token_storage')->getToken()->getUser();
        $usr->removeActiviteit($activiteit);
        $em = $this->getDoctrine()->getManager();
        $em->persist($usr);
        $em->flush();
        return $this->redirectToRoute('activiteiten');
    }

}
