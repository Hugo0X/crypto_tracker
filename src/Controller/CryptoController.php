<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\ApiTrackerController;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Crypto;
use App\Repository\CryptoRepository;
use App\Form\CryptoType;
use Doctrine\ORM\EntityManagerInterface;


/**
* @Route("/crypto")
*/
class CryptoController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/", name="app_crypto_index")
     */
    public function index(CryptoRepository $cryptoRepository)
    {
        $query = $this->em->createQuery(
            'SELECT IDENTITY(con.crypto) cryptoId, SUM(con.price) price, SUM(con.quantity) quantity
            FROM App\Entity\Contract con
            GROUP BY con.crypto '
        );
        $sumCrypto = $query->getResult();

        return $this->render('crypto/index.html.twig', [
            'sumCrypto' => $sumCrypto,
            'cryptos' => $cryptoRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="app_crypto_new")
     */
    public function formNewCrypto(Request $request, ApiTrackerController $apiTracker, CryptoRepository $cryptoRepository): Response
    {    
        $crypto = new Crypto;
        $form = $this->createForm(CryptoType::class, $crypto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $apiTracker->isCryptoExist($form['name']->getData())) {

            $crypto = $form->getData();


            if($apiTracker->isCryptoExist($crypto->getName()) == 'dash') {
                $crypto->setName(str_replace(' ', '-', strtolower($crypto->getName())));        
            }
            else {
                 if ($cryptoRepository->findOneBy(['name' => str_replace(' ', '', strtolower($crypto->getName()))])) {
                    $this->addFlash('error', 'Cette crypto monnaie existe déjà.');
                    return $this->render('crypto/formNewCrypto.html.twig', [
                        'form' => $form->createView(), 
                    ]);
                 }
                 $crypto->setName(str_replace(' ', '', strtolower($crypto->getName())));
            }
            
            $crypto->setAcronym($apiTracker->getAcronym($crypto->getName()));
            $this->em->persist($crypto);
            $this->em->flush();

            $this->addFlash('success', 'La crypto monnaie a bien été ajoutée !');
            return $this->redirectToRoute('app_crypto_index');
        }
        elseif($form->isSubmitted() && $form->isValid())
            $this->addFlash('error', 'Cette crypto monnaie n\'existe pas.');

        return $this->render('crypto/formNewCrypto.html.twig', [
            'form' => $form->createView(), 
        ]);
    }

    /**
     * @Route("/delete/{id<[0-9]+>}", name="app_crypto_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Crypto $crypto): Response
    {
        if ($this->isCsrfTokenValid('crypto_deletion_'.$crypto->getId(), $request->request->get('_token'))) {
      
            $this->em->remove($crypto);
        
            $this->em->flush();

        }
        $this->addFlash('success', 'La crypto monnaie a bien été supprimée !');
        return $this->redirectToRoute('app_crypto_index');
    }

}
