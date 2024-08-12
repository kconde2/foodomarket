<?php

declare(strict_types=1);

namespace App\Controller;

use App\Mercuriale\Domain\Model\Product\Dto\SupplierView;
use App\Mercuriale\Domain\Model\Product\Service\ProductFileUploaderInterface;
use App\Mercuriale\UseCase\Supplier\GetSupplierList\GetSupplierListHandler;
use App\Message\ImportProductData;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/catalog/import', name: 'catalog_import')]
final class ImportMercurialeController extends AbstractController
{
    public function __construct(
        private GetSupplierListHandler $getSupplierListHandler,
        private ValidatorInterface $validator,
        private MessageBusInterface $messageBus,
        private ProductFileUploaderInterface $productFileUploader
    ) {
    }

    public function __invoke(Request $request): Response
    {
        $getSupplierListHandler = $this->getSupplierListHandler;
        $suppliers = $getSupplierListHandler();

        if ($request->isMethod('POST')) {
            return $this->handleImport($request, $suppliers);
        }

        return $this->render('catalog/import.html.twig', [
            'suppliers' => $suppliers,
        ]);
    }

    private function handleImport(Request $request, array $suppliers): Response
    {
        $supplierView = new SupplierView($request->request->get('supplier_name'));

        /** @var UploadedFile|null $file */
        $file = $request->files->get('file');

        $errors = $this->validateData($supplierView, $file);

        if (\count($errors) > 0) {
            return $this->render('catalog/import.html.twig', [
                'errors' => $errors,
                'suppliers' => $suppliers,
            ]);
        }

        $filePath = $this->productFileUploader->upload($file);
        $this->messageBus->dispatch(new ImportProductData($supplierView->name, $filePath));

        $this->addFlash('success', "Le processus d'importation a commencé. Vous recevrez un email une fois l'importation terminée.");

        return $this->redirectToRoute('catalog_index');
    }

    private function validateData(SupplierView $supplierView, ?UploadedFile $file): ConstraintViolationListInterface
    {
        $errors = $this->validator->validate($supplierView);

        if (!$file || !$file->isValid()) {
            $errors->add(new ConstraintViolation(
                message: 'The file is invalid or missing.',
                messageTemplate: '',
                parameters: [],
                root: $file,
                propertyPath: 'file',
                invalidValue: null
            ));
        }

        return $errors;
    }
}
