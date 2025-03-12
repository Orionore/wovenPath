<?php

namespace App\Service;

use Exception;
use Intervention\Image\ImageManager;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class MediaService
{
    private string $uploadsDirectory;
    private ImageManager $imageManager;
    private ValidatorInterface $validator;

    public function __construct(
        string $uploadsDirectory,
        ValidatorInterface $validator,
        ImageManager $imageManager
    ) {
        $this->uploadsDirectory = $uploadsDirectory;
        $this->validator = $validator;
        $this->imageManager = $imageManager;
    }

    /**
     * Traite et enregistre une image
     * @param UploadedFile $imageFile
     * @param string $subDirectory
     * @return string|null Le nom du fichier uniquement (sans le chemin)
     * @throws Exception
     */
    public function processAndSaveImage(UploadedFile $imageFile, string $subDirectory = 'stories'): ?string
    {
        $violations = $this->validateImage($imageFile);
        if (count($violations) > 0) {
            throw new Exception($violations[0]->getMessage());
        }

        $targetDirectory = $this->uploadsDirectory . '/' . $subDirectory;
        if (!file_exists($targetDirectory)) {
            mkdir($targetDirectory, 0777, true);
        }

        $randomString = bin2hex(random_bytes(8));
        $newFilename = $randomString . '-' . uniqid() . '.webp';

        $imageFile->move($targetDirectory, $newFilename);
        $fullPath = $targetDirectory . '/' . $newFilename;
        $image = $this->imageManager->read($fullPath);
        $image = $image->cover(300, 150);
        $image->toWebp(90)->save($fullPath);

        return $newFilename;
    }

    /**
     * Valide le fichier image
     */
    private function validateImage(UploadedFile $file): ConstraintViolationListInterface
    {
        $constraints = new Assert\Collection([
            'file' => [
                new Assert\File([
                    'maxSize' => '2M',
                    'mimeTypes' => [
                        'image/jpeg',
                        'image/png',
                        'image/webp'
                    ],
                    'mimeTypesMessage' => 'Veuillez télécharger une image valide (JPG, PNG ou WebP).',
                    'maxSizeMessage' => 'L\'image ne doit pas dépasser 2 Mo.',
                ])
            ]
        ]);

        return $this->validator->validate(['file' => $file], $constraints);
    }

    /**
     * Supprime une image
     */
    public function deleteImage(string $imageName, string $subDirectory = 'stories'): bool
    {
        $fullPath = $this->uploadsDirectory . '/' . $subDirectory . '/' . $imageName;

        if (file_exists($fullPath)) {
            return unlink($fullPath);
        }

        return false;
    }
}