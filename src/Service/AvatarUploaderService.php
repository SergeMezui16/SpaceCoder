<?php
namespace App\Service;

use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

/**
 * Upload avatar file
 *
 * This Service help to upload avatar file safely in a directory
 */
class AvatarUploaderService {

    /** @var string upload target directory  */
    public const TARGET_DIRECTORY = 'data/user/avatar';

    public function __construct(private SluggerInterface $slugger)
    {}

    /**
     * Set a safe name and move the uploaded file to a directory
     *
     * @param UploadedFile $file File uploaded
     * @param string $name Unique name to give to the file
     * @return string Path of the file after upload
     * 
     * @throws FileException
     */
    public function upload(UploadedFile $file, string $name): string
    {
        // $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

        $safeFilename = $this->slugger->slug($name.'-spacecoder');
        $fileName = $safeFilename.'.'.$file->guessExtension();

        try {
            $file->move($this::TARGET_DIRECTORY, $fileName);
        } catch (FileException $e) {
            throw $e;
        }

        return '/' . $this::TARGET_DIRECTORY .'/'. $fileName;
    }

    /**
     * Get upload target directory
     *
     * @return string
     */
    public function getTargetDirectory(): string
    {
        return $this::TARGET_DIRECTORY;
    }
}