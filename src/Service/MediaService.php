<?php
// src/Service/ImageUploader.php

namespace App\Service;

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class MediaService
{
    private $params;
    private $slugger;
    private $imageManager;

    public function __construct(ParameterBagInterface $params, SluggerInterface $slugger)
    {
        $this->params = $params;
        $this->slugger = $slugger;

        // Initialisation du gestionnaire d'image avec le driver GD
        // Vous pouvez aussi utiliser 'imagick' si l'extension est disponible
        $this->imageManager = new ImageManager(new Driver());
    }

    public function upload(UploadedFile $file, int $width = 300, int $height = 150): string
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $fileName = $safeFilename . '-' . uniqid() . '.' . 'jpg'; // On sauvegarde en JPG pour la cohérence
        $targetPath = $this->getTargetDirectory() . '/' . $fileName;

        try {
            // Créer l'image avec Intervention
            $image = $this->imageManager->read($file->getPathname());

            // Redimensionnement avec conservation du ratio et puis recadrage précis
            $image->cover($width, $height);

            // Alternative: resize proportionnellement sans recadrage
            // $image->scale(width: $width, height: $height);

            // Sauvegarder l'image avec une qualité de 85%
            $image->toJpeg(85)->save($targetPath);

        } catch (\Exception $e) {
            throw new \Exception('Une erreur est survenue pendant le traitement de l\'image: ' . $e->getMessage());
        }

        return $fileName;
    }

    /**
     * Redimensionne une image existante
     */
    public function resize(string $filename, int $width = 300, int $height = 150): bool
    {
        $filePath = $this->getTargetDirectory() . '/' . $filename;

        if (!file_exists($filePath)) {
            return false;
        }

        try {
            $image = $this->imageManager->read($filePath);
            $image->cover($width, $height);
            $image->toJpeg(85)->save($filePath);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Crée une miniature d'une image existante
     */
    public function createThumbnail(string $filename, string $thumbFilename, int $width = 100, int $height = 100): bool
    {
        $filePath = $this->getTargetDirectory() . '/' . $filename;
        $thumbPath = $this->getTargetDirectory() . '/thumbnails/' . $thumbFilename;

        // Créer le répertoire des miniatures s'il n'existe pas
        if (!is_dir($this->getTargetDirectory() . '/thumbnails/')) {
            mkdir($this->getTargetDirectory() . '/thumbnails/', 0755, true);
        }

        if (!file_exists($filePath)) {
            return false;
        }

        try {
            $image = $this->imageManager->read($filePath);
            $image->cover($width, $height);
            $image->toJpeg(80)->save($thumbPath);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getTargetDirectory(): string
    {
        return $this->params->get('story_images_directory');
    }

    public function remove(string $filename): bool
    {
        $file = $this->getTargetDirectory() . '/' . $filename;
        $thumb = $this->getTargetDirectory() . '/thumbnails/' . $filename;

        $success = true;

        if (file_exists($file)) {
            $success = unlink($file) && $success;
        }

        if (file_exists($thumb)) {
            $success = unlink($thumb) && $success;
        }

        return $success;
    }
}