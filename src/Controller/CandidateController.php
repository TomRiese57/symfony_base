<?php

namespace App\Controller;

use App\Form\CandidateFlowType;
use App\Entity\Candidate;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Flow\FormFlowInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/candidate', name: 'candidate_')]
final class CandidateController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(): Response
    {
        return $this->render('candidate/index.html.twig', [
            'controller_name' => 'CandidateController',
        ]);
    }

    #[Route('/apply', name: 'apply')]
    public function __invoke(Request $request): Response
    {
        /** @var FormFlowInterface $flow */
        $flow = $this->createForm(CandidateFlowType::class, new Candidate())
            ->handleRequest($request);
 
        if ($flow->isSubmitted() && $flow->isValid() && $flow->isFinished()) {
            //dd($flow->getData());
 
            $this->addFlash('success', 'Your form flow was successfully finished!');
 
            return $this->redirectToRoute('candidate_apply');
        }
 
        return $this->render('candidate/apply.html.twig', [
            'form' => $flow->getStepForm(),
        ]);
    }
}
