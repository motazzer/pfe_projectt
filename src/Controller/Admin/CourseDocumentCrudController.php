<?php

namespace App\Controller\Admin;

use App\Entity\CourseDocument;
use App\Message\TransformTextToVectors;
use Doctrine\Persistence\ManagerRegistry;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Random\RandomException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Messenger\MessageBusInterface;


class CourseDocumentCrudController extends AbstractCrudController
{

    private $managerRegistry;
    private $adminUrlGenerator;
    public function __construct(ManagerRegistry $managerRegistry,AdminUrlGenerator $adminUrlGenerator)
    {
        $this->managerRegistry = $managerRegistry;
        $this->adminUrlGenerator = $adminUrlGenerator;
    }
    public static function getEntityFqcn(): string
    {
        return CourseDocument::class;
    }

    public function configureFields(string $pageName): iterable
    {

        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('title'),
            TextField::new('content'),
            TextField::new('User.email', 'Upload_By')->hideOnForm(),
            TextField::new('status'),
        ];
    }
    public function configureActions(Actions $actions): Actions
    {
        $verifyAction = Action::new('verify', 'Verify')
            ->linkToCrudAction('verifyDocument')
            ->setIcon('fa fa-check')
            ->displayIf(static function ($entity) {
                return $entity->getStatus() === 'unverified';
            });

        return $actions
            ->add(Crud::PAGE_DETAIL, $verifyAction)
            ->remove(Crud::PAGE_INDEX, Action::NEW);
    }

    /**
     * @throws RandomException
     */
    public function verifyDocument(AdminContext $context,MessageBusInterface $bus): RedirectResponse
    {
        $documentId = $context->getRequest()->query->get('entityId');
        $entityManager = $this->managerRegistry->getManager();
        $document = $entityManager->getRepository(CourseDocument::class)->find($documentId);

        if (!$document) {
            throw $this->createNotFoundException(sprintf('Document with id "%s" not found', $documentId));
        }

        $message = new TransformTextToVectors($documentId);
        $bus->dispatch($message);

        $document->setStatus('verified');
        $entityManager->persist($document);
        $entityManager->flush();

        $this->addFlash('success', 'Document verified successfully and content transformed into vectors.');


        $url = $this->adminUrlGenerator
            ->setController(CourseDocumentCrudController::class)
            ->setAction(Action::DETAIL)
            ->generateUrl();

        return $this->redirect($url);
    }

}