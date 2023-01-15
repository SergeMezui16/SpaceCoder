<?php
namespace App\Service;

use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Upload Files
 * 
 * This Service help to update files safely
 */
class FileUploaderService
{

    public function __construct(private SluggerInterface $slugger)
    {}

    /**
     * Set a safe and unique name and move the uploaded file to a directory
     *
     * @param UploadedFile $file
     * @param string $targetDirectory
     * @return string safe file name
     * 
     * @throws FileException
     */
    public function upload(UploadedFile $file, string $targetDirectory): string
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $fileName = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

        try {
            $file->move($targetDirectory, $fileName);
        } catch (FileException $e) {
            throw $e;
        }

        return $fileName;
    }

    public function getTargetDirectory(): string
    {
        return '';
    }
}