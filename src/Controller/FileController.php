<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FileController extends AbstractController
{
    /**
     * @Route("/makeDir", name="makeDirectory")
     */
    public function index(): Response
    {
        $fileSystem = new Filesystem();
        try {
            $fileSystem->mkdir(
                Path::normalize(sys_get_temp_dir() . '/fileDemo_' . random_int(0, 1000)),
            );
            return new Response("Direcory created in tmp!");
        } catch (IOExceptionInterface $exception) {
            return new Response("An error occurred while creating your directory at " . $exception->getPath());
        }
        //return new Response("Welcome");
    }

    /**
     * @Route("/permissions", name="changePermissions")
     */
    public function changePermissions(): Response
    {
        $fileSystem = new Filesystem();
        try {
            $current_dir_path = getcwd();
            echo "Current Dir Path is: " . $current_dir_path . "<br/>";
            $new_dir_path = $current_dir_path . "/foo";

            if (!$fileSystem->exists($new_dir_path)) {
                $old = umask(0);
                $fileSystem->mkdir($new_dir_path, 0775);
                $fileSystem->chown($new_dir_path, "riyajoshi");
                $fileSystem->chgrp($new_dir_path, "riyajoshi");
                umask($old);
            }
            return new Response("Directory created and Permissions set!");
        } catch (IOExceptionInterface $exception) {
            return new Response("An error occurred while creating your directory at " . $exception);
        }
    }

    /**
     * @Route("/addContent", name="addContent")
     */
    public function addContent(): Response
    {
        $fileSystem = new Filesystem();
        try {
            $current_dir_path = getcwd();
            $new_file_path = $current_dir_path . "/foo/bar.txt";

            if (!$fileSystem->exists($new_file_path)) {
                $fileSystem->touch($new_file_path);
                $fileSystem->chmod($new_file_path, 0777);
                $fileSystem->dumpFile($new_file_path, "Adding dummy content to bar.txt file.\n");
                $fileSystem->appendToFile($new_file_path, "This should be added to the end of the file.\n");
            }
            return new Response("File created!");
        } catch (IOExceptionInterface $exception) {
            return new Response("Error creating file at " . $exception->getPath());
        }
    }

    /**
     * @Route("/copyFile", name="copyFile")
     */
    public function copyFile(): Response
    {
        $fileSystem = new Filesystem();
        try {
            $current_dir_path = getcwd();
            $src_file_path = $current_dir_path . '/foo/bar.txt';
            $dest_file_path = $current_dir_path . '/foo/bar_copy.txt';
            $renamed_dest_file_path = $current_dir_path . '/foo/bar1.txt';

            $fileSystem->copy($src_file_path, $dest_file_path);
            $fileSystem->rename($dest_file_path, $renamed_dest_file_path);

            return new Response("File copied and renamed!");
        } catch (IOExceptionInterface $exception) {
            return new Response("Error copying and renaming file at ". $exception->getPath());
        }
    }

    /**
     * @Route("/copyDir", name="copyDirectory")
     */
    public function copyDirectory(): Response
    {
        $fileSystem = new Filesystem();
        try {
            $current_dir_path = getcwd();
            $src_dir_path = $current_dir_path . "/foo";
            $dest_dir_path = $current_dir_path . "/foo_copy";

            $fileSystem->mirror($src_dir_path, $dest_dir_path);
            
            return new Response("Directory copied!");
        } catch (IOExceptionInterface $exception) {
            return new Response("Error copying directory at ". $exception->getPath());
        }
    }

    /**
     * @Route("/removeDir", name="removeDirectory")
     */
    public function removeDirectory(): Response
    {
        $fileSystem = new Filesystem();
        try {
            $current_dir_path = getcwd();
            $arr_dirs = array(
                $current_dir_path . "/foo",
                $current_dir_path . "/foo_copy"
            );        
            $fileSystem->remove($arr_dirs);
            return new Response("Directory deleted!");
        } catch (IOExceptionInterface $exception) {
            return new Response("Error deleting directory at". $exception->getPath());
        }
    }
}
