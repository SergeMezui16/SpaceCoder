<?php
namespace App\Service;

use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class AvatarUploaderService {

    public const TARGET_DIRECTORY = 'data/user/avatar';
    
    public function __construct(private SluggerInterface $slugger)
    {}

    public function upload(UploadedFile $file, string $pseudo)
    {
        // $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        
        $safeFilename = $this->slugger->slug($pseudo.'-spacecoder');
        $fileName = $safeFilename.'.'.$file->guessExtension();

        try {
            $file->move($this::TARGET_DIRECTORY, $fileName);
        } catch (FileException $e) {
            throw $e;
        }

        return '/' . $this::TARGET_DIRECTORY .'/'. $fileName;
    }

    public function getTargetDirectory()
    {
        return $this::TARGET_DIRECTORY;
    }
}