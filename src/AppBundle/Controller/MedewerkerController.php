<?php

namespace AppBundle\Controller;


use AppBundle\Entity\Activiteit;
use AppBundle\Entity\Soortactiviteit;
use AppBundle\Form\ActiviteitType;
use AppBundle\Form\SoortType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

class MedewerkerController extends Controller
{
    /**
     * @Route("/admin/activiteiten", name="activiteitenoverzicht")
     */
    public function activiteitenOverzichtAction()
    {

        $entities = $this->getEntities();

        return $this->render('medewerker/activiteiten.html.twig', [
            'activiteiten'=>$entities["activiteiten"],
            'soorten'=>$entities["soorten"],
            'deelnemers'=>$entities["deelnemers"],
        ]);
    }

    /**
     * @Route("/admin/details/{id}", name="details")
     */
    public function detailsAction($id)
    {
        $entities = $this->getEntities();

        $activiteit=$this->getDoctrine()
            ->getRepository('AppBundle:Activiteit')
            ->find($id);

        $deelnemers=$this->getDoctrine()
            ->getRepository('AppBundle:User')
            ->getDeelnemers($id);


        return $this->render('medewerker/details.html.twig', [
            'activiteit'=>$activiteit,
            'deelnemers'=>$deelnemers,
            'aantalA'=>count($entities["activiteiten"]),'aantalS'=>count($entities["soorten"]),'aantalU'=>count($entities["deelnemers"])
        ]);
    }

    /**
     * @Route("/admin/beheer", name="beheer")
     */
    public function beheerAction()
    {
        $entities = $this->getEntities();

        return $this->render('medewerker/beheer.html.twig', [
            'activiteiten'=>$entities["activiteiten"],
            'soorten'=>$entities["soorten"],
            'deelnemers'=>$entities["deelnemers"],
        ]);
    }

    /**
     * @Route("/admin/soortbeheer", name="soortbeheer")
     */
    public function soortBeheerAction()
    {
        $entities = $this->getEntities();

        return $this->render('medewerker/soortbeheer.html.twig', [
            'activiteiten'=>$entities["activiteiten"],
            'soorten'=>$entities["soorten"],
            'deelnemers'=>$entities["deelnemers"],
        ]);
    }

    /**
     * @Route("/admin/deelnemerbeheer", name="deelnemerbeheer")
     */
    public function deelnemerbeheerAction()
    {
        $entities = $this->getEntities();

        return $this->render("medewerker/deelnemerbeheer.html.twig", [
            'activiteiten'=>$entities["activiteiten"],
            'soorten'=>$entities["soorten"],
            'deelnemers'=>$entities["deelnemers"],
            ]);
    }

    /**
     * @Route("/admin/add", name="add")
     */
    public function addAction(Request $request)
    {
        // create a user and a contact
        $a=new Activiteit();

        $form = $this->createForm(ActiviteitType::class, $a);
        $form->add('save', SubmitType::class, array('label'=>"voeg toe"));
        //$form->add('reset', ResetType::class, array('label'=>"reset"));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($a);
            $em->flush();

            $this->addFlash(
                'notice',
                'activiteit toegevoegd!'
            );
            return $this->redirectToRoute('beheer');
        }
        $entities = $this->getEntities();

        return $this->render('medewerker/add.html.twig',array('form'=>$form->createView(),'naam'=>'toevoegen','aantalA'=>count($entities["activiteiten"]),'aantalS'=>count($entities["soorten"]),'aantalU'=>count($entities["deelnemers"])));
    }

    /**
     * @Route("/admin/soortadd", name="soortadd")
     */
    public function soortAddAction(Request $request)
    {
        // create a user and a contact
        $a=new Soortactiviteit();

        $form = $this->createForm(SoortType::class, $a);
        $form->add('save', SubmitType::class, array('label'=>"voeg toe"));
        //$form->add('reset', ResetType::class, array('label'=>"reset"));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($a);
            $em->flush();

            $this->addFlash(
                'notice',
                'soort activiteit toegevoegd!'
            );
            return $this->redirectToRoute('soortbeheer');
        }
        $entities = $this->getEntities();

        return $this->render('medewerker/addsoort.html.twig',array('form'=>$form->createView(),'naam'=>'toevoegen','aantalA'=>count($entities["activiteiten"]),'aantalS'=>count($entities["soorten"]),'aantalU'=>count($entities["deelnemers"])));
    }

    /**
     * @Route("/admin/update/{id}", name="update")
     */
    public function updateAction($id,Request $request)
    {
        $a=$this->getDoctrine()
            ->getRepository('AppBundle:Activiteit')
            ->find($id);

        $form = $this->createForm(ActiviteitType::class, $a);
        $form->add('save', SubmitType::class, array('label'=>"aanpassen"));

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();

            // tells Doctrine you want to (eventually) save the contact (no queries yet)
            $em->persist($a);


            // actually executes the queries (i.e. the INSERT query)
            $em->flush();
            $this->addFlash(
                'notice',
                'activiteit aangepast!'
            );
            return $this->redirectToRoute('beheer');
        }

        $entities = $this->getEntities();

        return $this->render('medewerker/addsoort.html.twig',array('form'=>$form->createView(),'naam'=>'aanpassen','aantalA'=>count($entities["activiteiten"]),'aantalS'=>count($entities["soorten"]),'aantalU'=>count($entities["deelnemers"])));
    }

    /**
     * @Route("/admin/updatesoort/{id}", name="updatesoort")
     */
    public function updateSoortAction($id,Request $request)
    {
        $a=$this->getDoctrine()
            ->getRepository('AppBundle:Soortactiviteit')
            ->find($id);

        $form = $this->createForm(SoortType::class, $a);
        $form->add('save', SubmitType::class, array('label'=>"aanpassen"));

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();

            // tells Doctrine you want to (eventually) save the contact (no queries yet)
            $em->persist($a);


            // actually executes the queries (i.e. the INSERT query)
            $em->flush();
            $this->addFlash(
                'notice',
                'soort aangepast!'
            );
            return $this->redirectToRoute('soortbeheer');
        }

        $entities = $this->getEntities();

        return $this->render('medewerker/addsoort.html.twig',array('form'=>$form->createView(),'naam'=>'aanpassen','aantalA'=>count($entities["activiteiten"]),'aantalS'=>count($entities["soorten"]),'aantalU'=>count($entities["deelnemers"])));
    }

    /**
     * @Route("/admin/resetdeelnemer/{id}", name="resetdeelnemer")
     */
    public function resetDeelnemerAction($id,Request $request)
    {
        $u=$this->getDoctrine()
            ->getRepository('AppBundle:User')
            ->find($id);

        $em = $this->getDoctrine()->getManager();
        $u->setPassword("qwerty");

        // tells Doctrine you want to (eventually) save the contact (no queries yet)
        $em->persist($u);


        // actually executes the queries (i.e. the INSERT query)
        $em->flush();
        $this->addFlash(
            'notice',
            'wachtwoord van deelnemer reset!'
        );
        return $this->redirectToRoute('deelnemerbeheer');
    }

    /**
     * @Route("/admin/delete/{id}", name="delete")
     */
    public function deleteAction($id)
    {
        $em=$this->getDoctrine()->getManager();
        $a= $this->getDoctrine()
            ->getRepository('AppBundle:Activiteit')->find($id);
        $em->remove($a);
        $em->flush();

        $this->addFlash(
            'notice',
            'activiteit verwijderd!'
        );
        return $this->redirectToRoute('beheer');

    }

    /**
     * @Route("/admin/deletesoort/{id}", name="deletesoort")
     */
    public function deleteSoortAction($id)
    {
        $em=$this->getDoctrine()->getManager();
        $a= $this->getDoctrine()
            ->getRepository('AppBundle:Soortactiviteit')->find($id);
        $em->remove($a);
        $em->flush();

        $this->addFlash(
            'notice',
            'soort verwijderd!'
        );
        return $this->redirectToRoute('soortbeheer');

    }

    /**
     * @Route("/admin/deletedeelnemer/{id}", name="deletedeelnemer")
     */
    public function deleteDeelnemerAction($id)
    {
        $em=$this->getDoctrine()->getManager();
        $a= $this->getDoctrine()
            ->getRepository('AppBundle:User')->find($id);
        $em->remove($a);
        $em->flush();

        $this->addFlash(
            'notice',
            'gebruiker verwijderd!'
        );
        return $this->redirectToRoute('deelnemerbeheer');
    }

    private function getEntities(){
        $activiteiten=$this->getDoctrine()
            ->getRepository('AppBundle:Activiteit')
            ->findAll();
        $soorten=$this->getDoctrine()
            ->getRepository('AppBundle:Soortactiviteit')
            ->findAll();
        $deelnemers=$this->getDoctrine()
            ->getRepository('AppBundle:User')
            ->findAll();
        $entities = Array("activiteiten"=>$activiteiten, "soorten"=>$soorten, "deelnemers"=>$deelnemers);
        return $entities;
    }
}
